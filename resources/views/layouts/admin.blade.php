<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- This title sets the Browser Tab Name --}}
    <title>@yield('title', 'Mt. Claramuel Resort & Events Place')</title>

    <link rel="icon" href="{{ asset('/favicon.ico?v=2') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        /* Custom Scrollbar */
        .custom-scrollbar::-webkit-scrollbar {
            width: 5px;
            height: 5px;
        }

        .custom-scrollbar::-webkit-scrollbar-track {
            background: transparent;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: rgba(153, 27, 27, 0.5);
            border-radius: 4px;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: rgba(153, 27, 27, 0.8);
        }

        /* Loading Animation */
        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        .badge-loading {
            display: inline-block;
            width: 16px;
            height: 16px;
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            border-top-color: #fff;
            animation: spin 1s linear infinite;
        }
    </style>

    @yield('content_css')
</head>

<body class="font-sans antialiased h-screen overflow-hidden flex flex-col">

    {{-- Mobile Header --}}
    <header
        class="md:hidden bg-white shadow-sm border-b border-gray-200 h-16 flex items-center justify-between px-4 z-30 relative shrink-0">
        <button id="toggleSidebarMobile"
            class="text-gray-600 hover:text-red-700 focus:outline-none p-2 rounded-md hover:bg-gray-100 transition-colors">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16">
                </path>
            </svg>
        </button>

        <a href="{{ route('index') }}" class="font-bold text-gray-800 text-lg">
            Mt. Claramuel
        </a>

        <div class="relative group">
            @auth
                <button class="flex items-center focus:outline-none">
                    <img src="{{ url('imgs/profiles/' . Auth::user()->profile_img) }}"
                        class="h-8 w-8 rounded-full object-cover border border-gray-200 shadow-sm" alt="Profile">
                </button>
                <div
                    class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-xl py-1 border border-gray-100 invisible opacity-0 group-focus-within:visible group-focus-within:opacity-100 transition-all duration-200 transform origin-top-right z-50">
                    <div class="px-4 py-2 border-b border-gray-100 bg-gray-50">
                        <p class="text-xs font-semibold text-gray-500 uppercase">Account</p>
                    </div>
                    <a href="{{ route('profile.edit') }}"
                        class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">Profile</a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                            class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50">Logout</button>
                    </form>
                </div>
            @endauth
        </div>
    </header>

    <div class="flex flex-1 h-[calc(100vh-4rem)] md:h-screen overflow-hidden relative">

        <div id="sidebar-overlay"
            class="fixed inset-0 bg-gray-900/60 z-30 transition-opacity duration-300 opacity-0 pointer-events-none md:hidden backdrop-blur-sm">
        </div>

        <aside id="sidebar"
            class="fixed md:static inset-y-0 left-0 z-40 w-64 bg-gradient-to-b from-red-900 to-red-800 text-white shadow-xl transform -translate-x-full md:translate-x-0 transition-transform duration-300 ease-in-out flex flex-col h-full">
            @include('admin.sidebar')
        </aside>

        <main class="flex-1 flex flex-col min-w-0 bg-gray-200 h-full overflow-hidden relative">
            <header
                class="hidden md:flex items-center justify-end bg-white h-16 px-6 shadow-sm border-b border-gray-200 sticky top-0 z-20 shrink-0">
                <div class="flex items-center gap-6">
                    <div class="relative group">
                        @auth
                            <button
                                class="flex items-center gap-3 focus:outline-none hover:bg-gray-50 p-1.5 rounded-full pr-3 transition-colors border border-transparent hover:border-gray-200">
                                <img src="{{ url('imgs/profiles/' . Auth::user()->profile_img) }}"
                                    class="h-8 w-8 rounded-full object-cover border border-gray-200" alt="Profile">
                                <span class="text-sm font-medium text-gray-700">{{ Auth::user()->firstname }}</span>
                                <i
                                    class="fas fa-chevron-down text-xs text-gray-400 transition-transform duration-200 group-focus-within:rotate-180"></i>
                            </button>

                            <div
                                class="absolute right-0 mt-2 w-56 bg-white rounded-lg shadow-xl py-1 border border-gray-100 invisible opacity-0 group-focus-within:visible group-focus-within:opacity-100 transition-all duration-200 transform origin-top-right z-50">
                                <div class="px-4 py-3 border-b border-gray-100 bg-gray-50">
                                    <p class="text-sm text-gray-900 font-bold">{{ Auth::user()->firstname }}
                                        {{ Auth::user()->lastname }}</p>
                                    <p class="text-xs text-gray-500 truncate">{{ Auth::user()->email }}</p>
                                </div>

                                <div class="py-1">
                                    <a href="{{ route('profile.edit') }}"
                                        class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 hover:text-red-700 transition-colors">
                                        <i class="fas fa-user-cog w-5 text-gray-400 mr-2"></i> Profile Settings
                                    </a>
                                </div>

                                <div class="border-t border-gray-100 my-1"></div>

                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit"
                                        class="w-full flex items-center px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition-colors">
                                        <i class="fas fa-sign-out-alt w-5 mr-2"></i> Sign out
                                    </button>
                                </form>
                            </div>
                        @endauth
                    </div>
                </div>
            </header>

            <div class="flex-1 overflow-y-auto p-4 md:p-6 scroll-smooth custom-scrollbar">
                @yield('content')
            </div>
        </main>
    </div>

    <audio id="notificationSound" src="{{ asset('sounds/notification/mixkit-software-interface-back-2575.wav') }}"
        preload="auto"></audio>

    <script src="https://cdn.jsdelivr.net/npm/flowbite@3.1.2/dist/flowbite.min.js"></script>

    <script type="module">
        let notificationCooldown = false;
        let lastBookingId = null;
        let lastEventId = null;

        document.addEventListener('DOMContentLoaded', () => {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebar-overlay');
            const toggleBtn = document.getElementById('toggleSidebarMobile');

            // --- Toggle Sidebar Function ---
            function toggleSidebar() {
                const isClosed = sidebar.classList.contains('-translate-x-full');

                if (isClosed) {
                    // Open Sidebar
                    sidebar.classList.remove('-translate-x-full');
                    overlay.classList.remove('opacity-0', 'pointer-events-none');
                } else {
                    // Close Sidebar
                    sidebar.classList.add('-translate-x-full');
                    overlay.classList.add('opacity-0', 'pointer-events-none');
                }
            }

            // --- Event Listeners ---
            if (toggleBtn) toggleBtn.addEventListener('click', toggleSidebar);
            if (overlay) overlay.addEventListener('click', toggleSidebar);

            // Close sidebar when clicking a link inside it (Mobile UX improvement)
            sidebar.querySelectorAll('a').forEach(link => {
                link.addEventListener('click', () => {
                    if (window.innerWidth < 768) toggleSidebar();
                });
            });

            // Handle Screen Resizing (Reset state if moving from mobile to desktop)
            window.addEventListener('resize', () => {
                if (window.innerWidth >= 768) {
                    // Desktop: Sidebar always visible
                    sidebar.classList.remove('-translate-x-full');
                    overlay.classList.add('opacity-0', 'pointer-events-none');
                } else {
                    // Mobile: Ensure sidebar starts closed when resizing down
                    sidebar.classList.add('-translate-x-full');
                }
            });

            // --- Permission Request ---
            if ("Notification" in window && Notification.permission !== 'granted') {
                Notification.requestPermission();
            }
        });

        // --- Notification Helpers ---
        function playNotificationSound() {
            const sound = document.getElementById('notificationSound');
            if (sound) {
                sound.currentTime = 0;
                sound.play().catch(e => console.log("Audio autoplay prevented"));
            }
        }

        function showBookingNotification(title, message) {
            if (notificationCooldown) return;

            notificationCooldown = true;
            setTimeout(() => { notificationCooldown = false; }, 3000);

            playNotificationSound();

            // Native Browser Notification
            if ("Notification" in window && Notification.permission === 'granted') {
                new Notification(title, { body: message, icon: '/favicon.ico' });
            }

            // Toastr Notification
            if (typeof toastr !== 'undefined') {
                toastr.info(message, title, {
                    timeOut: 5000,
                    closeButton: true,
                    progressBar: true,
                    preventDuplicates: true,
                    positionClass: "toast-top-right"
                });
            }
        }

        // --- Real-time Listeners (Echo) ---

        // 1. Unread Counts (Debounced)
        let debounceTimer;
        if (window.Echo) {
            window.Echo.channel('unread-counts')
                .listen('.counts.updated', (e) => {
                    if (e.id && e.id === lastEventId) return;
                    lastEventId = e.id;

                    clearTimeout(debounceTimer);
                    debounceTimer = setTimeout(() => { updateAllBadges(e.counts); }, 300);
                });

            // 2. New Booking Alerts
            window.Echo.channel('bookings')
                .listen('.booking.created', (e) => {
                    if (e.booking.id && e.booking.id === lastBookingId) return;
                    lastBookingId = e.booking.id;

                    showBookingNotification(
                        'New Booking',
                        `New booking from ${e.booking.user.firstname} (ID: ${e.booking.id})`
                    );
                });
        }

        // --- UI Updaters ---
        function updateAllBadges(counts) {
            // Update "New" badge in sidebar
            const badge = document.getElementById('inquiries-badge');
            if (badge) {
                if (counts.inquiriesCount > 0) {
                    badge.innerHTML = `${counts.inquiriesCount} new`;
                    badge.classList.remove('hidden');
                } else {
                    badge.classList.add('hidden');
                }
            }
            hideLoadingIndicators();
        }

        function hideLoadingIndicators() {
            document.querySelectorAll('.badge-loading').forEach(el => el.style.display = 'none');
        }
    </script>

    @yield('content_js')
</body>

</html>