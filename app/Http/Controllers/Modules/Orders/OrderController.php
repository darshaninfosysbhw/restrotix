<?php

namespace App\Http\Controllers\Modules\Orders;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Branch;
use App\Models\MenuItem;
use App\Models\MenuItemAddon;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderItemAddon;
use App\Models\TableAccessSession;
use App\Models\Table;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Events\NewOrderReceived;
use Illuminate\Support\Facades\Auth;
use App\Services\PublicMenu\TableAccessSessionService;

class OrderController extends Controller
{
    protected TableAccessSessionService $tableAccessSessionService;

    public function __construct(TableAccessSessionService $tableAccessSessionService)
    {
        $this->tableAccessSessionService = $tableAccessSessionService;
    }

    public function store(Request $request)
    {
        // 🌟 FIX 1: unique_key aur addons ke inner attributes ko nullable/optional kiya taaki Waiter/POS panel na toote
        $request->validate([
            'items'                    => 'required|array|min:1',
            'items.*.id'               => 'required',
            'items.*.unique_key'       => 'nullable|string', // Changed from required to nullable
            'items.*.name'             => 'required|string',
            'items.*.price'            => 'required|numeric',
            'items.*.quantity'         => 'required|integer|min:1',
            'items.*.variant_name'     => 'nullable|string',
            'items.*.notes'            => 'nullable|string',
            'items.*.addons'           => 'nullable|array',
            'table_id'                 => 'nullable|integer|exists:tables,id',
            'session_token'            => 'nullable|string|max:255',
            'client_latitude'          => 'nullable|numeric|between:-90,90',
            'client_longitude'         => 'nullable|numeric|between:-180,180',
            'order_type'               => 'required|string',
            'source'                   => 'nullable|in:waiter,qr,web,pos',
            'overall_instructions'     => 'nullable|string'
        ]);

        $kotNumberLockAcquired = false;

        try {
            $cartItems = $request->items;
            $user = Auth::user();
            $contextTable = null;
            $tableSession = null;

            if ($request->filled('table_id')) {
                $contextTable = Table::query()
                    ->with('branch')
                    ->find((int) $request->table_id);

                if ($contextTable && $request->filled('qr_token')) {
                    $incomingQrToken = trim((string) $request->qr_token);

                    if ($incomingQrToken !== '' && strcasecmp((string) $contextTable->qr_token, $incomingQrToken) !== 0) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Table QR token mismatch.',
                        ], 422);
                    }
                }
            } elseif ($request->filled('qr_token')) {
                $contextTable = Table::query()
                    ->with('branch')
                    ->where('qr_token', trim((string) $request->qr_token))
                    ->first();
            }

            if (!$contextTable && !$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unable to resolve table context for this order.',
                ], 422);
            }

            if (!$user && $contextTable) {
                $sessionToken = trim((string) $request->input('session_token', ''));
                $latestSession = $this->tableAccessSessionService->getLatestSessionForTable($contextTable);
                $coolingDownMessage = 'This table is resetting. Please scan again after a few minutes.';

                if ($sessionToken === '') {
                    if ($latestSession?->isCoolingDown()) {
                        return response()->json([
                            'success' => false,
                            'message' => $coolingDownMessage,
                        ], 422);
                    }

                    return response()->json([
                        'success' => false,
                        'message' => 'Table session expired. Please scan the QR again.',
                    ], 422);
                }

                $tableSession = $this->tableAccessSessionService->findValidSessionForTable($contextTable, $sessionToken);

                if (!$tableSession) {
                    if ($latestSession?->isCoolingDown()) {
                        return response()->json([
                            'success' => false,
                            'message' => $coolingDownMessage,
                        ], 422);
                    }

                    return response()->json([
                        'success' => false,
                        'message' => 'Table session expired. Please scan the QR again.',
                    ], 422);
                }

                $branch = $contextTable->branch;
                $branchLatitude = $branch?->latitude !== null ? (float) $branch->latitude : null;
                $branchLongitude = $branch?->longitude !== null ? (float) $branch->longitude : null;

                if ($branchLatitude !== null && $branchLongitude !== null) {
                    $clientLatitude = $request->filled('client_latitude') ? (float) $request->input('client_latitude') : null;
                    $clientLongitude = $request->filled('client_longitude') ? (float) $request->input('client_longitude') : null;

                    if ($clientLatitude === null || $clientLongitude === null) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Please allow location access to place this order.',
                        ], 422);
                    }

                    $distanceMeters = $this->calculateDistanceMeters(
                        $branchLatitude,
                        $branchLongitude,
                        $clientLatitude,
                        $clientLongitude
                    );

                    if ($distanceMeters > 50) {
                        return response()->json([
                            'success' => false,
                            'message' => 'You must be within 50 meters of the restaurant to place an order.',
                        ], 422);
                    }
                }
            }

            DB::beginTransaction();

            $tenantId = $contextTable
                ? (int) ($contextTable->tenant_id ?? $contextTable->branch?->tenant_id ?? 0)
                : (int) ($user?->tenant_id ?? 0);

            $branchId = $contextTable
                ? ((int) ($contextTable->branch_id ?? $contextTable->branch?->id ?? 0) > 0
                    ? (int) ($contextTable->branch_id ?? $contextTable->branch?->id ?? 0)
                    : null)
                : ($user?->branch_id ? (int) $user->branch_id : null);

            $tableId = $contextTable ? (int) $contextTable->id : (int) ($request->table_id ?? 0);
            $tableNumber = $contextTable
                ? (string) $contextTable->table_number
                : (string) $request->table_number;

            if ($tenantId <= 0) {
                DB::rollBack();

                return response()->json([
                    'success' => false,
                    'message' => 'Tenant context missing for order creation.',
                ], 422);
            }

            // 🚀 STEP 1: Same table ka running order find karo
            $order = null;
            if ($request->order_type === 'dine_in' && $tableId) {
                $order = Order::where('table_id', $tableId)
                    ->where('status', 'running')
                    ->first();
            }

            // 🚀 STEP 2: Naya Order Invoice Create Karo agar Running nahi hai
            if (!$order) {
                $order = Order::create([
                    'tenant_id'       => $tenantId,
                    'branch_id'       => $branchId,
                    'table_id'        => $tableId ?: null,
                    'order_number'    => 'ORD-' . strtoupper(uniqid()),
                    'table_number'    => $tableNumber,
                    'order_type'      => $request->order_type ?? 'dine_in',
                    'subtotal'        => 0,
                    'discount_amount' => 0,
                    'tax_amount'      => 0,
                    'grand_total'     => 0,
                    'status'          => 'running',
                    'payment_status'  => 'pending',
                    'notes'           => $request->overall_instructions,
                    'source'          => $request->source ?? 'qr',
                    'created_by'      => Auth::id(),
                ]);
            } else {
                if ($request->filled('overall_instructions')) {
                    $order->notes = $order->notes ? $order->notes . ' | ' . $request->overall_instructions : $request->overall_instructions;
                }
                $order->save();
            }

            $kotNumberLockAcquired = $this->acquireKotNumberLock();
            $kotNumber = $this->generateNextKotNumber();

            if (empty($order->ordered_at)) {
                $order->ordered_at = $order->created_at ?? now();
                $order->save();
            }

            $submittedQuantityCount = 0;
            $submittedLineCount = 0;

            // 🚀 STEP 3: Multi-Source Items Parsing (POS + Waiter App + QR)
            foreach ($cartItems as $item) {
                $submittedQuantityCount += max((int) ($item['quantity'] ?? 0), 0);
                $submittedLineCount++;
                $itemSource = trim((string) ($request->source ?? $order->source ?? 'manual'));
                $itemCreatedBy = Auth::id();
                $incomingMenuItemId = (int) ($item['id'] ?? 0);
                $resolvedMenuItemId = $this->resolveMenuItemIdForOrderItem($item, $incomingMenuItemId);

                // 🌟 FIX 2: Fallback Engine - Agar unique_key missing hai (Waiter/POS Panel), to default string banao
                $uniqueKey = $item['unique_key'] ?? ($item['id'] . '_0_0_none');

                $keyParts = explode('_', $uniqueKey);
                $variantId = (isset($keyParts[1]) && $keyParts[1] !== '0') ? (int)$keyParts[1] : null;
                $itemNote = !empty($item['notes']) ? $item['notes'] : null;
                $compiledName = trim((string) ($item['name'] ?? ''));
                if (!empty($item['variant_name'])) {
                    $compiledName .= ' (' . $item['variant_name'] . ')';
                }

                $potentialDuplicates = OrderItem::where('order_id', $order->id)
                    ->where('kot_number', $kotNumber)
                    ->where('menu_item_variant_id', $variantId)
                    ->where('notes', $itemNote)
                    ->when(
                        !empty($resolvedMenuItemId),
                        function ($query) use ($resolvedMenuItemId) {
                            $query->where('menu_item_id', $resolvedMenuItemId);
                        },
                        function ($query) use ($compiledName) {
                            $query->where('item_name', $compiledName);
                        }
                    )
                    // Only merge with a matching NEW line. Preparing/ready lines must stay separate.
                    ->where('status', 'new')
                    ->with('orderItemAddons')
                    ->get();

                $existingItem = null;

                foreach ($potentialDuplicates as $candidate) {
                    $candidateTokens = $candidate->orderItemAddons->map(function ($addon) {
                        return $addon->menu_item_addon_id . '-' . $addon->quantity;
                    })->sort()->values()->toArray();

                    $incomingTokens = collect($item['addons'] ?? [])->map(function ($addon) {
                        // Safe extraction backing up waiter app payload key maps
                        $addonId = $addon['id'] ?? ($addon['menu_item_addon_id'] ?? null);
                        $addonQty = $addon['quantity'] ?? 1;
                        return $addonId . '-' . $addonQty;
                    })->filter()->sort()->values()->toArray();

                    if ($candidateTokens === $incomingTokens) {
                        $existingItem = $candidate;
                        break;
                    }
                }

                if ($existingItem) {
                    $existingItem->quantity += $item['quantity'];
                    if ((int) ($existingItem->kot_number ?? 0) <= 0) {
                        $existingItem->kot_number = $kotNumber;
                    }
                    $existingItem->save();

                    if (isset($item['addons']) && is_array($item['addons'])) {
                        foreach ($item['addons'] as $incomingAddon) {
                            $incomingAddonId = $incomingAddon['id'] ?? ($incomingAddon['menu_item_addon_id'] ?? null);
                            $incomingAddonQty = $incomingAddon['quantity'] ?? 1;
                            $incomingAddonPrice = (float) ($incomingAddon['price'] ?? 0);
                            if ($incomingAddonPrice <= 0 && $incomingAddonId) {
                                $masterAddon = MenuItemAddon::find($incomingAddonId);
                                $incomingAddonPrice = (float) ($masterAddon?->price ?? 0);
                            }

                            $dbAddon = OrderItemAddon::where('order_item_id', $existingItem->id)
                                ->where('menu_item_addon_id', $incomingAddonId)
                                ->first();
                            if ($dbAddon) {
                                $dbAddon->quantity += $incomingAddonQty;
                                if ((float) ($dbAddon->price ?? 0) <= 0 && $incomingAddonPrice > 0) {
                                    $dbAddon->price = $incomingAddonPrice;
                                }
                                $dbAddon->save();
                            }
                        }
                    }

                    $freshAddonsSum = OrderItemAddon::where('order_item_id', $existingItem->id)
                        ->get()
                        ->reduce(function ($sum, $addonInstance) {
                            return $sum + ($addonInstance->price * $addonInstance->quantity);
                        }, 0);

                    $existingItem->total = ($existingItem->quantity * $existingItem->price) + $freshAddonsSum;
                    $existingItem->save();
                } else {
                    $incomingAddonsSum = 0;
                    if (isset($item['addons']) && is_array($item['addons'])) {
                        foreach ($item['addons'] as $addon) {
                            $addonQty = $addon['quantity'] ?? 1;
                            $addonPrice = (float) ($addon['price'] ?? 0);
                            $incomingAddonId = $addon['id'] ?? ($addon['menu_item_addon_id'] ?? null);
                            if ($addonPrice <= 0 && $incomingAddonId) {
                                $masterAddon = MenuItemAddon::find($incomingAddonId);
                                $addonPrice = (float) ($masterAddon?->price ?? 0);
                            }
                            $incomingAddonsSum += ($addonPrice * $addonQty);
                        }
                    }

                    $orderItem = OrderItem::create([
                        'order_id'             => $order->id,
                        'source'               => $itemSource !== '' ? $itemSource : 'manual',
                        'created_by'           => $itemCreatedBy,
                        'kot_number'           => $kotNumber,
                        'menu_item_id'         => $resolvedMenuItemId,
                        'menu_item_variant_id' => $variantId,
                        'item_name'            => $compiledName,
                        'price'                => $item['price'],
                        'quantity'             => $item['quantity'],
                        'total'                => ($item['price'] * $item['quantity']) + $incomingAddonsSum,
                        'notes'                => $itemNote,
                        'status'               => 'new'
                    ]);

                    if (isset($item['addons']) && is_array($item['addons'])) {
                        foreach ($item['addons'] as $addon) {
                            $incomingAddonId = $addon['id'] ?? ($addon['menu_item_addon_id'] ?? null);
                            $addonQty = $addon['quantity'] ?? 1;
                            $addonPrice = (float) ($addon['price'] ?? 0);
                            if ($addonPrice <= 0 && $incomingAddonId) {
                                $masterAddon = MenuItemAddon::find($incomingAddonId);
                                $addonPrice = (float) ($masterAddon?->price ?? 0);
                            }

                            if ($incomingAddonId) {
                                OrderItemAddon::create([
                                    'order_item_id'      => $orderItem->id,
                                    'menu_item_addon_id' => $incomingAddonId,
                                    'addon_name'         => $addon['name'],
                                    'price'              => $addonPrice,
                                    'quantity'           => $addonQty
                                ]);
                            }
                        }
                    }
                }
            }

            // 🚀 STEP 4: Live Order Totals Recalculation
            $newSubtotal = OrderItem::where('order_id', $order->id)->sum('total');
            $branch = Branch::find($branchId);
            $taxSetting = $branch?->tax_setting ?? 'exclusive';
            // $taxRate = 0.05;
            $taxRate = (float) ($branch?->tax_rate ?? 5.00) / 100;

            if ($taxSetting === 'inclusive') {
                $taxAmount = $newSubtotal - ($newSubtotal / (1 + $taxRate));
                $grandTotal = $newSubtotal;
            } else {
                $taxAmount = $newSubtotal * $taxRate;
                $grandTotal = $newSubtotal + $taxAmount;
            }

            $order->update([
                'subtotal'    => $newSubtotal,
                'tax_amount'  => round($taxAmount, 2),
                'grand_total' => round($grandTotal, 2),
            ]);

            if ($order->items()->whereIn('status', ['new', 'pending'])->exists()) {
                $order->update(['kitchen_status' => 'pending']);
            }

            // 🚀 STEP 5: Occupy Table Entity
            if ($order->order_type === 'dine_in' && $order->table_id) {
                Table::where('id', $order->table_id)->update(['status' => 'occupied']);
            }

            if ($tableSession instanceof TableAccessSession) {
                $this->tableAccessSessionService->touchSession($tableSession, $request);
            }

            // 🚀 STEP 6: Broadcast Event to Live KDS Monitors / Kitchen Panels
            broadcast(new \App\Events\NewOrderReceived([
                'table_id'     => $order->table_id,
                'table_number' => $order->table_number,
                'order_number' => $order->order_number,
                'order_id'     => $order->id,
                'kot_number'   => $kotNumber,
                'batch_key'    => $order->id . ':' . $kotNumber,
                'items_count'  => $submittedQuantityCount,
                'line_items_count' => $submittedLineCount,
                'tenant_id'    => $order->tenant_id,
                'branch_id'    => $order->branch_id,
            ]))->toOthers();

            DB::commit();

            $redirectUrl = null;
            if ($order->table_id) {
                $table = Table::find($order->table_id);
                if ($table?->qr_token) {
                    $redirectUrl = route('public.order.status', ['qr_token' => $table->qr_token]);
                }
            } elseif ($request->filled('qr_token')) {
                $redirectUrl = route('public.order.status', ['qr_token' => $request->qr_token]);
            }

            return response()->json([
                'success'  => true,
                'message'  => 'Order processed successfully across all platform sources',
                'order_id' => $order->id,
                'kot_number' => $kotNumber,
                'redirect_url' => $redirectUrl,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Order store failed', [
                'message' => $e->getMessage(),
                'request' => $request->all(),
                'user_id' => Auth::id(),
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong inside backend engine transaction layer',
                'error'   => $e->getMessage()
            ], 500);
        } finally {
            if ($kotNumberLockAcquired) {
                $this->releaseKotNumberLock();
            }
        }
    }

    private function generateNextKotNumber(): int
    {
        $lastKotNumber = (int) (OrderItem::query()
            ->whereNotNull('kot_number')
            ->whereHas('order', function ($query) {
                $query->where('status', 'running');
            })
            ->max('kot_number') ?? 0);

        return $lastKotNumber + 1;
    }

    private function acquireKotNumberLock(): bool
    {
        $result = DB::selectOne('SELECT GET_LOCK(?, 10) AS lock_status', ['restochain_kot_number_generation']);

        if ((int) ($result->lock_status ?? 0) !== 1) {
            throw new \RuntimeException('Unable to reserve KOT number.');
        }

        return true;
    }

    private function releaseKotNumberLock(): void
    {
        DB::selectOne('SELECT RELEASE_LOCK(?) AS release_status', ['restochain_kot_number_generation']);
    }

    private function resolveMenuItemIdForOrderItem(array $item, int $incomingMenuItemId): ?int
    {
        if ($incomingMenuItemId > 0) {
            $menuItem = MenuItem::withTrashed()->find($incomingMenuItemId);
            if ($menuItem) {
                return (int) $menuItem->id;
            }
        }

        $itemName = trim((string) ($item['name'] ?? ''));
        if ($itemName !== '') {
            $normalizedName = function_exists('mb_strtolower')
                ? mb_strtolower($itemName)
                : strtolower($itemName);

            $menuItem = MenuItem::withTrashed()
                ->whereRaw('LOWER(TRIM(name)) = ?', [$normalizedName])
                ->first();

            if ($menuItem) {
                return (int) $menuItem->id;
            }
        }

        Log::warning('Menu item could not be resolved for order item; storing null menu_item_id.', [
            'incoming_menu_item_id' => $incomingMenuItemId,
            'item_name' => $itemName ?? null,
        ]);

        return null;
    }

    private function calculateDistanceMeters(float $lat1, float $lng1, float $lat2, float $lng2): float
    {
        $earthRadius = 6371000;

        $lat1Rad = deg2rad($lat1);
        $lat2Rad = deg2rad($lat2);
        $deltaLat = deg2rad($lat2 - $lat1);
        $deltaLng = deg2rad($lng2 - $lng1);

        $a = sin($deltaLat / 2) ** 2
            + cos($lat1Rad) * cos($lat2Rad) * sin($deltaLng / 2) ** 2;

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }
}
