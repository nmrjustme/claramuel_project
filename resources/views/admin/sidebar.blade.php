<aside
    class="fixed inset-y-0 left-0 z-40 w-64 text-white shadow-xl transform -translate-x-full md:translate-x-0 transition-transform duration-300 ease-in-out bg-gradient-to-b from-red-800 to-red-800"
    id="sidebar">
    <div class="relative flex flex-col h-full">
        <!-- Close button (mobile only) -->
        <button
            class="md:hidden absolute top-4 right-4 text-white hover:text-red-200 focus:outline-none z-50 transition-colors duration-200"
            id="sidebar-close" aria-label="Close sidebar">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>

        <!-- Logo & Title -->
        <div class="flex items-center space-x-3 px-6 py-6 border-b border-red-700">
            <x-logo-icon size="xl" />
            <span class="font-bold text-xl tracking-wide">Mt.Claramuel</span>
        </div>

        <!-- Navigation Links -->
        <nav class="flex-1 overflow-y-auto py-4 px-2">
            <ul class="space-y-1">
                <!-- Dashboard -->
                <li>
                    <a href="{{ route('admin.dashboard') }}"
                        class="flex items-center py-3 px-4 rounded-lg transition-all duration-200 {{ $active === 'dashboard' ? 'bg-red-700 font-medium' : 'hover:bg-red-700/50' }}">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-3" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                stroke-width="{{ $active === 'dashboard' ? '2' : '1.5' }}"
                                d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                        </svg>
                        <span>Dashboard</span>
                    </a>
                </li>

                <!-- Manage Bookings Dropdown -->
                <li>
                    <div class="relative">
                        <button
                            class="flex items-center justify-between w-full py-3 px-4 rounded-lg transition-all duration-200 {{ in_array($active, ['bookings', 'arrivals', 'calendar']) ? 'bg-red-700 font-medium' : 'hover:bg-red-700/50' }}"
                            id="manage-bookings-dropdown" aria-expanded="false">
                            <div class="flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-3" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        stroke-width="{{ in_array($active, ['bookings', 'arrivals', 'calendar']) ? '2' : '1.5' }}"
                                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                                </svg>
                                <span>Manage Bookings</span>
                            </div>
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 transition-transform duration-200" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        
                        <!-- Dropdown Menu -->
                        <div class="mt-1 ml-4 overflow-hidden transition-all duration-300 ease-in-out max-h-0"
                            id="manage-bookings-menu">
                            <ul class="space-y-1">
                                <!-- Bookings -->
                                <li>
                                    <a href="{{ route('admin.bookings') }}"
                                        class="flex items-center py-2 px-4 rounded-lg transition-all duration-200 {{ $active === 'bookings' ? 'bg-red-700 font-medium' : 'hover:bg-red-700/50' }}">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-3" fill="none" viewBox="0 0 24 24"
                                            stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                stroke-width="{{ $active === 'bookings' ? '2' : '1.5' }}"
                                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                                        </svg>
                                        <span>Bookings</span>
                                    </a>
                                </li>
                                
                                <!-- Arrivals -->
                                <li>
                                    <a href="{{ route('incoming.list') }}"
                                        class="flex items-center py-2 px-4 rounded-lg transition-all duration-200 {{ $active === 'arrivals' ? 'bg-red-700 font-medium' : 'hover:bg-red-700/50' }}">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-3" fill="none" viewBox="0 0 24 24"
                                            stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                stroke-width="{{ $active === 'arrivals' ? '2' : '1.5' }}"
                                                d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M7.5 10.5l4.5 4.5m0 0l4.5-4.5m-4.5 4.5V3" />
                                        </svg>
                                        <span>Arrivals</span>
                                    </a>
                                </li>
                                
                                <!-- Calendar -->
                                <li>
                                    <a href="{{ route('admin.calendar') }}"
                                        class="flex items-center py-2 px-4 rounded-lg transition-all duration-200 {{ $active === 'calendar' ? 'bg-red-700 font-medium' : 'hover:bg-red-700/50' }}">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-3" fill="none" viewBox="0 0 24 24"
                                            stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                stroke-width="{{ $active === 'calendar' ? '2' : '1.5' }}"
                                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                        <span>Calendar</span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </li>

                <!--Day Tour -->
                <li>
                    <a href="{{ route('admin.daytour.logs') }}"
                        class="flex items-center py-3 px-4 rounded-lg transition-all duration-200 {{ $active === 'day_tour' ? 'bg-red-700 font-medium' : 'hover:bg-red-700/50' }}">
                        <div class="flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-3" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    stroke-width="{{ $active === 'day_tour' ? '2' : '1.5' }}"
                                    d="M12 3v2m0 14v2m9-9h-2M5 12H3m15.364-6.364l-1.414 1.414M6.05 17.95l-1.414-1.414M18.364 18.364l-1.414-1.414M6.05 6.05L4.636 7.464M12 8a4 4 0 100 8 4 4 0 000-8z" />
                            </svg>
                            <span>Day Tour Logs</span>
                        </div>
                    </a>
                </li>

                <!-- Facility Monitoring -->
                <li>
                    <a href="{{ route('admin.daytour.facility_monitoring') }}"
                        class="flex items-center py-3 px-4 rounded-lg transition-all duration-200 {{ $active === 'facility_monitoring' ? 'bg-red-700 font-medium' : 'hover:bg-red-700/50' }}">
                        <div class="flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-3" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    stroke-width="{{ $active === 'facility_monitoring' ? '2' : '1.5' }}"
                                    d="M3 9.75L12 4l9 5.75V19a2 2 0 01-2 2H5a2 2 0 01-2-2V9.75z" />
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    stroke-width="{{ $active === 'facility_monitoring' ? '2' : '1.5' }}"
                                    d="M9 22V12h6v10" />
                            </svg>
                            <span>Facility Monitoring</span>
                        </div>
                    </a>
                </li>

                <!-- Facilities -->
                <li>
                    <a href="{{ route('admin.facilities.index') }}"
                        class="flex items-center py-3 px-4 rounded-lg transition-all duration-200 {{ $active === 'facilities' ? 'bg-red-700 font-medium' : 'hover:bg-red-700/50' }}">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-3" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                stroke-width="{{ $active === 'facilities' ? '2' : '1.5' }}"
                                d="M3 10h18M3 6h18M3 14h18M3 18h18" />
                        </svg>
                        <span>Facilities</span>
                    </a>
                </li>

                <!-- Accounting & Analytics Dropdown -->
                <li>
                    <div class="relative">
                        <button
                            class="flex items-center justify-between w-full py-3 px-4 rounded-lg transition-all duration-200 {{ in_array($active, ['accounting', 'expenses', 'earnings', 'daytour-earnings']) ? 'bg-red-700 font-medium' : 'hover:bg-red-700/50' }}"
                            id="accounting-analytics-dropdown" aria-expanded="false">
                            <div class="flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-3" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        stroke-width="{{ in_array($active, ['accounting', 'expenses', 'earnings', 'daytour-earnings']) ? '2' : '1.5' }}"
                                       d="M2.25 6.75h19.5v10.5H2.25zM6 9h.008v.008H6V9zm12 0h.008v.008H18V9zm-6 2.25a2.25 2.25 0 100 4.5 2.25 2.25 0 000-4.5z" />
                                    </svg>
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-3" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        stroke-width="{{ in_array($active, ['accounting', 'expenses', 'earnings', 'daytour-earnings']) ? '2' : '1.5' }}"
                                        d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                
                                    </svg>
                                    
                                <span>Accounting & Analytics</span>
                            </div>
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 transition-transform duration-200" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        
                        <!-- Dropdown Menu -->
                        <div class="mt-1 ml-4 overflow-hidden transition-all duration-300 ease-in-out max-h-0"
                            id="accounting-analytics-menu">
                            <ul class="space-y-1">
                                <!-- Accounting Dashboard -->
                                <li>
                                    <a href="{{ route('admin.accounting.index') }}"
                                        class="flex items-center py-2 px-4 rounded-lg transition-all duration-200 {{ $active === 'accounting' ? 'bg-red-700 font-medium' : 'hover:bg-red-700/50' }}">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-3" fill="none" viewBox="0 0 24 24"
                                            stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                stroke-width="{{ $active === 'accounting' ? '2' : '1.5' }}"
                                                d="M2.25 6.75h19.5v10.5H2.25zM6 9h.008v.008H6V9zm12 0h.008v.008H18V9zm-6 2.25a2.25 2.25 0 100 4.5 2.25 2.25 0 000-4.5z" />
                                        </svg>
                                        <span>Accounting Dashboard</span>
                                    </a>
                                </li>
                                
                                <!-- Expenses -->
                                <li>
                                    <a href="{{ route('admin.expenses.index') }}"
                                        class="flex items-center py-2 px-4 rounded-lg transition-all duration-200 {{ $active === 'expenses' ? 'bg-red-700 font-medium' : 'hover:bg-red-700/50' }}">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-3" fill="none" viewBox="0 0 24 24"
                                            stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                stroke-width="{{ $active === 'expenses' ? '2' : '1.5' }}"
                                                d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" />
                                        </svg>
                                        <span>Expenses</span>
                                    </a>
                                </li>
                                
                                <!-- Earnings Chart -->
                                <li>
                                    <a href="{{ route('earnings.chart') }}"
                                        class="flex items-center py-2 px-4 rounded-lg transition-all duration-200 {{ $active === 'earnings' ? 'bg-red-700 font-medium' : 'hover:bg-red-700/50' }}">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-3" fill="none" viewBox="0 0 24 24"
                                            stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                stroke-width="{{ $active === 'earnings' ? '2' : '1.5' }}"
                                                d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                        </svg>
                                        <span>Room Earnings</span>
                                    </a>
                                </li>
                                
                                <!-- Day-Tour Chart -->
                                <li>
                                    <a href="{{ route('day_tour.earnings') }}"
                                        class="flex items-center py-2 px-4 rounded-lg transition-all duration-200 {{ $active === 'daytour-earnings' ? 'bg-red-700 font-medium' : 'hover:bg-red-700/50' }}">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-3" fill="none" viewBox="0 0 24 24"
                                            stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                stroke-width="{{ $active === 'daytour-earnings' ? '2' : '1.5' }}"
                                                d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                        </svg>
                                        <span>Day Tour Earnings</span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </li>

                <!-- Admin -->
                <li>
                    <a href="{{ route('admin.list.management') }}"
                        class="flex items-center py-3 px-4 rounded-lg transition-all duration-200 {{ $active === 'admin' ? 'bg-red-700 font-medium' : 'hover:bg-red-700/50' }}">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-3" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                stroke-width="{{ $active === 'admin' ? '2' : '1.5' }}"
                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        <span>Admin</span>
                    </a>
                </li>

            </ul>
        </nav>

        <!-- User Section -->
        <div class="p-4 border-t border-red-700 bg-red-700/30">
            <div class="flex items-center space-x-3">
                <div
                    class="w-10 h-10 rounded-full bg-white/20 flex items-center justify-center overflow-hidden border border-white/20">
                    @if(Auth::user()->profile_img)
                        <img src="{{ url('imgs/profiles/' . Auth::user()->profile_img) }}"
                            alt="{{ Auth::user()->firstname }}" class="w-full h-full object-cover">
                    @else
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                    @endif
                </div>

                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium truncate">{{ Auth::user()->firstname }} {{ Auth::user()->lastname }}
                    </p>
                    <p class="text-xs text-white/80 truncate">{{ Auth::user()->email }}</p>
                </div>

                <a href="{{ route('logout') }}" class="p-1.5 rounded-full hover:bg-white/10 transition-all duration-200"
                    onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                    </svg>
                </a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
                    @csrf
                </form>
            </div>
        </div>
    </div>
