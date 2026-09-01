{{--
@extends('core.layouts.chef')
@section('content')
    <div class="space-y-6 p-1">

        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-white">Kitchen Command Center</h1>
                <p class="text-sm text-gray-400">Real-time overview for <span class="text-orange-500 font-semibold">Main
                        Kitchen</span></p>
            </div>
            <div class="flex items-center gap-3">
                <span class="px-3 py-1 bg-green-900/30 text-green-400 rounded-full text-xs font-bold animate-pulse">●
                    Kitchen Live</span>
                <button class="p-2 bg-gray-800 border border-gray-700 rounded-lg">
                    <i class="fas fa-sync-alt text-gray-400"></i>
                </button>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="bg-gray-800 p-5 rounded-2xl border border-gray-700 card-hover">
                <div class="flex justify-between">
                    <p class="text-gray-400 text-sm font-medium">Active Orders</p>
                    <div class="p-2 bg-orange-900/20 rounded-lg text-orange-500"><i class="fas fa-fire-alt"></i></div>
                </div>
                <h2 class="text-3xl font-bold text-white mt-2">12</h2>
                <p class="text-xs text-red-500 mt-2 font-semibold">4 Needs Attention</p>
            </div>

            <div class="bg-gray-800 p-5 rounded-2xl border border-gray-700 card-hover">
                <div class="flex justify-between">
                    <p class="text-gray-400 text-sm font-medium">Avg. Prep Time</p>
                    <div class="p-2 bg-blue-900/20 rounded-lg text-blue-500"><i class="fas fa-clock"></i>
                    </div>
                </div>
                <h2 class="text-3xl font-bold text-white mt-2">14m</h2>
                <p class="text-xs text-green-500 mt-2 font-semibold">↓ 2m vs yesterday</p>
            </div>

            <div class="bg-gray-800 p-5 rounded-2xl border border-gray-700 card-hover">
                <div class="flex justify-between">
                    <p class="text-gray-400 text-sm font-medium">Stock Alerts</p>
                    <div class="p-2 bg-red-900/20 rounded-lg text-red-500"><i class="fas fa-exclamation-triangle"></i></div>
                </div>
                <h2 class="text-3xl font-bold text-white mt-2">03</h2>
                <p class="text-xs text-gray-400 mt-2 font-semibold italic">Critical items low</p>
            </div>

            <div class="bg-gray-800 p-5 rounded-2xl border border-gray-700 card-hover">
                <div class="flex justify-between">
                    <p class="text-gray-400 text-sm font-medium">Ready to Serve</p>
                    <div class="p-2 bg-green-900/20 rounded-lg text-green-500"><i class="fas fa-check-circle"></i></div>
                </div>
                <h2 class="text-3xl font-bold text-white mt-2">86</h2>
                <p class="text-xs text-gray-400 mt-2 font-semibold">Total dishes today</p>
            </div>
        </div>

        <div class="bg-gray-800 rounded-2xl border border-gray-700 overflow-hidden">
            <div class="p-5 border-b border-gray-700 flex justify-between items-center">
                <h3 class="font-bold text-white text-lg">Live Kitchen Queue (KDS)</h3>
                <div class="flex gap-2">
                    <button class="text-xs font-bold text-orange-500 hover:underline">Mark All Ready</button>
                </div>
            </div>
            <div class="p-5 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <div class="border-2 border-orange-500/40 rounded-xl p-4 bg-orange-500/5">
                    <div class="flex justify-between items-center mb-3">
                        <span class="px-2 py-1 bg-orange-500 text-white text-[10px] font-bold rounded">URGENT</span>
                        <span class="text-gray-400 text-xs font-mono">Timer: 18:45m</span>
                    </div>
                    <h4 class="font-bold text-white">Order #2042</h4>
                    <ul class="mt-3 space-y-2 text-sm text-gray-300">
                        <li class="flex justify-between"><span>2x Paneer Tikka</span> <i
                                class="fas fa-check text-gray-300"></i></li>
                        <li class="flex justify-between font-bold text-red-500"><span>1x Butter Chicken</span> <span>Extra
                                Spicy!!</span></li>
                        <li class="flex justify-between"><span>3x Garlic Naan</span> <i
                                class="fas fa-check text-gray-300"></i></li>
                    </ul>
                    <button
                        class="w-full mt-4 py-2 bg-orange-500 hover:bg-orange-600 text-white text-xs font-bold rounded-lg transition-all">READY
                        TO SERVE</button>
                </div>

                <div class="border border-gray-700 rounded-xl p-4 bg-gray-700/30">
                    <div class="flex justify-between items-center mb-3">
                        <span class="px-2 py-1 bg-green-500 text-white text-[10px] font-bold rounded">NORMAL</span>
                        <span class="text-green-500 text-xs font-mono">Timer: 04:20m</span>
                    </div>
                    <h4 class="font-bold text-white">Order #2045</h4>
                    <ul class="mt-3 space-y-2 text-sm text-gray-300">
                        <li>1x Veg Burger</li>
                        <li>1x Large Fries</li>
                    </ul>
                    <button
                        class="w-full mt-4 py-2 border border-green-500 text-green-500 hover:bg-green-500 hover:text-white text-xs font-bold rounded-lg transition-all">MARK
                        READY</button>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="bg-gray-800 rounded-2xl border border-gray-700 p-6">
                <h3 class="font-bold text-white mb-4">Urgent Stock Requirement</h3>
                <div class="space-y-4">
                    <div class="flex items-center justify-between p-3 bg-gray-700/50 rounded-xl">
                        <div>
                            <p class="text-sm font-bold text-gray-200">Cooking Oil (Refined)</p>
                            <p class="text-[10px] text-red-500 uppercase font-black">Stock Critical: 2L Left</p>
                        </div>
                        <button
                            class="px-4 py-1.5 bg-orange-500 text-white text-xs font-bold rounded-lg shadow-md shadow-orange-500/20">RAISE
                            INDENT</button>
                    </div>
                    <div class="flex items-center justify-between p-3 bg-gray-700/50 rounded-xl">
                        <div>
                            <p class="text-sm font-bold text-gray-200">Chicken Breast</p>
                            <p class="text-[10px] text-orange-500 uppercase font-black">Reorder Soon: 5kg Left</p>
                        </div>
                        <button
                            class="px-4 py-1.5 border border-orange-500 text-orange-500 text-xs font-bold rounded-lg">RAISE
                            INDENT</button>
                    </div>
                </div>
            </div>

            <div class="bg-gray-800 rounded-2xl border border-gray-700 p-6">
                <h3 class="font-bold text-white mb-4">Quick Waste Log</h3>
                <div class="grid grid-cols-2 gap-3 mb-3">
                    <input type="text" placeholder="Item Name"
                        class="col-span-2 bg-gray-700 border border-gray-600 rounded-xl p-3 text-sm text-white placeholder-gray-400 focus:ring-2 focus:ring-orange-500 focus:border-orange-500">
                    <input type="number" placeholder="Qty"
                        class="bg-gray-700 border border-gray-600 rounded-xl p-3 text-sm text-white placeholder-gray-400 focus:ring-2 focus:ring-orange-500 focus:border-orange-500">
                    <select
                        class="bg-gray-700 border border-gray-600 rounded-xl p-3 text-sm text-gray-300 focus:ring-2 focus:ring-orange-500 focus:border-orange-500">
                        <option>Spoilage</option>
                        <option>Burnt</option>
                        <option>Expired</option>
                    </select>
                </div>
                <button
                    class="w-full py-3 bg-gray-700 text-white font-bold rounded-xl hover:bg-gray-600 transition-all">RECORD
                    WASTE</button>
            </div>
        </div>
    </div>
@endsection --}}
