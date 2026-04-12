<div class="bg-gray-800 border border-gray-700 rounded-xl p-5 xl:col-span-2 space-y-5">
    <h2 class="text-lg font-semibold text-white">Personal Information</h2>

    <form id="profileForm" method="POST" action="{{ route('admin.profile.update') }}">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs text-gray-400 mb-2">Full Name</label>
                <input type="text" name="name" value="{{ old('name', $profile['name']) }}"
                    class="w-full bg-gray-900 border border-gray-700 rounded-lg px-3 py-2.5 text-sm text-white focus:ring-1 focus:ring-orange-500">
            </div>

            <div>
                <label class="block text-xs text-gray-400 mb-2">Email</label>
                <input type="email" name="email" value="{{ old('email', $profile['email']) }}"
                    class="w-full bg-gray-900 border border-gray-700 rounded-lg px-3 py-2.5 text-sm text-white focus:ring-1 focus:ring-orange-500">
            </div>

            <div>
                <label class="block text-xs text-gray-400 mb-2">Phone</label>
                <input type="text" name="phone_number" value="{{ old('phone_number', $profile['phone_number']) }}"
                    class="w-full bg-gray-900 border border-gray-700 rounded-lg px-3 py-2.5 text-sm text-white focus:ring-1 focus:ring-orange-500">
            </div>

            <div>
                <label class="block text-xs text-gray-400 mb-2">Role</label>
                <input type="text" value="{{ $profile['role_label'] }}" disabled
                    class="w-full bg-gray-900 border border-gray-700 rounded-lg px-3 py-2.5 text-sm text-gray-400">
            </div>
        </div>
    </form>
</div>
