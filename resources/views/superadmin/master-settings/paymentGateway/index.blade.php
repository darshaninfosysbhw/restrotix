@extends('core.layouts.superadmin')

@section('content')
    @php
        // Dummy data for structure
        $gateways =
            $gateways ??
            collect([
                [
                    'id' => 1,
                    'name' => 'Stripe',
                    'slug' => 'stripe',
                    'logo' => 'fab fa-stripe',
                    'currencies' => ['USD', 'EUR'],
                    'mode' => 'Live',
                    'status' => 'Active',
                    'updated_at' => '2026-03-20 14:30',
                ],
                [
                    'id' => 2,
                    'name' => 'Khalti',
                    'slug' => 'khalti',
                    'logo' => 'fas fa-wallet',
                    'currencies' => ['NPR'],
                    'mode' => 'Sandbox',
                    'status' => 'Active',
                    'updated_at' => '2026-03-22 10:15',
                ],
            ]);
    @endphp

    <div class="sa-main relative z-0 flex-1 overflow-y-auto p-4 md:p-6 space-y-6">
        {{-- Header --}}
        <div class="glass-panel p-5 md:p-6">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <p class="text-xs uppercase tracking-[0.2em] text-slate-400">Settings</p>
                    <h1 class="text-2xl md:text-3xl font-bold text-white mt-1">Payment Gateways</h1>
                    <p class="text-sm text-slate-400 mt-2">
                        Manage, configure, and update payment gateways seamlessly
                    </p>
                </div>
                <x-core::ui.button id="openGatewayModal" data-modal-open="gatewayModal" data-mode="sandbox"
                    data-name="New Gateway">
                    <i class="fas fa-plus"></i> Add Payment Gateway
                </x-core::ui.button>
                {{-- <x-core::ui.button class="openGatewayModal" data-name="New Gateway" data-slug="stripe" data-mode="sandbox">
                    <i class="fas fa-plus"></i> Add Payment Gateway
                </x-core::ui.button> --}}
            </div>
        </div>

        {{-- Gateway Table --}}
        <div class="glass-panel p-5">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="text-xs text-slate-400 border-b border-white/10 uppercase tracking-wide">
                        <tr>
                            <th class="text-left py-3 px-4">Gateway</th>
                            <th class="text-left py-3 px-4">Currencies</th>
                            <th class="text-left py-3 px-4">Mode</th>
                            <th class="text-left py-3 px-4">Status</th>
                            <th class="text-right py-3 px-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5">
                        @foreach ($gateways as $gateway)
                            <tr class="hover:bg-white/5 transition">
                                <td class="py-3 px-4 flex items-center gap-3">
                                    <div
                                        class="w-9 h-9 rounded-lg bg-white/5 flex items-center justify-center text-orange-500 border border-white/5">
                                        <i class="{{ $gateway['logo'] }} text-xl"></i>
                                    </div>
                                    <span class="font-medium text-white">{{ $gateway['name'] }}</span>
                                </td>
                                <td class="py-3 px-4">
                                    @foreach ($gateway['currencies'] as $curr)
                                        <span
                                            class="px-2 py-0.5 rounded text-[10px] bg-slate-500/20 text-slate-300 border border-white/10">{{ $curr }}</span>
                                    @endforeach
                                </td>
                                <td class="py-3 px-4">
                                    <span
                                        class="px-2.5 py-1 rounded-full text-[10px] font-bold {{ $gateway['mode'] == 'Live' ? 'bg-emerald-500/15 text-emerald-400 border-emerald-500/20' : 'bg-amber-500/15 text-amber-400 border-amber-500/20' }}">
                                        {{ strtoupper($gateway['mode']) }}
                                    </span>
                                </td>
                                <td class="py-3 px-4">
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="checkbox" class="sr-only peer"
                                            {{ $gateway['status'] == 'Active' ? 'checked' : '' }}>
                                        <div
                                            class="w-9 h-5 bg-white/10 rounded-full peer peer-checked:bg-orange-500 after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-slate-400 after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:after:translate-x-full peer-checked:after:bg-white">
                                        </div>
                                    </label>
                                </td>
                                <td class="py-3 px-4 text-right">
                                    <button
                                        class="openGatewayModal px-3 py-1.5 rounded-lg bg-orange-500/10 text-orange-500 border border-orange-500/20 hover:bg-orange-500/20 text-xs transition"
                                        data-name="{{ $gateway['name'] }}" data-slug="{{ $gateway['slug'] }}"
                                        data-mode="{{ strtolower($gateway['mode']) }}">
                                        <i class="fas fa-cog mr-1"></i> Settings
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Configuration Modal --}}
    <div id="gatewayModal" class="fixed inset-0 z-[2000] hidden overflow-y-auto">
        <div class="absolute inset-0 bg-black/70 backdrop-blur-md closeModal sa-overlay-close"></div>
        <div class="relative z-10 min-h-screen flex items-center justify-center p-4 pointer-events-none">
            <div
                class="w-full max-w-2xl glass-panel border border-white/10 rounded-2xl overflow-hidden shadow-2xl pointer-events-auto">
                <div class="px-6 py-4 border-b border-white/10 flex items-center justify-between bg-white/5">
                    <h2 class="text-lg font-semibold text-white uppercase tracking-tight">Setup <span id="modalGatewayName"
                            class="text-orange-500">Gateway</span></h2>
                    <button type="button" class="closeModal text-slate-400 hover:text-white transition"><i
                            class="fas fa-times"></i></button>
                </div>

                <form id="gatewayForm" class="p-6 space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        {{-- General --}}
                        <div class="space-y-4">
                            <label
                                class="block text-xs text-slate-400 uppercase tracking-widest font-bold">Environment</label>
                            <div class="flex gap-2 p-1 bg-[#0f172a] border border-white/10 rounded-lg">
                                <label
                                    class="flex-1 text-center py-2 rounded-md text-[10px] font-bold cursor-pointer transition">
                                    <input type="radio" name="mode" value="sandbox" class="hidden peer">
                                    <span class="text-slate-500 peer-checked:text-amber-400">SANDBOX</span>
                                </label>
                                <label
                                    class="flex-1 text-center py-2 rounded-md text-[10px] font-bold cursor-pointer transition">
                                    <input type="radio" name="mode" value="live" class="hidden peer">
                                    <span class="text-slate-500 peer-checked:text-emerald-400">LIVE</span>
                                </label>
                            </div>

                            <label
                                class="block text-xs text-slate-400 uppercase tracking-widest font-bold mt-4">Currencies</label>
                            <select multiple
                                class="sa-form-input w-full bg-[#0f172a] border border-white/10 rounded-lg px-3 py-2 text-xs text-white h-24 focus:ring-1 focus:ring-orange-500">
                                <option value="USD">USD - Dollar</option>
                                <option value="NPR">NPR - Rupee</option>
                                <option value="INR">INR - Rupee</option>
                            </select>
                        </div>

                        {{-- Dynamic Keys --}}
                        <div class="space-y-4">
                            <label class="block text-xs text-slate-400 uppercase tracking-widest font-bold">API Keys</label>

                            <div id="fields-stripe" class="gateway-fields hidden space-y-3">
                                <input type="text" placeholder="Stripe Publishable Key"
                                    class="sa-form-input w-full bg-[#0f172a] border border-white/10 rounded-lg px-3 py-2.5 text-sm text-white">
                                <div class="relative">
                                    <input type="password" placeholder="Stripe Secret Key"
                                        class="sa-form-input w-full bg-[#0f172a] border border-white/10 rounded-lg pl-3 pr-10 py-2.5 text-sm text-white">
                                    <button type="button"
                                        class="togglePassword absolute right-3 top-1/2 -translate-y-1/2 text-slate-500"><i
                                            class="fas fa-eye"></i></button>
                                </div>
                            </div>

                            <div id="fields-khalti" class="gateway-fields hidden space-y-3">
                                <input type="text" placeholder="Khalti Public Key"
                                    class="sa-form-input w-full bg-[#0f172a] border border-white/10 rounded-lg px-3 py-2.5 text-sm text-white">
                                <input type="password" placeholder="Khalti Secret Key"
                                    class="sa-form-input w-full bg-[#0f172a] border border-white/10 rounded-lg px-3 py-2.5 text-sm text-white">
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end gap-3 pt-6 border-t border-white/10">
                        <button type="button"
                            class="closeModal px-5 py-2.5 text-xs bg-white/5 text-slate-400 rounded-lg uppercase font-bold">Cancel</button>
                        <button type="submit"
                            class="px-6 py-2.5 text-xs bg-orange-500/20 text-orange-500 border border-orange-500/40 rounded-lg uppercase font-bold hover:bg-orange-500/30">Save
                            Settings</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
