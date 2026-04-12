<div class="bg-gray-800 border border-gray-700 rounded-xl p-5">
    <h3 class="text-base font-semibold text-white mb-4">Account Security</h3>

    <form method="POST" action="{{ route('admin.profile.password.update') }}" class="space-y-3">
        @csrf
        @method('PUT')

        <div>
            <label class="block text-xs text-gray-400 mb-2">Current Password</label>
            <input type="password" name="current_password"
                class="w-full bg-gray-900 border border-gray-700 rounded-lg px-3 py-2.5 text-sm text-white">
        </div>

        <div>
            <label class="block text-xs text-gray-400 mb-2">New Password</label>
            <input type="password" name="password"
                class="w-full bg-gray-900 border border-gray-700 rounded-lg px-3 py-2.5 text-sm text-white">
        </div>

        <div>
            <label class="block text-xs text-gray-400 mb-2">Confirm Password</label>
            <input type="password" name="password_confirmation"
                class="w-full bg-gray-900 border border-gray-700 rounded-lg px-3 py-2.5 text-sm text-white">
        </div>

        <button type="submit" class="w-full bg-gray-700 hover:bg-gray-600 text-white py-2.5 rounded-lg text-sm">
            Change Password
        </button>
    </form>
</div>
