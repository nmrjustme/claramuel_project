<!-- Mobile Header -->
<div class="md:hidden fixed top-0 left-0 right-0 bg-white shadow-md z-[var(--z-header)] flex items-center justify-between p-4">
    <!-- Sidebar Toggle Button -->
    <button class="text-red-600 text-2xl focus:outline-none" id="toggleSidebarMobile" aria-label="Toggle sidebar">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
        </svg>
    </button>

    <!-- Logo and Site Name -->
    <div class="flex items-center space-x-2">
        <x-logo-icon size="default" />
        <span class="text-gray-800 font-semibold text-lg whitespace-nowrap truncate max-w-[160px]">
            <a href="#">Ｍｔ.ＣＬＡＲＡＭＵＥＬ</a>
        </span>
    </div>

    <!-- User Profile Dropdown -->
    <div class="relative group">
        @auth
        <button class="flex items-center gap-2 focus:outline-none" aria-label="User menu">
            <img src="{{ url('imgs/profiles/' . Auth::user()->profile_img) }}" 
                 class="h-8 w-8 rounded-full object-cover border border-gray-200"
                 alt="{{ Auth::user()->firstname }}'s profile picture">
            <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
            </svg>
        </button>

        <!-- Dropdown Menu -->
        <div class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-md shadow-lg py-1 z-50 opacity-0 invisible group-focus-within:opacity-100 group-focus-within:visible transition-all duration-200">
            <a href="{{ route('profile.edit') }}"
               class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
               Profile
            </a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                        class="w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                        Logout
                </button>
            </form>
        </div>
        @endauth
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const sidebar = document.getElementById('sidebar');
        const toggleSidebarMobile = document.getElementById('toggleSidebarMobile');
        
        // Toggle sidebar visibility
        toggleSidebarMobile.addEventListener('click', function () {
            sidebar.classList.toggle('translate-x-0');
            sidebar.classList.toggle('-translate-x-full');
            document.body.classList.toggle('overflow-hidden');
        });

        // Close sidebar when clicking on a nav link (mobile)
        const navLinks = document.querySelectorAll('#sidebar a');
        navLinks.forEach(link => {
            link.addEventListener('click', function() {
                if (window.innerWidth < 768) {
                    sidebar.classList.remove('translate-x-0');
                    sidebar.classList.add('-translate-x-full');
                    document.body.classList.remove('overflow-hidden');
                }
            });
        });
    });
</script>