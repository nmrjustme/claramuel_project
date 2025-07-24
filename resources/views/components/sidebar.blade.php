
<!-- Sidebar -->
<aside class="fixed inset-y-0 left-0 z-40 w-64 bg-gradient-to-b from-white to-red-50 text-gray-800 shadow-xl border-r border-red-100 transform -translate-x-full md:translate-x-0 transition-transform duration-300 ease-in-out" id="sidebar">
    <!-- Background pattern -->
    <div class="absolute inset-0 opacity-5 z-0" style="background-image: url('data:image/svg+xml;base64,...');"></div>

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
                <!-- Dashboard -->
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

                <!-- Bookings -->
                <li>
                    <a href="{{ route('admin.inquiries') }}"
                       class="group flex items-center py-3 px-4 rounded-lg hover:bg-white hover:shadow-sm transition-all duration-200 {{ $active === 'booking_management' ? 'bg-white text-red-600 font-medium shadow-sm' : 'text-gray-600 hover:text-red-600' }}">
                        <div class="p-1.5 mr-3 rounded-lg {{ $active === 'booking_management' ? 'bg-red-100' : 'bg-gray-100 group-hover:bg-red-100' }}">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="{{ $active === 'booking_management' ? '2' : '1.5' }}" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                        </div>
                        <span class="flex-1">Bookings</span>
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-red-300 opacity-0 group-hover:opacity-100 transition-opacity" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </a>
                </li>
                
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

        <!-- Sidebar footer -->
        <div class="p-4 border-t border-red-100 relative z-10">
            <div class="flex items-center space-x-3">
                <img src="{{ Auth::user()->profile_img ? url('imgs/profiles/' . Auth::user()->profile_img) : asset('imgs/default-profile.png') }}"
                     class="w-10 h-10 rounded-full border-2 border-red-200 object-cover">
                <div>
                    <p class="font-medium text-gray-800">{{ Auth::user()->firstname }} {{ Auth::user()->lastname }}</p>
                    <p class="text-xs text-gray-500">{{ Auth::user()->email }}</p>
                </div>
            </div>
        </div>
    </div>
</aside>

<!-- Mobile sidebar overlay -->
<div class="fixed inset-0 bg-black bg-opacity-50 z-30 md:hidden hidden" id="sidebar-overlay"></div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const sidebar = document.getElementById('sidebar');
        const toggleSidebarMobile = document.getElementById('toggleSidebarMobile');
        const sidebarClose = document.getElementById('sidebar-close');
        const sidebarOverlay = document.getElementById('sidebar-overlay');

        function toggleSidebar() {
            sidebar.classList.toggle('-translate-x-full');
            sidebarOverlay.classList.toggle('hidden');
            document.body.classList.toggle('overflow-hidden');
        }

        // Toggle from mobile header button
        toggleSidebarMobile?.addEventListener('click', toggleSidebar);

        // Close from sidebar button
        sidebarClose?.addEventListener('click', toggleSidebar);

        // Close from overlay click
        sidebarOverlay?.addEventListener('click', toggleSidebar);

        // Auto-close when clicking nav links on mobile
        document.querySelectorAll('#sidebar a').forEach(link => {
            link.addEventListener('click', () => {
                if (window.innerWidth < 768) toggleSidebar();
            });
        });
    });
</script>
