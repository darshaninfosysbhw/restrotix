<?php

namespace App\Services;

use App\Events\QrOrderSubmissionUpdated;
use App\Http\Controllers\Modules\Orders\OrderController;
use App\Models\MenuItem;
use App\Models\MenuItemAddon;
use App\Models\MenuItemVariant;
use App\Models\QrOrderSubmission;
use App\Models\Table;
use App\Models\TableAccessSession;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class QrOrderSubmissionService
{
    public function normalizeItems(array $items, Table $table): array
    {
        return array_map(function ($item) use ($table) {
            $menu = MenuItem::query()->where('tenant_id', $table->tenant_id)
                ->where(fn ($query) => $query->where('branch_id', $table->branch_id)->orWhereNull('branch_id'))->where('is_active', true)
                ->where('is_available', true)->find($item['id']);
            if (! $menu) {
                throw ValidationException::withMessages(['items' => 'An item is unavailable. Please refresh the menu.']);
            }
            $variantId = (int) (explode('_', $item['unique_key'] ?? '')[1] ?? 0);
            $variant = $variantId ? MenuItemVariant::where('menu_item_id', $menu->id)->where('is_available', true)->find($variantId) : null;
            if (($variantId && ! $variant) || ($menu->has_variants && ! $variant)) {
                throw ValidationException::withMessages(['items' => 'Please select an available item variant.']);
            }
            $priceSource = $variant ?? $menu;
            $item['name'] = $menu->name;
            $item['price'] = (float) ($priceSource->sale_price > 0 ? $priceSource->sale_price : $priceSource->base_price);
            $item['variant_name'] = $variant?->name ?? '';
            $item['unique_key'] = $menu->id.'_'.($variant?->id ?? 0).'_0_none';
            $item['addons'] = array_map(function ($addon) use ($menu) {
                $master = MenuItemAddon::where('menu_item_id', $menu->id)->where('is_active', true)->find($addon['id'] ?? $addon['menu_item_addon_id'] ?? 0);
                if (! $master) {
                    throw ValidationException::withMessages(['items' => 'An add-on is unavailable. Please refresh the menu.']);
                }

                return ['id' => $master->id, 'name' => $master->name, 'price' => (float) $master->price, 'quantity' => max(1, (int) ($addon['quantity'] ?? 1))];
            }, $item['addons'] ?? []);

            return $item;
        }, $items);
    }

    public function submit(Table $table, TableAccessSession $session, Request $request): QrOrderSubmission
    {
        return DB::transaction(function () use ($table, $session, $request) {
            $table = Table::whereKey($table->id)->lockForUpdate()->firstOrFail();
            $session = TableAccessSession::whereKey($session->id)->firstOrFail();
            if (! $table->is_active || ! $session->isActive()) {
                throw ValidationException::withMessages(['order' => 'Table session has ended. Scan the QR again.']);
            }
            $key = $request->input('request_key');
            if ($key && ($existing = QrOrderSubmission::where('table_access_session_id', $session->id)->where('request_key', $key)->first())) {
                return $existing;
            }
            $items = $this->normalizeItems($request->items, $table);
            $subtotal = collect($items)->sum(fn ($item) => $item['price'] * $item['quantity'] + collect($item['addons'])->sum(fn ($addon) => $addon['price'] * $addon['quantity']));
            $branch = $table->branch;
            $total = $branch->tax_setting === 'inclusive' ? $subtotal : $subtotal * (1 + (float) $branch->tax_rate / 100);
            $submission = QrOrderSubmission::create([
                'public_token' => (string) Str::uuid(), 'tenant_id' => $table->tenant_id, 'branch_id' => $table->branch_id,
                'table_id' => $table->id, 'table_access_session_id' => $session->id, 'request_key' => $key,
                'payload' => ['items' => $items, 'table_id' => $table->id, 'order_type' => 'dine_in', 'source' => 'qr', 'tax_setting' => $branch->tax_setting, 'tax_rate' => $branch->tax_rate, 'overall_instructions' => $request->input('overall_instructions')],
                'total' => round($total, 2), 'quantity' => collect($items)->sum('quantity'),
            ]);
            app(\App\Services\PublicMenu\TableAccessSessionService::class)->touchSession($session, $request);
            if (! $branch->auto_accept_qr_orders) {
                DB::afterCommit(fn () => $this->notify($submission));
            }

            return $submission;
        });
    }

    public function handle(QrOrderSubmission $submission, ?User $user, bool $accept, ?string $reason = null): QrOrderSubmission
    {
        $locked = false;
        try {
            if ($accept && DB::getDriverName() === 'mysql') {
                $locked = (int) DB::selectOne('SELECT GET_LOCK(?, 10) AS acquired', ['restochain_kot_number_generation'])->acquired === 1;
                if (! $locked) {
                    throw ValidationException::withMessages(['order' => 'Kitchen is busy. Please retry.']);
                }
            }

            return DB::transaction(function () use ($submission, $user, $accept, $reason) {
                $submission = QrOrderSubmission::whereKey($submission->id)->lockForUpdate()->firstOrFail();
                if ($submission->status !== 'pending') {
                    return $submission;
                }
                $table = Table::whereKey($submission->table_id)->lockForUpdate()->firstOrFail();
                if ($accept) {
                    $session = $submission->accessSession;
                    if (! $session?->isActive() || ! $table->is_active) {
                        throw ValidationException::withMessages(['order' => 'Table session has ended. Reject this request instead.']);
                    }
                    $payload = $submission->payload;
                    if ((string) $table->branch->tax_setting !== (string) $payload['tax_setting'] || (float) $table->branch->tax_rate !== (float) $payload['tax_rate']) {
                        throw ValidationException::withMessages(['order' => 'Tax settings changed. Reject this request and ask the guest to submit again.']);
                    }
                    // Recheck availability; keep the submitted price snapshot for the customer's confirmed amount.
                    $this->normalizeItems($payload['items'], $table);
                    $request = Request::create('/place-order', 'POST', $payload);
                    $request->attributes->set('approved_qr_submission', $submission);
                    $result = app(OrderController::class)->store($request);
                    if ($result->getStatusCode() >= 400 || ! $result->getData(true)['success']) {
                        throw ValidationException::withMessages(['order' => 'Could not accept this order. Please retry.']);
                    }
                    $data = $result->getData(true);
                    $submission->order_id = $data['order_id'];
                    $submission->kot_number = $data['kot_number'];
                }
                $submission->fill(['status' => $accept ? 'accepted' : 'rejected', 'handled_by' => $user?->id,
                    'handled_at' => now(), 'rejection_reason' => $accept ? null : ($reason ?: 'Unable to fulfil this order.')])->save();
                DB::afterCommit(fn () => $this->notify($submission));

                return $submission;
            });
        } finally {
            if ($locked) {
                DB::selectOne('SELECT RELEASE_LOCK(?)', ['restochain_kot_number_generation']);
            }
        }
    }

    private function notify(QrOrderSubmission $submission): void
    {
        try {
            event(new QrOrderSubmissionUpdated(['id' => $submission->id, 'branch_id' => $submission->branch_id, 'status' => $submission->status]));
        } catch (\Throwable $exception) {
            report($exception);
        } // Polling restores notifications when the socket is unavailable.
    }
}
