  <!-- Price Comparison Dashboard -->
  <section id="comparison" class="py-16 sm:py-20 bg-white">
      <div class="container mx-auto px-4 sm:px-6">
          <div class="text-center mb-12 sm:mb-16">
              <h2 class="text-2xl sm:text-3xl md:text-4xl font-bold text-gray-900 mb-3 sm:mb-4">Smart Price
                  Comparison Dashboard</h2>
              <p class="text-base sm:text-lg text-gray-600 max-w-2xl mx-auto">Automatically compare suppliers and
                  save up to 25% on ingredients across all your branches.</p>
          </div>

          <div
              class="max-w-6xl mx-auto bg-white rounded-xl sm:rounded-2xl shadow-xl overflow-hidden border border-gray-200">
              <div class="comparison-container flex flex-col lg:flex-row">
                  <!-- Left Panel - Ingredients -->
                  <div class="ingredients-panel lg:w-1/3 border-r border-gray-200">
                      <div class="p-4 sm:p-6 bg-gray-50 border-b border-gray-200">
                          <h3 class="text-lg sm:text-xl font-bold text-gray-900">Ingredient Dashboard</h3>
                          <div class="mt-3 sm:mt-4 relative">
                              <input type="text" placeholder="Search ingredients..."
                                  class="w-full p-2.5 sm:p-3 pl-9 sm:pl-10 border border-gray-300 rounded-lg text-sm sm:text-base">
                              <i class="fas fa-search absolute left-3 top-2.5 sm:top-3.5 text-gray-400"></i>
                          </div>
                      </div>

                      <div class="p-4 sm:p-6">
                          <div class="mb-4 sm:mb-6 bg-white p-3 sm:p-4 rounded-lg border border-gray-200 shadow-sm">
                              <p class="text-xs sm:text-sm text-gray-600">Monthly Ingredient Spend</p>
                              <p class="text-xl sm:text-2xl font-bold text-gray-900">$12,450</p>
                              <div class="flex items-center mt-1 sm:mt-2">
                                  <span class="text-green-600 text-xs sm:text-sm font-medium">Potential Savings:
                                      18%</span>
                                  <span
                                      class="ml-2 bg-green-100 text-green-800 text-xs font-medium px-1.5 sm:px-2 py-0.5 rounded">$2,241</span>
                              </div>
                          </div>

                          <h4 class="font-medium text-gray-700 mb-3 sm:mb-4 text-sm sm:text-base">Frequently
                              Purchased</h4>
                          <div class="space-y-2 sm:space-y-3">
                              <div class="p-3 rounded-lg border-l-4 border-orange-500 bg-orange-50 cursor-pointer">
                                  <div class="flex justify-between items-center">
                                      <span class="font-medium text-sm sm:text-base">Onion</span>
                                      <span class="text-xs sm:text-sm text-gray-600">$2.49/kg</span>
                                  </div>
                                  <div class="flex items-center mt-1">
                                      <span class="text-xs text-gray-500">Last ordered: 3 days ago</span>
                                      <span
                                          class="ml-2 text-xs bg-blue-100 text-blue-800 px-1.5 sm:px-2 py-0.5 rounded">3
                                          suppliers</span>
                                  </div>
                              </div>

                              <div class="p-3 rounded-lg border border-gray-200 hover:bg-gray-50 cursor-pointer">
                                  <div class="flex justify-between items-center">
                                      <span class="font-medium text-sm sm:text-base">Tomato</span>
                                      <span class="text-xs sm:text-sm text-gray-600">$3.29/kg</span>
                                  </div>
                                  <div class="flex items-center mt-1">
                                      <span class="text-xs text-gray-500">Last ordered: 1 day ago</span>
                                  </div>
                              </div>

                              <div class="p-3 rounded-lg border border-gray-200 hover:bg-gray-50 cursor-pointer">
                                  <div class="flex justify-between items-center">
                                      <span class="font-medium text-sm sm:text-base">Flour</span>
                                      <span class="text-xs sm:text-sm text-gray-600">$1.89/kg</span>
                                  </div>
                                  <div class="flex items-center mt-1">
                                      <span class="text-xs text-gray-500">Last ordered: 5 days ago</span>
                                  </div>
                              </div>

                              <div class="p-3 rounded-lg border border-gray-200 hover:bg-gray-50 cursor-pointer">
                                  <div class="flex justify-between items-center">
                                      <span class="font-medium text-sm sm:text-base">Chicken Breast</span>
                                      <span class="text-xs sm:text-sm text-gray-600">$8.99/kg</span>
                                  </div>
                                  <div class="flex items-center mt-1">
                                      <span class="text-xs text-gray-500">Last ordered: Today</span>
                                  </div>
                              </div>

                              <div class="p-3 rounded-lg border border-gray-200 hover:bg-gray-50 cursor-pointer">
                                  <div class="flex justify-between items-center">
                                      <span class="font-medium text-sm sm:text-base">Cooking Oil</span>
                                      <span class="text-xs sm:text-sm text-gray-600">$4.49/L</span>
                                  </div>
                                  <div class="flex items-center mt-1">
                                      <span class="text-xs text-gray-500">Last ordered: 2 days ago</span>
                                  </div>
                              </div>
                          </div>
                      </div>
                  </div>

                  <!-- Right Panel - Supplier Comparison -->
                  <div class="suppliers-panel lg:w-2/3">
                      <div class="p-4 sm:p-6 border-b border-gray-200">
                          <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center">
                              <div class="mb-3 sm:mb-0">
                                  <h3 class="text-lg sm:text-xl font-bold text-gray-900">Supplier Comparison: <span
                                          class="text-orange-500">Onion</span> (per kg)</h3>
                                  <p class="text-gray-600 text-sm sm:text-base">Prices updated 2 hours ago</p>
                              </div>
                              <div class="flex items-center">
                                  <span class="text-xs sm:text-sm text-gray-600 mr-2 sm:mr-3">Auto-order when price
                                      drops 10%</span>
                                  <div class="relative inline-block w-10 sm:w-12 mr-2 align-middle select-none">
                                      <input type="checkbox" name="toggle" id="auto-order"
                                          class="toggle-checkbox absolute block w-4 h-4 sm:w-6 sm:h-6 rounded-full bg-white border-4 appearance-none cursor-pointer"
                                          checked />
                                      <label for="auto-order"
                                          class="toggle-label block overflow-hidden h-5 sm:h-6 rounded-full bg-gray-300 cursor-pointer"></label>
                                  </div>
                              </div>
                          </div>
                      </div>

                      <div class="p-5 sm:p-7">
                          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5 sm:gap-7 items-stretch">
                              <!-- Supplier 1 -->
                              <div class="h-full bg-white rounded-2xl border-2 border-green-500 shadow-md">
                                  <div class="p-5 sm:p-6 h-full flex flex-col">
                                      <div class="flex justify-between items-start gap-2 flex-wrap mb-4 sm:mb-5">
                                          <div>
                                              <h4 class="font-bold text-base sm:text-lg text-gray-900">Metro Foods
                                              </h4>
                                              <div class="flex items-center mt-1">
                                                  <div class="flex text-yellow-400 text-sm sm:text-base">
                                                      <i class="fas fa-star"></i>
                                                      <i class="fas fa-star"></i>
                                                      <i class="fas fa-star"></i>
                                                      <i class="fas fa-star"></i>
                                                      <i class="fas fa-star-half-alt"></i>
                                                  </div>
                                                  <span
                                                      class="text-xs sm:text-sm text-gray-600 ml-1 sm:ml-2">4.2/5</span>
                                              </div>
                                          </div>
                                          <div
                                              class="inline-flex items-center justify-center shrink-0 bg-green-100 text-green-800 text-[10px] sm:text-xs font-bold leading-tight py-1 px-2 sm:px-3 rounded-lg whitespace-nowrap">
                                              LOWEST PRICE
                                          </div>
                                      </div>

                                      <div class="text-center py-4 sm:py-5">
                                          <p class="text-2xl sm:text-3xl font-bold text-gray-900">$2.49</p>
                                          <p class="text-xs sm:text-sm text-gray-600 mt-1">per kg</p>
                                      </div>

                                      <div class="space-y-2.5 sm:space-y-3 mb-6 sm:mb-7">
                                          <div class="flex justify-between py-0.5">
                                              <span class="text-gray-600 text-sm">Delivery</span>
                                              <span class="font-medium text-sm">2-4 hours</span>
                                          </div>
                                          <div class="flex justify-between py-0.5">
                                              <span class="text-gray-600 text-sm">Minimum Order</span>
                                              <span class="font-medium text-sm">$100</span>
                                          </div>
                                          <div class="flex justify-between py-0.5">
                                              <span class="text-gray-600 text-sm">Last Order</span>
                                              <span class="font-medium text-sm">3 days ago</span>
                                          </div>
                                          <div class="flex justify-between py-0.5">
                                              <span class="text-gray-600 text-sm">Quality Score</span>
                                              <span class="font-medium text-sm">9.2/10</span>
                                          </div>
                                      </div>

                                      <button
                                          class="mt-auto w-full bg-green-500 hover:bg-green-600 text-white font-semibold py-2.5 sm:py-3 rounded-lg transition text-sm sm:text-base">
                                          Order Now
                                      </button>
                                  </div>
                              </div>

                              <!-- Supplier 2 -->
                              <div class="h-full bg-white rounded-2xl border border-gray-200 shadow-sm">
                                  <div class="p-5 sm:p-6 h-full flex flex-col">
                                      <div class="flex justify-between items-start gap-2 flex-wrap mb-4 sm:mb-5">
                                          <div>
                                              <h4 class="font-bold text-base sm:text-lg text-gray-900">Sysco</h4>
                                              <div class="flex items-center mt-1">
                                                  <div class="flex text-yellow-400 text-sm sm:text-base">
                                                      <i class="fas fa-star"></i>
                                                      <i class="fas fa-star"></i>
                                                      <i class="fas fa-star"></i>
                                                      <i class="fas fa-star"></i>
                                                      <i class="fas fa-star"></i>
                                                  </div>
                                                  <span
                                                      class="text-xs sm:text-sm text-gray-600 ml-1 sm:ml-2">4.8/5</span>
                                              </div>
                                          </div>
                                      </div>

                                      <div class="text-center py-4 sm:py-5">
                                          <p class="text-2xl sm:text-3xl font-bold text-gray-900">$2.89</p>
                                          <p class="text-xs sm:text-sm text-gray-600 mt-1">per kg</p>
                                      </div>

                                      <div class="space-y-2.5 sm:space-y-3 mb-6 sm:mb-7">
                                          <div class="flex justify-between py-0.5">
                                              <span class="text-gray-600 text-sm">Delivery</span>
                                              <span class="font-medium text-sm">Next day</span>
                                          </div>
                                          <div class="flex justify-between py-0.5">
                                              <span class="text-gray-600 text-sm">Minimum Order</span>
                                              <span class="font-medium text-sm">$200</span>
                                          </div>
                                          <div class="flex justify-between py-0.5">
                                              <span class="text-gray-600 text-sm">Last Order</span>
                                              <span class="font-medium text-sm">1 week ago</span>
                                          </div>
                                          <div class="flex justify-between py-0.5">
                                              <span class="text-gray-600 text-sm">Quality Score</span>
                                              <span class="font-medium text-sm">9.5/10</span>
                                          </div>
                                      </div>

                                      <button
                                          class="mt-auto w-full border-2 border-gray-300 text-gray-800 font-semibold py-2.5 sm:py-3 rounded-lg hover:border-orange-300 transition text-sm sm:text-base">
                                          Compare
                                      </button>
                                  </div>
                              </div>

                              <!-- Supplier 3 -->
                              <div class="h-full bg-white rounded-2xl border border-gray-200 shadow-sm">
                                  <div class="p-5 sm:p-6 h-full flex flex-col">
                                      <div class="flex justify-between items-start gap-2 flex-wrap mb-4 sm:mb-5">
                                          <div>
                                              <h4 class="font-bold text-base sm:text-lg text-gray-900">Local Farm
                                                  Co-op</h4>
                                              <div class="flex items-center mt-1">
                                                  <div class="flex text-yellow-400 text-sm sm:text-base">
                                                      <i class="fas fa-star"></i>
                                                      <i class="fas fa-star"></i>
                                                      <i class="fas fa-star"></i>
                                                      <i class="fas fa-star"></i>
                                                      <i class="fas fa-star"></i>
                                                  </div>
                                                  <span
                                                      class="text-xs sm:text-sm text-gray-600 ml-1 sm:ml-2">4.5/5</span>
                                              </div>
                                          </div>
                                          <div
                                              class="inline-flex items-center justify-center shrink-0 bg-blue-100 text-blue-800 text-[10px] sm:text-xs font-bold leading-tight py-1 px-2 sm:px-3 rounded-lg whitespace-nowrap">
                                              LOCAL
                                          </div>
                                      </div>

                                      <div class="text-center py-4 sm:py-5">
                                          <p class="text-2xl sm:text-3xl font-bold text-gray-900">$2.69</p>
                                          <p class="text-xs sm:text-sm text-gray-600 mt-1">per kg</p>
                                      </div>

                                      <div class="space-y-2.5 sm:space-y-3 mb-6 sm:mb-7">
                                          <div class="flex justify-between py-0.5">
                                              <span class="text-gray-600 text-sm">Delivery</span>
                                              <span class="font-medium text-sm">6-8 hours</span>
                                          </div>
                                          <div class="flex justify-between py-0.5">
                                              <span class="text-gray-600 text-sm">Minimum Order</span>
                                              <span class="font-medium text-sm">$50</span>
                                          </div>
                                          <div class="flex justify-between py-0.5">
                                              <span class="text-gray-600 text-sm">Last Order</span>
                                              <span class="font-medium text-sm">New supplier</span>
                                          </div>
                                          <div class="flex justify-between py-0.5">
                                              <span class="text-gray-600 text-sm">Quality Score</span>
                                              <span class="font-medium text-sm">9.8/10</span>
                                          </div>
                                      </div>

                                      <button
                                          class="mt-auto w-full border-2 border-gray-300 text-gray-800 font-semibold py-2.5 sm:py-3 rounded-lg hover:border-orange-300 transition text-sm sm:text-base">
                                          Compare
                                      </button>
                                  </div>
                              </div>
                          </div>

                          <div class="mt-6 sm:mt-8 pt-4 sm:pt-6 border-t border-gray-200">
                              <p class="text-gray-600 text-xs sm:text-sm"><i
                                      class="fas fa-info-circle text-blue-500 mr-2"></i> Based on your order history,
                                  Metro Foods offers the best value for onions across all 5 branches.</p>
                          </div>
                      </div>
                  </div>
              </div>
          </div>


      </div>
  </section>
