<!-- Sidebar -->
<aside class="fixed inset-y-0 left-0 z-40 w-64 bg-gradient-to-b from-white to-red-50 text-gray-800 shadow-xl border-r border-red-100 transform -translate-x-full md:translate-x-0" id="sidebar">
    <!-- Background pattern -->
    <div class="absolute inset-0 opacity-5 z-0" style="background-image: url('data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSIxMDAlIiBoZWlnaHQ9IjEwMCUiPjxkZWZzPjxwYXR0ZXJuIGlkPSJwYXR0ZXJuIiB3aWR0aD0iNDAiIGhlaWdodD0iNDAiIHBhdHRlcm5Vbml0cz0idXNlclNwYWNlT25Vc2UiIHBhdHRlcm5UcmFuc2Zvcm09InJvdGF0ZSg0NSkiPjxyZWN0IHdpZHRoPSIyMCIgaGVpZ2h0PSIyMCIgZmlsbD0icmdiKDIxNSwgMTMsIDMyKSIgZmlsbC1vcGFjaXR5PSIwLjEiLz48L3BhdHRlcm4+PC9kZWZzPjxyZWN0IHdpZHRoPSIxMDAlIiBoZWlnaHQ9IjEwMCUiIGZpbGw9InVybCgjcGF0dGVybikiLz48L3N2Zz4=');"></div>

    <div class="relative flex flex-col h-full">
        <!-- Close button (mobile only) -->
        <button class="md:hidden absolute top-4 right-4 text-gray-500 hover:text-red-600 focus:outline-none z-50" id="sidebar-close" aria-label="Close sidebar">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>

        <!-- Logo & Title -->
        <div class="relative z-10 flex items-center space-x-3 p-6 border-b border-red-100">
            <div class="w-10 h-10 rounded-full bg-gradient-to-br from-red-500 to-red-600 flex items-center justify-center text-white shadow-md">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h.01M17 16h.01" />
                </svg>
            </div>
            <span class="font-bold text-xl bg-gradient-to-r from-red-600 to-red-800 bg-clip-text text-transparent tracking-wide">Admin Panel</span>
        </div>

        <!-- Navigation Links -->
        <nav class="flex-1 overflow-y-auto py-4 px-2 relative z-10">
            <ul class="space-y-1">
                <li>
                    <a href="{{ route('admin.dashboard') }}"
                        class="group flex items-center py-3 px-4 rounded-lg hover:bg-white hover:shadow-sm transition-all duration-200 {{ $active === 'dashboard' ? 'bg-white text-red-600 font-medium shadow-sm' : 'text-gray-600 hover:text-red-600' }}">
                        <div class="p-1.5 mr-3 rounded-lg {{ $active === 'dashboard' ? 'bg-red-100' : 'bg-gray-100 group-hover:bg-red-100' }}">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="{{ $active === 'dashboard' ? '2' : '1.5' }}" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                            </svg>
                        </div>
                        <span class="flex-1">Dashboard</span>
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-red-300 opacity-0 group-hover:opacity-100 transition-opacity" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.bookings') }}"
                        class="group flex items-center py-3 px-4 rounded-lg hover:bg-white hover:shadow-sm transition-all duration-200 {{ $active === 'bookings' ? 'bg-white text-red-600 font-medium shadow-sm' : 'text-gray-600 hover:text-red-600' }}">
                        <div class="p-1.5 mr-3 rounded-lg {{ $active === 'bookings' ? 'bg-red-100' : 'bg-gray-100 group-hover:bg-red-100' }}">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="{{ $active === 'bookings' ? '2' : '1.5' }}" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                            </svg>
                        </div>
                        <span class="flex-1">Bookings</span>
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-red-300 opacity-0 group-hover:opacity-100 transition-opacity" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.inquiries') }}"
                        class="group flex items-center py-3 px-4 rounded-lg hover:bg-white hover:shadow-sm transition-all duration-200 {{ $active === 'inquiries' ? 'bg-white text-red-600 font-medium shadow-sm' : 'text-gray-600 hover:text-red-600' }}">
                        <div class="p-1.5 mr-3 rounded-lg {{ $active === 'booking_inquiries' ? 'bg-red-100' : 'bg-gray-100 group-hover:bg-red-100' }}">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="{{ $active === 'booking_management' ? '2' : '1.5' }}" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                        </div>
                        <span class="flex-1">Inquiries Log</span>
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-red-300 opacity-0 group-hover:opacity-100 transition-opacity" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.payments') }}"
                       class="group flex items-center py-3 px-4 rounded-lg hover:bg-white hover:shadow-sm transition-all duration-200 {{ $active === 'payments' ? 'bg-white text-red-600 font-medium shadow-sm' : 'text-gray-600 hover:text-red-600' }}">
                        <div class="p-1.5 mr-3 rounded-lg {{ $active === 'payments' ? 'bg-red-100' : 'bg-gray-100 group-hover:bg-red-100' }}">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="{{ $active === 'payments' ? '2' : '1.5' }}" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <span class="flex-1">Advance Payments</span>
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-red-300 opacity-0 group-hover:opacity-100 transition-opacity" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.calendar') }}"
                       class="group flex items-center py-3 px-4 rounded-lg hover:bg-white hover:shadow-sm transition-all duration-200 {{ $active === 'calendar' ? 'bg-white text-red-600 font-medium shadow-sm' : 'text-gray-600 hover:text-red-600' }}">
                        <div class="p-1.5 mr-3 rounded-lg {{ $active === 'calendar' ? 'bg-red-100' : 'bg-gray-100 group-hover:bg-red-100' }}">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="{{ $active === 'calendar' ? '2' : '1.5' }}" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <span class="flex-1">Calendar</span>
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-red-300 opacity-0 group-hover:opacity-100 transition-opacity" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </a>
                </li>
                <!-- Facilities -->
                <li>
                    <a href="{{ route('admin.facilities.index') }}"
                       class="group flex items-center py-3 px-4 rounded-lg hover:bg-white hover:shadow-sm transition-all duration-200 {{ $active === 'facilities' ? 'bg-white text-red-600 font-medium shadow-sm' : 'text-gray-600 hover:text-red-600' }}">
                        <div class="p-1.5 mr-3 rounded-lg {{ $active === 'facilities' ? 'bg-red-100' : 'bg-gray-100 group-hover:bg-red-100' }}">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="{{ $active === 'facilities' ? '2' : '1.5' }}" d="M3 10h18M3 6h18M3 14h18M3 18h18" />
                            </svg>
                        </div>
                        <span class="flex-1">Facilities</span>
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-red-300 opacity-0 group-hover:opacity-100 transition-opacity" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </a>
                </li>
            </ul>
        </nav>
    </div>
</aside>