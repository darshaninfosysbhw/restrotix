@extends('core.layouts.superadmin')

@section('content')
    <div class="sa-main relative z-0 flex-1 overflow-y-auto p-4 md:p-6 space-y-6">

        <div class="glass-panel p-5 md:p-6">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <p class="text-xs uppercase tracking-[0.2em] text-slate-400">Tenant Management</p>
                    <h1 class="text-2xl md:text-3xl font-bold text-white mt-1">Registered Restaurants</h1>
                    <p class="text-sm text-slate-400 mt-2">All onboarded restaurants are listed here. You can add a new
                        restaurant using the modal form.</p>
                </div>
                <x-core::ui.button id="openRestaurantModal" data-modal-open="RestaurantModal">
                    <i class="fas fa-plus"></i> Add New Restaurant
                </x-core::ui.button>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
            <x-ui.stat-card title="Total Restaurants" :value="$restaurantStats['total']" icon="fas fa-store" color="orange" />
            <x-ui.stat-card title="Active" :value="$restaurantStats['active']" icon="fas fa-check-circle" color="emerald" />
            <x-ui.stat-card title="On Trial" :value="$restaurantStats['trial']" icon="fas fa-hourglass-half" color="amber" />
            <x-ui.stat-card title="Total Branches" :value="$restaurantStats['branches']" icon="fas fa-code-branch" color="sky" />
        </div>

        <div class="glass-panel p-5">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-3 mb-4">
                <div class="flex items-center gap-2">
                    <span class="text-sm text-slate-400">All Restaurants</span>
                    <span id="restaurantCountBadge"
                        class="px-2.5 py-1 rounded-full text-xs bg-orange-500/15 text-orange-500 border border-orange-500/30">
                        Total : {{ $restaurantStats['total'] }}
                    </span>
                </div>
                <div class="flex flex-col sm:flex-row sm:items-center gap-2 w-full lg:w-auto">
                    <div class="relative w-full sm:w-64">
                        <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                        <input id="restaurantTableSearch" type="text" placeholder="Search restaurant, owner, plan..."
                            class="sa-form-input w-full bg-[#0f172a] border border-white/10 rounded-lg pl-9 pr-3 py-2 text-sm text-white placeholder-slate-500 focus:outline-none focus:ring-1 focus:ring-orange-500">
                    </div>
                    <div class="flex flex-wrap items-center gap-2">
                        <button id="restaurantSearchReset" type="button"
                            class="px-3 py-2 rounded-lg text-xs bg-white/5 hover:bg-white/10 text-slate-300 border border-white/10 transition">
                            Reset
                        </button>
                        <button type="button"
                            class="px-3 py-2 rounded-lg text-xs bg-emerald-500/15 hover:bg-emerald-500/25 text-emerald-400 border border-emerald-500/25 transition inline-flex items-center gap-1.5">
                            <i class="fas fa-file-import"></i>
                            Import
                        </button>
                        <button type="button"
                            class="px-3 py-2 rounded-lg text-xs bg-sky-500/15 hover:bg-sky-500/25 text-sky-400 border border-sky-500/25 transition inline-flex items-center gap-1.5">
                            <i class="fas fa-file-export"></i>
                            Export
                        </button>
                    </div>
                </div>
            </div>
            <div class="overflow-x-auto overflow-y-visible">
                <table class="w-full text-sm">
                    <thead class="text-xs text-slate-400 border-b border-white/10 uppercase tracking-wide">
                        <tr>
                            <th class="text-left py-3 pr-4 font-medium">#</th>
                            <th class="text-left py-3 px-4 font-medium">Restaurant</th>
                            <th class="text-left py-3 px-4 font-medium">Owner</th>
                            <th class="text-left py-3 px-4 font-medium">Contact</th>
                            <th class="text-left py-3 px-4 font-medium">Plan</th>
                            <th class="text-left py-3 px-4 font-medium">Branches</th>
                            <th class="text-left py-3 px-4 font-medium">Status</th>
                            <th class="text-left py-3 px-4 font-medium">Joined</th>
                            <th class="text-left py-3 pl-8 font-medium">Action</th>
                        </tr>
                    </thead>
                    <tbody id="restaurantTableBody" class="divide-y divide-white/5">
                        @foreach ($restaurants as $index => $restaurant)
                            <tr class="restaurant-row hover:bg-white/5 transition">
                                <td class="py-3 pr-4 text-slate-300">{{ $restaurants->firstItem() + $index }}</td>
                                <td class="py-3 px-4">
                                    <div>
                                        <p class="font-medium text-white">{{ $restaurant['name'] }}</p>
                                        <p class="text-xs text-slate-400">{{ $restaurant['city'] }}</p>
                                    </div>
                                </td>
                                <td class="py-3 px-4 text-white">{{ $restaurant['owner'] }}</td>
                                <td class="py-3 px-4">
                                    <p class="text-slate-300">{{ $restaurant['email'] }}</p>
                                    <p class="text-xs text-slate-400">{{ $restaurant['phone'] }}</p>
                                </td>
                                <td class="py-3 px-4">
                                    <span
                                        class="bg-white/5 px-2.5 py-1 rounded-full text-xs">{{ $restaurant['plan'] ?? '—' }}</span>
                                </td>
                                <td class="py-3 px-4">
                                    <span class="bg-white/5 px-2.5 py-1 rounded-full text-xs">{{ $restaurant['branches'] }}
                                        branches</span>
                                </td>
                                <td class="py-3 px-4">
                                    @if ($restaurant['status'] === 'Active')
                                        <span
                                            class="px-2.5 py-1 rounded-full text-xs bg-emerald-500/15 text-emerald-400">Active</span>
                                    @elseif($restaurant['status'] === 'Trial')
                                        <span
                                            class="px-2.5 py-1 rounded-full text-xs bg-amber-400/15 text-amber-400">Trial</span>
                                    @elseif($restaurant['status'] === 'Canceled')
                                        <span
                                            class="px-2.5 py-1 rounded-full text-xs bg-rose-400/10 text-rose-400">Canceled</span>
                                    @else
                                        <span
                                            class="px-2.5 py-1 rounded-full text-xs bg-rose-400/10 text-rose-400">Expired</span>
                                    @endif
                                </td>
                                <td class="py-3 px-4 whitespace-nowrap">
                                    <p class="text-slate-300">{{ $restaurant['joined'] }}</p>
                                    <p class="text-xs text-slate-400 mt-0.5">{{ $restaurant['joined_bs'] }}</p>
                                </td>
                                <td class="py-3 pl-8">
                                    <div class="flex items-center gap-2">
                                        @if (!empty($restaurant['owner_user_id']))
                                            <a href="{{ route('impersonate', $restaurant['owner_user_id']) }}"
                                                class="px-2.5 py-1.5 rounded-md text-xs bg-orange-500/10 hover:bg-orange-500/20 text-orange-400 border border-orange-500/20 transition inline-flex items-center gap-1.5">
                                                <i class="fas fa-user-lock text-[11px]"></i>
                                                Login
                                            </a>
                                        @else
                                            <button type="button" disabled
                                                class="px-2.5 py-1.5 rounded-md text-xs bg-slate-500/10 text-slate-500 border border-slate-500/20 cursor-not-allowed inline-flex items-center gap-1.5">
                                                <i class="fas fa-user-lock text-[11px]"></i>
                                                Login
                                            </button>
                                        @endif
                                        @include('core.components.actions', [
                                            'editClass' => 'openRestaurantEditModal',
                                            'editData' => [
                                                'id' => $restaurant['id'],
                                                'name' => $restaurant['name'],
                                                'slug' => $restaurant['slug'],
                                                'owner' => $restaurant['owner'],
                                                'email' => $restaurant['email'],
                                                'phone' => $restaurant['phone'],
                                                'city' => $restaurant['city'],
                                                'country-id' => $restaurant['country_id'],
                                                'plan-id' => $restaurant['plan_id'],
                                                'billing-cycle' => $restaurant['billing_cycle'] ?? 'monthly',
                                                'status-key' => $restaurant['status_key'] ?? strtolower($restaurant['status'] ?? ''),
                                            ],
                                        
                                            'deleteRoute' => route(
                                                'superadmin.tenants.destroy',
                                                $restaurant['id']),
                                        ])
                                    </div>
                                </td>

                            </tr>
                        @endforeach
                        <tr id="restaurantNoResultRow" class="{{ $restaurantStats['total'] ? 'hidden' : '' }}">
                            <td colspan="9" class="py-6 text-center text-sm text-slate-400">No restaurant found.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            @if ($restaurants->hasPages())
                <div class="mt-4 flex justify-end">
                    <x-core::ui.pagination :paginator="$restaurants" :show-summary="false" />
                </div>
            @endif
        </div>
    </div>

    <div id="restaurantModal" class="fixed inset-0 z-[120] hidden overflow-y-auto"
        data-store-url="{{ route('superadmin.tenants.store') }}"
        data-update-url-template="{{ route('superadmin.tenants.update', ['tenant' => '__ID__']) }}">
        <div id="restaurantModalBackdrop" class="sa-overlay absolute inset-0 bg-black/50"></div>
        <div class="relative z-10 min-h-screen flex items-start md:items-center justify-center p-4">
            <div
                class="w-full max-w-3xl max-h-[calc(100dvh-2rem)] overflow-y-auto glass-panel border border-white/10 rounded-2xl">
                <div class="px-5 py-4 border-b border-white/10 flex items-center justify-between">
                    <div>
                        <h2 id="restaurantModalTitle" class="text-lg font-semibold text-white">Add New Restaurant</h2>
                        <p id="restaurantModalSubtitle" class="text-xs text-slate-400 mt-1">Fill details to register a new
                            restaurant tenant</p>
                    </div>
                    <button id="closeRestaurantModal" type="button"
                        class="w-8 h-8 rounded-md bg-white/5 hover:bg-white/10 text-slate-300 transition">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <form id="restaurantForm" action="{{ route('superadmin.tenants.store') }}" method="POST"
                    class="p-5 space-y-4">
                    @csrf
                    <input id="restaurantFormMethod" type="hidden" name="_method" value="PUT" disabled>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs text-slate-400 mb-1.5">Restaurant Name</label>
                            <input id="restaurantCompanyName" type="text" name="company_name" required
                                placeholder="e.g. Royal Tandoor"
                                class="sa-form-input w-full bg-[#0f172a] border border-white/10 rounded-lg px-3 py-2.5 text-sm text-white placeholder-slate-500 focus:outline-none focus:ring-1 focus:ring-orange-500">
                        </div>
                        <div>
                            <label class="block text-xs text-slate-400 mb-1.5">Subdomain / Slug</label>
                            <input id="restaurantSlug" type="text" name="slug" required
                                placeholder="e.g. royaltandoor"
                                class="sa-form-input w-full bg-[#0f172a] border border-white/10 rounded-lg px-3 py-2.5 text-sm text-white placeholder-slate-500 focus:outline-none focus:ring-1 focus:ring-orange-500">
                            <p class="text-[10px] text-slate-500 mt-1">URL: {slug}.restrochain.com (Only small letters &
                                hyphens)</p>
                        </div>
                        <div>
                            <label class="block text-xs text-slate-400 mb-1.5">Owner Name</label>
                            <input id="restaurantOwnerName" type="text" name="owner_name" required
                                placeholder="e.g. Rajan Kushwaha"
                                class="sa-form-input w-full bg-[#0f172a] border border-white/10 rounded-lg px-3 py-2.5 text-sm text-white placeholder-slate-500 focus:outline-none focus:ring-1 focus:ring-orange-500">
                        </div>
                        <div>
                            <label class="block text-xs text-slate-400 mb-1.5">Email</label>
                            <input id="restaurantEmail" type="email" name="email" required
                                placeholder="owner@example.com"
                                class="sa-form-input w-full bg-[#0f172a] border border-white/10 rounded-lg px-3 py-2.5 text-sm text-white placeholder-slate-500 focus:outline-none focus:ring-1 focus:ring-orange-500">
                        </div>
                        <div>
                            <label class="block text-xs text-slate-400 mb-1.5">Phone</label>
                            <input id="restaurantPhone" type="text" name="phone" required
                                placeholder="+91 98XXXXXXXX"
                                class="sa-form-input w-full bg-[#0f172a] border border-white/10 rounded-lg px-3 py-2.5 text-sm text-white placeholder-slate-500 focus:outline-none focus:ring-1 focus:ring-orange-500">
                        </div>

                        {{-- Blade Modal Form के अंदर --}}
                        <div>
                            <label class="block text-xs text-slate-400 mb-1.5">Country</label>
                            <select id="restaurantCountry" name="country_id" required
                                class="sa-form-input w-full bg-[#0f172a] border border-white/10 rounded-lg px-3 py-2.5 text-sm text-white focus:outline-none focus:ring-1 focus:ring-orange-500">
                                <option value="">Select country</option>
                                @foreach (\App\Models\Country::where('is_active', 1)->get() as $country)
                                    <option value="{{ $country->id }}"
                                        {{ session('active_country_id') == $country->id ? 'selected' : '' }}>
                                        {{ $country->flag ?? $country->iso_code }} {{ $country->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs text-slate-400 mb-1.5">City</label>
                            <input id="restaurantCity" type="text" name="city" required
                                placeholder="e.g. Kathmandu"
                                class="sa-form-input w-full bg-[#0f172a] border border-white/10 rounded-lg px-3 py-2.5 text-sm text-white placeholder-slate-500 focus:outline-none focus:ring-1 focus:ring-orange-500">
                        </div>

                        <div>
                            <label class="block text-xs text-slate-400 mb-1.5">Plan</label>
                            <select id="restaurantPlan" name="subscription_plan" required
                                class="sa-form-input w-full bg-[#0f172a] border border-white/10 rounded-lg px-3 py-2.5 text-sm text-white focus:outline-none focus:ring-1 focus:ring-orange-500">
                                <option value="">Select plan</option>
                                @foreach ($plans as $plan)
                                    @php
                                        // Is plan ki current currency wali price nikalo
                                        $currentPrice = $plan->prices->first();
                                        $symbol = session('currency_symbol', '₹');
                                    @endphp
                                    <option value="{{ $plan->id }}">
                                        {{ $plan->name }}
                                        @if ($currentPrice)
                                            ({{ $symbol }}{{ $currentPrice->monthly_price }}/mo)
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs text-slate-400 mb-1.5">Billing Cycle</label>
                            <select id="restaurantBillingCycle" name="billing_cycle" required
                                class="sa-form-input w-full bg-[#0f172a] border border-white/10 rounded-lg px-3 py-2.5 text-sm text-white focus:outline-none focus:ring-1 focus:ring-orange-500">
                                <option value="monthly" selected>Monthly</option>
                                <option value="yearly">Yearly</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs text-slate-400 mb-1.5">Status</label>
                            <select id="restaurantStatus" name="subscription_status" required
                                class="sa-form-input w-full bg-[#0f172a] border border-white/10 rounded-lg px-3 py-2.5 text-sm text-white focus:outline-none focus:ring-1 focus:ring-orange-500">
                                <option value="">Select status</option>
                                <option value="trial">Trial</option>
                                <option value="active">Active</option>
                                <option value="expired">Expired</option>
                                <option value="canceled">Canceled</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs text-slate-400 mb-1.5">Address</label>
                        <textarea id="restaurantAddress" name="address" rows="3" placeholder="Restaurant full address"
                            class="sa-form-input w-full bg-[#0f172a] border border-white/10 rounded-lg px-3 py-2.5 text-sm text-white placeholder-slate-500 focus:outline-none focus:ring-1 focus:ring-orange-500"></textarea>
                    </div>

                    <div class="flex justify-end gap-2 pt-1">
                        <button id="cancelRestaurantModal" type="button"
                            class="px-4 py-2.5 rounded-lg text-sm bg-white/5 hover:bg-white/10 text-slate-300 transition">
                            Cancel
                        </button>
                        <button id="restaurantModalSubmit" type="submit"
                            class="px-4 py-2.5 rounded-lg text-sm bg-orange-500/15 hover:bg-orange-500/25 text-orange-500 border border-orange-500/30 transition">
                            Register Restaurant
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Blade file ke niche script tag mein
        document.getElementById('restaurantCompanyName').addEventListener('input', function() {
            let slug = this.value.toLowerCase()
                .replace(/[^\w ]+/g, '')
                .replace(/ +/g, '-');
            document.getElementById('restaurantSlug').value = slug;
        });
    </script>
@endsection
