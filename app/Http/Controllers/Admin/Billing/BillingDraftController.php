<?php

namespace App\Http\Controllers\Admin\Billing;

use App\Http\Controllers\Controller;
use App\Models\Table;
use App\Services\Admin\Billing\BillingDraftService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BillingDraftController extends Controller
{
    public function __construct(
        protected BillingDraftService $billingDraftService
    ) {}

    public function show(Table $table)
    {
        $user = Auth::user();
        abort_unless($user && $user->tenant_id, 403);
        abort_unless((int) $table->tenant_id === (int) $user->tenant_id, 403);

        $draft = $this->billingDraftService->getForTable($table);

        if (!$draft) {
            return response()->json([
                'success' => false,
                'message' => 'No held bill found for this table.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Held bill loaded successfully.',
            'data' => $this->presentDraft($draft),
        ]);
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        abort_unless($user && $user->tenant_id, 403);

        $validated = $request->validate([
            'table_id' => 'required|integer|exists:tables,id',
            'table_number' => 'required|string|max:50',
            'payload' => 'required|array',
            'payload.items' => 'required|array|min:1',
        ]);

        $table = Table::query()
            ->where('tenant_id', (int) $user->tenant_id)
            ->findOrFail((int) $validated['table_id']);

        $payload = array_merge((array) $validated['payload'], [
            'table_id' => (int) $table->id,
            'table_number' => (string) $table->table_number,
        ]);

        $draft = $this->billingDraftService->saveForTable($table, $payload, $user);

        return response()->json([
            'success' => true,
            'message' => 'Bill has been put on hold.',
            'data' => $this->presentDraft($draft),
        ]);
    }

    public function destroy(Table $table)
    {
        $user = Auth::user();
        abort_unless($user && $user->tenant_id, 403);
        abort_unless((int) $table->tenant_id === (int) $user->tenant_id, 403);

        $deleted = $this->billingDraftService->clearForTable($table);

        return response()->json([
            'success' => true,
            'message' => $deleted > 0 ? 'Held bill cleared.' : 'No held bill found.',
            'deleted' => $deleted,
        ]);
    }

    private function presentDraft($draft): array
    {
        return [
            'id' => (int) $draft->id,
            'tenant_id' => (int) $draft->tenant_id,
            'branch_id' => (int) $draft->branch_id,
            'table_id' => (int) $draft->table_id,
            'table_number' => (string) $draft->table_number,
            'order_id' => $draft->order_id ? (int) $draft->order_id : null,
            'held_by_user_id' => $draft->held_by_user_id ? (int) $draft->held_by_user_id : null,
            'held_at' => $draft->held_at?->toIso8601String(),
            'payload' => $draft->payload_json ?? [],
        ];
    }
}
