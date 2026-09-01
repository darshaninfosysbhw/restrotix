@extends('core.layouts.superadmin')

@section('content')
    @php
        $currencies = $currencies ?? collect();
        $countries = $countries ?? collect();
        $currencyStats = $currencyStats ?? [
            'total' => $currencies->count(),
            'active' => $currencies->where('status', 'Active')->count(),
            'inactive' => $currencies->where('status', 'Inactive')->count(),
            'default' => $currencies->where('is_default', true)->count(),
        ];
    @endphp

    <div class="sa-main relative z-0 flex-1 overflow-y-auto p-4 md:p-6 space-y-6">
        <div class="glass-panel p-5 md:p-6">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <p class="text-xs uppercase tracking-[0.2em] text-slate-400">Master Settings</p>
                    <h1 class="text-2xl md:text-3xl font-bold text-white mt-1">Currencies</h1>
                    <p class="text-sm text-slate-400 mt-2">
                        Configure available currencies, exchange rates, and default billing currency for your platform.
                    </p>
                </div>
                <x-core::ui.button id="openCurrencyModal" data-modal-open="currencyModal">
                    <i class="fas fa-plus"></i> Add Currency
                </x-core::ui.button>
            </div>

        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
            <x-ui.stat-card title="Total Currencies" :value="$currencyStats['total']" icon="fas fa-coins" color="orange" />
            <x-ui.stat-card title="Active" :value="$currencyStats['active']" icon="fas fa-check-circle" color="emerald" />
            <x-ui.stat-card title="Inactive" :value="$currencyStats['inactive']" icon="fas fa-pause-circle" color="amber" />
            <x-ui.stat-card title="Default Currency" :value="$currencyStats['default']" icon="fas fa-star" color="sky" />
        </div>

        <div class="glass-panel p-5">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-3 mb-4">
                <div class="flex items-center gap-2">
                    <span class="text-sm text-slate-400">All Currencies</span>
                    <span id="currencyCountBadge"
                        class="px-2.5 py-1 rounded-full text-xs bg-orange-500/15 text-orange-500 border border-orange-500/30">
                        Total : {{ $currencyStats['total'] ?? 0 }}
                    </span>
                </div>
                <div class="flex flex-col sm:flex-row sm:items-center gap-2 w-full lg:w-auto">
                    <div class="relative w-full sm:w-64">
                        <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                        <input id="currencyTableSearch" type="text"
                            placeholder="Search currency, code, symbol, country..."
                            class="sa-form-input w-full bg-[#0f172a] border border-white/10 rounded-lg pl-9 pr-3 py-2 text-sm text-white placeholder-slate-500 focus:outline-none focus:ring-1 focus:ring-orange-500">
                    </div>
                    <button id="currencySearchReset" type="button"
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
                            <th class="text-left py-3 px-4 font-medium">Currency</th>
                            <th class="text-left py-3 px-4 font-medium">Code</th>
                            <th class="text-left py-3 px-4 font-medium">Symbol</th>
                            <th class="text-left py-3 px-4 font-medium">Country</th>
                            <th class="text-left py-3 px-4 font-medium">Rate</th>
                            <th class="text-left py-3 px-4 font-medium">Status</th>
                            <th class="text-left py-3 pl-8 font-medium">Action</th>
                        </tr>
                    </thead>
                    <tbody id="currencyTableBody" class="divide-y divide-white/5">
                        @forelse ($currencies as $index => $currency)
                            <tr class="currency-row hover:bg-white/5 transition">
                                <td class="py-3 pr-4 text-slate-300">{{ $index + 1 }}</td>
                                <td class="py-3 px-4">
                                    <div>
                                        <p class="font-medium text-white">{{ $currency['name'] ?? '—' }}</p>
                                        <p class="text-xs text-slate-400">
                                            {{ ($currency['position'] ?? 'Prefix') === 'Prefix' ? 'Symbol before amount' : 'Symbol after amount' }}
                                        </p>
                                    </div>
                                </td>
                                <td class="py-3 px-4 text-slate-300">{{ $currency['code'] ?? '—' }}</td>
                                <td class="py-3 px-4 text-slate-300">{{ $currency['symbol'] ?? '—' }}</td>
                                <td class="py-3 px-4 text-slate-300">{{ $currency['country_name'] ?? '—' }}</td>
                                <td class="py-3 px-4 text-slate-300">{{ $currency['exchange_rate'] ?? '—' }}</td>
                                <td class="py-3 px-4">
                                    @if (($currency['status'] ?? '') === 'Active')
                                        <span
                                            class="px-2.5 py-1 rounded-full text-xs bg-emerald-500/15 text-emerald-400">Active</span>
                                    @else
                                        <span
                                            class="px-2.5 py-1 rounded-full text-xs bg-slate-500/20 text-slate-300">Inactive</span>
                                    @endif
                                    @if (!empty($currency['is_default']))
                                        <span class="ml-1.5 px-2 py-1 rounded-full text-xs bg-sky-500/15 text-sky-400">
                                            Default
                                        </span>
                                    @endif
                                </td>
                                <td class="py-3 pl-8">
                                    @include('core.components.actions', [
                                        'editClass' => 'openCurrencyEditModal',
                                        'editData' => [
                                            'id' => $currency['id'] ?? '',
                                            'name' => $currency['name'] ?? '',
                                            'code' => $currency['code'] ?? '',
                                            'symbol' => $currency['symbol'] ?? '',
                                            'country-id' => $currency['country_id'] ?? '',
                                            'rate' => $currency['exchange_rate'] ?? '',
                                            'position' => $currency['position'] ?? 'Prefix',
                                            'status' => $currency['status'] ?? 'Active',
                                            'default' => !empty($currency['is_default']) ? '1' : '0',
                                            'decimals' => $currency['decimals'] ?? 2,
                                        ],
                                        'deleteRoute' => route(
                                            'superadmin.currencies.destroy',
                                            $currency['id'] ?? 0),
                                    ])
                                </td>
                            </tr>
                        @empty
                            <tr id="currencyNoResultRow">
                                <td colspan="8" class="py-6 text-center text-sm text-slate-400">No currencies found.</td>
                            </tr>
                        @endforelse


                    </tbody>
                </table>

            </div>

        </div>
    </div>

    <div id="currencyModal" class="fixed inset-0 z-[120] hidden overflow-y-auto"
        data-store-url="{{ route('superadmin.currencies.store') }}"
        data-update-url-template="{{ route('superadmin.currencies.update', ['currency' => '__ID__']) }}">
        <div id="currencyModalBackdrop" class="sa-overlay absolute inset-0 bg-black/50"></div>
        <div class="relative z-10 min-h-screen flex items-start md:items-center justify-center p-4">
            <div
                class="w-full max-w-3xl max-h-[calc(100dvh-2rem)] overflow-y-auto glass-panel border border-white/10 rounded-2xl">
                <div class="px-5 py-4 border-b border-white/10 flex items-center justify-between">
                    <div>
                        <h2 id="currencyModalTitle" class="text-lg font-semibold text-white">Add Currency</h2>
                        <p id="currencyModalSubtitle" class="text-xs text-slate-400 mt-1">
                            Configure a new currency for billing and reporting
                        </p>
                    </div>
                    <button id="closeCurrencyModal" type="button"
                        class="w-8 h-8 rounded-md bg-white/5 hover:bg-white/10 text-slate-300 transition">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <form id="currencyForm" action="{{ route('superadmin.currencies.store') }}" method="POST"
                    class="p-5 space-y-4">
                    @csrf
                    <input id="currencyFormMethod" type="hidden" name="_method" value="PUT" disabled>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs text-slate-400 mb-1.5">Currency Name</label>
                            <input id="currencyName" type="text" name="name" required
                                placeholder="e.g. Nepalese Rupee"
                                class="sa-form-input w-full bg-[#0f172a] border border-white/10 rounded-lg px-3 py-2.5 text-sm text-white placeholder-slate-500 focus:outline-none focus:ring-1 focus:ring-orange-500">
                        </div>
                        <div>
                            <label class="block text-xs text-slate-400 mb-1.5">Currency Code</label>
                            <input id="currencyCode" type="text" name="code" maxlength="3" required
                                placeholder="e.g. NPR"
                                class="sa-form-input w-full uppercase bg-[#0f172a] border border-white/10 rounded-lg px-3 py-2.5 text-sm text-white placeholder-slate-500 focus:outline-none focus:ring-1 focus:ring-orange-500">
                        </div>
                        <div>
                            <label class="block text-xs text-slate-400 mb-1.5">Symbol</label>
                            <input id="currencySymbol" type="text" name="symbol" required placeholder="e.g. Rs"
                                class="sa-form-input w-full bg-[#0f172a] border border-white/10 rounded-lg px-3 py-2.5 text-sm text-white placeholder-slate-500 focus:outline-none focus:ring-1 focus:ring-orange-500">
                        </div>
                        <div>
                            <label class="block text-xs text-slate-400 mb-1.5">Country</label>
                            <select id="currencyCountry" name="country_id" required
                                class="sa-form-input w-full bg-[#0f172a] border border-white/10 rounded-lg px-3 py-2.5 text-sm text-white focus:outline-none focus:ring-1 focus:ring-orange-500">
                                <option value="">Select country</option>
                                @foreach ($countries as $country)
                                    <option value="{{ $country->id }}">
                                        {{ $country->flag ?? $country->iso_code }} {{ $country->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs text-slate-400 mb-1.5">Exchange Rate</label>
                            <input id="currencyRate" type="number" name="exchange_rate" min="0" step="0.000001"
                                required placeholder="e.g. 1.000000"
                                class="sa-form-input w-full bg-[#0f172a] border border-white/10 rounded-lg px-3 py-2.5 text-sm text-white placeholder-slate-500 focus:outline-none focus:ring-1 focus:ring-orange-500">
                        </div>
                        <div>
                            <label class="block text-xs text-slate-400 mb-1.5">Decimal Places</label>
                            <select id="currencyDecimals" name="decimals" required
                                class="sa-form-input w-full bg-[#0f172a] border border-white/10 rounded-lg px-3 py-2.5 text-sm text-white focus:outline-none focus:ring-1 focus:ring-orange-500">
                                <option value="0">0</option>
                                <option value="2" selected>2</option>
                                <option value="3">3</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs text-slate-400 mb-1.5">Symbol Position</label>
                            <select id="currencyPosition" name="position" required
                                class="sa-form-input w-full bg-[#0f172a] border border-white/10 rounded-lg px-3 py-2.5 text-sm text-white focus:outline-none focus:ring-1 focus:ring-orange-500">
                                <option value="Prefix">Prefix (Rs 100)</option>
                                <option value="Suffix">Suffix (100 Rs)</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs text-slate-400 mb-1.5">Status</label>
                            <select id="currencyStatus" name="status" required
                                class="sa-form-input w-full bg-[#0f172a] border border-white/10 rounded-lg px-3 py-2.5 text-sm text-white focus:outline-none focus:ring-1 focus:ring-orange-500">
                                <option value="Active">Active</option>
                                <option value="Inactive">Inactive</option>
                            </select>
                        </div>
                    </div>

                    <label
                        class="flex items-center gap-2 text-sm text-slate-300 bg-white/5 border border-white/10 rounded-lg px-3 py-2.5">
                        <input id="currencyDefault" type="checkbox" name="is_default" value="1"
                            class="rounded border-white/20 bg-transparent text-orange-500 focus:ring-orange-500">
                        Set as default billing currency
                    </label>

                    <div class="flex justify-end gap-2 pt-1">
                        <button id="cancelCurrencyModal" type="button"
                            class="px-4 py-2.5 rounded-lg text-sm bg-white/5 hover:bg-white/10 text-slate-300 transition">
                            Cancel
                        </button>
                        <button id="currencyModalSubmit" type="submit"
                            class="px-4 py-2.5 rounded-lg text-sm bg-orange-500/15 hover:bg-orange-500/25 text-orange-500 border border-orange-500/30 transition">
                            Save Currency
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
