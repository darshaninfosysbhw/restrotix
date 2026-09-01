 <!-- Enquiry Form Section -->
 <section id="enquiry" class="py-16 sm:py-20 bg-white border-t border-gray-200">
     <div class="container mx-auto px-4 sm:px-6">
         <div class="max-w-6xl mx-auto bg-gray-50 border border-gray-200 rounded-2xl p-6 sm:p-8 lg:p-10">
             <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 lg:gap-10 items-stretch">
                 <div class="h-full flex flex-col">
                     <h2 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-3">Need Support or Have an Enquiry?</h2>
                     <p class="text-gray-600 text-sm sm:text-base mb-6">
                         Share your requirement with us. Our support and onboarding team will connect with you quickly.
                     </p>

                     <div class="space-y-3 mb-6">
                         <div class="flex items-center gap-3 text-sm sm:text-base text-gray-700">
                             <i class="fas fa-headset text-orange-500"></i>
                             <span>Dedicated implementation support</span>
                         </div>
                         <div class="flex items-center gap-3 text-sm sm:text-base text-gray-700">
                             <i class="fas fa-clock text-orange-500"></i>
                             <span>Fast response from our technical team</span>
                         </div>
                         <div class="flex items-center gap-3 text-sm sm:text-base text-gray-700">
                             <i class="fas fa-shield-alt text-orange-500"></i>
                             <span>Secure and reliable assistance</span>
                         </div>
                     </div>

                     <div
                         class="relative w-[280px] h-[280px] sm:w-[340px] sm:h-[340px] mt-auto mx-auto lg:mx-0 flex items-end justify-center">
                         <div class="absolute inset-0 rounded-full border border-orange-200"></div>
                         <div class="absolute inset-4 rounded-full bg-orange-100"></div>
                         <img src="{{ asset('images/support.png') }}" alt="Support Enquiry"
                             class="relative z-10 w-full h-full object-contain object-bottom p-3">
                     </div>
                 </div>

                 <form class="space-y-5 bg-white border border-gray-200 rounded-xl p-5 sm:p-6 h-full flex flex-col">
                     <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                         <div>
                             <label for="enquiry-name" class="block text-sm font-medium text-gray-700 mb-2">Full
                                 Name</label>
                             <input id="enquiry-name" type="text"
                                 class="w-full rounded-lg border border-gray-300 px-4 py-3 text-gray-800 focus:outline-none focus:ring-2 focus:ring-orange-400 focus:border-orange-400"
                                 placeholder="Enter your name">
                         </div>
                         <div>
                             <label for="enquiry-phone" class="block text-sm font-medium text-gray-700 mb-2">Phone
                                 Number</label>
                             <input id="enquiry-phone" type="tel"
                                 class="w-full rounded-lg border border-gray-300 px-4 py-3 text-gray-800 focus:outline-none focus:ring-2 focus:ring-orange-400 focus:border-orange-400"
                                 placeholder="Enter your phone number">
                         </div>
                     </div>

                     <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                         <div>
                             <label for="enquiry-email"
                                 class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                             <input id="enquiry-email" type="email"
                                 class="w-full rounded-lg border border-gray-300 px-4 py-3 text-gray-800 focus:outline-none focus:ring-2 focus:ring-orange-400 focus:border-orange-400"
                                 placeholder="Enter your email">
                         </div>
                         <div>
                             <label for="enquiry-subject"
                                 class="block text-sm font-medium text-gray-700 mb-2">Subject</label>
                             <input id="enquiry-subject" type="text"
                                 class="w-full rounded-lg border border-gray-300 px-4 py-3 text-gray-800 focus:outline-none focus:ring-2 focus:ring-orange-400 focus:border-orange-400"
                                 placeholder="Enter enquiry subject">
                         </div>
                     </div>

                     <div class="flex-1">
                         <label for="enquiry-message"
                             class="block text-sm font-medium text-gray-700 mb-2">Message</label>
                         <textarea id="enquiry-message" rows="6"
                             class="w-full min-h-[180px] rounded-lg border border-gray-300 px-4 py-3 text-gray-800 focus:outline-none focus:ring-2 focus:ring-orange-400 focus:border-orange-400"
                             placeholder="Write your requirement..."></textarea>
                     </div>

                     <div class="pt-2">
                         <button type="submit"
                             class="gradient-orange text-white font-semibold px-8 py-3 rounded-lg shadow-md hover:shadow-lg transition">
                             Submit Enquiry
                         </button>
                     </div>
                 </form>
             </div>
         </div>
     </div>
 </section>
