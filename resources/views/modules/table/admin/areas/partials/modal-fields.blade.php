<div class="space-y-4">

    {{-- Area / Floor Name --}}
    <div>
        <label
            for="areaName"
            class="block text-xs text-gray-400 mb-1.5 font-medium"
        >
            Area / Floor Name
            <span class="text-orange-500">*</span>
        </label>

        <input
            id="areaName"
            type="text"
            name="name"
            value="{{ old('name') }}"
            required
            maxlength="100"
            autocomplete="off"
            placeholder="e.g. Ground Floor, Rooftop, Garden"
            class="w-full bg-gray-900 border border-gray-700 rounded-lg px-3 py-2.5
                   text-sm text-white placeholder-gray-500
                   focus:outline-none focus:ring-1 focus:ring-orange-500
                   transition"
        >

       
    </div>

    <div>
    <label class="block text-sm text-gray-400 mb-1.5 font-medium">
        Area Code <span class="text-orange-500">*</span>
    </label>

    <input
        type="text"
        id="areaCode"
        name="code"
        maxlength="20"
        placeholder="ex. AC, GF, RF"
        class="w-full bg-gray-900 border border-gray-700 rounded-lg px-3 py-2.5
               text-sm text-white uppercase focus:outline-none focus:ring-1 focus:ring-orange-500">
    </div>


    {{-- Active Status --}}
    <div
        class="flex items-center justify-between p-3 rounded-lg border border-gray-700 bg-transparent">

        <div class="flex items-center gap-3">

            <div
                class="w-8 h-8 rounded bg-emerald-500/10
                       text-emerald-400 flex items-center justify-center text-xs"
            >
                <i class="fas fa-check-circle"></i>
            </div>

            <div>
                <p class="text-xs font-medium text-white">
                    Active Area
                </p>

                <p class="text-[10px] text-gray-500">
                    Allow tables to be assigned to this area
                </p>
            </div>

        </div>

        <label class="relative inline-flex items-center cursor-pointer">

            <input
                id="areaIsActive"
                type="checkbox"
                name="is_active"
                value="1"
                class="sr-only peer"
                {{ old('is_active', true) ? 'checked' : '' }}
            >

            <div
                class="w-9 h-5 bg-gray-700 rounded-full peer
                       peer-focus:outline-none
                       peer-checked:after:translate-x-full
                       peer-checked:after:border-white
                       after:content-['']
                       after:absolute
                       after:top-[2px]
                       after:left-[2px]
                       after:bg-white
                       after:border-gray-300
                       after:border
                       after:rounded-full
                       after:h-4
                       after:w-4
                       after:transition-all
                       peer-checked:!bg-orange-500">
            </div>

        </label>

    </div>

</div>