
    <div class="flex items-center gap-3 px-6 py-5 border-b border-red-700/50 shrink-0">
        {{-- If you have a logo component --}}
        {{-- <x-logo-icon size="xl" class="text-white" /> --}}
        <div class="p-2 bg-white/10 rounded-lg">
            <i class="fas fa-mountain text-xl"></i>
        </div>
        <div>
            <span class="block font-bold text-lg tracking-wide leading-none">Mt. Claramuel</span>
            <span class="text-[10px] text-red-200 uppercase tracking-wider font-semibold">Admin Panel</span>
        </div>

        <button id="sidebar-close" class="md:hidden ml-auto text-white/70 hover:text-white">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    </div>

    <nav class="flex-1 overflow-y-auto px-3 py-4 space-y-1 custom-scrollbar">

        {{-- Dashboard --}}
        <a href="{{ route('admin.dashboard') }}"
            class="flex items-center py-2.5 px-4 rounded-lg transition-all duration-200 group {{ $active === 'dashboard' ? 'bg-red-700 text-white font-medium shadow-md' : 'text-red-100 hover:bg-white/10 hover:text-white' }}">
            <svg class="w-5 h-5 mr-3 {{ $active === 'dashboard' ? 'text-white' : 'text-red-300 group-hover:text-white' }}"
                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
            </svg>
            Dashboard
        </a>

        {{-- Section Label --}}
        <div class="pt-4 pb-2 px-4 text-xs font-bold text-red-300 uppercase tracking-wider">Operations</div>

        {{-- Manage Bookings Dropdown --}}
        <div class="relative">
            <button
                class="sidebar-dropdown-toggle flex items-center justify-between w-full py-2.5 px-4 rounded-lg transition-all duration-200 group {{ in_array($active, ['bookings', 'arrivals', 'calendar']) ? 'bg-red-700/50 text-white' : 'text-red-100 hover:bg-white/10' }}"
                aria-expanded="{{ in_array($active, ['bookings', 'arrivals', 'calendar']) ? 'true' : 'false' }}">
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-3 {{ in_array($active, ['bookings', 'arrivals', 'calendar']) ? 'text-white' : 'text-red-300 group-hover:text-white' }}"
                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <span>Bookings</span>

                    {{-- Notification Badge Placeholder --}}
                    <span id="inquiries-badge"
                        class="hidden ml-2 px-1.5 py-0.5 bg-yellow-400 text-red-900 text-[10px] font-bold rounded-full animate-pulse">
                        New
                    </span>
                </div>
                <svg class="w-4 h-4 transition-transform duration-200 transform {{ in_array($active, ['bookings', 'arrivals', 'calendar']) ? 'rotate-180' : '' }}"
                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
            </button>
            <div
                class="sidebar-submenu overflow-hidden transition-all duration-300 ease-in-out {{ in_array($active, ['bookings', 'arrivals', 'calendar']) ? 'max-h-96' : 'max-h-0' }}">
                <ul class="pt-1 pb-2 pl-12 pr-2 space-y-1">
                    <li><a href="{{ route('admin.bookings') }}"
                            class="block py-2 px-2 rounded hover:text-white text-sm {{ $active === 'bookings' ? 'text-white font-medium' : 'text-red-200' }}">All
                            Bookings</a></li>
                    <li><a href="{{ route('incoming.list') }}"
                            class="block py-2 px-2 rounded hover:text-white text-sm {{ $active === 'arrivals' ? 'text-white font-medium' : 'text-red-200' }}">Arrivals</a>
                    </li>
                    <li><a href="{{ route('admin.calendar') }}"
                            class="block py-2 px-2 rounded hover:text-white text-sm {{ $active === 'calendar' ? 'text-white font-medium' : 'text-red-200' }}">Calendar</a>
                    </li>
                </ul>
            </div>
        </div>

        {{-- Day Tour Logs --}}
        <a href="{{ route('admin.daytour.logs') }}"
            class="flex items-center py-2.5 px-4 rounded-lg transition-all duration-200 group {{ $active === 'day_tour' ? 'bg-red-700 text-white font-medium shadow-md' : 'text-red-100 hover:bg-white/10 hover:text-white' }}">
            <svg class="w-5 h-5 mr-3 {{ $active === 'day_tour' ? 'text-white' : 'text-red-300 group-hover:text-white' }}"
                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            Day Tour Logs
        </a>

        {{-- Facility Monitoring --}}
        <a href="{{ route('admin.daytour.facility_monitoring') }}"
            class="flex items-center py-2.5 px-4 rounded-lg transition-all duration-200 group {{ $active === 'facility_monitoring' ? 'bg-red-700 text-white font-medium shadow-md' : 'text-red-100 hover:bg-white/10 hover:text-white' }}">
            <svg class="w-5 h-5 mr-3 {{ $active === 'facility_monitoring' ? 'text-white' : 'text-red-300 group-hover:text-white' }}"
                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            Facility Monitoring
        </a>

        <div class="pt-4 pb-2 px-4 text-xs font-bold text-red-300 uppercase tracking-wider">Reports & Config</div>

        {{-- Revenue Reports Dropdown --}}
        <div class="relative">
            <button
                class="sidebar-dropdown-toggle flex items-center justify-between w-full py-2.5 px-4 rounded-lg transition-all duration-200 group {{ in_array($active, ['accounting', 'expenses', 'earnings', 'daytour-earnings']) ? 'bg-red-700/50 text-white' : 'text-red-100 hover:bg-white/10' }}"
                aria-expanded="{{ in_array($active, ['accounting', 'expenses', 'earnings', 'daytour-earnings']) ? 'true' : 'false' }}">
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-3 {{ in_array($active, ['accounting', 'expenses', 'earnings', 'daytour-earnings']) ? 'text-white' : 'text-red-300 group-hover:text-white' }}"
                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                    <span>Revenue & Expenses</span>
                </div>
                <svg class="w-4 h-4 transition-transform duration-200 transform {{ in_array($active, ['accounting', 'expenses', 'earnings', 'daytour-earnings']) ? 'rotate-180' : '' }}"
                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
            </button>
            <div
                class="sidebar-submenu overflow-hidden transition-all duration-300 ease-in-out {{ in_array($active, ['accounting', 'expenses', 'earnings', 'daytour-earnings']) ? 'max-h-96' : 'max-h-0' }}">
                <ul class="pt-1 pb-2 pl-12 pr-2 space-y-1">
                    <li><a href="{{ route('admin.accounting.index') }}"
                            class="block py-2 px-2 rounded hover:text-white text-sm {{ $active === 'accounting' ? 'text-white font-medium' : 'text-red-200' }}">Accounting</a>
                    </li>
                    <li><a href="{{ route('admin.earnings.chart') }}"
                            class="block py-2 px-2 rounded hover:text-white text-sm {{ $active === 'earnings' ? 'text-white font-medium' : 'text-red-200' }}">Room
                            Earnings</a></li>
                    <li><a href="{{ route('day_tour.earnings') }}"
                            class="block py-2 px-2 rounded hover:text-white text-sm {{ $active === 'daytour-earnings' ? 'text-white font-medium' : 'text-red-200' }}">Day
                            Tour Earnings</a></li>
                    <li><a href="{{ route('admin.expenses.index') }}"
                            class="block py-2 px-2 rounded hover:text-white text-sm {{ $active === 'expenses' ? 'text-white font-medium' : 'text-red-200' }}">Expenses</a>
                    </li>
                </ul>
            </div>
        </div>

        {{-- Facilities Config Dropdown --}}
        <div class="relative">
            <button
                class="sidebar-dropdown-toggle flex items-center justify-between w-full py-2.5 px-4 rounded-lg transition-all duration-200 group {{ in_array($active, ['facilities', 'day-tour facilities']) ? 'bg-red-700/50 text-white' : 'text-red-100 hover:bg-white/10' }}"
                aria-expanded="{{ in_array($active, ['facilities', 'day-tour facilities']) ? 'true' : 'false' }}">
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-3 {{ in_array($active, ['facilities', 'day-tour facilities']) ? 'text-white' : 'text-red-300 group-hover:text-white' }}"
                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                    <span>Facility Settings</span>
                </div>
                <svg class="w-4 h-4 transition-transform duration-200 transform {{ in_array($active, ['facilities', 'day-tour facilities']) ? 'rotate-180' : '' }}"
                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
            </button>
            <div
                class="sidebar-submenu overflow-hidden transition-all duration-300 ease-in-out {{ in_array($active, ['facilities', 'day-tour facilities']) ? 'max-h-96' : 'max-h-0' }}">
                <ul class="pt-1 pb-2 pl-12 pr-2 space-y-1">
                    <li><a href="{{ route('admin.facilities.index') }}"
                            class="block py-2 px-2 rounded hover:text-white text-sm {{ $active === 'facilities' ? 'text-white font-medium' : 'text-red-200' }}">Room
                            Facilities</a></li>
                    <li><a href="{{ route('admin.day-tour-facilities.index') }}"
                            class="block py-2 px-2 rounded hover:text-white text-sm {{ $active === 'day-tour facilities' ? 'text-white font-medium' : 'text-red-200' }}">Day
                            Tour Facilities</a></li>
                </ul>
            </div>
        </div>

        {{-- Admin Management --}}
        <a href="{{ route('admin.list.management') }}"
            class="flex items-center py-2.5 px-4 rounded-lg transition-all duration-200 group {{ $active === 'admin' ? 'bg-red-700 text-white font-medium shadow-md' : 'text-red-100 hover:bg-white/10 hover:text-white' }}">
            <svg class="w-5 h-5 mr-3 {{ $active === 'admin' ? 'text-white' : 'text-red-300 group-hover:text-white' }}"
                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
            </svg>
            User Management
        </a>

        {{-- Gallery --}}
        <a href="{{ route('admin.galleries.index') }}"
            class="flex items-center py-2.5 px-4 rounded-lg transition-all duration-200 group {{ $active === 'gallery' ? 'bg-red-700 text-white font-medium shadow-md' : 'text-red-100 hover:bg-white/10 hover:text-white' }}">
            <svg class="w-5 h-5 mr-3 {{ $active === 'gallery' ? 'text-white' : 'text-red-300 group-hover:text-white' }}"
                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
            </svg>
            Gallery
        </a>

    </nav>

    <div class="p-4 border-t border-red-700/50 bg-red-900/50 shrink-0">
        <div class="flex items-center gap-3">
            <div class="relative">
                <img src="{{ url('imgs/profiles/' . Auth::user()->profile_img) }}" alt="User"
                    class="w-10 h-10 rounded-full object-cover border-2 border-red-200/30">
                <span
                    class="absolute bottom-0 right-0 w-2.5 h-2.5 bg-green-400 border-2 border-red-900 rounded-full"></span>
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-semibold truncate">{{ Auth::user()->firstname }}</p>
                <p class="text-xs text-red-200 truncate">Administrator</p>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                    class="p-2 text-red-200 hover:text-white hover:bg-white/10 rounded-full transition-colors"
                    title="Logout">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                    </svg>
                </button>
            </form>
        </div>
    </div>


