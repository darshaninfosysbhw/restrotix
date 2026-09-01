@extends('core.layouts.superadmin')

@section('content')
    @php
        $services = $services ?? collect();
        $serviceStats = $serviceStats ?? [
            'total' => $services->count(),
            'active' => 0,
            'inactive' => 0,
            'revenue' => 0,
        ];
    @endphp

    <div class="sa-main relative z-0 flex-1 overflow-y-auto p-4 md:p-6 space-y-6">
        <div class="glass-panel p-5 md:p-6">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <p class="text-xs uppercase tracking-[0.2em] text-slate-400">Service Management</p>
                    <h1 class="text-2xl md:text-3xl font-bold text-white mt-1">Service Add-ons</h1>
                    <p class="text-sm text-slate-400 mt-2">
                        Manage the add-on services available to tenant restaurants. Create new services and control pricing.
                    </p>
                </div>
                {{-- <button id="openServiceModal" type="button"
                    class="inline-flex items-center justify-center gap-2 bg-orange-500/15 hover:bg-orange-500/25 text-orange-500 border border-orange-500/30 px-4 py-2.5 rounded-lg text-sm font-medium transition">
                    <i class="fas fa-plus"></i>
                    Add New Service
                </button> --}}
                <x-core::ui.button id="openServiceModal" data-modal-open="ServiceModal">
                    <i class="fas fa-plus"></i> Add New Service
                </x-core::ui.button>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
            <x-ui.stat-card title="Total Services" :value="$serviceStats['total']" icon="fas fa-layer-group" color="orange" />
            <x-ui.stat-card title="Active" :value="$serviceStats['active']" icon="fas fa-check-circle" color="emerald" />
            <x-ui.stat-card title="Inactive" :value="$serviceStats['inactive']" icon="fas fa-pause-circle" color="amber" />
            <x-ui.stat-card title="Monthly Revenue" :value="$serviceStats['revenue']" icon="fas fa-chart-line" color="sky" />
            {{--
            <div class="glass-panel p-4">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-xs text-slate-400 uppercase tracking-wide">Monthly Revenue</p>
                        <p class="text-2xl font-bold text-white mt-1">NPR {{ $serviceStats['revenue'] ?? 0 }}</p>
                    </div>
                    <div class="w-9 h-9 rounded-full bg-sky-500/15 text-sky-400 flex items-center justify-center">
                        <i class="fas fa-chart-line text-sm"></i>
                    </div>
                </div>
            </div> --}}
        </div>

        <div class="glass-panel p-5">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-3 mb-4">
                <div class="flex items-center gap-2">
                    <span class="text-sm text-slate-400">All Services</span>
                    <span id="serviceCountBadge"
                        class="px-2.5 py-1 rounded-full text-xs bg-orange-500/15 text-orange-500 border border-orange-500/30">
                        Total : {{ $serviceStats['total'] ?? 0 }}
                    </span>
                </div>
                <div class="flex flex-col sm:flex-row sm:items-center gap-2 w-full lg:w-auto">
                    <div class="relative w-full sm:w-64">
                        <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                        <input id="serviceTableSearch" type="text" placeholder="Search service, slug, price..."
                            class="sa-form-input w-full bg-[#0f172a] border border-white/10 rounded-lg pl-9 pr-3 py-2 text-sm text-white placeholder-slate-500 focus:outline-none focus:ring-1 focus:ring-orange-500">
                    </div>
                    <div class="flex flex-wrap items-center gap-2">
                        <button id="serviceSearchReset" type="button"
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
                            <th class="text-left py-3 px-4 font-medium">Service</th>
                            <th class="text-left py-3 px-4 font-medium">Slug</th>
                            <th class="text-left py-3 px-4 font-medium">Price</th>
                            <th class="text-left py-3 px-4 font-medium">Description</th>
                            <th class="text-left py-3 px-4 font-medium">Status</th>
                            <th class="text-left py-3 pl-8 font-medium">Action</th>
                        </tr>
                    </thead>
                    <tbody id="serviceTableBody" class="divide-y divide-white/5">
                        @forelse ($services as $index => $service)
                            <tr class="service-row hover:bg-white/5 transition">
                                <td class="py-3 pr-4 text-slate-300">{{ $index + 1 }}</td>
                                <td class="py-3 px-4">
                                    <div>
                                        <p class="font-medium text-white">{{ $service->name ?? ($service['name'] ?? '—') }}
                                        </p>
                                        <p class="text-xs text-slate-400">Addon service</p>
                                    </div>
                                </td>
                                <td class="py-3 px-4 text-slate-300">{{ $service->slug ?? ($service['slug'] ?? '—') }}</td>
                                <td class="py-3 px-4 text-slate-300">NPR {{ $service->price ?? ($service['price'] ?? '0') }}
                                </td>
                                <td class="py-3 px-4 text-slate-300 max-w-[18rem]">
                                    <span
                                        class="block">{{ $service->description ?? ($service['description'] ?? '—') }}</span>
                                </td>
                                <td class="py-3 px-4">
                                    @php
                                        $status = $service->status ?? ($service['status'] ?? 'Active');
                                    @endphp
                                    @if ($status === 'Active')
                                        <span
                                            class="px-2.5 py-1 rounded-full text-xs bg-emerald-500/15 text-emerald-400">Active</span>
                                    @else
                                        <span
                                            class="px-2.5 py-1 rounded-full text-xs bg-slate-500/20 text-slate-300">Inactive</span>
                                    @endif
                                </td>

                                <td class="py-3 pl-8">
                                    @include('core.components.actions', [
                                        'editClass' => 'openServiceEditModal',
                                        'editData' => [
                                            'id' => $service->id ?? ($service['id'] ?? ''),
                                            'name' => $service->name ?? ($service['name'] ?? ''),
                                            'slug' => $service->slug ?? ($service['slug'] ?? ''),
                                            'price' => $service->price ?? ($service['price'] ?? ''),
                                            'description' =>
                                                $service->description ?? ($service['description'] ?? ''),
                                            'status' => $service->status ?? ($service['status'] ?? ''),
                                        ],
                                        'deleteRoute' => route(
                                            'superadmin.services.destroy',
                                            $service->id ?? ($service['id'] ?? 0)),
                                    ])
                                </td>
                            </tr>
                        @empty
                            <tr id="serviceNoResultRow">
                                <td colspan="7" class="py-6 text-center text-sm text-slate-400">No services found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div id="serviceModal" class="fixed inset-0 z-[120] hidden overflow-y-auto"
        data-store-url="{{ route('superadmin.services.store') }}"
        data-update-url-template="{{ route('superadmin.services.update', ['service' => '__ID__']) }}">
        <div id="serviceModalBackdrop" class="sa-overlay absolute inset-0 bg-black/50"></div>
        <div class="relative z-10 min-h-screen flex items-start md:items-center justify-center p-4">
            <div
                class="w-full max-w-3xl max-h-[calc(100dvh-2rem)] overflow-y-auto glass-panel border border-white/10 rounded-2xl">
                <div class="px-5 py-4 border-b border-white/10 flex items-center justify-between">
                    <div>
                        <h2 id="serviceModalTitle" class="text-lg font-semibold text-white">Add New Service</h2>
                        <p id="serviceModalSubtitle" class="text-xs text-slate-400 mt-1">Define a new add-on service for
                            tenants</p>
                    </div>
                    <button id="closeServiceModal" type="button"
                        class="w-8 h-8 rounded-md bg-white/5 hover:bg-white/10 text-slate-300 transition">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <form id="serviceForm" action="{{ route('superadmin.services.store') }}" method="POST"
                    class="p-5 space-y-4">
                    @csrf
                    <input id="serviceFormMethod" type="hidden" name="_method" value="PUT" disabled>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs text-slate-400 mb-1.5">Service Name</label>
                            <input id="serviceName" type="text" name="name" required
                                placeholder="e.g. Inventory Management"
                                class="sa-form-input w-full bg-[#0f172a] border border-white/10 rounded-lg px-3 py-2.5 text-sm text-white placeholder-slate-500 focus:outline-none focus:ring-1 focus:ring-orange-500">
                        </div>
                        <div>
                            <label class="block text-xs text-slate-400 mb-1.5">Slug</label>
                            <input id="serviceSlug" type="text" name="slug" required placeholder="e.g. inventory"
                                class="sa-form-input w-full bg-[#0f172a] border border-white/10 rounded-lg px-3 py-2.5 text-sm text-white placeholder-slate-500 focus:outline-none focus:ring-1 focus:ring-orange-500">
                        </div>
                        <div>
                            <label class="block text-xs text-slate-400 mb-1.5">Price (NPR)</label>
                            <input id="servicePrice" type="number" name="price" min="0" step="0.01"
                                required placeholder="e.g. 1200"
                                class="sa-form-input w-full bg-[#0f172a] border border-white/10 rounded-lg px-3 py-2.5 text-sm text-white placeholder-slate-500 focus:outline-none focus:ring-1 focus:ring-orange-500">
                        </div>
                        <div>
                            <label class="block text-xs text-slate-400 mb-1.5">Status</label>
                            <select id="serviceStatus" name="status" required
                                class="sa-form-input w-full bg-[#0f172a] border border-white/10 rounded-lg px-3 py-2.5 text-sm text-white focus:outline-none focus:ring-1 focus:ring-orange-500">
                                <option value="Active">Active</option>
                                <option value="Inactive">Inactive</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs text-slate-400 mb-1.5">Description</label>
                        <textarea id="serviceDescription" name="description" rows="3" placeholder="Short service summary"
                            class="sa-form-input w-full bg-[#0f172a] border border-white/10 rounded-lg px-3 py-2.5 text-sm text-white placeholder-slate-500 focus:outline-none focus:ring-1 focus:ring-orange-500"></textarea>
                    </div>

                    <div class="flex justify-end gap-2 pt-1">
                        <button id="cancelServiceModal" type="button"
                            class="px-4 py-2.5 rounded-lg text-sm bg-white/5 hover:bg-white/10 text-slate-300 transition">
                            Cancel
                        </button>
                        <button id="serviceModalSubmit" type="submit"
                            class="px-4 py-2.5 rounded-lg text-sm bg-orange-500/15 hover:bg-orange-500/25 text-orange-500 border border-orange-500/30 transition">
                            Save Service
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
