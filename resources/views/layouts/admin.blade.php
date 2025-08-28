<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>@yield('title', 'Mt. Claramuel Resort & Events Place')</title>
    
    <!-- Fonts and Styles -->
    <link rel="icon" href="{{ asset('/favicon.ico?v=2') }}">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
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
        /* Loading state for badges */
        .badge-loading {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 2px solid rgba(255,255,255,0.3);
            border-radius: 50%;
            border-top-color: #fff;
            animation: spin 1s ease-in-out infinite;
        }
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
    </style>
    
    @yield('content_css')
</head>
<body class="bg-gray-100">
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

    <!-- Mobile sidebar overlay -->
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
                    
                    <div class="flex items-center space-x-6">
                        <!-- Email Icon with Badge -->
                        <a href="{{ route('admin.email') }}" class="relative text-white hover:text-gray-200 transition-colors" title="Email Inbox">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                            <span id="emailBadge" class="absolute -top-2 -right-2 bg-yellow-400 text-xs text-gray-900 font-bold rounded-full h-5 w-5 flex items-center justify-center">
                                <span class="badge-loading"></span>
                            </span>
                        </a>
                        
                        <!-- User Profile Dropdown -->
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
            // Initial load of all counts
            getAllUnreadCounts();
            
            // Sidebar toggle functionality
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
            
            // Event listeners for sidebar
            toggleSidebarMobile.addEventListener('click', toggleSidebar);
            sidebarClose.addEventListener('click', toggleSidebar);
            sidebarOverlay.addEventListener('click', function() {
                if (!sidebarOverlay.classList.contains('opacity-0')) {
                    toggleSidebar();
                }
            });
            
            // Close sidebar when clicking nav links (mobile)
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
            
            // Request notification permission
            if (Notification.permission !== 'granted') {
                Notification.requestPermission().then(permission => {
                    console.log('Notification permission:', permission);
                });
            }
        });

        // Function to fetch all unread counts
        function getAllUnreadCounts() {
            showLoadingIndicators();
            
            fetch(`/unread-counts/all`, {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                updateAllBadges(data);
            })
            .catch(error => {
                console.error('Error fetching unread counts:', error);
                hideLoadingIndicators();
                // Optionally show error to user
                toastr.error('Failed to load notification counts', 'Error');
            });
        }

        // Function to update all badges
        function updateAllBadges(counts) {
            console.log('Updating badges with:', counts);
            
            // Update email badge
            const emailBadge = document.getElementById('emailBadge');
            if (emailBadge) {
                if (counts.emailBadgeCount > 0) {
                    emailBadge.innerHTML = counts.emailBadgeCount;
                    emailBadge.classList.remove('hidden');
                } else {
                    emailBadge.classList.add('hidden');
                }
            }
            
            // Update sidebar badges
            const inquiriesBadge = document.getElementById('inquiries-badge');
            const paymentBadge = document.getElementById('payment-badge');
            
            if (inquiriesBadge) {
                if (counts.inquiriesCount > 0) {
                    inquiriesBadge.innerHTML = `${counts.inquiriesCount} new`;
                    inquiriesBadge.classList.remove('hidden');
                } else {
                    inquiriesBadge.classList.add('hidden');
                }
            }
            
            if (paymentBadge) {
                if (counts.paymentCount > 0) {
                    paymentBadge.innerHTML = `${counts.paymentCount} new`;
                    paymentBadge.classList.remove('hidden');
                } else {
                    paymentBadge.classList.add('hidden');
                }
            }
            
            hideLoadingIndicators();
        }

        // Loading state management
        function showLoadingIndicators() {
            document.querySelectorAll('.badge-loading').forEach(el => {
                el.style.display = 'inline-block';
            });
        }

        function hideLoadingIndicators() {
            document.querySelectorAll('.badge-loading').forEach(el => {
                el.style.display = 'none';
            });
        }

        // Real-time updates with debouncing
        let debounceTimer;
        window.Echo.channel('unread-counts')
            .listen('.counts.updated', (e) => {
                console.log('Real-time update received:', e);
                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(() => {
                    updateAllBadges(e.counts);
                }, 300); // 300ms debounce to prevent rapid updates
            });

        // Booking notifications
        window.Echo.channel('bookings')
            .listen('.booking.created', (e) => {
                console.log('New booking received:', e);
                
                // Show desktop notification
                if (Notification.permission === 'granted') {
                    new Notification('New Booking', {
                        body: `New booking from ${e.booking.user.firstname} ${e.booking.user.lastname} (ID: ${e.booking.id})`,
                        icon: '/favicon.ico'
                    });
                }
                
                // Show toast notification
                toastr.info(`New booking from ${e.booking.user.firstname} ${e.booking.user.lastname}`, 'New Booking', {
                    timeOut: 5000,
                    closeButton: true,
                    progressBar: true
                });
                
                // Refresh counts
                getAllUnreadCounts();
            });
    </script>
    
    @yield('content_js')
</body>
</html>