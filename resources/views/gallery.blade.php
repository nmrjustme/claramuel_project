<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gallery - Mt. ClaRamuel Resort & Events Place</title>
    <meta name="description" content="Explore our stunning gallery showcasing luxury accommodations, event venues, and breathtaking views at Mt. ClaRamuel Resort in Isabela, Philippines.">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#1a365d',
                        secondary: '#e53e3e',
                        accent: '#990F02',
                        darkAccent: '#950606'
                    },
                    fontFamily: {
                        serif: ['Georgia', 'serif'],
                        sans: ['"Open Sans"', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;500;600;700&display=swap');

        body {
            font-family: 'Open Sans', sans-serif;
        }

        .section-title {
            position: relative;
            display: inline-block;
        }

        .section-title:after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 0;
            width: 50%;
            height: 3px;
            background: #e53e3e;
        }

        /* Animation Styles */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-fadeInUp {
            animation: fadeInUp 0.6s ease-out forwards;
        }

        /* Gallery card hover animation */
        .gallery-card {
            transition: all 0.3s ease;
        }

        .gallery-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }

        /* Gallery image hover effect */
        .gallery-item img {
            transition: transform 0.5s ease, opacity 0.3s ease;
        }

        .gallery-item:hover img {
            transform: scale(1.05);
        }

        /* Button pulse animation */
        @keyframes pulse {
            0% {
                transform: scale(1);
            }
            50% {
                transform: scale(1.05);
            }
            100% {
                transform: scale(1);
            }
        }

        .btn-pulse:hover {
            animation: pulse 1.5s infinite;
        }

        /* Fixed aspect ratio containers */
        .aspect-container {
            position: relative;
            width: 100%;
        }
        .aspect-4-3::before {
            content: '';
            display: block;
            padding-top: 75%; /* 4:3 Aspect Ratio */
        }
        .aspect-4-3 > * {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
        }

        /* Line clamp utilities */
        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .line-clamp-1 {
            display: -webkit-box;
            -webkit-line-clamp: 1;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        /* Lightbox styles matching landing page */
        .lightbox-control {
            background: rgba(0, 0, 0, 0.7) !important;
            backdrop-filter: blur(12px) !important;
            border: 1px solid rgba(255, 255, 255, 0.15) !important;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3) !important;
            transition: all 0.2s ease !important;
        }

        .lightbox-control:hover {
            background: rgba(0, 0, 0, 0.9) !important;
            transform: scale(1.1) !important;
            border-color: rgba(255, 255, 255, 0.3) !important;
        }

        /* Loading animation */
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .animate-spin {
            animation: spin 1s linear infinite;
        }
    </style>
</head>

