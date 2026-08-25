@extends('core.layouts.admin')

@section('content')
    @php
        $branches = $branches ?? collect();
        $gateways = $gateways ?? collect();
        $configs = $configs ?? collect();
        $selectedBranch = $selectedBranch ?? $branches->first();
        $selectedGateway = $selectedGateway ?? null;
        $selectedConfig = $selectedConfig ?? null;
        $selectedCheckoutMode = $selectedCheckoutMode ?? 'disabled';
        $isCurrentActive = (bool) ($selectedConfig?->is_active ?? false);
        $selectedGatewaySlug = optional($selectedGateway)->slug ?? '';
        $isKhaltiGateway = $selectedGatewaySlug === 'khalti';
        $credentialValue = $selectedConfig?->credentials
            ? json_encode($selectedConfig->credentials, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)
            : '';
    @endphp

    <div class="flex-1 overflow-y-auto p-6 bg-gray-900 space-y-6 text-white">
        <div class="bg-gray-800 border border-gray-700 rounded-xl p-5 md:p-6">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <p class="text-xs uppercase tracking-[0.2em] text-gray-400">Payment Settings</p>
                    <h1 class="text-2xl md:text-3xl font-bold text-white mt-1">Tenant Payment Configuration</h1>
                    <p class="text-sm text-gray-400 mt-2 max-w-3xl">
                        Branch-level payment setup wahi language me rakha gaya hai jo admin branches screen me use hoti
                        hai.
                    </p>

                </div>

                <div class="flex flex-wrap gap-2 items-center">
                    <span
                        class="inline-flex items-center gap-2 rounded-full border border-white/10 bg-white/5 px-3 py-2 text-xs font-semibold text-gray-300">
                        <span
                            class="h-2 w-2 rounded-full {{ $selfPaymentEnabled ? 'bg-emerald-500' : 'bg-rose-500' }}"></span>
                        Self Payment: {{ $selfPaymentEnabled ? 'Enabled in plan' : 'Locked by plan' }}
                    </span>
                    <a href="{{ route('admin.branches.index') }}"
                        class="inline-flex items-center justify-center gap-2 bg-sky-500/10 hover:bg-sky-500/20 text-sky-400 border border-sky-500/30 px-4 py-2.5 rounded-lg text-sm font-medium transition">
                        <i class="fas fa-arrow-left"></i>
                        Back to Branches
                    </a>
                </div>
            </div>
        </div>

        <div class="px-6 py-6 space-y-6">
            <section class="space-y-6">
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div class="bg-gray-800 border border-gray-700 rounded-xl p-4">
                        <p class="text-xs text-gray-400 uppercase tracking-wide">Selected Branch</p>
                        <p class="text-2xl font-bold text-white mt-1">{{ $selectedBranch->branch_name ?? '—' }}</p>
                        <p class="text-sm text-gray-400 mt-1">{{ $selectedBranch->city ?? 'Branch city not set' }}</p>
                    </div>
                    <div class="bg-gray-800 border border-gray-700 rounded-xl p-4">
                        <p class="text-xs text-gray-400 uppercase tracking-wide">Configured Gateways</p>
                        <p class="text-2xl font-bold text-white mt-1">{{ $configs->count() }}</p>
                    </div>
                    <div class="bg-gray-800 border border-gray-700 rounded-xl p-4">
                        <p class="text-xs text-gray-400 uppercase tracking-wide">Active Gateway Rows</p>
                        <p class="text-2xl font-bold text-white mt-1">{{ $activeConfigsCount }}</p>
                    </div>
                </div>

                <div class="bg-gray-800 border border-gray-700 rounded-xl p-5">
                    <form method="GET" action="{{ route('admin.branches.payment-gateways') }}"
                        class="flex flex-col gap-3 lg:flex-row lg:items-end">
                        <div class="flex-1">
                            <label class="mb-2 block text-xs text-gray-400 uppercase tracking-wide">Select
                                Branch</label>
                            <select name="branch_id"
                                class="w-full bg-gray-900 border border-gray-700 rounded-lg px-3 py-2.5 text-sm text-white placeholder-gray-500 focus:outline-none focus:ring-1 focus:ring-orange-500">
                                @foreach ($branches as $branch)
                                    <option value="{{ $branch->id }}"
                                        {{ (int) $selectedBranch->id === (int) $branch->id ? 'selected' : '' }}>
                                        {{ $branch->branch_name }}{{ $branch->city ? ' - ' . $branch->city : '' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <button type="submit"
                            class="inline-flex items-center justify-center gap-2 bg-orange-500/10 hover:bg-orange-500/20 text-orange-500 border border-orange-500/30 px-4 py-2.5 rounded-lg text-sm font-medium transition">
                            Load Branch
                        </button>
                    </form>
                </div>

                @if (!$selfPaymentEnabled)
                    <div
                        class="bg-gray-800 border border-gray-700 rounded-xl p-5 md:p-6">
                        <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                            <div class="max-w-2xl">
                                <p class="text-xs uppercase tracking-[0.2em] text-gray-400">Plan Status</p>
                                <h2 class="mt-2 text-2xl font-bold text-white">Self-payment module is not available
                                    in current plan.</h2>
                                <p class="mt-3 text-sm leading-6 text-gray-400">
                                    Abhi branch yaha se online checkout configure nahi kar sakta. Customer side par
                                    request bill / waiter flow hi available rahega.
                                </p>
                            </div>
                            <div
                                class="rounded-xl border border-gray-700 bg-gray-900 p-4 text-sm text-gray-400 lg:w-[360px]">
                                <p class="font-semibold text-white">Only unpaid bill mode</p>
                                <p class="mt-2">Enable online self-checkout tabhi dikhega jab plan feature active
                                    hoga.</p>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="bg-gray-800 border border-gray-700 rounded-xl p-5 md:p-6">
                        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                            <div>
                                <p class="text-xs uppercase tracking-[0.2em] text-gray-400">Checkout Setup</p>
                                <h2 class="mt-2 text-2xl font-bold text-white">Payment Configuration</h2>
                                <p class="mt-2 text-sm text-gray-400">
                                    Branch ke liye payment setup modal me open hoga.
                                </p>
                            </div>
                            <button id="openPaymentModal" type="button"
                                class="inline-flex items-center justify-center gap-2 bg-orange-500/10 hover:bg-orange-500/20 text-orange-500 border border-orange-500/30 px-4 py-2.5 rounded-lg text-sm font-medium transition">
                                <i class="fas fa-credit-card"></i>
                                Set Payment
                            </button>
                        </div>
                    </div>

                        <div class="mt-6 bg-gray-800 border border-gray-700 rounded-xl p-5">
                            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-3 mb-4">
                                <div class="flex items-center gap-2">
                                    <span class="text-sm text-gray-400">Configured Gateways</span>
                                    <span
                                        class="px-2.5 py-1 rounded-full text-xs bg-orange-500/10 text-orange-500 border border-orange-500/30">
                                        Total : {{ $configs->count() }}
                                    </span>
                                </div>
                                <p class="text-sm text-gray-400">Use the action menu like the branch table.</p>
                            </div>

                            <div class="overflow-x-auto overflow-y-visible">
                                <table class="w-full text-sm">
                                    <thead class="text-xs text-gray-400 border-b border-gray-700 uppercase tracking-wide">
                                        <tr>
                                            <th class="text-left py-3 pr-4 font-medium">#</th>
                                            <th class="text-left py-3 px-4 font-medium">Gateway</th>
                                            <th class="text-left py-3 px-4 font-medium">Mode</th>
                                            <th class="text-left py-3 px-4 font-medium">Status</th>
                                            <th class="text-left py-3 px-4 font-medium">Updated</th>
                                            <th class="text-left py-3 pl-8 font-medium">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-700/80">
                                        @forelse ($configs as $index => $config)
                                            @php
                                                $actionData = [
                                                    'branch_id' => $selectedBranch->id,
                                                    'gateway_id' => $config->payment_gateway_id,
                                                    'config_id' => $config->id,
                                                    'url' => route('admin.branches.payment-gateways', ['branch_id' => $selectedBranch->id, 'gateway_id' => $config->payment_gateway_id, 'config_id' => $config->id]),
                                                ];
                                            @endphp
                                            <tr
                                                class="{{ (int) $selectedConfigId === (int) $config->id ? 'bg-orange-500/5' : '' }} hover:bg-white/5 transition">
                                                <td class="py-3 pr-4 text-gray-300">{{ $index + 1 }}</td>
                                                <td class="py-3 px-4">
                                                    <div>
                                                        <p class="font-medium text-white">{{ $config->gateway?->name ?? 'Unknown Gateway' }}</p>
                                                        <p class="text-xs text-gray-400">{{ $config->static_qr_label ?: 'No QR label set' }}</p>
                                                    </div>
                                                </td>
                                                <td class="py-3 px-4">
                                                    <span class="px-2.5 py-1 rounded-full text-xs bg-white/5 text-gray-300 border border-white/10">
                                                        {{ strtoupper($config->checkout_mode ?? 'disabled') }}
                                                    </span>
                                                </td>
                                                <td class="py-3 px-4">
                                                    @if ($config->is_active)
                                                        <span class="px-2.5 py-1 rounded-full text-xs bg-emerald-500/15 text-emerald-400">
                                                            Active
                                                        </span>
                                                    @else
                                                        <span class="px-2.5 py-1 rounded-full text-xs bg-slate-500/20 text-slate-300">
                                                            Inactive
                                                        </span>
                                                    @endif
                                                </td>
                                                <td class="py-3 px-4 text-gray-300">
                                                    {{ optional($config->updated_at)->format('Y-m-d H:i') ?? '—' }}
                                                </td>
                                                <td class="py-3 pl-8">
                                                    @include('core.components.actions', [
                                                        'viewClass' => 'paymentGatewayActionView',
                                                        'viewData' => $actionData,
                                                        'editClass' => 'paymentGatewayActionEdit',
                                                        'editData' => $actionData,
                                                        'deleteRoute' => route('admin.branches.payment-gateways.destroy', $config->id),
                                                        'deleteConfirm' => 'Are you sure you want to delete this payment gateway setting?',
                                                    ])
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6" class="py-6 text-center text-sm text-gray-400">No configured gateway rows found for this branch.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div id="paymentModal" class="fixed inset-0 z-[120] hidden overflow-y-auto">
                        <div id="paymentModalBackdrop" class="absolute inset-0 bg-black/50"></div>
                        <div class="relative z-10 min-h-screen flex items-start md:items-center justify-center p-4">
                            <div class="w-full max-w-5xl max-h-[calc(100dvh-2rem)] overflow-y-auto bg-gray-800 border border-gray-700 rounded-2xl">
                                <div class="px-5 py-4 border-b border-gray-700 flex items-center justify-between">
                                    <div>
                                        <h2 class="text-lg font-semibold text-white">Set Payment</h2>
                                        <p class="text-xs text-gray-400 mt-1">Configure branch payment gateway and checkout mode</p>
                                    </div>
                                    <button id="closePaymentModal" type="button"
                                        class="w-8 h-8 rounded-md bg-white/5 hover:bg-white/10 text-gray-300 transition">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>

                                <form method="POST" action="{{ route('admin.branches.payment-gateways.store') }}"
                                    enctype="multipart/form-data" class="p-5 space-y-5" id="gatewaySettingsForm">
                                    @csrf
                                    <input type="hidden" name="branch_id" value="{{ $selectedBranch->id }}">
                                    <input type="hidden" name="payment_gateway_id" value="{{ optional($selectedGateway)->id ?? 0 }}">

                                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
                                        <div>
                                            <label class="mb-2 block text-xs text-gray-400 uppercase tracking-wide">Checkout Mode</label>
                                            <select name="checkout_mode" id="checkoutMode"
                                                class="w-full bg-gray-900 border border-gray-700 rounded-lg px-3 py-2.5 text-sm text-white placeholder-gray-500 focus:outline-none focus:ring-1 focus:ring-orange-500">
                                                <option value="dynamic_api" {{ $selectedCheckoutMode === 'dynamic_api' ? 'selected' : '' }}>Payment Gateway API</option>
                                                <option value="static_qr" {{ $selectedCheckoutMode === 'static_qr' ? 'selected' : '' }}>Static QR</option>
                                                <option value="disabled" {{ $selectedCheckoutMode === 'disabled' ? 'selected' : '' }}>Disabled</option>
                                            </select>
                                        </div>
                                        <div>
                                            <label class="mb-2 block text-xs text-gray-400 uppercase tracking-wide">Gateway Environment</label>
                                            <select name="mode"
                                                class="w-full bg-gray-900 border border-gray-700 rounded-lg px-3 py-2.5 text-sm text-white placeholder-gray-500 focus:outline-none focus:ring-1 focus:ring-orange-500">
                                                <option value="sandbox" {{ ($selectedConfig?->mode ?? 'sandbox') === 'sandbox' ? 'selected' : '' }}>Sandbox</option>
                                                <option value="live" {{ ($selectedConfig?->mode ?? 'sandbox') === 'live' ? 'selected' : '' }}>Live</option>
                                            </select>
                                        </div>
                                        <div>
                                            <label class="mb-2 block text-xs text-gray-400 uppercase tracking-wide">Gateway Status</label>
                                            <label class="flex h-[52px] items-center gap-3 rounded-xl border border-gray-700 bg-gray-900 px-4">
                                                <input type="hidden" name="is_active" value="0">
                                                <input type="checkbox" name="is_active" value="1"
                                                    class="h-4 w-4 rounded border-gray-600 text-emerald-600 focus:ring-emerald-500 bg-gray-900"
                                                    {{ $isCurrentActive ? 'checked' : '' }}>
                                                <span class="text-sm text-gray-300">Enable this gateway for the branch</span>
                                            </label>
                                        </div>
                                    </div>

                                    <div class="rounded-xl border border-gray-700 bg-gray-900 p-4">
                                        <div data-mode-panel="dynamic_api" class="space-y-4">
                                            <div class="rounded-xl border border-sky-500/20 bg-sky-500/10 p-4 text-sm text-sky-200">Customer pays from app or QR scan and the order is auto-marked when payment is confirmed.</div>
                                            <div>
                                                <label class="mb-2 block text-xs text-gray-400 uppercase tracking-wide">Payment Gateway</label>
                                                <select name="gateway_id" id="gatewayId"
                                                    class="w-full bg-gray-800 border border-gray-700 rounded-lg px-3 py-2.5 text-sm text-white placeholder-gray-500 focus:outline-none focus:ring-1 focus:ring-orange-500">
                                                    @foreach ($gateways as $gateway)
                                                        <option value="{{ $gateway->id }}" {{ (int) $selectedGatewayId === (int) $gateway->id ? 'selected' : '' }}>
                                                            {{ $gateway->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                <p class="mt-2 text-xs text-gray-400">Choose the gateway first, then fill its credentials below.</p>
                                            </div>

                                            <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                                                <div class="rounded-xl border border-gray-700 bg-gray-800 p-4">
                                                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-gray-400">
                                                        {{ $isKhaltiGateway ? 'Public Key' : 'Merchant Code' }}
                                                    </p>
                                                    <input type="text"
                                                        name="{{ $isKhaltiGateway ? 'credentials[public_key]' : 'credentials[merchant_code]' }}"
                                                        value="{{ old($isKhaltiGateway ? 'credentials.public_key' : 'credentials.merchant_code', data_get($selectedConfig?->credentials ?? [], $isKhaltiGateway ? 'public_key' : 'merchant_code')) }}"
                                                        placeholder="{{ $isKhaltiGateway ? 'khalti_public_key_...' : 'MERCHANT123456' }}"
                                                        class="mt-3 w-full rounded-lg border border-gray-700 bg-gray-900 px-3 py-2.5 text-sm text-white placeholder-gray-500 outline-none focus:border-orange-400">
                                                </div>
                                                <div class="rounded-xl border border-gray-700 bg-gray-800 p-4 {{ $isKhaltiGateway ? 'hidden' : '' }}">
                                                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-gray-400">API Password</p>
                                                    <input type="password" name="credentials[api_password]"
                                                        value="{{ old('credentials.api_password', data_get($selectedConfig?->credentials ?? [], 'api_password')) }}"
                                                        placeholder="********"
                                                        class="mt-3 w-full rounded-lg border border-gray-700 bg-gray-900 px-3 py-2.5 text-sm text-white placeholder-gray-500 outline-none focus:border-orange-400">
                                                </div>
                                                <div class="rounded-xl border border-gray-700 bg-gray-800 p-4">
                                                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-gray-400">Secret Key</p>
                                                    <input type="password" name="credentials[secret_key]"
                                                        value="{{ old('credentials.secret_key', data_get($selectedConfig?->credentials ?? [], 'secret_key')) }}"
                                                        placeholder="********"
                                                        class="mt-3 w-full rounded-lg border border-gray-700 bg-gray-900 px-3 py-2.5 text-sm text-white placeholder-gray-500 outline-none focus:border-orange-400">
                                                </div>
                                            </div>
                                            @if ($isKhaltiGateway)
                                                <p class="text-xs text-gray-400">
                                                    Khalti ke liye public key aur secret key use hoti hai. Merchant code ki zaroorat nahi hoti.
                                                </p>
                                            @endif
                                        </div>

                                        <div data-mode-panel="static_qr" class="hidden space-y-4">
                                            <div class="rounded-xl border border-orange-500/20 bg-orange-500/10 p-4 text-sm text-orange-100">Customer scans static QR and waiter or owner can verify payment from the POS flow.</div>
                                            <div class="grid grid-cols-1 xl:grid-cols-[1fr_260px] gap-4">
                                                <label class="block">
                                                    <span class="mb-2 block text-xs text-gray-400 uppercase tracking-wide">Static QR Label</span>
                                                    <input type="text" name="static_qr_label"
                                                        value="{{ old('static_qr_label', $selectedConfig?->static_qr_label) }}"
                                                        placeholder="eSewa / Fonepay QR"
                                                        class="w-full bg-gray-800 border border-gray-700 rounded-lg px-3 py-2.5 text-sm text-white placeholder-gray-500 focus:outline-none focus:ring-1 focus:ring-orange-500">
                                                </label>
                                                <div class="rounded-xl border border-dashed border-gray-700 bg-gray-800 p-4">
                                                    <span class="mb-2 block text-xs text-gray-400 uppercase tracking-wide">Upload Static QR Image</span>
                                                    <input type="file" name="static_qr_image" accept="image/*"
                                                        class="w-full text-sm text-gray-300 file:mr-4 file:rounded-lg file:border-0 file:bg-orange-500/10 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-orange-400">
                                                    @if ($selectedConfig?->static_qr_image)
                                                        <div class="mt-4 flex items-center gap-3 rounded-xl border border-gray-700 bg-gray-900 p-3">
                                                            <img src="{{ asset('storage/' . $selectedConfig->static_qr_image) }}" alt="Static QR"
                                                                class="h-16 w-16 rounded-lg object-cover border border-gray-700">
                                                            <div class="text-sm">
                                                                <p class="font-semibold text-white">Current QR image</p>
                                                                <p class="text-gray-400">Upload a new file to replace it.</p>
                                                            </div>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>

                                        <div data-mode-panel="disabled"
                                            class="hidden rounded-xl border border-gray-700 bg-gray-800 p-5 text-sm text-gray-400">
                                            <p class="font-semibold text-white">Disabled mode</p>
                                            <p class="mt-2">No QR or online payment is exposed. Customer can only request bill and wait for waiter action.</p>
                                        </div>
                                    </div>

                                    <div class="flex flex-col gap-3 border-t border-gray-700 pt-5 sm:flex-row sm:items-center sm:justify-between">
                                        <p class="text-xs text-gray-400">Branch-level config is saved separately from the global gateway catalogue.</p>
                                        <button type="submit"
                                            class="inline-flex items-center justify-center gap-2 bg-orange-500/10 hover:bg-orange-500/20 text-orange-500 border border-orange-500/30 px-4 py-2.5 rounded-lg text-sm font-medium transition">
                                            <i class="fas fa-save"></i>
                                            Save Settings
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                @endif
        </div>
        </section>
    </div>
    </div>

    <script>
        (function() {
            const checkoutMode = document.getElementById('checkoutMode');
            const panels = document.querySelectorAll('[data-mode-panel]');
            const gatewayId = document.getElementById('gatewayId');
            const form = document.getElementById('gatewaySettingsForm');
            const modal = document.getElementById('paymentModal');
            const openBtn = document.getElementById('openPaymentModal');
            const closeBtn = document.getElementById('closePaymentModal');
            const backdrop = document.getElementById('paymentModalBackdrop');
            const setMode = () => {
                const value = checkoutMode ? checkoutMode.value : 'disabled';
                panels.forEach((panel) => {
                    panel.classList.toggle('hidden', panel.dataset.modePanel !== value);
                });
            };

            if (checkoutMode) {
                checkoutMode.addEventListener('change', setMode);
                setMode();
            }

            if (gatewayId) {
                gatewayId.addEventListener('change', () => {
                    if (form) {
                        form.querySelector('input[name="payment_gateway_id"]').value = gatewayId.value;
                    }
                });
            }

            const query = new URLSearchParams(window.location.search);
            if (query.get('config_id') && modal) {
                modal.classList.remove('hidden');
                document.body.classList.add('overflow-hidden');
                setMode();
            }

            const openModal = () => {
                if (!modal) return;
                modal.classList.remove('hidden');
                document.body.classList.add('overflow-hidden');
                setMode();
            };

            const closeModal = () => {
                if (!modal) return;
                modal.classList.add('hidden');
                document.body.classList.remove('overflow-hidden');
            };

            openBtn && openBtn.addEventListener('click', openModal);
            closeBtn && closeBtn.addEventListener('click', closeModal);
            backdrop && backdrop.addEventListener('click', closeModal);

            document.addEventListener('keydown', (event) => {
                if (event.key === 'Escape' && modal && !modal.classList.contains('hidden')) {
                    closeModal();
                }
            });

            document.addEventListener('click', (event) => {
                const viewBtn = event.target.closest('.paymentGatewayActionView');
                const editBtn = event.target.closest('.paymentGatewayActionEdit');

                if (!viewBtn && !editBtn) {
                    return;
                }

                const target = viewBtn || editBtn;
                const url = target?.dataset?.url;

                if (url) {
                    window.location.href = url;
                }
            });
        })();
    </script>
@endsection
