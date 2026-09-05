<?php

namespace App\Http\Controllers\Modules\Kds;

use App\Http\Controllers\Controller;
use App\Models\KitchenNotificationLog;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class KitchenNotificationController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $branchId = $this->branchId($user);

        $notifications = KitchenNotificationLog::query()
            ->where('tenant_id', $user->tenant_id)
            ->where('branch_id', $branchId)
            ->whereNull('cleared_at')
            ->with(['cancelledBy:id,name,role'])
            ->latest('cancelled_at')
            ->limit(50)
            ->get()
            ->map(fn (KitchenNotificationLog $notification) => $this->payload($notification));

        return response()->json(['notifications' => $notifications]);
    }

    public function history(Request $request): JsonResponse
    {
        $user = $request->user();
        $branchId = $this->branchId($user);

        $notifications = KitchenNotificationLog::query()
            ->where('tenant_id', $user->tenant_id)
            ->where('branch_id', $branchId)
            ->with(['cancelledBy:id,name,role', 'openedBy:id,name', 'clearedBy:id,name'])
            ->latest('cancelled_at')
            ->paginate(min((int) $request->input('per_page', 50), 100));

        $notifications->getCollection()->transform(fn (KitchenNotificationLog $notification) => $this->payload($notification, true));

        return response()->json($notifications);
    }

    public function markOpened(Request $request): JsonResponse
    {
        $user = $request->user();
        $branchId = $this->branchId($user);

        KitchenNotificationLog::query()
            ->where('tenant_id', $user->tenant_id)
            ->where('branch_id', $branchId)
            ->whereNull('cleared_at')
            ->whereNull('opened_at')
            ->update([
                'opened_at' => now(),
                'opened_by' => $user->id,
                'updated_at' => now(),
            ]);

        return response()->json(['success' => true]);
    }

    public function clear(Request $request): JsonResponse
    {
        $user = $request->user();
        $branchId = $this->branchId($user);

        KitchenNotificationLog::query()
            ->where('tenant_id', $user->tenant_id)
            ->where('branch_id', $branchId)
            ->whereNull('cleared_at')
            ->update([
                'cleared_at' => now(),
                'cleared_by' => $user->id,
                'updated_at' => now(),
            ]);

        return response()->json(['success' => true]);
    }

    private function branchId(object $user): int
    {
        abort_unless($user->tenant_id && in_array(strtolower((string) $user->role), [
            'chef', 'admin', 'manager', 'superadmin',
        ], true), 403);

        $branchId = (int) session('active_branch_id', $user->branch_id ?? 0);
        abort_unless($branchId > 0, 403);

        return $branchId;
    }

    private function payload(KitchenNotificationLog $notification, bool $includeAudit = false): array
    {
        $data = [
            'id' => $notification->id,
            'order_id' => $notification->order_id,
            'order_item_id' => $notification->order_item_id,
            'item_name' => $notification->item_name,
            'table_number' => $notification->table_number,
            'reason' => $notification->reason,
            'cancelled_at' => optional($notification->cancelled_at)->toIso8601String(),
            'cancelled_by' => $notification->cancelledBy?->name ?? 'Unknown',
            'opened_at' => optional($notification->opened_at)->toIso8601String(),
            'cleared_at' => optional($notification->cleared_at)->toIso8601String(),
        ];

        if ($includeAudit) {
            $data['opened_by'] = $notification->openedBy?->name;
            $data['cleared_by'] = $notification->clearedBy?->name;
            $data['created_at'] = optional($notification->created_at)->toIso8601String();
            $data['updated_at'] = optional($notification->updated_at)->toIso8601String();
        }

        return $data;
    }
}
