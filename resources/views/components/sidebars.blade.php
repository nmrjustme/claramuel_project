@props(['active'])
<div class="flex flex-col px-4 py-4 overflow-y-auto bg-white h-full shadow-sm border-r border-gray-200">
    <!-- Logo Section -->
    <div class="mb-8 px-4 pt-2 flex items-center justify-start">
        <div class="flex items-center">
            <svg class="w-10 h-10 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
            </svg>
            <span class="ml-3 text-xl font-bold text-gray-800">HotelAdmin</span>
        </div>
    </div>

    <div class="flex-1 flex flex-col">
        <nav class="flex-1 space-y-1">
            <!-- Dashboard -->
            <a href="{{ route('admin.index') }}"
               class="flex items-center px-4 py-3 rounded-lg transition-all duration-200 ease-in-out 
                      {{ $active === 'dashboard' ? 'bg-red-50 text-red-600 border-l-4 border-red-500 font-semibold' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
                <span class="truncate">Dashboard</span>
            </a>

            <!-- Bookings -->
            <a href=""
               class="flex items-center px-4 py-3 rounded-lg transition-all duration-200 ease-in-out 
                      {{ $active === 'bookings' ? 'bg-red-50 text-red-600 border-l-4 border-red-500 font-semibold' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
                <span class="truncate">Bookings</span>
            </a>
        </nav>
    </div>
    
    <div class="p-4 border-t border-gray-200 mt-auto">
        <button class="w-full flex items-center justify-center px-4 py-3 rounded-md transition-colors duration-200 ease-in-out shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2">
            <svg class="w-5 h-5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
            </svg>
            Quick Booking
        </button>
    </div>
</div>