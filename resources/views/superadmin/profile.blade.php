@extends('core.layouts.superadmin')
@section('content')
    <div class="relative z-0 flex-1 overflow-y-auto p-4 md:p-6 space-y-6">
        <div class="glass-panel p-5 md:p-6">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div class="flex items-center gap-4">
                    <div
                        class="w-16 h-16 rounded-2xl bg-gradient-to-br from-orange-500 to-orange-600 flex items-center justify-center">
                        <i class="fas fa-user-shield text-white text-2xl"></i>
                    </div>
                    <div>
                        <p class="text-xs uppercase tracking-wide text-slate-400">Super Admin Profile</p>
                        <h1 class="text-2xl font-bold text-white">{{ $profile['name'] }}</h1>
                        <p class="text-sm text-slate-400">{{ $profile['email'] }}</p>
                    </div>
                </div>
                <button type="submit" form="profileInfoForm"
                    class="bg-orange-500/10 hover:bg-orange-500/20 text-orange-500 px-4 py-2 rounded-lg text-sm transition">
                    <i class="fas fa-save mr-2"></i>Save Changes
                </button>
            </div>
        </div>

        <div class="grid grid-cols-1 xl:grid-cols-3 gap-5">
            <div class="glass-panel p-5 xl:col-span-2 space-y-5">
                <h2 class="text-lg font-semibold text-white">Personal Information</h2>
                <form id="profileInfoForm" method="POST" action="{{ route('superadmin.profile.update') }}">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs text-slate-400 mb-2">Full Name</label>
                            <input type="text" name="name" value="{{ old('name', $profile['name']) }}"
                                class="sa-form-input w-full bg-[#0f172a] border border-white/5 rounded-lg px-3 py-2.5 text-sm text-white focus:outline-none focus:ring-1 focus:ring-orange-500">
                        </div>
                        <div>
                            <label class="block text-xs text-slate-400 mb-2">Email</label>
                            <input type="email" name="email" value="{{ old('email', $profile['email']) }}"
                                class="sa-form-input w-full bg-[#0f172a] border border-white/5 rounded-lg px-3 py-2.5 text-sm text-white focus:outline-none focus:ring-1 focus:ring-orange-500">
                        </div>
                        <div>
                            <label class="block text-xs text-slate-400 mb-2">Phone</label>
                            <input type="text" name="phone_number"
                                value="{{ old('phone_number', $profile['phone_number']) }}"
                                class="sa-form-input w-full bg-[#0f172a] border border-white/5 rounded-lg px-3 py-2.5 text-sm text-white focus:outline-none focus:ring-1 focus:ring-orange-500">
                        </div>
                        <div>
                            <label class="block text-xs text-slate-400 mb-2">Role</label>
                            <input type="text" value="{{ $profile['role_label'] }}" disabled
                                class="sa-form-input w-full bg-[#0f172a] border border-white/5 rounded-lg px-3 py-2.5 text-sm text-slate-400 cursor-not-allowed">
                        </div>
                    </div>
                </form>
            </div>

            <div class="space-y-5">
                <div class="glass-panel p-5">
                    <h3 class="text-base font-semibold text-white mb-4">Account Security</h3>
                    <form method="POST" action="{{ route('superadmin.profile.password.update') }}" class="space-y-3">
                        @csrf
                        @method('PUT')

                        <div>
                            <label class="block text-xs text-slate-400 mb-2">Current Password</label>
                            <input type="password" name="current_password"
                                class="sa-form-input w-full bg-[#0f172a] border border-white/5 rounded-lg px-3 py-2.5 text-sm text-white focus:outline-none focus:ring-1 focus:ring-orange-500">
                        </div>

                        <div>
                            <label class="block text-xs text-slate-400 mb-2">New Password</label>
                            <input type="password" name="password"
                                class="sa-form-input w-full bg-[#0f172a] border border-white/5 rounded-lg px-3 py-2.5 text-sm text-white focus:outline-none focus:ring-1 focus:ring-orange-500">
                        </div>

                        <div>
                            <label class="block text-xs text-slate-400 mb-2">Confirm New Password</label>
                            <input type="password" name="password_confirmation"
                                class="sa-form-input w-full bg-[#0f172a] border border-white/5 rounded-lg px-3 py-2.5 text-sm text-white focus:outline-none focus:ring-1 focus:ring-orange-500">
                        </div>

                        <button type="submit"
                            class="w-full mt-1 bg-white/5 hover:bg-white/10 text-slate-300 py-2.5 rounded-lg text-sm transition">
                            <i class="fas fa-key mr-2"></i>Change Password
                        </button>
                    </form>
                </div>

                <div class="glass-panel p-5">
                    <h3 class="text-base font-semibold text-white mb-4">Recent Activity</h3>
                    <div class="space-y-3">
                        @forelse ($recentActivities as $activity)
                            <div class="bg-white/5 rounded-lg p-3">
                                <p class="text-sm text-white">{{ $activity['description'] }}</p>
                                <p class="text-xs text-slate-400 mt-1">{{ $activity['at_display'] }}</p>
                            </div>
                        @empty
                            <div class="bg-white/5 rounded-lg p-3">
                                <p class="text-sm text-white">No recent profile activity found.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
    </main>
    </div>
@endsection
