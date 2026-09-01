<?php

namespace App\Services\Admin\Billing;

use App\Models\BillingDraft;
use App\Models\Table;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class BillingDraftService
{
    public function getForTable(Table $table): ?BillingDraft
    {
        return BillingDraft::query()
            ->where('tenant_id', (int) $table->tenant_id)
            ->where('table_id', (int) $table->id)
            ->latest('id')
            ->first();
    }

    public function saveForTable(Table $table, array $payload, ?User $user = null): BillingDraft
    {
        return DB::transaction(function () use ($table, $payload, $user) {
            $normalizedPayload = array_merge($payload, [
                'tenant_id' => (int) $table->tenant_id,
                'branch_id' => (int) $table->branch_id,
                'table_id' => (int) $table->id,
                'table_number' => (string) $table->table_number,
                'qr_token' => (string) ($table->qr_token ?? ($payload['qr_token'] ?? '')),
            ]);

            $draft = BillingDraft::query()->updateOrCreate(
                [
                    'tenant_id' => (int) $table->tenant_id,
                    'table_id' => (int) $table->id,
                ],
                [
                    'branch_id' => (int) $table->branch_id,
                    'table_number' => (string) $table->table_number,
                    'order_id' => (int) ($payload['order_id'] ?? 0) > 0 ? (int) $payload['order_id'] : null,
                    'held_by_user_id' => $user?->id,
                    'payload_json' => $normalizedPayload,
                    'held_at' => now(),
                ]
            );

            return $draft->refresh();
        });
    }

    public function clearForTable(Table $table): int
    {
        return BillingDraft::query()
            ->where('tenant_id', (int) $table->tenant_id)
            ->where('table_id', (int) $table->id)
            ->delete();
    }
}
