 <!-- Key Metrics Cards -->
 <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
     <!-- ... same cards ... (I'll keep them concise for space) -->
     <div class="bg-gray-800 rounded-xl p-6 border border-gray-700 card-hover">
         <div class="flex items-center justify-between">
             <div>
                 <p class="text-sm text-gray-400">Total Revenue (All Branches)</p>
                 <h3 class="text-2xl font-bold text-white mt-1">₹2,84,500</h3>
                 <p class="text-xs text-green-400 mt-2"><i class="fas fa-arrow-up mr-1"></i> +12.3% vs
                     last week</p>
             </div>
             <div class="w-12 h-12 rounded-full bg-orange-500/20 flex items-center justify-center"><i
                     class="fas fa-rupee-sign text-orange-500 text-xl"></i></div>
         </div>
     </div>
     <div class="bg-gray-800 rounded-xl p-6 border border-gray-700 card-hover">
         <div class="flex items-center justify-between">
             <div>
                 <p class="text-sm text-gray-400">Total Orders</p>
                 <h3 class="text-2xl font-bold text-white mt-1">1,256</h3>
                 <p class="text-xs text-green-400 mt-2"><i class="fas fa-arrow-up mr-1"></i> +8.2% vs
                     last week</p>
             </div>
             <div class="w-12 h-12 rounded-full bg-blue-500/20 flex items-center justify-center"><i
                     class="fas fa-shopping-cart text-blue-500 text-xl"></i></div>
         </div>
     </div>
     <div class="bg-gray-800 rounded-xl p-6 border border-gray-700 card-hover">
         <div class="flex items-center justify-between">
             <div>
                 <p class="text-sm text-gray-400">Avg. Order Value</p>
                 <h3 class="text-2xl font-bold text-white mt-1">₹226</h3>
                 <p class="text-xs text-yellow-400 mt-2"><i class="fas fa-minus mr-1"></i> 2% from last
                     week</p>
             </div>
             <div class="w-12 h-12 rounded-full bg-purple-500/20 flex items-center justify-center"><i
                     class="fas fa-chart-pie text-purple-500 text-xl"></i></div>
         </div>
     </div>
     <div class="bg-gray-800 rounded-xl p-6 border border-gray-700 card-hover">
         <div class="flex items-center justify-between">
             <div>
                 <p class="text-sm text-gray-400">Active Branches</p>
                 <h3 class="text-2xl font-bold text-white mt-1">12 / 12</h3>
                 <p class="text-xs text-green-400 mt-2">All operational</p>
             </div>
             <div class="w-12 h-12 rounded-full bg-green-500/20 flex items-center justify-center"><i
                     class="fas fa-store text-green-500 text-xl"></i></div>
         </div>
     </div>
 </div>

 <!-- Map & Branch Performance -->
 <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
     <div class="bg-gray-800 rounded-xl p-6 border border-gray-700 lg:col-span-2">
         <div class="flex items-center justify-between mb-4">
             <h3 class="text-lg font-semibold text-white">Branch Locations & Performance</h3><button
                 class="text-sm text-orange-500 hover:text-orange-400">View All Branches →</button>
         </div>
         <div class="relative bg-gray-700 rounded-lg h-64 overflow-hidden">
             <div class="absolute inset-0 bg-gradient-to-br from-gray-600 to-gray-800 opacity-50"></div>
             <!-- pins -->
             <div class="absolute top-1/4 left-1/4">
                 <div class="relative group">
                     <div
                         class="w-6 h-6 rounded-full bg-green-500 border-2 border-gray-900 flex items-center justify-center">
                         <i class="fas fa-store text-white text-xs"></i>
                     </div>
                     <div
                         class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 hidden group-hover:block bg-gray-900 text-white text-xs rounded py-1 px-2 whitespace-nowrap">
                         Downtown: ₹1,24,500</div>
                 </div>
             </div>
             <div class="absolute top-1/3 right-1/3">
                 <div class="relative group">
                     <div
                         class="w-6 h-6 rounded-full bg-green-500 border-2 border-gray-900 flex items-center justify-center">
                         <i class="fas fa-store text-white text-xs"></i>
                     </div>
                     <div
                         class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 hidden group-hover:block bg-gray-900 text-white text-xs rounded py-1 px-2 whitespace-nowrap">
                         Westside: ₹98,200</div>
                 </div>
             </div>
             <div class="absolute bottom-1/3 left-1/3">
                 <div class="relative group">
                     <div
                         class="w-6 h-6 rounded-full bg-yellow-500 border-2 border-gray-900 flex items-center justify-center">
                         <i class="fas fa-store text-white text-xs"></i>
                     </div>
                     <div
                         class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 hidden group-hover:block bg-gray-900 text-white text-xs rounded py-1 px-2 whitespace-nowrap">
                         East End: ₹45,300</div>
                 </div>
             </div>
             <div class="absolute bottom-1/4 right-1/4">
                 <div class="relative group">
                     <div
                         class="w-6 h-6 rounded-full bg-green-500 border-2 border-gray-900 flex items-center justify-center">
                         <i class="fas fa-store text-white text-xs"></i>
                     </div>
                     <div
                         class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 hidden group-hover:block bg-gray-900 text-white text-xs rounded py-1 px-2 whitespace-nowrap">
                         Uptown: ₹1,02,800</div>
                 </div>
             </div>
             <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2">
                 <div
                     class="w-10 h-10 rounded-full bg-orange-500 border-4 border-gray-900 flex items-center justify-center">
                     <i class="fas fa-user-tie text-white"></i>
                 </div>
             </div>
         </div>
     </div>
     <div class="bg-gray-800 rounded-xl p-6 border border-gray-700">
         <h3 class="text-lg font-semibold text-white mb-4">Top Performing Branches</h3>
         <div class="space-y-4">
             <div class="flex items-center justify-between">
                 <div>
                     <p class="text-sm font-medium text-white">Downtown</p>
                     <p class="text-xs text-gray-400">Revenue: ₹1,24,500</p>
                 </div><span class="text-xs bg-green-900/50 text-green-400 px-2 py-1 rounded-full">+14%</span>
             </div>
             <div class="flex items-center justify-between">
                 <div>
                     <p class="text-sm font-medium text-white">Uptown</p>
                     <p class="text-xs text-gray-400">Revenue: ₹1,02,800</p>
                 </div><span class="text-xs bg-green-900/50 text-green-400 px-2 py-1 rounded-full">+8%</span>
             </div>
             <div class="flex items-center justify-between">
                 <div>
                     <p class="text-sm font-medium text-white">Westside</p>
                     <p class="text-xs text-gray-400">Revenue: ₹98,200</p>
                 </div><span class="text-xs bg-green-900/50 text-green-400 px-2 py-1 rounded-full">+5%</span>
             </div>
             <div class="flex items-center justify-between">
                 <div>
                     <p class="text-sm font-medium text-white">Northside</p>
                     <p class="text-xs text-gray-400">Revenue: ₹76,400</p>
                 </div><span class="text-xs bg-yellow-900/50 text-yellow-400 px-2 py-1 rounded-full">+2%</span>
             </div>
             <div class="flex items-center justify-between">
                 <div>
                     <p class="text-sm font-medium text-white">East End</p>
                     <p class="text-xs text-gray-400">Revenue: ₹45,300</p>
                 </div><span class="text-xs bg-red-900/50 text-red-400 px-2 py-1 rounded-full">-2%</span>
             </div>
         </div>
     </div>
 </div>

 <!-- Inventory & Staff -->
 <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
     <div class="bg-gray-800 rounded-xl p-6 border border-gray-700">
         <h3 class="text-lg font-semibold text-white mb-4">Low Stock Across Branches</h3>
         <div class="space-y-3">
             <div class="flex items-center justify-between">
                 <div>
                     <p class="text-sm font-medium text-white">Tomatoes</p>
                     <p class="text-xs text-gray-400">Affected: Downtown, Westside, Uptown</p>
                 </div><span class="text-xs bg-orange-900/50 text-orange-400 px-2 py-1 rounded-full">Reorder
                     soon</span>
             </div>
             <div class="flex items-center justify-between">
                 <div>
                     <p class="text-sm font-medium text-white">Chicken Breast</p>
                     <p class="text-xs text-gray-400">Affected: East End, Northside</p>
                 </div><span class="text-xs bg-red-900/50 text-red-400 px-2 py-1 rounded-full">Critical</span>
             </div>
             <div class="flex items-center justify-between">
                 <div>
                     <p class="text-sm font-medium text-white">Cooking Oil</p>
                     <p class="text-xs text-gray-400">Affected: All except Downtown</p>
                 </div><span class="text-xs bg-yellow-900/50 text-yellow-400 px-2 py-1 rounded-full">Low</span>
             </div>
             <div class="flex items-center justify-between">
                 <div>
                     <p class="text-sm font-medium text-white">Flour</p>
                     <p class="text-xs text-gray-400">Affected: Westside, Uptown</p>
                 </div><span class="text-xs bg-orange-900/50 text-orange-400 px-2 py-1 rounded-full">Reorder
                     soon</span>
             </div>
         </div>
         <button class="w-full mt-4 text-sm text-orange-500 hover:text-orange-400">Manage Global
             Inventory →</button>
     </div>
     <div class="bg-gray-800 rounded-xl p-6 border border-gray-700">
         <h3 class="text-lg font-semibold text-white mb-4">Staff Overview</h3>
         <div class="grid grid-cols-2 gap-4 mb-4">
             <div class="bg-gray-700/50 rounded-lg p-3 text-center">
                 <p class="text-2xl font-bold text-white">86</p>
                 <p class="text-xs text-gray-400">Total Staff</p>
             </div>
             <div class="bg-gray-700/50 rounded-lg p-3 text-center">
                 <p class="text-2xl font-bold text-white">12</p>
                 <p class="text-xs text-gray-400">On Leave</p>
             </div>
         </div>
         <div class="space-y-2">
             <div class="flex justify-between items-center"><span class="text-sm text-gray-400">Downtown</span><span
                     class="text-sm text-white">14/16</span><span class="text-xs text-green-400">+2</span>
             </div>
             <div class="flex justify-between items-center"><span class="text-sm text-gray-400">Westside</span><span
                     class="text-sm text-white">12/12</span><span class="text-xs text-green-400">Full</span>
             </div>
             <div class="flex justify-between items-center"><span class="text-sm text-gray-400">East
                     End</span><span class="text-sm text-white">8/10</span><span
                     class="text-xs text-red-400">-2</span></div>
             <div class="flex justify-between items-center"><span class="text-sm text-gray-400">Uptown</span><span
                     class="text-sm text-white">10/12</span><span class="text-xs text-yellow-400">-2</span>
             </div>
         </div>
         <button class="w-full mt-4 text-sm text-orange-500 hover:text-orange-400">Manage Staff
             →</button>
     </div>
 </div>

 <!-- Supplier Comparison & Quick Actions -->
 <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
     <div class="bg-gray-800 rounded-xl p-6 border border-gray-700 lg:col-span-2">
         <h3 class="text-lg font-semibold text-white mb-4">Supplier Price Comparison (Across Branches)
         </h3>
         <div class="overflow-x-auto">
             <table class="w-full text-sm text-left supplier-comparison-table">
                 <thead class="text-xs text-gray-400 uppercase border-b border-gray-700">
                     <tr>
                         <th class="px-4 py-3">Ingredient</th>
                         <th class="px-4 py-3">Metro Foods</th>
                         <th class="px-4 py-3">Sysco</th>
                         <th class="px-4 py-3">Local Co-op</th>
                         <th class="px-4 py-3">Best Price</th>
                     </tr>
                 </thead>
                 <tbody>
                     <tr class="border-b border-gray-700">
                         <td class="px-4 py-3 font-medium text-white">Onion (per kg)</td>
                         <td class="px-4 py-3">₹45</td>
                         <td class="px-4 py-3">₹52</td>
                         <td class="px-4 py-3 text-green-400 font-medium">₹42</td>
                         <td class="px-4 py-3"><span
                                 class="bg-green-900/50 text-green-400 px-2 py-1 rounded-full text-xs">Local
                                 Co-op</span></td>
                     </tr>
                     <tr class="border-b border-gray-700">
                         <td class="px-4 py-3 font-medium text-white">Tomato (per kg)</td>
                         <td class="px-4 py-3">₹38</td>
                         <td class="px-4 py-3 text-green-400 font-medium">₹35</td>
                         <td class="px-4 py-3">₹40</td>
                         <td class="px-4 py-3"><span
                                 class="bg-green-900/50 text-green-400 px-2 py-1 rounded-full text-xs">Sysco</span>
                         </td>
                     </tr>
                     <tr class="border-b border-gray-700">
                         <td class="px-4 py-3 font-medium text-white">Chicken (per kg)</td>
                         <td class="px-4 py-3 text-green-400 font-medium">₹210</td>
                         <td class="px-4 py-3">₹225</td>
                         <td class="px-4 py-3">₹215</td>
                         <td class="px-4 py-3"><span
                                 class="bg-green-900/50 text-green-400 px-2 py-1 rounded-full text-xs">Metro
                                 Foods</span></td>
                     </tr>
                     <tr>
                         <td class="px-4 py-3 font-medium text-white">Flour (per kg)</td>
                         <td class="px-4 py-3">₹32</td>
                         <td class="px-4 py-3">₹30</td>
                         <td class="px-4 py-3 text-green-400 font-medium">₹28</td>
                         <td class="px-4 py-3"><span
                                 class="bg-green-900/50 text-green-400 px-2 py-1 rounded-full text-xs">Local
                                 Co-op</span></td>
                     </tr>
                 </tbody>
             </table>
         </div>
         <button class="w-full mt-4 text-sm text-orange-500 hover:text-orange-400">View Full Comparison
             →</button>
     </div>
     <div class="bg-gray-800 rounded-xl p-6 border border-gray-700">
         <h3 class="text-lg font-semibold text-white mb-4">Quick Actions</h3>
         <div class="space-y-3">
             <button
                 class="w-full bg-gray-700 hover:bg-gray-600 text-white text-sm font-medium py-3 px-4 rounded-lg flex items-center justify-between"><span><i
                         class="fas fa-plus-circle mr-2 text-orange-500"></i> Add New Branch</span><i
                     class="fas fa-chevron-right text-gray-400"></i></button>
             <button
                 class="w-full bg-gray-700 hover:bg-gray-600 text-white text-sm font-medium py-3 px-4 rounded-lg flex items-center justify-between"><span><i
                         class="fas fa-sync-alt mr-2 text-orange-500"></i> Sync Menu to All
                     Branches</span><i class="fas fa-chevron-right text-gray-400"></i></button>
             <button
                 class="w-full bg-gray-700 hover:bg-gray-600 text-white text-sm font-medium py-3 px-4 rounded-lg flex items-center justify-between"><span><i
                         class="fas fa-truck mr-2 text-orange-500"></i> Bulk Order Supplies</span><i
                     class="fas fa-chevron-right text-gray-400"></i></button>
             <button
                 class="w-full bg-gray-700 hover:bg-gray-600 text-white text-sm font-medium py-3 px-4 rounded-lg flex items-center justify-between"><span><i
                         class="fas fa-file-pdf mr-2 text-orange-500"></i> Generate Consolidated
                     Report</span><i class="fas fa-chevron-right text-gray-400"></i></button>
         </div>
     </div>
 </div>