</aside>

<!-- JavaScript for dropdown functionality -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Manage Bookings Dropdown
        const bookingsDropdownButton = document.getElementById('manage-bookings-dropdown');
        const bookingsDropdownMenu = document.getElementById('manage-bookings-menu');
        
        // Accounting & Analytics Dropdown
        const accountingDropdownButton = document.getElementById('accounting-analytics-dropdown');
        const accountingDropdownMenu = document.getElementById('accounting-analytics-menu');

        // Function to handle dropdown toggle
        function setupDropdown(dropdownButton, dropdownMenu) {
            dropdownButton.addEventListener('click', function() {
                const isExpanded = this.getAttribute('aria-expanded') === 'true';
                this.setAttribute('aria-expanded', !isExpanded);
                
                // Toggle the dropdown icon rotation
                const icon = this.querySelector('svg:last-child');
                icon.style.transform = isExpanded ? 'rotate(0deg)' : 'rotate(180deg)';
                
                // Toggle the dropdown menu
                if (isExpanded) {
                    dropdownMenu.style.maxHeight = '0';
                } else {
                    dropdownMenu.style.maxHeight = dropdownMenu.scrollHeight + 'px';
                }
            });
            
            // Auto-expand if current page is in the dropdown
            if (dropdownMenu.querySelector('a.bg-red-700')) {
                dropdownButton.setAttribute('aria-expanded', 'true');
                dropdownButton.querySelector('svg:last-child').style.transform = 'rotate(180deg)';
                dropdownMenu.style.maxHeight = dropdownMenu.scrollHeight + 'px';
            }
        }

        // Initialize both dropdowns
        setupDropdown(bookingsDropdownButton, bookingsDropdownMenu);
        setupDropdown(accountingDropdownButton, accountingDropdownMenu);

        // Close other dropdown when one is opened
        function closeOtherDropdowns(currentDropdown) {
            const allDropdowns = [
                { button: bookingsDropdownButton, menu: bookingsDropdownMenu },
                { button: accountingDropdownButton, menu: accountingDropdownMenu }
            ];

            allDropdowns.forEach(dropdown => {
                if (dropdown.button !== currentDropdown && dropdown.button.getAttribute('aria-expanded') === 'true') {
                    dropdown.button.setAttribute('aria-expanded', 'false');
                    dropdown.button.querySelector('svg:last-child').style.transform = 'rotate(0deg)';
                    dropdown.menu.style.maxHeight = '0';
                }
            });
        }

        // Add click listeners to close other dropdowns
        bookingsDropdownButton.addEventListener('click', () => closeOtherDropdowns(bookingsDropdownButton));
        accountingDropdownButton.addEventListener('click', () => closeOtherDropdowns(accountingDropdownButton));
    });
</script>