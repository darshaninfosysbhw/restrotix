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
                <p class="text-sm text-white">No activity found</p>
            </div>
        @endforelse
    </div>
</div>