<body class="bg-white text-gray-700">
    <!-- Header -->
    <header id="main-header"
        class="bg-gradient-to-r from-red-900 to-red-600 text-white fixed w-full shadow-lg z-50 transition-all duration-300">
        <div class="container mx-auto px-6 py-3 flex justify-between items-center">
            <div class="flex items-center space-x-3">
                <img src="{{ url('imgs/logo.png') }}" class="h-12" alt="Mt. ClaRamuel Logo">
                <div>
                    <h1 class="text-xl font-bold leading-tight">
                        <span class="text-white">Mt. ClaRamuel</span>
                    </h1>
                    <p class="text-xs text-gray-300">Resort & Events Place</p>
                </div>
            </div>
            <nav class="hidden lg:flex space-x-6 items-center">
                <a href="{{ url('/') }}" class="hover:text-gray-300 transition duration-300 font-medium">Home</a>
                <a href="{{ url('/') }}#about" class="hover:text-gray-300 transition duration-300 font-medium">About</a>
                <a href="{{ url('/') }}#services" class="hover:text-gray-300 transition duration-300 font-medium">Services</a>
                <a href="{{ url('/gallery') }}" class="text-gray-300 font-semibold transition duration-300 font-medium">Gallery</a>
                <a href="{{ url('/') }}#contact" class="hover:text-gray-300 transition duration-300 font-medium">Contact</a>
                <a href="{{ route('login') }}" class="hover:text-gray-300 transition duration-300 font-medium">Login</a>
                <a href="{{ route('dashboard.bookings') }}"
                    class="bg-accent hover:bg-darkAccent text-white px-5 py-2 rounded-sm transition duration-300 font-medium flex items-center btn-pulse">
                    <i class="fas fa-calendar-check mr-2"></i> Book Now
                </a>
            </nav>
            <button id="mobile-menu-button" class="lg:hidden text-white focus:outline-none">
                <i class="fas fa-bars text-2xl"></i>
            </button>
        </div>
    </header>

    <!-- Mobile Menu -->
    <div id="mobile-menu"
        class="lg:hidden fixed top-0 left-0 w-full h-full bg-gray-800/95 z-40 pt-20 px-6 transform translate-x-full transition-transform duration-300 backdrop-blur-sm">
        <div class="flex flex-col space-y-5 py-6">
            <a href="{{ url('/') }}"
                class="text-white hover:text-gray-300 text-lg transition border-b border-gray-700 pb-3">Home</a>
            <a href="{{ url('/') }}#about"
                class="text-white hover:text-gray-300 text-lg transition border-b border-gray-700 pb-3">About</a>
            <a href="{{ url('/') }}#services"
                class="text-white hover:text-gray-300 text-lg transition border-b border-gray-700 pb-3">Services</a>
            <a href="{{ url('/gallery') }}"
                class="text-gray-300 font-semibold text-lg transition border-b border-gray-700 pb-3">Gallery</a>
            <a href="{{ url('/') }}#contact"
                class="text-white hover:text-gray-300 text-lg transition border-b border-gray-700 pb-3">Contact</a>
            <a href="{{ route('login') }}"
                class="text-white hover:text-gray-300 text-lg transition border-b border-gray-700 pb-3">Login</a>
            <a href="{{ route('dashboard.bookings') }}"
                class="bg-accent hover:bg-darkAccent text-white px-6 py-3 rounded-sm transition flex items-center justify-center text-lg mt-4 btn-pulse">
                <i class="fas fa-calendar-check mr-2"></i> Book Now
            </a>
        </div>
    </div>

    <!-- Hero Section -->
    <section class="pt-32 pb-20 bg-gradient-to-r from-red-900 to-red-600 text-white">
        <div class="container mx-auto px-6 text-center">
            <h1 class="text-3xl md:text-5xl lg:text-6xl font-bold mb-6 font-serif animate-fadeInUp">
                Our Gallery
            </h1>
            <p class="text-lg md:text-xl mb-8 max-w-2xl mx-auto leading-relaxed animate-fadeInUp"
                style="animation-delay: 0.2s;">
                Immerse yourself in the visual journey of Mt. ClaRamuel Resort. Explore our luxury accommodations, event venues, and breathtaking surroundings.
            </p>
            <div class="animate-fadeInUp" style="animation-delay: 0.4s;">
                <a href="{{ route('dashboard.bookings') }}"
                    class="inline-block bg-accent hover:bg-darkAccent text-white font-semibold px-8 py-3 rounded-sm shadow-lg transition duration-300 btn-pulse">
                    Book Your Stay
                </a>
            </div>
        </div>
    </section>

    <!-- Gallery Section -->
    <section class="py-20 bg-white">
        <div class="container mx-auto px-6">
            <!-- Page Header -->
            <div class="text-center mb-16 animate-fadeInUp">
                <h2 class="text-2xl md:text-4xl text-darkAccent mb-6 font-serif font-light">
                    𝐸𝓍𝓅𝓁𝑜𝓇𝑒 𝑜𝓊𝓇 𝒷𝓇𝑒𝒶𝓉𝒽𝓉𝒶𝓀𝒾𝓃𝑔 𝓈𝓅𝒶𝒸𝑒𝓈
                </h2>
                <p class="text-gray-600 max-w-3xl mx-auto text-lg leading-relaxed">
                    Discover the beauty and elegance of Mt. ClaRamuel through our curated collection of images showcasing our facilities, accommodations, and natural surroundings.
                </p>
            </div>

            <!-- Category Filters -->
            <div class="flex flex-wrap justify-center gap-3 mb-12 animate-fadeInUp" style="animation-delay: 0.2s;">
                <button class="filter-btn active px-6 py-3 rounded-full bg-darkAccent text-white transition-all duration-300 shadow-lg hover:shadow-xl transform hover:scale-105 font-medium" 
                        data-category="all">
                    <i class="fas fa-images mr-2"></i> All Photos
                </button>
                
                @foreach($categories as $category)
                    @if(!empty($category))
                    <button class="filter-btn px-6 py-3 rounded-full border-2 border-darkAccent text-darkAccent transition-all duration-300 hover:bg-darkAccent hover:text-white transform hover:scale-105 font-medium" 
                            data-category="{{ $category }}">
                        {{ ucfirst($category) }}
                    </button>
                    @endif
                @endforeach
            </div>

            <!-- Full Gallery Sections -->
            <div class="space-y-20" id="full-gallery">
                @foreach($galleries as $gallery)
                <div class="gallery-section animate-fadeInUp" data-category="{{ $gallery->category }}">
                    <!-- Gallery Header -->
                    <div class="text-center mb-12">
                        <h2 class="text-3xl md:text-4xl font-serif text-darkAccent mb-4">{{ $gallery->title }}</h2>
                        @if($gallery->description)
                        <p class="text-gray-600 text-lg max-w-2xl mx-auto">{{ $gallery->description }}</p>
                        @endif
                        <div class="w-24 h-1 bg-gradient-to-r from-darkAccent to-amber-400 mx-auto mt-6 rounded-full"></div>
                    </div>

                    <!-- Images Grid -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                        @foreach($gallery->images as $image)
                        <div class="group relative overflow-hidden rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-500 cursor-pointer transform hover:scale-[1.02] bg-gray-100 gallery-item"
                             data-category="{{ $gallery->category }}"
                             onclick="openLightbox('{{ asset('storage/' . $image->image_path) }}', '{{ addslashes($image->title) }}', '{{ addslashes($image->caption ?? '') }}', {{ $loop->index + 8 }})">
                            
                            <!-- Fixed aspect ratio container -->
                            <div class="aspect-container aspect-4-3">
                                <img src="{{ asset('storage/' . $image->image_path) }}" 
                                     alt="{{ $image->image_alt ?? $image->title }}"
                                     class="w-full h-full object-cover transform group-hover:scale-110 transition-transform duration-700"
                                     loading="lazy">
                            </div>
                            
                            <!-- Gradient Overlay -->
                            <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/30 to-transparent opacity-0 group-hover:opacity-100 transition-all duration-500 flex flex-col justify-between p-4 md:p-6">
                                <!-- Top Badges -->
                                <div class="flex justify-between items-start">
                                    @if($image->is_featured)
                                    <span class="bg-gradient-to-r from-amber-400 to-amber-500 text-white px-3 py-1.5 rounded-full text-xs font-bold shadow-lg backdrop-blur-sm">
                                        <i class="fas fa-star mr-1"></i> Featured
                                    </span>
                                    @endif
                                    <span class="bg-white/20 backdrop-blur-sm text-white px-3 py-1.5 rounded-full text-xs font-medium">
                                        {{ $gallery->category }}
                                    </span>
                                </div>
                                
                                <!-- Bottom Content -->
                                <div class="transform translate-y-6 group-hover:translate-y-0 transition-transform duration-500">
                                    <h3 class="text-white font-semibold text-lg mb-2 drop-shadow-2xl line-clamp-1">{{ $image->title }}</h3>
                                    @if($image->caption)
                                    <p class="text-white/90 text-sm leading-relaxed drop-shadow-2xl line-clamp-2">
                                        {{ $image->caption }}
                                    </p>
                                    @endif
                                    <div class="flex items-center mt-3 text-white/80 text-xs">
                                        <i class="fas fa-expand mr-2"></i>
                                        Click to view
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>

                    @if($gallery->images->isEmpty())
                    <!-- Empty Gallery State -->
                    <div class="text-center py-16 bg-gradient-to-br from-gray-50 to-gray-100 rounded-2xl border-2 border-dashed border-gray-300">
                        <i class="fas fa-camera text-gray-400 text-5xl mb-4"></i>
                        <h3 class="text-xl font-semibold text-gray-500 mb-2">Coming Soon</h3>
                        <p class="text-gray-400 max-w-sm mx-auto">We're preparing amazing photos for this gallery</p>
                    </div>
                    @endif
                </div>
                @endforeach

                @if($galleries->isEmpty())
                <!-- Empty State -->
                <div class="text-center py-20 animate-fadeInUp">
                    <div class="bg-gradient-to-br from-gray-50 to-gray-100 rounded-3xl p-12 max-w-2xl mx-auto border-2 border-dashed border-gray-300">
                        <i class="fas fa-camera text-gray-400 text-6xl mb-6"></i>
                        <h2 class="text-3xl font-serif text-gray-500 mb-4">Gallery Coming Soon</h2>
                        <p class="text-gray-400 text-lg mb-8 max-w-md mx-auto">
                            We're preparing an amazing visual experience for you. Check back soon to explore our resort!
                        </p>
                        <div class="mt-8">
                            <a href="{{ url('/') }}" class="inline-flex items-center px-8 py-4 bg-darkAccent text-white rounded-full hover:bg-opacity-90 transition-all duration-300 transform hover:scale-105 shadow-lg">
                                <i class="fas fa-home mr-3"></i>
                                Return to Homepage
                            </a>
                        </div>
                    </div>
                </div>
                @endif
            </div>

            <!-- Back to Top -->
            <div class="text-center mt-16 animate-fadeInUp">
                <button onclick="scrollToTop()" 
                        class="inline-flex items-center px-8 py-4 border-2 border-darkAccent text-darkAccent rounded-full hover:bg-darkAccent hover:text-white transition-all duration-300 transform hover:scale-105 font-medium group">
                    <i class="fas fa-arrow-up mr-3 transform group-hover:-translate-y-1 transition-transform duration-300"></i>
                    Back to Top
                </button>
            </div>
        </div>
    </section>

    <!-- Enhanced Lightbox - EXACTLY like landing page -->
    <div id="lightbox" class="fixed inset-0 bg-black/95 z-50 hidden items-center justify-center p-4 backdrop-blur-sm">
        <div class="relative w-full h-full flex items-center justify-center">
            <!-- Close Button - Fixed Position -->
            <button onclick="closeLightbox()" 
                    class="fixed top-6 right-6 z-50 text-white hover:text-gray-300 transition-all duration-200 lightbox-control rounded-full p-3"
                    title="Close (Esc)">
                <i class="fas fa-times"></i>
            </button>
            
            <!-- Navigation Arrows - Fixed Positions -->
            <button id="lightbox-prev" 
                    onclick="navigateLightbox(-1)"
                    class="fixed left-6 top-1/2 transform -translate-y-1/2 z-50 text-white hover:text-gray-300 transition-all duration-200 lightbox-control rounded-full p-4"
                    title="Previous (←)">
                <i class="fas fa-chevron-left"></i>
            </button>
            
            <button id="lightbox-next" 
                    onclick="navigateLightbox(1)"
                    class="fixed right-6 top-1/2 transform -translate-y-1/2 z-50 text-white hover:text-gray-300 transition-all duration-200 lightbox-control rounded-full p-4"
                    title="Next (→)">
                <i class="fas fa-chevron-right"></i>
            </button>

            <!-- Toggle Caption Button -->
            <button id="toggle-caption" 
                    onclick="toggleCaption()"
                    class="fixed top-6 left-6 z-50 text-white hover:text-gray-300 transition-all duration-200 lightbox-control rounded-full p-3"
                    title="Toggle caption (C)">
                <i id="caption-show-icon" class="fas fa-info-circle"></i>
                <i id="caption-hide-icon" class="fas fa-eye-slash hidden"></i>
            </button>

            <!-- Image Container -->
            <div id="lightbox-container" class="relative flex items-center justify-center max-w-[90vw] max-h-[85vh]">
                <!-- Loading Spinner -->
                <div id="lightbox-loading" class="absolute inset-0 flex items-center justify-center z-10 hidden">
                    <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-white"></div>
                </div>
                
                <!-- Main Image -->
                <img id="lightbox-image" 
                     src="" 
                     alt="" 
                     class="max-w-[85vw] max-h-[80vh] w-auto h-auto rounded-lg shadow-2xl transition-all duration-300 cursor-zoom-in"
                     style="object-fit: contain;"
                     onload="handleImageLoad(this)"
                     onerror="handleImageError(this)">
            </div>
            
            <!-- Caption Panel -->
            <div id="lightbox-caption-panel" class="fixed bottom-6 left-1/2 transform -translate-x-1/2 bg-black/70 backdrop-blur-sm rounded-xl p-4 md:p-6 max-w-2xl w-full mx-4 transition-all duration-300 opacity-100 border border-white/10">
                <h3 id="lightbox-title" class="text-white text-lg md:text-xl font-semibold mb-2 text-center"></h3>
                <p id="lightbox-caption" class="text-gray-300 text-sm md:text-base leading-relaxed text-center"></p>
                
                <!-- Image Info -->
                <div class="flex justify-center items-center mt-3 text-gray-400 text-xs md:text-sm space-x-4">
                    <span id="image-dimensions" class="flex items-center">
                        <i class="fas fa-expand-arrows-alt mr-1"></i>
                        <span id="dimension-text">Loading...</span>
                    </span>
                    <span>•</span>
                    <span id="image-index" class="font-medium"></span>
                    <span>•</span>
                    <span id="image-orientation" class="capitalize"></span>
                </div>
            </div>

            <!-- Zoom Controls -->
            <div class="fixed bottom-24 right-6 flex flex-col space-y-2 z-50">
                <button id="zoom-in" onclick="zoomImage(0.1)"
                        class="lightbox-control text-white rounded-full p-3"
                        title="Zoom In (+)">
                    <i class="fas fa-search-plus"></i>
                </button>
                <button id="zoom-out" onclick="zoomImage(-0.1)"
                        class="lightbox-control text-white rounded-full p-3"
                        title="Zoom Out (-)">
                    <i class="fas fa-search-minus"></i>
                </button>
                <button id="reset-zoom" onclick="resetZoom()"
                        class="lightbox-control text-white rounded-full p-3 text-xs font-medium"
                        title="Reset Zoom (0)">
                    1:1
                </button>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white pt-16 pb-8">
        <div class="container mx-auto px-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-12 mb-12">
                <div>
                    <div class="flex items-center mb-6 animate-fadeInUp">
                        <img src="{{ url('imgs/logo.png') }}" class="h-12 mr-3" alt="Logo">
                        <span class="text-xl font-bold">Mt. ClaRamuel Resort</span>
                    </div>
                    <p class="text-gray-400 mb-6 animate-fadeInUp text-sm" style="animation-delay: 0.2s;">
                        Experience the Ultimate STAYCATION in Ilagan City — a premier destination in Isabela offering luxury accommodations, relaxing amenities, and exceptional event venues surrounded by breathtaking natural beauty.
                    </p>
                    <div class="flex gap-4 animate-fadeInUp" style="animation-delay: 0.3s;">
                        <a href="https://www.facebook.com/mtclaramuelresort" target="_blank"
                            class="text-gray-400 hover:text-white transition">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a href="https://www.instagram.com/mt_claramuelresort/" target="_blank"
                            class="text-gray-400 hover:text-white transition">
                            <i class="fab fa-instagram"></i>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-white transition">
                            <i class="fab fa-twitter"></i>
                        </a>
                    </div>
                </div>

                <div>
                    <h3 class="text-lg font-semibold mb-6 border-b border-gray-700 pb-2 animate-fadeInUp"
                        style="animation-delay: 0.4s;">Quick Links</h3>
                    <ul class="space-y-3">
                        <li class="animate-fadeInUp" style="animation-delay: 0.5s;"><a href="{{ url('/') }}"
                                class="text-gray-400 hover:text-accent transition">Home</a></li>
                        <li class="animate-fadeInUp" style="animation-delay: 0.6s;"><a href="{{ url('/') }}#about"
                                class="text-gray-400 hover:text-accent transition">About Us</a></li>
                        <li class="animate-fadeInUp" style="animation-delay: 0.7s;"><a href="{{ url('/') }}#services"
                                class="text-gray-400 hover:text-accent transition">Services</a></li>
                        <li class="animate-fadeInUp" style="animation-delay: 0.8s;"><a href="{{ url('/gallery') }}"
                                class="text-gray-400 hover:text-accent transition">Gallery</a></li>
                        <li class="animate-fadeInUp" style="animation-delay: 0.9s;"><a href="{{ url('/') }}#contact"
                                class="text-gray-400 hover:text-accent transition">Contact</a></li>
                    </ul>
                </div>

                <div>
                    <h3 class="text-lg font-semibold mb-6 border-b border-gray-700 pb-2 animate-fadeInUp"
                        style="animation-delay: 0.4s;">Services</h3>
                    <ul class="space-y-3">
                        <li class="animate-fadeInUp" style="animation-delay: 0.5s;"><a href="#"
                                class="text-gray-400 hover:text-accent transition">Accommodations</a></li>
                        <li class="animate-fadeInUp" style="animation-delay: 0.6s;"><a href="#"
                                class="text-gray-400 hover:text-accent transition">Event Hosting</a></li>
                        <li class="animate-fadeInUp" style="animation-delay: 0.7s;"><a href="#"
                                class="text-gray-400 hover:text-accent transition">Recreational Activities</a></li>
                        <li class="animate-fadeInUp" style="animation-delay: 0.8s;"><a href="#"
                                class="text-gray-400 hover:text-accent transition">Dining</a></li>
                        <li class="animate-fadeInUp" style="animation-delay: 0.9s;"><a href="#"
                                class="text-gray-400 hover:text-accent transition">Special Packages</a></li>
                    </ul>
                </div>

                <div>
                    <h3 class="text-lg font-semibold mb-6 border-b border-gray-700 pb-2 animate-fadeInUp"
                        style="animation-delay: 0.4s;">Contact Info</h3>
                    <ul class="space-y-3 text-gray-400">
                        <li class="flex items-start animate-fadeInUp" style="animation-delay: 0.5s;">
                            <i class="fas fa-map-marker-alt text-accent mr-3 mt-1"></i>
                            Narra Street, Brgy. Marana 3rd, Ilagan, Isabela
                        </li>
                        <li class="flex items-center animate-fadeInUp" style="animation-delay: 0.6s;">
                            <i class="fas fa-phone text-accent mr-3"></i>
                            <a href="tel:+639952901333" class="hover:text-accent transition">+63 995 290 1333</a>
                        </li>
                        <li class="flex items-center animate-fadeInUp" style="animation-delay: 0.7s;">
                            <i class="fas fa-envelope text-accent mr-3"></i>
                            <a href="mailto:mtclaramuelresort@gmail.com"
                                class="hover:text-accent transition">mtclaramuelresort@gmail.com</a>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="border-t border-gray-800 pt-8 flex flex-col md:flex-row justify-between items-center">
                <p class="text-gray-500 text-sm mb-4 md:mb-0 animate-fadeInUp">
                    © 2025 Mt. ClaRamuel Resort. All rights reserved.
                </p>
                <div class="flex gap-6 animate-fadeInUp" style="animation-delay: 0.2s;">
                    <a href="#" class="text-gray-500 hover:text-accent transition text-sm">Privacy Policy</a>
                    <a href="#" class="text-gray-500 hover:text-accent transition text-sm">Terms of Service</a>
                    <a href="#" class="text-gray-500 hover:text-accent transition text-sm">Sitemap</a>
                </div>
            </div>
        </div>
    </footer>

    <!-- Back to Top Button -->
    <button id="back-to-top"
        class="fixed bottom-8 right-8 bg-accent text-white p-4 rounded-full shadow-lg opacity-0 invisible transition-all duration-300 z-50 hover:bg-red-600">
        <i class="fas fa-arrow-up"></i>
    </button>

 <script>
    // Lightbox variables
    let currentImages = [];
    let currentIndex = 0;
    let isLoading = false;
    let currentScale = 1;
    let isCaptionVisible = true;
    let captionTimeout = null;

    // Initialize lightbox with CURRENTLY VISIBLE gallery images
    function initLightbox() {
        const visibleGalleryItems = document.querySelectorAll('.gallery-item:not([style*="display: none"])');
        currentImages = Array.from(visibleGalleryItems).map((item, index) => {
            const onclick = item.getAttribute('onclick');
            const match = onclick.match(/openLightbox\('([^']+)', '([^']+)', '([^']*)', (\d+)\)/);
            if (match) {
                return {
                    src: match[1],
                    title: match[2].replace(/\\'/g, "'"),
                    caption: match[3].replace(/\\'/g, "'"),
                    element: item,
                    actualIndex: index // Use the actual visible index
                };
            }
            return null;
        }).filter(item => item !== null);
    }

    function openLightbox(src, title, caption, clickedIndex) {
        // Always reinitialize to get current visible images
        initLightbox();
        
        // Find the clicked image in the current visible images
        currentIndex = currentImages.findIndex(img => img.src === src);
        
        // Fallback: if not found, use the first image
        if (currentIndex === -1) {
            currentIndex = 0;
        }
        
        const image = currentImages[currentIndex];
        
        if (!image) {
            console.error('No image found to display');
            return;
        }
        
        resetZoom();
        showCaption();
        
        isLoading = true;
        document.getElementById('lightbox-loading').classList.remove('hidden');
        
        document.getElementById('lightbox-title').textContent = image.title;
        document.getElementById('lightbox-caption').textContent = image.caption || 'No description available';
        document.getElementById('image-index').textContent = `${currentIndex + 1} of ${currentImages.length}`;
        
        document.getElementById('lightbox').classList.remove('hidden');
        document.getElementById('lightbox').classList.add('flex');
        document.body.style.overflow = 'hidden';
        
        // Auto-hide caption after 3 seconds
        clearTimeout(captionTimeout);
        captionTimeout = setTimeout(() => {
            if (isCaptionVisible) {
                hideCaption();
            }
        }, 3000);
        
        const lightboxImage = document.getElementById('lightbox-image');
        lightboxImage.src = image.src;
        lightboxImage.alt = image.title;
    }

    function handleImageLoad(img) {
        isLoading = false;
        document.getElementById('lightbox-loading').classList.add('hidden');
        
        const naturalWidth = img.naturalWidth;
        const naturalHeight = img.naturalHeight;
        const orientation = naturalHeight > naturalWidth ? 'portrait' : 'landscape';
        
        document.getElementById('dimension-text').textContent = `${naturalWidth} × ${naturalHeight}`;
        document.getElementById('image-orientation').textContent = orientation;
        
        optimizeImageSize(img, orientation);
    }

    function handleImageError(img) {
        isLoading = false;
        document.getElementById('lightbox-loading').classList.add('hidden');
        document.getElementById('dimension-text').textContent = 'Dimensions unavailable';
        document.getElementById('image-orientation').textContent = 'unknown';
        
        // Show a fallback message
        document.getElementById('lightbox-caption').textContent = 'Failed to load image';
    }

    function optimizeImageSize(img, orientation) {
        const viewportWidth = window.innerWidth * 0.85;
        const viewportHeight = window.innerHeight * 0.80;
        
        let optimalWidth, optimalHeight;
        
        if (orientation === 'portrait') {
            optimalHeight = Math.min(viewportHeight, img.naturalHeight);
            optimalWidth = (optimalHeight / img.naturalHeight) * img.naturalWidth;
            
            if (optimalWidth > viewportWidth) {
                optimalWidth = viewportWidth;
                optimalHeight = (optimalWidth / img.naturalWidth) * img.naturalHeight;
            }
        } else {
            optimalWidth = Math.min(viewportWidth, img.naturalWidth);
            optimalHeight = (optimalWidth / img.naturalWidth) * img.naturalHeight;
            
            if (optimalHeight > viewportHeight) {
                optimalHeight = viewportHeight;
                optimalWidth = (optimalHeight / img.naturalHeight) * img.naturalWidth;
            }
        }
        
        img.style.width = optimalWidth + 'px';
        img.style.height = optimalHeight + 'px';
        img.style.maxWidth = 'none';
        img.style.maxHeight = 'none';
    }

    function toggleCaption() {
        if (isCaptionVisible) {
            hideCaption();
        } else {
            showCaption();
            
            clearTimeout(captionTimeout);
            captionTimeout = setTimeout(() => {
                hideCaption();
            }, 5000);
        }
    }

    function showCaption() {
        isCaptionVisible = true;
        const panel = document.getElementById('lightbox-caption-panel');
        panel.classList.remove('opacity-0', 'translate-y-8');
        panel.classList.add('opacity-100');
        document.getElementById('caption-show-icon').classList.add('hidden');
        document.getElementById('caption-hide-icon').classList.remove('hidden');
    }

    function hideCaption() {
        isCaptionVisible = false;
        const panel = document.getElementById('lightbox-caption-panel');
        panel.classList.remove('opacity-100');
        panel.classList.add('opacity-0', 'translate-y-8');
        document.getElementById('caption-show-icon').classList.remove('hidden');
        document.getElementById('caption-hide-icon').classList.add('hidden');
    }

    function zoomImage(zoomFactor) {
        const img = document.getElementById('lightbox-image');
        currentScale += zoomFactor;
        
        currentScale = Math.max(0.5, Math.min(3, currentScale));
        
        img.style.transform = `scale(${currentScale})`;
        img.style.cursor = currentScale > 1 ? 'grab' : 'zoom-in';
        
        if (currentScale > 1 && isCaptionVisible) {
            hideCaption();
        }
    }

    function resetZoom() {
        currentScale = 1;
        const img = document.getElementById('lightbox-image');
        img.style.transform = 'scale(1)';
        img.style.cursor = 'zoom-in';
    }

    function navigateLightbox(direction) {
        if (isLoading) return;
        
        currentIndex += direction;
        
        if (currentIndex < 0) {
            currentIndex = currentImages.length - 1;
        } else if (currentIndex >= currentImages.length) {
            currentIndex = 0;
        }
        
        const image = currentImages[currentIndex];
        if (image) {
            // Update lightbox with the new image
            isLoading = true;
            document.getElementById('lightbox-loading').classList.remove('hidden');
            
            document.getElementById('lightbox-title').textContent = image.title;
            document.getElementById('lightbox-caption').textContent = image.caption || 'No description available';
            document.getElementById('image-index').textContent = `${currentIndex + 1} of ${currentImages.length}`;
            
            const lightboxImage = document.getElementById('lightbox-image');
            lightboxImage.src = image.src;
            lightboxImage.alt = image.title;
        }
    }

    function closeLightbox() {
        document.getElementById('lightbox').classList.add('hidden');
        document.getElementById('lightbox').classList.remove('flex');
        document.body.style.overflow = 'auto';
        isLoading = false;
        resetZoom();
        clearTimeout(captionTimeout);
        
        showCaption();
    }

    // Event listeners for lightbox
    document.addEventListener('keydown', function(e) {
        if (!document.getElementById('lightbox').classList.contains('hidden')) {
            switch(e.key) {
                case 'Escape':
                    closeLightbox();
                    break;
                case 'ArrowLeft':
                    navigateLightbox(-1);
                    break;
                case 'ArrowRight':
                    navigateLightbox(1);
                    break;
                case '+':
                case '=':
                    e.preventDefault();
                    zoomImage(0.1);
                    break;
                case '-':
                    e.preventDefault();
                    zoomImage(-0.1);
                    break;
                case '0':
                    resetZoom();
                    break;
                case 'c':
                case 'C':
                    e.preventDefault();
                    toggleCaption();
                    break;
            }
        }
    });

    document.getElementById('lightbox').addEventListener('click', function(e) {
        if (e.target === this) {
            closeLightbox();
        }
    });

    // Show caption when hovering near bottom
    document.getElementById('lightbox').addEventListener('mousemove', function(e) {
        const viewportHeight = window.innerHeight;
        const mouseY = e.clientY;
        
        if (mouseY > viewportHeight * 0.8 && !isCaptionVisible) {
            showCaption();
            
            clearTimeout(captionTimeout);
            captionTimeout = setTimeout(() => {
                if (isCaptionVisible && currentScale <= 1) {
                    hideCaption();
                }
            }, 3000);
        }
    });

    // Mouse wheel zoom
    document.getElementById('lightbox-image').addEventListener('wheel', function(e) {
        e.preventDefault();
        const zoomDirection = e.deltaY > 0 ? -0.1 : 0.1;
        zoomImage(zoomDirection);
    });

    // Initialize
    document.addEventListener('DOMContentLoaded', function() {
        initLightbox();
        
        // Filter functionality
        const filterButtons = document.querySelectorAll('.filter-btn');
        const gallerySections = document.querySelectorAll('.gallery-section');

        filterButtons.forEach(button => {
            button.addEventListener('click', function() {
                const category = this.getAttribute('data-category');
                
                // Update active button
                filterButtons.forEach(btn => {
                    btn.classList.remove('active', 'bg-darkAccent', 'text-white');
                    btn.classList.add('border-2', 'border-darkAccent', 'text-darkAccent');
                });
                this.classList.add('active', 'bg-darkAccent', 'text-white');
                this.classList.remove('border-2', 'border-darkAccent', 'text-darkAccent');
                
                // Filter content
                gallerySections.forEach(section => {
                    if (category === 'all' || section.getAttribute('data-category') === category) {
                        section.style.display = 'block';
                        setTimeout(() => {
                            section.style.animation = 'fadeIn 0.6s ease-in';
                        }, 10);
                    } else {
                        section.style.display = 'none';
                    }
                });

                // Reinitialize lightbox with filtered images
                setTimeout(initLightbox, 100);
            });
        });
    });

    // Handle window resize
    window.addEventListener('resize', function() {
        if (!document.getElementById('lightbox').classList.contains('hidden')) {
            const img = document.getElementById('lightbox-image');
            if (img.complete && img.naturalWidth !== 0) {
                const orientation = img.naturalHeight > img.naturalWidth ? 'portrait' : 'landscape';
                optimizeImageSize(img, orientation);
            }
        }
    });

        // Mobile menu toggle
        const mobileMenuButton = document.getElementById('mobile-menu-button');
        const mobileMenu = document.getElementById('mobile-menu');

        mobileMenuButton.addEventListener('click', () => {
            mobileMenu.classList.toggle('translate-x-0');
            mobileMenu.classList.toggle('translate-x-full');

            const icon = mobileMenuButton.querySelector('i');
            if (mobileMenu.classList.contains('translate-x-0')) {
                icon.classList.remove('fa-bars');
                icon.classList.add('fa-times');
            } else {
                icon.classList.remove('fa-times');
                icon.classList.add('fa-bars');
            }
        });

        // Close mobile menu when clicking on a link
        document.querySelectorAll('#mobile-menu a').forEach(link => {
            link.addEventListener('click', () => {
                mobileMenu.classList.remove('translate-x-0');
                mobileMenu.classList.add('translate-x-full');
                const icon = mobileMenuButton.querySelector('i');
                icon.classList.remove('fa-times');
                icon.classList.add('fa-bars');
            });
        });

        // Header scroll effect
        let lastScroll = 0;
        const header = document.getElementById('main-header');

        window.addEventListener('scroll', () => {
            const currentScroll = window.pageYOffset;

            if (currentScroll > 100) {
                if (currentScroll > lastScroll && !mobileMenu.classList.contains('translate-x-0')) {
                    header.style.transform = 'translateY(-100%)';
                } else {
                    header.style.transform = 'translateY(0)';
                }
            } else {
                header.style.transform = 'translateY(0)';
            }

            lastScroll = currentScroll;
        });

        // Smooth scrolling for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();

                const targetId = this.getAttribute('href');
                if (targetId === '#') return;

                const targetElement = document.querySelector(targetId);
                if (targetElement) {
                    window.scrollTo({
                        top: targetElement.offsetTop - 80,
                        behavior: 'smooth'
                    });
                }
            });
        });

        // Back to top button
        const backToTopButton = document.getElementById('back-to-top');
        window.addEventListener('scroll', () => {
            if (window.pageYOffset > 300) {
                backToTopButton.classList.remove('opacity-0', 'invisible');
                backToTopButton.classList.add('opacity-100', 'visible');
            } else {
                backToTopButton.classList.remove('opacity-100', 'visible');
                backToTopButton.classList.add('opacity-0', 'invisible');
            }
        });

        backToTopButton.addEventListener('click', () => {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });

        // Scroll animation for elements
        const animateOnScroll = () => {
            const elements = document.querySelectorAll('.animate-fadeInUp');

            elements.forEach(element => {
                const elementPosition = element.getBoundingClientRect().top;
                const windowHeight = window.innerHeight;

                if (elementPosition < windowHeight - 100) {
                    element.style.opacity = '1';
                    element.style.transform = 'translateY(0)';
                }
            });
        };

        // Initialize animation on load
        window.addEventListener('load', () => {
            document.querySelectorAll('.animate-fadeInUp').forEach(el => {
                el.style.opacity = '0';
                el.style.transform = 'translateY(20px)';
            });

            setTimeout(() => {
                animateOnScroll();
            }, 300);
        });

        // Animate on scroll
        window.addEventListener('scroll', animateOnScroll);

        // Scroll to top
        function scrollToTop() {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }
    </script>
</body>
</html>