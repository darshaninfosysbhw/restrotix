<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\QrOrderSubmission;
use App\Services\QrOrderSubmissionService;
use Illuminate\Http\Request;

class QrOrderSubmissionController extends Controller
{
    private function branchId(Request $request): int
    {
        $user = $request->user();
        abort_unless($user && in_array($user->role, ['admin', 'manager', 'waiter'], true), 403);

        return $user->role === 'admin' ? (int) session('active_branch_id', $user->branch_id) : (int) $user->branch_id;
    }

    public function index(Request $request)
    {
        $branchId = $this->branchId($request);

        return response()->json(QrOrderSubmission::where('tenant_id', $request->user()->tenant_id)
            ->where('branch_id', $branchId)->where('status', 'pending')->with(['table:id,table_number', 'branch.currency'])->oldest()->get()
            ->map(fn ($submission) => [
                'id' => $submission->id, 'table_number' => $submission->table?->table_number,
                'total' => $submission->total, 'quantity' => $submission->quantity,
                'currency' => $submission->branch?->currency?->symbol ?? '',
                'items' => collect($submission->payload['items'])->map(fn ($item) => ['name' => $item['name'], 'quantity' => $item['quantity'], 'variant' => $item['variant_name'] ?? '', 'notes' => $item['notes'] ?? '', 'addons' => $item['addons'] ?? []]),
                'notes' => $submission->payload['overall_instructions'] ?? '',
                'accept_url' => route('qr-submissions.handle', $submission),
            ]));
    }

    public function handle(Request $request, QrOrderSubmission $submission, QrOrderSubmissionService $service)
    {
        $branchId = $this->branchId($request);
        abort_unless((int) $submission->tenant_id === (int) $request->user()->tenant_id && (int) $submission->branch_id === $branchId, 403);
        $data = $request->validate(['action' => 'required|in:accept,reject', 'reason' => 'nullable|string|max:255']);
        $result = $service->handle($submission, $request->user(), $data['action'] === 'accept', $data['reason'] ?? null);

        return response()->json(['status' => $result->status, 'message' => 'Order '.$result->status.'.']);
    }

    public function status(Request $request, string $token)
    {
        $submission = QrOrderSubmission::where('public_token', $token)->with('table')->firstOrFail();
        $redirect = $submission->status === 'accepted' ? route('public.order.status', ['qr_token' => $submission->table->qr_token]) : null;
        if ($request->expectsJson()) {
            return response()->json(['status' => $submission->status, 'redirect_url' => $redirect, 'reason' => $submission->rejection_reason]);
        }
        if ($redirect) {
            if ($request->boolean('placed')) {
                $redirect .= '?placed=1';
            }
            return redirect($redirect);
        }

        return view('modules.public-menu.pending-order', [
            'submission' => $submission,
            'showOrderPlaced' => $request->boolean('placed') && $submission->status === 'pending',
        ]);
    }
}
