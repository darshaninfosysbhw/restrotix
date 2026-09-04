<?php

namespace App\Http\Controllers\Waiter;

use App\Events\KitchenPickupAlertUpdated;
use App\Http\Controllers\Controller;
use App\Models\KitchenPickupAlert;
use App\Services\KitchenPickupAlertService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KitchenPickupAlertController extends Controller
{
    public function index(Request $request, KitchenPickupAlertService $service): JsonResponse
    {
        $user = $request->user();
        $alerts = KitchenPickupAlert::query()
            ->where('tenant_id', $user->tenant_id)
            ->where('branch_id', $user->branch_id)
            ->where('status', 'pending')
            ->with('order.items')
            ->oldest('ready_at')
            ->get()
            ->map(fn ($alert) => $service->payload($alert));

        return response()->json(['alerts' => $alerts]);
    }

    public function accept(Request $request, KitchenPickupAlert $alert, KitchenPickupAlertService $service): JsonResponse
    {
        $user = $request->user();
        abort_unless((int) $alert->tenant_id === (int) $user->tenant_id
            && (int) $alert->branch_id === (int) $user->branch_id, 404);

        $accepted = DB::transaction(function () use ($alert, $user) {
            return KitchenPickupAlert::whereKey($alert->id)
                ->where('status', 'pending')
                ->update([
                    'status' => 'accepted',
                    'accepted_at' => now(),
                    'accepted_by_waiter_id' => $user->id,
                    'updated_at' => now(),
                ]);
        });

        $alert->refresh();
        if (! $accepted) {
            return response()->json(['message' => 'This KOT has already been picked up.'], 409);
        }

        $payload = $service->payload($alert);
        broadcast(new KitchenPickupAlertUpdated($payload))->toOthers();

        return response()->json(['message' => 'KOT accepted for pickup.', 'alert' => $payload]);
    }
}
