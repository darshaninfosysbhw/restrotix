<div class="w-full md:w-[400px] lg:w-[450px] space-y-6">
    <div class="p-10 text-white relative overflow-hidden">
        <div class="absolute -top-10 -left-20 w-48 h-48 bg-orange-600/20 blur-[80px] rounded-full"></div>
        <div class="absolute -bottom-10 -right-20 w-48 h-48 bg-orange-600/20 blur-[80px] rounded-full"></div>

        <div class="mb-10">
            <span class="text-[10px] font-bold uppercase tracking-[0.3em] text-orange-500 mb-1 block">Premium Plan</span>
            <h3 class="text-2xl font-extrabold text-black" x-text="plan"></h3>
            <p class="text-gray-400 mt-1 text-sm">{{ $planDetails->marketing_summary }}</p>
        </div>

        <div class="space-y-4 mb-8">
            @php
                $features = $planDetails->getDisplayFeatures();
            @endphp

            @if ($features)
                @foreach ($features as $feature)
                    @php
                        if (is_array($feature)) {
                            $featureName = $feature['name'] ?? '';
                            $isAvailable = $feature['available'] ?? true;
                            $isBold = $feature['bold'] ?? false;
                        } else {
                            $featureName = $feature;
                            $isAvailable = true;
                            $isBold = false;
                        }
                    @endphp

                    <div class="flex items-center gap-4 group">
                        <div
                            class="w-8 h-8 rounded-full bg-orange-600/10 flex items-center justify-center {{ $isAvailable ? 'group-hover:bg-orange-600' : 'opacity-50' }} transition-colors">
                            <i
                                class="fas {{ $isAvailable ? 'fa-check text-orange-500 group-hover:text-white' : 'fa-times text-gray-400' }} text-[10px]"></i>
                        </div>
                        <span class="text-sm font-medium text-gray-500 {{ !$isAvailable ? 'opacity-50' : '' }}">
                            {!! $isBold ? '<strong>' . e($featureName) . '</strong>' : e($featureName) !!}
                        </span>
                    </div>
                @endforeach
            @endif
        </div>

        <div class="pt-8 border-t border-white/5 flex items-end justify-between">
            <div>
                <span class="text-5xl font-black text-black" x-text="symbol + ' ' + price"></span>
                <span class="text-gray-500 text-sm ml-1" x-text="billingCycle === 'yearly' ? '/ year' : '/ month'"></span>
            </div>
            <div class="text-right">
                <p class="text-[10px] font-bold text-green-500 uppercase tracking-widest mb-1">Trial Period</p>
                <p class="text-sm font-bold text-black">{{ $planDetails->trial_days }} Days Free</p>
            </div>
        </div>
    </div>

    <div class="flex justify-center gap-8 px-4 opacity-70">
        <div class="flex items-center gap-2 text-xs font-bold text-gray-700">
            <i class="fas fa-shield-check text-orange-600"></i> SSL SECURE
        </div>
        <div class="flex items-center gap-2 text-xs font-bold text-gray-700">
            <i class="fas fa-clock text-orange-600"></i> CANCEL ANYTIME
        </div>
    </div>
</div>
