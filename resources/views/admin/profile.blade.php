{{-- @extends('core.layouts.admin')
@section('content')
    <div class="flex-1 overflow-y-auto p-6 bg-gray-900 space-y-6">
        <div class="bg-gray-800 border border-gray-700 rounded-xl p-5 md:p-6">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 rounded-xl bg-orange-500/20 flex items-center justify-center">
                        <i class="fas fa-user text-orange-500 text-xl"></i>
                    </div>
                    <div>
                        <p class="text-xs uppercase tracking-wide text-gray-400">My Profile</p>
                        <h1 class="text-2xl font-bold text-white">{{ $profile['name'] }}</h1>
                        <p class="text-sm text-gray-400">{{ $profile['email'] }}</p>
                    </div>
                </div>
                <button type="submit" form="adminProfileForm"
                    class="bg-orange-500/10 hover:bg-orange-500/20 text-orange-500 px-4 py-2 rounded-lg text-sm transition">
                    <i class="fas fa-save mr-2"></i>Save Changes
                </button>
            </div>
        </div>

        <div class="grid grid-cols-1 xl:grid-cols-3 gap-5">
            <div class="bg-gray-800 border border-gray-700 rounded-xl p-5 xl:col-span-2 space-y-5">
                <h2 class="text-lg font-semibold text-white">Personal Information</h2>

                <form id="adminProfileForm" method="POST" action="{{ route('admin.profile.update') }}">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs text-gray-400 mb-2">Full Name</label>
                            <input type="text" name="name" value="{{ old('name', $profile['name']) }}"
                                class="w-full bg-gray-900 border border-gray-700 rounded-lg px-3 py-2.5 text-sm text-white focus:outline-none focus:ring-1 focus:ring-orange-500">
                        </div>
                        <div>
                            <label class="block text-xs text-gray-400 mb-2">Email</label>
                            <input type="email" name="email" value="{{ old('email', $profile['email']) }}"
                                class="w-full bg-gray-900 border border-gray-700 rounded-lg px-3 py-2.5 text-sm text-white focus:outline-none focus:ring-1 focus:ring-orange-500">
                        </div>
                        <div>
                            <label class="block text-xs text-gray-400 mb-2">Phone</label>
                            <input type="text" name="phone_number"
                                value="{{ old('phone_number', $profile['phone_number']) }}"
                                class="w-full bg-gray-900 border border-gray-700 rounded-lg px-3 py-2.5 text-sm text-white focus:outline-none focus:ring-1 focus:ring-orange-500">
                        </div>
                        <div>
                            <label class="block text-xs text-gray-400 mb-2">Role</label>
                            <input type="text" value="{{ $profile['role_label'] }}" disabled
                                class="w-full bg-gray-900 border border-gray-700 rounded-lg px-3 py-2.5 text-sm text-gray-400 cursor-not-allowed">
                        </div>
                    </div>
                </form>
            </div>

            <div class="space-y-5">
                <div class="bg-gray-800 border border-gray-700 rounded-xl p-5">
                    <h3 class="text-base font-semibold text-white mb-4">Account Security</h3>
                    <form method="POST" action="{{ route('admin.profile.password.update') }}" class="space-y-3">
                        @csrf
                        @method('PUT')

                        <div>
                            <label class="block text-xs text-gray-400 mb-2">Current Password</label>
                            <input type="password" name="current_password"
                                class="w-full bg-gray-900 border border-gray-700 rounded-lg px-3 py-2.5 text-sm text-white focus:outline-none focus:ring-1 focus:ring-orange-500">
                        </div>

                        <div>
                            <label class="block text-xs text-gray-400 mb-2">New Password</label>
                            <input type="password" name="password"
                                class="w-full bg-gray-900 border border-gray-700 rounded-lg px-3 py-2.5 text-sm text-white focus:outline-none focus:ring-1 focus:ring-orange-500">
                        </div>

                        <div>
                            <label class="block text-xs text-gray-400 mb-2">Confirm New Password</label>
                            <input type="password" name="password_confirmation"
                                class="w-full bg-gray-900 border border-gray-700 rounded-lg px-3 py-2.5 text-sm text-white focus:outline-none focus:ring-1 focus:ring-orange-500">
                        </div>

                        <button type="submit"
                            class="w-full mt-1 bg-gray-700 hover:bg-gray-600 text-gray-200 py-2.5 rounded-lg text-sm transition">
                            <i class="fas fa-key mr-2"></i>Change Password
                        </button>
                    </form>
                </div>

                <div class="bg-gray-800 border border-gray-700 rounded-xl p-5">
                    <h3 class="text-base font-semibold text-white mb-4">Recent Activity</h3>
                    <div class="space-y-3">
                        @forelse ($recentActivities as $activity)
                            <div class="bg-gray-900 border border-gray-700 rounded-lg p-3">
                                <p class="text-sm text-white">{{ $activity['description'] }}</p>
                                <p class="text-xs text-gray-400 mt-1">{{ $activity['at_display'] }}</p>
                            </div>
                        @empty
                            <div class="bg-gray-900 border border-gray-700 rounded-lg p-3">
                                <p class="text-sm text-white">No recent profile activity found.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection --}}
