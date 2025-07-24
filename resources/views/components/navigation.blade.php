<!-- Customer Homepage Navigation -->
<nav class="dark:bg-gray-600 bg-white shadow-sm px-4 sm:px-6 py-3">
    <div class="flex items-center justify-between h-12">
        <!-- Left: Logo + Title -->
        <div class="flex items-center space-x-3">
            <x-logo-icon size="default" />
            <a href="{{ route('index') }}"
                class="dark:text-white text-gray-700 font-semibold text-lg whitespace-nowrap">
                Ｍｔ.ＣＬＡＲＡＭＵＥＬ
            </a>
        </div>

        @auth
        <!-- Right: Desktop and Mobile Menu -->
        <div class="flex items-center space-x-4">
            <!-- Mobile Menu Button (for small screens) -->
            <button id="mobile-menu-button"
                class="sm:hidden inline-flex items-center justify-center p-2 rounded-md text-gray-500 hover:text-gray-700 hover:bg-gray-100 focus:outline-none transition duration-150 ease-in-out">
                <!-- Hamburger icon -->
                <svg id="icon-hamburger" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 block" fill="none"
                    viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
                <!-- X icon -->
                <svg id="icon-close" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 hidden" fill="none"
                    viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>

            <!-- Desktop Menu (hidden on mobile) -->
            <div class="hidden sm:flex items-center gap-4">
                <!-- Notification Icon -->
                <form action="{{ route('MyBookings') }}">
                    <button
                        class="flex items-center justify-center text-gray-400 dark:hover:text-gray-100 hover:text-gray-600 focus:outline-none transition h-10 w-10">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6 6 0 00-5-5.917V4a2 2 0 10-4 0v1.083A6 6 0 004 11v3.159c0 .538-.214 1.055-.595 1.436L2 17h5m7 0a3 3 0 11-6 0h6z" />
                        </svg>
                    </button>
                </form>

                <!-- Profile Dropdown -->
                <div class="relative group">
                    <button class="flex items-center gap-2 focus:outline-none">
                        <img src="{{ url('imgs/profiles/' . Auth::user()->profile_img) }}"
                            class="h-8 w-8 rounded-full object-cover" alt="Profile" />
                        <span class="text-sm font-medium dark:text-gray-200 text-gray-700">{{ Auth::user()->firstname
                            }}</span>
                        <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" stroke-width="2"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>

                    <!-- Dropdown Menu -->
                    <div
                        class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-md shadow-lg py-1 z-50 opacity-0 scale-95 group-focus-within:opacity-100 group-focus-within:scale-100 group-focus-within:block transition-all ease-in-out duration-200 hidden">
                        <a href="{{ route('profile.edit') }}"
                            class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-600">Profile</a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit"
                                class="w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-600">Logout</button>
                        </form>
                    </div>
                </div>
            </div>

        </div>

        <!-- Mobile Menu Dropdown (shown only on small screens) -->
        <div id="mobile-menu"
            class="sm:hidden fixed inset-0 mt-16 bg-white dark:bg-gray-600 shadow-lg z-40 border-t border-gray-200 dark:border-gray-500 hidden">
            <div class="px-2 pt-2 pb-3 space-y-1">
                <!-- Notifications Link -->
                <a href="{{ route('MyBookings') }}"
                    class="flex items-center px-3 py-2 text-base font-medium text-gray-700 dark:text-gray-200 hover:text-gray-900 dark:hover:text-white hover:bg-gray-50 dark:hover:bg-gray-500 rounded-md">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6 6 0 00-5-5.917V4a2 2 0 10-4 0v1.083A6 6 0 004 11v3.159c0 .538-.214 1.055-.595 1.436L2 17h5m7 0a3 3 0 11-6 0h6z" />
                    </svg>
                    Notifications
                </a>

                <!-- Profile Link -->
                <a href="{{ route('profile.edit') }}"
                    class="flex items-center px-3 py-2 text-base font-medium text-gray-700 dark:text-gray-200 hover:text-gray-900 dark:hover:text-white hover:bg-gray-50 dark:hover:bg-gray-500 rounded-md">
                    <img src="{{ url('imgs/profiles/' . Auth::user()->profile_img) }}"
                        class="h-6 w-6 rounded-full mr-2 object-cover" alt="Profile" />
                    Profile
                </a>

                <!-- Logout Button -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                        class="w-full text-left flex items-center px-3 py-2 text-base font-medium text-gray-700 dark:text-gray-200 hover:text-gray-900 dark:hover:text-white hover:bg-gray-50 dark:hover:bg-gray-500 rounded-md">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                        Logout
                    </button>
                </form>
            </div>
        </div>
        @endauth
    </div>
</nav>

<!-- JS script to toggle mobile menu -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const menuButton = document.getElementById('mobile-menu-button');
        const mobileMenu = document.getElementById('mobile-menu');
        const iconHamburger = document.getElementById('icon-hamburger');
        const iconClose = document.getElementById('icon-close');

        menuButton.addEventListener('click', function () {
            mobileMenu.classList.toggle('hidden');
            iconHamburger.classList.toggle('hidden');
            iconClose.classList.toggle('hidden');
        });
    });
</script>