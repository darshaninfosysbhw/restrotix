<?php

namespace App\Http\Controllers\Admin\Branch;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\BranchPaymentGateway;
use App\Models\PaymentGateway;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class BranchPaymentGatewayController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        abort_unless($user && $user->tenant_id, 403);

        $tenant = $user->tenant;
        $selfPaymentEnabled = (bool) ($tenant?->plan?->hasFeature('self_payment_enabled') ?? false);
        $branches = $tenant->branches()
            ->orderBy('branch_name')
            ->get(['id', 'branch_name', 'branch_email', 'contact_number', 'city']);

        abort_unless($branches->isNotEmpty(), 404, 'No branches found for this tenant.');

        $selectedBranchId = (int) $request->query('branch_id', $user->branch_id ?: $branches->first()->id);
        $selectedBranch = $branches->firstWhere('id', $selectedBranchId) ?? $branches->first();

        $gateways = PaymentGateway::query()
            ->active()
            ->where('slug', '!=', 'stripe')
            ->orderBy('name')
            ->get();

        $selectedGatewayId = (int) $request->query('gateway_id', $gateways->first()?->id ?? 0);
        $selectedGateway = $gateways->firstWhere('id', $selectedGatewayId) ?? $gateways->first();
        $selectedConfigId = (int) $request->query('config_id', 0);

        $configs = BranchPaymentGateway::query()
            ->where('tenant_id', (int) $tenant->id)
            ->where('branch_id', (int) $selectedBranch->id)
            ->with('gateway')
            ->get()
            ->filter(fn (BranchPaymentGateway $config) => $config->gateway?->slug !== 'stripe')
            ->keyBy('payment_gateway_id');

        $activeConfigsCount = $configs->where('is_active', true)->count();
        $selectedConfig = $selectedConfigId > 0
            ? $configs->firstWhere('id', $selectedConfigId)
            : ($selectedGateway ? $configs->get($selectedGateway->id) : null);
        if (! $selectedConfig && $selectedGateway) {
            $selectedConfig = $configs->get($selectedGateway->id);
        }
        $selectedCheckoutMode = old('checkout_mode', $selectedConfig?->checkout_mode ?? (
            $selectedConfig?->static_qr_image ? 'static_qr' : ($selectedConfig?->credentials ? 'dynamic_api' : 'disabled')
        ));

        return view('admin.settings.payment-gateways', [
            'tenant' => $tenant,
            'branches' => $branches,
            'selectedBranch' => $selectedBranch,
            'gateways' => $gateways,
            'selectedGateway' => $selectedGateway,
            'selectedGatewayId' => $selectedGatewayId,
            'selectedConfigId' => $selectedConfigId,
            'selectedConfig' => $selectedConfig,
            'selectedCheckoutMode' => $selectedCheckoutMode,
            'configs' => $configs,
            'activeConfigsCount' => $activeConfigsCount,
            'selfPaymentEnabled' => $selfPaymentEnabled,
        ]);
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        abort_unless($user && $user->tenant_id, 403);

        $validator = $request->validate([
            'branch_id' => [
                'required',
                'integer',
                Rule::exists('branches', 'id')->where(fn ($query) => $query->where('tenant_id', $user->tenant_id)),
            ],
            'payment_gateway_id' => [
                'required',
                'integer',
                Rule::exists('payment_gateways', 'id'),
            ],
            'mode' => 'required|in:sandbox,live',
            'checkout_mode' => 'required|in:dynamic_api,static_qr,disabled',
            'credentials' => 'nullable|array',
            'static_qr_label' => 'nullable|string|max:255',
            'is_active' => 'nullable|boolean',
            'static_qr_image' => 'nullable|file|image|max:4096',
        ]);

        $credentials = [];
        if (!empty($validator['credentials'])) {
            $credentials = $validator['credentials'];
        }

        $branchGateway = BranchPaymentGateway::query()
            ->where('tenant_id', (int) $user->tenant_id)
            ->where('branch_id', (int) $validator['branch_id'])
            ->where('payment_gateway_id', (int) $validator['payment_gateway_id'])
            ->first();

        $staticQrImagePath = $branchGateway?->static_qr_image;
        if ($request->hasFile('static_qr_image')) {
            if ($staticQrImagePath) {
                Storage::disk('public')->delete($staticQrImagePath);
            }

            $staticQrImagePath = $request->file('static_qr_image')->store('branch-payment-gateways', 'public');
        }

        $isActive = (bool) ($validator['is_active'] ?? false);
        if ($validator['checkout_mode'] === 'disabled') {
            $isActive = false;
        }

        BranchPaymentGateway::updateOrCreate(
            [
                'tenant_id' => (int) $user->tenant_id,
                'branch_id' => (int) $validator['branch_id'],
                'payment_gateway_id' => (int) $validator['payment_gateway_id'],
            ],
            [
                'credentials' => $credentials,
                'mode' => $validator['mode'],
                'checkout_mode' => $validator['checkout_mode'],
                'is_active' => $isActive,
                'static_qr_image' => $staticQrImagePath,
                'static_qr_label' => $validator['static_qr_label'] ?? null,
            ]
        );

        return redirect()
            ->route('admin.branches.payment-gateways', ['branch_id' => $validator['branch_id']])
            ->with('toast', [[
                'type' => 'success',
                'message' => 'Payment gateway settings saved successfully.',
                'duration' => 5000,
            ]]);
    }

    public function destroy(Request $request, BranchPaymentGateway $config)
    {
        $user = Auth::user();
        abort_unless($user && $user->tenant_id, 403);

        abort_unless((int) $config->tenant_id === (int) $user->tenant_id, 403);

        if ($config->static_qr_image) {
            Storage::disk('public')->delete($config->static_qr_image);
        }

        $branchId = (int) $config->branch_id;
        $config->delete();

        return redirect()
            ->route('admin.branches.payment-gateways', ['branch_id' => $branchId])
            ->with('toast', [[
                'type' => 'success',
                'message' => 'Payment gateway setting deleted successfully.',
                'duration' => 5000,
            ]]);
    }
}
