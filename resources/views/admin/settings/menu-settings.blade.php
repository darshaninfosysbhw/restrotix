@extends('core.layouts.admin')

@section('content')
    <div class="flex-1 overflow-y-auto p-6 bg-gray-900 space-y-6">
        <div class="bg-gray-800 border border-gray-700 rounded-xl p-5 md:p-6">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <p class="text-xs uppercase tracking-[0.2em] text-gray-400">Settings</p>
                    <h1 class="text-2xl md:text-3xl font-bold text-white mt-1">Menu Settings</h1>
                    <p class="text-sm text-gray-400 mt-2">Choose how the public menu should look for each branch.</p>
                </div>
                <div class="flex items-center gap-2 text-xs text-gray-400">
                    <span class="px-2.5 py-1 rounded-full border border-orange-500/30 bg-orange-500/10 text-orange-400">
                        Branch level
                    </span>
                    <span class="px-2.5 py-1 rounded-full border border-white/10 bg-white/5">
                        Dark / Light theme
                    </span>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 xl:grid-cols-2 gap-4">
            @forelse ($branches as $branch)
                <div class="bg-gray-800 border border-gray-700 rounded-2xl p-5">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <p class="text-xs uppercase tracking-[0.2em] text-gray-400">Branch</p>
                            <h2 class="text-xl font-bold text-white mt-1">{{ $branch->branch_name }}</h2>
                            <p class="text-sm text-gray-400 mt-1">{{ $branch->city ?: 'No city set' }}</p>
                        </div>
                        <span class="px-2.5 py-1 rounded-full text-xs border border-white/10 text-gray-300 bg-white/5">
                            {{ strtoupper($branch->branch_menu_theme ?? 'dark') }} mode
                        </span>
                    </div>

                    <form action="{{ route('admin.settings.menu.update', $branch->id) }}" method="POST" class="mt-5 space-y-4">
                        @csrf
                        @method('PUT')

                        <div>
                            <label class="block text-xs text-gray-400 mb-1.5 font-medium">Public Menu Theme</label>
                            <select name="branch_menu_theme"
                                class="w-full bg-gray-900 border border-gray-700 rounded-lg px-3 py-2.5 text-sm text-white focus:outline-none focus:ring-1 focus:ring-orange-500 transition">
                                <option value="dark" {{ ($branch->branch_menu_theme ?? 'dark') === 'dark' ? 'selected' : '' }}>
                                    Dark Background
                                </option>
                                <option value="light" {{ ($branch->branch_menu_theme ?? 'dark') === 'light' ? 'selected' : '' }}>
                                    Light Background
                                </option>
                            </select>
                        </div>

                        <div class="flex items-center justify-between gap-3 pt-1">
                            <p class="text-xs text-gray-500">This only changes customer side menu appearance.</p>
                            <button type="submit"
                                class="inline-flex items-center justify-center gap-2 bg-orange-500/10 hover:bg-orange-500/20 text-orange-500 border border-orange-500/30 px-4 py-2.5 rounded-lg text-sm font-medium transition">
                                <i class="fas fa-save"></i>
                                Save Theme
                            </button>
                        </div>
                    </form>
                </div>
            @empty
                <div class="bg-gray-800 border border-gray-700 rounded-xl p-6 text-gray-400">
                    No branches found for this tenant.
                </div>
            @endforelse
        </div>
    </div>
@endsection
