<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>@yield('title', 'Mt. Claramuel Resort & Events Place')</title>
    
    <!-- Fonts and Styles -->
    <link rel="icon" href="{{ asset('/favicon.ico?v=2') }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    @vite(['resources/js/app.js'])
    <!-- In your layout file's head or before the closing body tag -->
        <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        lightGray: '#D3D3D3',
                        darkGray: '#A0A0A0',
                        backgroundLightGray: '#f6f6f6'
                    },
                }
            }
        }
    </script>
    
    <style>
        /* Smooth transitions for overlay */
        #sidebar-overlay {
            transition: opacity 0.3s ease;
        }
        #sidebar {
            transition: transform 0.3s ease;
        }
        /* Ensure content doesn't shift when scrollbar appears/disappears */
        html {
            overflow-y: scroll;
        }
        .red-gradient-header {
            background: linear-gradient(135deg, #ff0000, #cc0000);
        }
    </style>
    
    @yield('content_css')
</head>
<body class="bg-backgroundLightGray">
    <!-- Notification sound element -->
    <!-- <audio id="notificationSound" src="{{ asset('sounds/mixkit-software-interface-back-2575.wav') }}" preload="auto"></audio> -->
    
    <!-- Mobile Header -->
    <div class="md:hidden mt-1 fixed top-0 left-0 right-0 bg-white shadow-md z-50 flex items-center justify-between p-4 h-16">
        <button class="text-red-600 text-2xl focus:outline-none" id="toggleSidebarMobile" aria-label="Toggle sidebar">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
        </button>

        <div class="flex items-center space-x-2">
            <span class="text-gray-800 font-semibold text-lg whitespace-nowrap truncate max-w-[160px]">
                <a href="{{ route('index') }}"></a>
            </span>
        </div>

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

            <div class="absolute right-0 mt-2 w-48 bg-white border border-gray-200 rounded-md shadow-lg py-1 z-50 opacity-0 invisible group-focus-within:opacity-100 group-focus-within:visible transition-all duration-200">
                <a href="{{ route('profile.edit') }}"
                class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors">
                Profile
                </a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                            class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors">
                            Logout
                    </button>
                </form>
            </div>
            @endauth
        </div>
    </div>
    
    <div class="md:hidden h-16"></div>
    @include('admin.sidebar')

    <!-- Mobile sidebar overlay - now with lighter color and smooth transition -->
    <div class="fixed inset-0 bg-black bg-opacity-50 z-30 md:hidden pointer-events-none opacity-0 transition-all duration-300" id="sidebar-overlay"></div>
    
    <!-- Main Content Area -->
    <div class="flex min-h-screen">
        <div class="flex-1 flex flex-col md:ml-64">
            <!-- Desktop Header -->
            <header class="hidden md:block bg-gradient-to-r from-red-600 to-red-800 text-white shadow-md sticky top-0 z-50">
                <div class="px-6 py-6 flex justify-between items-center">
                    <div class="flex items-center space-x-3">
                        <span class="text-2xl font-bold">
                            <a href="{{ route('index') }}"></a>
                        </span>
                    </div>
                    
                    <div class="relative group">
                        @auth
                        <button class="flex items-center gap-2 focus:outline-none">
                            <img src="{{ url('imgs/profiles/' . Auth::user()->profile_img) }}"
                                class="h-8 w-8 rounded-full object-cover"
                                alt="Profile">
                            <span class="text-sm font-medium text-white">{{ Auth::user()->firstname }}</span>
                            <svg class="w-4 h-4 text-gray-300" fill="none" stroke="currentColor" stroke-width="2"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>

                        <div class="absolute right-0 mt-2 w-48 bg-white border border-gray-200 rounded-md shadow-lg py-1 z-50 opacity-0 invisible group-focus-within:opacity-100 group-focus-within:visible transition-all duration-200">
                            <a href="{{ route('profile.edit') }}"
                                class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors">Profile</a>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit"
                                    class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors">Logout</button>
                            </form>
                        </div>
                        @endauth
                    </div>
                </div>
            </header>
            
            <!-- Page Content -->
            <main class="flex-1 p-2 md:p-2 overflow-x-hidden md:rounded-tl-lg">
                @yield('content')
            </main>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/flowbite@3.1.2/dist/flowbite.min.js"></script>
    
    <script type="module">

            
        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.getElementById('sidebar');
            const toggleSidebarMobile = document.getElementById('toggleSidebarMobile');
            const sidebarClose = document.getElementById('sidebar-close');
            const sidebarOverlay = document.getElementById('sidebar-overlay');
            
            function toggleSidebar() {
                sidebar.classList.toggle('-translate-x-full');
                sidebarOverlay.classList.toggle('opacity-0');
                sidebarOverlay.classList.toggle('pointer-events-none');
                
                if (sidebarOverlay.classList.contains('opacity-0')) {
                    document.body.classList.remove('overflow-hidden');
                } else {
                    document.body.classList.add('overflow-hidden');
                }
            }

            
            // Mobile header toggle button
            toggleSidebarMobile.addEventListener('click', toggleSidebar);
            
            // Sidebar close button
            sidebarClose.addEventListener('click', toggleSidebar);
            
            // Close when clicking overlay
            sidebarOverlay.addEventListener('click', function() {
                if (!sidebarOverlay.classList.contains('opacity-0')) {
                    toggleSidebar();
                }
            });
            
            // Close when clicking on a nav link (mobile)
            document.querySelectorAll('#sidebar a').forEach(link => {
                link.addEventListener('click', () => {
                    if (window.innerWidth < 768) toggleSidebar();
                });
            });
            
            // Handle window resize
            window.addEventListener('resize', function() {
                if (window.innerWidth >= 768) {
                    sidebar.classList.remove('-translate-x-full');
                    sidebarOverlay.classList.add('opacity-0');
                    sidebarOverlay.classList.add('pointer-events-none');
                    document.body.classList.remove('overflow-hidden');
                }
            });
        });
        
        window.Echo.channel('bookings')
            .listen('.booking.created', (e) => {
                console.log('New booking received:', e);
                
                // Show notification if permission is granted
                if (Notification.permission === 'granted') {
                    new Notification('New Booking', {
                        body: `${e.booking.user.firstname} ${e.booking.user.lastname} made a booking: ID= ${e.booking.id}`,
                    });
                }
            });
        // Request notification permission
        if (Notification.permission !== 'granted') {
            Notification.requestPermission();
        }
        
    </script>
    
    @yield('content_js')
    
</body>
</html>