<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Generic Dropdown Toggle Logic
        // Selects all buttons with the specific class
        const toggles = document.querySelectorAll('.sidebar-dropdown-toggle');

        toggles.forEach(toggle => {
            toggle.addEventListener('click', function (e) {
                // Find the next sibling which is the submenu
                const menu = this.nextElementSibling;
                const icon = this.querySelector('svg:last-child');
                const isExpanded = this.getAttribute('aria-expanded') === 'true';

                // Close all other menus (Accordion style - optional)
                document.querySelectorAll('.sidebar-submenu').forEach(otherMenu => {
                    if (otherMenu !== menu) {
                        otherMenu.style.maxHeight = '0';
                        otherMenu.previousElementSibling.setAttribute('aria-expanded', 'false');
                        otherMenu.previousElementSibling.querySelector('svg:last-child').classList.remove('rotate-180');
                    }
                });

                // Toggle Current
                if (isExpanded) {
                    menu.style.maxHeight = '0';
                    this.setAttribute('aria-expanded', 'false');
                    icon.classList.remove('rotate-180');
                } else {
                    menu.style.maxHeight = menu.scrollHeight + "px";
                    this.setAttribute('aria-expanded', 'true');
                    icon.classList.add('rotate-180');
                }
            });
        });
    });
</script>