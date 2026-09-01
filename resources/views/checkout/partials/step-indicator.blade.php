<div class="flex items-center justify-between mb-6 px-1">
    <template x-for="i in [1,2,3]">
        <div class="flex items-center flex-1">
            <div :class="step >= i ? 'bg-orange-600 text-white shadow-lg shadow-orange-200' : 'bg-gray-100 text-gray-400'"
                class="w-8 h-8 rounded-full flex items-center justify-center text-sm font-bold transition-all duration-300"
                x-text="i"></div>
            <div x-show="i < 3" class="flex-1 h-[2px] mx-4" :class="step > i ? 'bg-orange-600' : 'bg-gray-100'"></div>
        </div>
    </template>
</div>
