@extends('core.layouts.superadmin')

@section('content')
    @php
        $plans = $plans ?? collect();
        $currencies = $currencies ?? collect();
        $featureServices = $featureServices ?? collect();
        $planStats = $planStats ?? [
            'total' => $plans->count(),
            'active' => $plans->where('status', 'Active')->count(),
            'inactive' => $plans->where('status', 'Inactive')->count(),
            'recommended' => $plans->where('is_recommended', true)->count(),
        ];
    @endphp

    <div class="sa-main relative z-0 flex-1 overflow-y-auto p-4 md:p-6 space-y-6">
        <div class="glass-panel p-5 md:p-6">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <p class="text-xs uppercase tracking-[0.2em] text-slate-400">Master Settings</p>
                    <h1 class="text-2xl md:text-3xl font-bold text-white mt-1">Plan Management</h1>
                    <p class="text-sm text-slate-400 mt-2">
                        Configure plan limits, features, and multi-currency pricing from one place.
                    </p>
                </div>
                <x-core::ui.button id="openPlanModal" data-modal-open="planModal">
                    <i class="fas fa-plus"></i> Add Plan
                </x-core::ui.button>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
            <x-ui.stat-card title="Total Plans" :value="$planStats['total']" icon="fas fa-layer-group" color="orange" />
            <x-ui.stat-card title="Active" :value="$planStats['active']" icon="fas fa-check-circle" color="emerald" />
            <x-ui.stat-card title="Inactive" :value="$planStats['inactive']" icon="fas fa-pause-circle" color="amber" />
            <x-ui.stat-card title="Recommended" :value="$planStats['recommended']" icon="fas fa-star" color="sky" />
        </div>

        <div class="glass-panel p-5">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-3 mb-4">
                <div class="flex items-center gap-2">
                    <span class="text-sm text-slate-400">All Plans</span>
                    <span id="planCountBadge"
                        class="px-2.5 py-1 rounded-full text-xs bg-orange-500/15 text-orange-500 border border-orange-500/30">
                        Total : {{ $planStats['total'] ?? 0 }}
                    </span>
                </div>
                <div class="flex flex-col sm:flex-row sm:items-center gap-2 w-full lg:w-auto">
                    <div class="relative w-full sm:w-72">
                        <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                        <input id="planTableSearch" type="text"
                            placeholder="Search by plan, price, status, subscribers..."
                            class="sa-form-input w-full bg-[#0f172a] border border-white/10 rounded-lg pl-9 pr-3 py-2 text-sm text-white placeholder-slate-500 focus:outline-none focus:ring-1 focus:ring-orange-500">
                    </div>
                    <button id="planSearchReset" type="button"
                        class="px-3 py-2 rounded-lg text-xs bg-white/5 hover:bg-white/10 text-slate-300 border border-white/10 transition">
                        Reset
                    </button>
                </div>
            </div>

            <div class="overflow-x-auto overflow-y-visible">
                <table class="w-full text-sm">
                    <thead class="text-xs text-slate-400 border-b border-white/10 uppercase tracking-wide">
                        <tr>
                            <th class="text-left py-3 pr-4 font-medium">#</th>
                            <th class="text-left py-3 px-4 font-medium">Plan Name</th>
                            <th class="text-left py-3 px-4 font-medium">Price</th>
                            <th class="text-left py-3 px-4 font-medium">Limits</th>
                            <th class="text-left py-3 px-4 font-medium">Subscribers</th>
                            <th class="text-left py-3 pl-4 font-medium">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="planTableBody" class="divide-y divide-white/5">
                        @forelse ($plans as $index => $plan)
                            @php
                                $monthly = $plan['default_monthly_price'];
                                $yearly = $plan['default_yearly_price'];
                                $code = $plan['default_currency_code'];
                            @endphp
                            <tr class="plan-row hover:bg-white/5 transition">
                                <td class="py-3 pr-4 text-slate-300">{{ $index + 1 }}</td>
                                <td class="py-3 px-4">
                                    <div class="space-y-1">
                                        <div class="flex flex-wrap items-center gap-2">
                                            <p class="font-medium text-white">{{ $plan['name'] ?? '—' }}</p>
                                            @if (!empty($plan['is_recommended']))
                                                <span
                                                    class="px-2 py-0.5 rounded-full text-[10px] bg-orange-500/20 text-orange-400 border border-orange-500/30">Most
                                                    Popular</span>
                                            @endif
                                            @if (($plan['status'] ?? '') === 'Active')
                                                <span
                                                    class="px-2 py-0.5 rounded-full text-[10px] bg-emerald-500/15 text-emerald-400">Active</span>
                                            @else
                                                <span
                                                    class="px-2 py-0.5 rounded-full text-[10px] bg-slate-500/20 text-slate-300">Inactive</span>
                                            @endif
                                        </div>
                                        <p class="text-xs text-slate-500">{{ $plan['slug'] ?? '—' }}</p>
                                    </div>
                                </td>
                                <td class="py-3 px-4 text-slate-300">
                                    @if ($monthly !== null && $yearly !== null && $code)
                                        <div class="space-y-1">
                                            <p>{{ $code }} {{ number_format((float) $monthly, 2) }} / month</p>
                                            <p class="text-xs text-slate-400">{{ $code }}
                                                {{ number_format((float) $yearly, 2) }} / year</p>
                                        </div>
                                    @else
                                        <span class="text-slate-500">No default pricing</span>
                                    @endif
                                </td>
                                <td class="py-3 px-4 text-slate-300">
                                    <div class="space-y-1">
                                        <p>Branches: {{ $plan['max_branches'] ?? 1 }}</p>
                                        <p class="text-xs text-slate-400">Trial: {{ $plan['trial_days'] ?? 0 }} days</p>
                                    </div>
                                </td>
                                <td class="py-3 px-4 text-slate-300">
                                    <span
                                        class="px-2.5 py-1 rounded-full text-xs bg-sky-500/15 text-sky-400 border border-sky-500/25">
                                        {{ $plan['tenants_count'] ?? 0 }}
                                    </span>
                                </td>
                                <td class="py-3 pl-4">
                                    @php
                                        $actionData = [
                                            'id' => $plan['id'] ?? '',
                                            'name' => $plan['name'] ?? '',
                                            'summary' => $plan['summary'] ?? '',
                                            'max-branches' => $plan['max_branches'] ?? 1,
                                            'trial-days' => $plan['trial_days'] ?? 0,
                                            'status' => $plan['status'] ?? 'Active',
                                            'is-recommended' => !empty($plan['is_recommended']) ? '1' : '0',
                                            'default-currency-id' => $plan['default_currency_id'] ?? '',
                                            'default-monthly-price' => $plan['default_monthly_price'] ?? '',
                                            'default-yearly-price' => $plan['default_yearly_price'] ?? '',
                                            'features' => json_encode($plan['features'] ?? []),
                                            'prices' => json_encode($plan['prices'] ?? []),
                                        ];
                                    @endphp

                                    @include('core.components.actions', [
                                        'viewClass' => 'openPlanViewModal',
                                        'viewData' => $actionData,
                                        'editClass' => 'openPlanEditModal',
                                        'editData' => $actionData,
                                        'deleteRoute' => route('superadmin.plans.destroy', $plan['id'] ?? 0),
                                        'deleteConfirm' => 'Are you sure you want to delete this plan?',
                                    ])
                                </td>
                            </tr>
                        @empty
                            <tr id="planNoResultRow">
                                <td colspan="6" class="py-6 text-center text-sm text-slate-400">No plans found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div id="planModal" class="fixed inset-0 z-[2000] hidden overflow-y-auto"
        data-store-url="{{ route('superadmin.plans.store') }}"
        data-update-url-template="{{ route('superadmin.plans.update', ['plan' => '__ID__']) }}">
        <div id="planModalBackdrop" class="sa-overlay absolute inset-0 bg-black/50"></div>
        <div class="relative z-10 min-h-screen flex items-start md:items-center justify-center p-2 sm:p-4">
            <div
                class="w-[min(100vw-1rem,1080px)] sm:w-[min(100vw-2rem,1080px)] max-h-[calc(100dvh-1rem)] sm:max-h-[calc(100dvh-2rem)] overflow-y-auto glass-panel border border-white/10 rounded-2xl">
                <div class="px-5 py-4 border-b border-white/10 flex items-center justify-between">
                    <div>
                        <h2 id="planModalTitle" class="text-lg font-semibold text-white">Add Plan</h2>
                        <p id="planModalSubtitle" class="text-xs text-slate-400 mt-1">
                            Configure general settings and pricing matrix
                        </p>
                    </div>
                    <button id="closePlanModal" type="button"
                        class="w-8 h-8 rounded-md bg-white/5 hover:bg-white/10 text-slate-300 transition">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <form id="planForm" action="{{ route('superadmin.plans.store') }}" method="POST" class="p-5 space-y-5">
                    @csrf
                    <input id="planFormMethod" type="hidden" name="_method" value="PUT" disabled>

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
                        <section class="space-y-4">
                            <div class="bg-white/5 border border-white/10 rounded-xl p-4 space-y-4">
                                <h3 class="text-sm font-semibold text-white">General Settings</h3>

                                <div>
                                    <label class="block text-xs text-slate-400 mb-1.5">Plan Name</label>
                                    <input id="planName" type="text" name="name" required
                                        placeholder="e.g. Starter, Professional, Unlimited"
                                        class="sa-form-input w-full bg-[#0f172a] border border-white/10 rounded-lg px-3 py-2.5 text-sm text-white placeholder-slate-500 focus:outline-none focus:ring-1 focus:ring-orange-500">
                                    <p id="planSlugPreview" class="text-[11px] text-slate-500 mt-1">Slug preview: -</p>
                                </div>

                                <div>
                                    <label class="block text-xs text-slate-400 mb-1.5">Card Summary</label>
                                    <textarea id="planSummary" name="summary" rows="3" maxlength="255"
                                        placeholder="e.g. Flexible for your restaurant business"
                                        class="sa-form-input w-full bg-[#0f172a] border border-white/10 rounded-lg px-3 py-2.5 text-sm text-white placeholder-slate-500 focus:outline-none focus:ring-1 focus:ring-orange-500 resize-none"></textarea>
                                    <p class="text-[11px] text-slate-500 mt-1">Shown on landing and checkout plan cards.</p>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-xs text-slate-400 mb-1.5">Trial Days</label>
                                        <input id="planTrialDays" type="number" name="trial_days" min="0"
                                            required placeholder="e.g. 14"
                                            class="sa-form-input w-full bg-[#0f172a] border border-white/10 rounded-lg px-3 py-2.5 text-sm text-white placeholder-slate-500 focus:outline-none focus:ring-1 focus:ring-orange-500">
                                    </div>
                                    <div>
                                        <label class="block text-xs text-slate-400 mb-1.5">Max Branches</label>
                                        <input id="planMaxBranches" type="number" name="max_branches" min="1"
                                            required placeholder="e.g. 5"
                                            class="sa-form-input w-full bg-[#0f172a] border border-white/10 rounded-lg px-3 py-2.5 text-sm text-white placeholder-slate-500 focus:outline-none focus:ring-1 focus:ring-orange-500">
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-xs text-slate-400 mb-2">Plan Status</label>
                                    <select id="planStatus" name="status" required
                                        class="sa-form-input w-full bg-[#0f172a] border border-white/10 rounded-lg px-3 py-2.5 text-sm text-white focus:outline-none focus:ring-1 focus:ring-orange-500">
                                        <option value="Active">Active</option>
                                        <option value="Inactive">Inactive</option>
                                    </select>
                                </div>

                                <label
                                    class="flex items-center gap-2 text-sm text-slate-300 bg-white/5 border border-white/10 rounded-lg px-3 py-2.5">
                                    <input type="hidden" name="is_recommended" value="0">
                                    <input id="planIsRecommended" type="checkbox" name="is_recommended" value="1"
                                        class="rounded border-white/20 bg-transparent text-orange-500 focus:ring-orange-500">
                                    Mark as Most Popular (Recommended)
                                </label>
                            </div>

                            <div class="bg-white/5 border border-white/10 rounded-xl p-4 space-y-3">
                                <div class="flex items-center justify-between gap-3">
                                    <h3 class="text-sm font-semibold text-white">Features Access</h3>
                                    <a href="{{ route('superadmin.services.index') }}"
                                        class="text-[11px] font-medium text-orange-400 hover:text-orange-300 transition">
                                        + Add / Manage Services
                                    </a>
                                </div>

                                @if ($featureServices->isNotEmpty())
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                                        @foreach ($featureServices as $service)
                                            @php
                                                $serviceStatus = $service->status ?? 'Active';
                                                $isActiveService = $serviceStatus === 'Active';
                                            @endphp

                                            <label
                                                class="flex items-center gap-2 text-sm text-slate-300 bg-white/5 border border-white/10 rounded-lg px-3 py-2 {{ !$isActiveService ? 'opacity-70' : '' }}">
                                                <input type="hidden" name="features[{{ $service->slug }}]" value="0">
                                                <input id="feature_{{ $service->slug }}" type="checkbox"
                                                    data-feature-checkbox="{{ $service->slug }}"
                                                    name="features[{{ $service->slug }}]" value="1"
                                                    class="rounded border-white/20 bg-transparent text-orange-500 focus:ring-orange-500">
                                                <span class="flex-1 min-w-0">
                                                    <span class="block font-medium text-white truncate">
                                                        {{ $service->name }}
                                                    </span>
                                                    <span class="block text-[10px] text-slate-500 uppercase tracking-wider">
                                                        {{ $service->slug }}
                                                    </span>
                                                </span>
                                                @if (!$isActiveService)
                                                    <span
                                                        class="shrink-0 rounded-full bg-slate-500/20 px-2 py-0.5 text-[10px] text-slate-300 border border-slate-500/20">
                                                        Inactive
                                                    </span>
                                                @endif
                                            </label>
                                        @endforeach
                                    </div>
                                @else
                                    <div
                                        class="rounded-lg border border-amber-500/25 bg-amber-500/10 p-3 text-sm text-amber-300">
                                        No services found. Add a new service first, then it will appear here.
                                    </div>
                                @endif
                            </div>
                        </section>

                        <section>
                            <div class="bg-white/5 border border-white/10 rounded-xl p-4 space-y-4 h-full">
                                <div>
                                    <h3 class="text-sm font-semibold text-white">Pricing Matrix</h3>
                                    <p class="text-xs text-slate-400 mt-1">Yearly price suggestion = Monthly x 10</p>
                                </div>

                                @if ($currencies->isNotEmpty())
                                    @php
                                        $defaultCurrency = $currencies->firstWhere('is_default', true) ?? $currencies->first();
                                    @endphp
                                    <div class="space-y-3">
                                        @foreach ($currencies as $currency)
                                            @if ($defaultCurrency && $currency->id === $defaultCurrency->id)
                                                <div class="rounded-lg border border-white/10 bg-white/5 p-3">
                                                    <div class="flex items-center justify-between mb-2">
                                                        <p class="text-sm font-medium text-white">Plan Price</p>
                                                        <span
                                                            class="px-2 py-0.5 rounded-full text-[10px] bg-sky-500/15 text-sky-400">Default</span>
                                                    </div>
                                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                                        <div>
                                                            <label class="block text-xs text-slate-400 mb-1">Monthly
                                                                Price</label>
                                                            <input type="number" min="0" step="0.01"
                                                                name="prices[{{ $currency->id }}][monthly]"
                                                                data-price-monthly="{{ $currency->id }}"
                                                                data-plan-price-primary="1" required
                                                                placeholder="0.00"
                                                                class="sa-form-input w-full bg-[#0f172a] border border-white/10 rounded-lg px-3 py-2 text-sm text-white placeholder-slate-500 focus:outline-none focus:ring-1 focus:ring-orange-500">
                                                        </div>
                                                        <div>
                                                            <label class="block text-xs text-slate-400 mb-1">Yearly
                                                                Price</label>
                                                            <input type="number" min="0" step="0.01"
                                                                name="prices[{{ $currency->id }}][yearly]"
                                                                data-price-yearly="{{ $currency->id }}"
                                                                data-plan-price-primary="1" required
                                                                placeholder="0.00"
                                                                class="sa-form-input w-full bg-[#0f172a] border border-white/10 rounded-lg px-3 py-2 text-sm text-white placeholder-slate-500 focus:outline-none focus:ring-1 focus:ring-orange-500">
                                                        </div>
                                                    </div>
                                                </div>
                                            @else
                                                <div class="hidden" aria-hidden="true">
                                                    <div class="rounded-lg border border-white/10 bg-white/5 p-3">
                                                        <div class="flex items-center justify-between mb-2">
                                                            <p class="text-sm font-medium text-white">
                                                                {{ $currency->code }} ({{ $currency->name }})
                                                            </p>
                                                        </div>
                                                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                                            <div>
                                                                <label class="block text-xs text-slate-400 mb-1">Monthly
                                                                    Price</label>
                                                                <input type="number" min="0" step="0.01"
                                                                    name="prices[{{ $currency->id }}][monthly]"
                                                                    data-price-monthly="{{ $currency->id }}"
                                                                    data-plan-price-secondary="1" required
                                                                    placeholder="0.00"
                                                                    class="sa-form-input w-full bg-[#0f172a] border border-white/10 rounded-lg px-3 py-2 text-sm text-white placeholder-slate-500 focus:outline-none focus:ring-1 focus:ring-orange-500">
                                                            </div>
                                                            <div>
                                                                <label class="block text-xs text-slate-400 mb-1">Yearly
                                                                    Price</label>
                                                                <input type="number" min="0" step="0.01"
                                                                    name="prices[{{ $currency->id }}][yearly]"
                                                                    data-price-yearly="{{ $currency->id }}"
                                                                    data-plan-price-secondary="1" required
                                                                    placeholder="0.00"
                                                                    class="sa-form-input w-full bg-[#0f172a] border border-white/10 rounded-lg px-3 py-2 text-sm text-white placeholder-slate-500 focus:outline-none focus:ring-1 focus:ring-orange-500">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                        @endforeach
                                    </div>
                                @else
                                    <div
                                        class="rounded-lg border border-amber-500/25 bg-amber-500/10 p-3 text-sm text-amber-300">
                                        No active currencies found. Please activate at least one currency first.
                                    </div>
                                @endif
                            </div>
                        </section>
                    </div>

                    <div class="flex justify-end gap-2 pt-1">
                        <button id="cancelPlanModal" type="button"
                            class="px-4 py-2.5 rounded-lg text-sm bg-white/5 hover:bg-white/10 text-slate-300 transition">
                            Cancel
                        </button>
                        <button id="planModalSubmit" type="submit"
                            class="px-4 py-2.5 rounded-lg text-sm bg-orange-500/15 hover:bg-orange-500/25 text-orange-500 border border-orange-500/30 transition">
                            Save Plan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script>
        (() => {
            const parseJson = (raw) => {
                try {
                    return raw ? JSON.parse(raw) : {};
                } catch {
                    return {};
                }
            };

            const applyPlanPrices = (button) => {
                const prices = parseJson(button.dataset.prices);
                const defaultCurrencyId = button.dataset.defaultCurrencyId || '';
                const defaultMonthlyPrice = button.dataset.defaultMonthlyPrice || '';
                const defaultYearlyPrice = button.dataset.defaultYearlyPrice || '';

                const monthlyInputs = Array.from(document.querySelectorAll('[data-price-monthly]'));
                const yearlyInputs = Array.from(document.querySelectorAll('[data-price-yearly]'));
                const primaryMonthlyInput = document.querySelector('[data-plan-price-primary="1"][data-price-monthly]');
                const primaryYearlyInput = document.querySelector('[data-plan-price-primary="1"][data-price-yearly]');

                const fallbackPrice = defaultCurrencyId && prices[defaultCurrencyId] ? prices[defaultCurrencyId] : Object.values(prices)[0] || {};

                if (primaryMonthlyInput) {
                    primaryMonthlyInput.value = defaultMonthlyPrice || fallbackPrice.monthly || '';
                }

                if (primaryYearlyInput) {
                    primaryYearlyInput.value = defaultYearlyPrice || fallbackPrice.yearly || '';
                }

                monthlyInputs.forEach((input) => {
                    const currencyId = input.dataset.priceMonthly;
                    const price = prices[currencyId];
                    if (price?.monthly !== undefined) {
                        input.value = price.monthly;
                    }
                });

                yearlyInputs.forEach((input) => {
                    const currencyId = input.dataset.priceYearly;
                    const price = prices[currencyId];
                    if (price?.yearly !== undefined) {
                        input.value = price.yearly;
                    }
                    input.dataset.manual = '1';
                });
            };

            document.addEventListener('click', (event) => {
                const button = event.target.closest('.openPlanEditModal, .openPlanViewModal');
                if (!button) return;

                requestAnimationFrame(() => applyPlanPrices(button));
            });
        })();
    </script>
@endsection
