@extends('core.layouts.admin')

@section('content')
    <div class="flex-1 overflow-y-auto p-6 bg-gray-900 space-y-6">
        <div class="bg-gray-800 border border-gray-700 rounded-xl p-5 md:p-6">
            <p class="text-xs uppercase tracking-[0.2em] text-gray-400">Settings</p>
            <h1 class="text-2xl md:text-3xl font-bold text-white mt-1">Identity Masking</h1>
            <p class="text-sm text-gray-400 mt-2">Control the branch name shown to customers and internal users.</p>
        </div>

        @if (!$canMaskIdentity)
            <div class="max-w-2xl rounded-xl border border-orange-500/30 bg-orange-500/10 p-5">
                <h2 class="text-lg font-semibold text-orange-300">Upgrade required</h2>
                <p class="mt-2 text-sm text-orange-100/80">
                    Identity Masking is not active for your current plan. Enable the Identity Masking service or upgrade your plan.
                </p>
            </div>
        @else
            <div class="grid grid-cols-1 xl:grid-cols-2 gap-4">
                @forelse ($branches as $branch)
                    <div class="bg-gray-800 border border-gray-700 rounded-2xl p-5">
                        <div>
                            <p class="text-xs uppercase tracking-[0.2em] text-gray-400">Branch</p>
                            <h2 class="text-xl font-bold text-white mt-1">{{ $branch->branch_name }}</h2>
                        </div>

                        <form action="{{ route('admin.settings.identity-masking.update', $branch) }}" method="POST" class="mt-5 space-y-4">
                            @csrf
                            @method('PUT')

                            <div>
                                <label class="block text-xs text-gray-400 mb-1.5 font-medium">Mask Scope</label>
                                <select name="mask_scope"
                                    class="w-full bg-gray-900 border border-gray-700 rounded-lg px-3 py-2.5 text-sm text-white focus:outline-none focus:ring-1 focus:ring-orange-500 transition">
                                    <option value="none" @selected(old('mask_scope', $branch->mask_scope) === 'none')>None</option>
                                    <option value="public_only" @selected(old('mask_scope', $branch->mask_scope) === 'public_only')>Public only</option>
                                    <option value="everywhere" @selected(old('mask_scope', $branch->mask_scope) === 'everywhere')>Everywhere</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-xs text-gray-400 mb-1.5 font-medium">Display Name</label>
                                <input type="text" name="display_name" maxlength="150"
                                    value="{{ old('display_name', $branch->display_name) }}"
                                    placeholder="e.g. Guest Check"
                                    class="w-full bg-gray-900 border border-gray-700 rounded-lg px-3 py-2.5 text-sm text-white placeholder-gray-500 focus:outline-none focus:ring-1 focus:ring-orange-500 transition">
                            </div>

                            <div class="flex items-center justify-between gap-3 pt-1">
                                <p class="text-xs text-gray-500">Public only keeps the original name inside the system.</p>
                                <button type="submit"
                                    class="inline-flex items-center justify-center gap-2 bg-orange-500/10 hover:bg-orange-500/20 text-orange-500 border border-orange-500/30 px-4 py-2.5 rounded-lg text-sm font-medium transition">
                                    <i class="fas fa-save"></i>
                                    Save
                                </button>
                            </div>
                        </form>
                    </div>
                @empty
                    <div class="bg-gray-800 border border-gray-700 rounded-xl p-6 text-gray-400">No branches found.</div>
                @endforelse
            </div>
        @endif
    </div>
@endsection
