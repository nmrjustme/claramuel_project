@extends('layouts.app')
@section('title', 'Mt. ClaRamuel Resort & Events Place')
@section('content')
<style>
    html,
    body {
        width: 100%;
        margin: 0;
        padding: 0;
        overflow-x: hidden;
        scroll-behavior: smooth;
    }


    /* Performance optimizations */
    img, video {
        content-visibility: auto;
    }
    
    /* Animation enhancements */
    @keyframes float {
        0% { transform: translateY(0px); }
        50% { transform: translateY(-15px); }
        100% { transform: translateY(0px); }
    }
    
    .floating {
        animation: float 3s ease-in-out infinite;
    }
</style>

<!-- Sticky Navigation -->
<header id="navbar" class="fixed w-full transition-all duration-500 ease-in-out z-50 bg-blue-700/90 backdrop-blur-md left-0 right-0">
    
    <div class="container mx-auto flex justify-between items-center py-3 px-4 sm:px-6 lg:px-8">
        <!-- Logo -->
        <div class="flex items-center space-x-2 sm:space-x-4 group">
            <x-logo-icon size="default"
                class="w-8 h-8 sm:w-10 sm:h-10 lg:w-12 lg:h-12 text-red-400 group-hover:rotate-12 transition-transform duration-300" />
            <span
                class="text-transparent bg-clip-text bg-gradient-to-r from-red-300 to-red-500 text-base sm:text-lg lg:text-2xl xl:text-3xl font-bold tracking-wide">
                <a href="{{ route('index') }}"
                    class="text-white transition-colors duration-300 relative group">
                    Ｍｔ.ＣＬＡＲＡＭＵＥＬ
                    <span
                        class="absolute bottom-0 left-0 w-0 h-0.5 transition-all duration-300 group-hover:w-full"></span>
                </a>
            </span>
        </div>
        
        <!-- Mobile Menu Button -->
        <button id="mobile-menu-button" class="md:hidden text-white focus:outline-none">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
            </svg>
        </button>
        
        <!-- Desktop Navigation -->
        <nav class="hidden md:flex space-x-1 lg:space-x-4">
            <a href="#hero" class="px-3 py-2 text-white hover:text-red-300 transition-colors duration-300">Home</a>
            <a href="#about" class="px-3 py-2 text-white hover:text-red-300 transition-colors duration-300">About</a>
            <a href="#services" class="px-3 py-2 text-white hover:text-red-300 transition-colors duration-300">Services</a>
            <a href="#events" class="px-3 py-2 text-white hover:text-red-300 transition-colors duration-300">Events</a>
            <a href="#contact" class="px-3 py-2 text-white hover:text-red-300 transition-colors duration-300">Contact</a>
            <a href="{{ route('login') }}" class="px-3 py-2 text-white hover:text-red-300 transition-colors duration-300">Login</a>
            <a href="{{ route('customer_bookings') }}" class="ml-4 px-4 py-2 bg-red-500 hover:bg-red-600 text-white rounded-full transition-colors duration-300">Book Now</a>
        </nav>
    </div>
    
    <!-- Mobile Menu -->
    <div id="mobile-menu" class="hidden md:hidden bg-blue-800/95 backdrop-blur-sm">
        <div class="px-2 pt-2 pb-3 space-y-1 sm:px-3">
            <a href="#hero" class="block px-3 py-2 text-white hover:bg-blue-700 rounded-md">Home</a>
            <a href="#about" class="block px-3 py-2 text-white hover:bg-blue-700 rounded-md">About</a>
            <a href="#services" class="block px-3 py-2 text-white hover:bg-blue-700 rounded-md">Services</a>
            <a href="#events" class="block px-3 py-2 text-white hover:bg-blue-700 rounded-md">Events</a>
            <a href="#contact" class="block px-3 py-2 text-white hover:bg-blue-700 rounded-md">Contact</a>
            <a href="{{ route('login') }}" class="px-3 py-2 text-white hover:text-red-300 transition-colors duration-300">Login</a>
            <a href="{{ route('customer_bookings') }}" class="block px-3 py-2 mt-2 bg-red-500 hover:bg-red-600 text-white text-center rounded-md">Book Now</a>
        </div>
    </div>
</header>

<!-- Hero Section -->
<section id="hero" class="relative h-screen overflow-hidden">
    <!-- Background video container with performance optimizations -->
    <div class="absolute inset-0 w-full h-full">
        <video 
            class="absolute inset-0 w-full h-full object-cover"
            autoplay 
            loop 
            muted 
            playsinline 
            poster="{{ url('imgs/hero-fallback.jpg') }}"
            style="object-position: center center; filter: brightness(1.2) contrast(1.2);">
            <source src="{{ url('video/welcomeVideo.mp4') }}" type="video/mp4">
        </video>
    </div>
    
    <!-- Hero Content -->
    <div class="relative z-10 flex flex-col items-center justify-center h-full text-center px-4">
        <div class="text-white max-w-4xl" data-aos="fade-up" data-aos-duration="1500">
            <h1 class="text-4xl sm:text-5xl md:text-6xl font-bold mb-6">
                <span class="text-red-400 drop-shadow-lg">Mt. ClaRamuel</span> Resort & Events Place
            </h1>
            <p class="text-lg sm:text-xl md:text-2xl mb-8 font-light max-w-2xl mx-auto text-white/90">
                Experience a Perfect Getaway with Nature's Serenity and Modern Comfort
            </p>
            <div class="flex flex-col sm:flex-row justify-center gap-4">
                <!-- Book Now Button -->
                <a href="{{ route('customer_bookings') }}" class="inline-block bg-red-500 hover:bg-red-600 text-white font-semibold py-3 px-8 rounded-full shadow-lg transition duration-300 transform hover:scale-105">
                    Book Now
                </a>
                <a href="{{ route('customer_bookings.cottage') }}" class="inline-block bg-green-500 hover:bg-green-600 text-white font-semibold py-3 px-8 rounded-full shadow-lg transition duration-300 transform hover:scale-105">
                    Book Cottage
                </a>
            </div>
        </div>
    </div>
    
    <!-- Scroll indicator -->
    <a href="#services" class="absolute bottom-10 left-1/2 transform -translate-x-1/2 animate-bounce z-10">
        <svg class="w-8 h-8 text-white hover:text-red-400 transition-colors duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path>
        </svg>
    </a>
</section>


<!-- Featured Highlights Bar -->
<div class="bg-gray-800 text-white py-4">
    <div class="container mx-auto">
        <div class="flex flex-wrap justify-center items-center gap-6 md:gap-12">
            <div class="flex items-center">
                <svg class="w-6 h-6 text-red-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>Open Daily 8AM-10PM</span>
            </div>
            <div class="flex items-center">
                <svg class="w-6 h-6 text-red-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                </svg>
                <span>Accommodations</span>
            </div>
            <div class="flex items-center">
                <svg class="w-6 h-6 text-red-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                <span>Event Hosting</span>
            </div>
        </div>
    </div>
</div>

<!-- Services Section -->
<section id="services" class="py-16 md:py-24 bg-gray-900">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-3xl sm:text-4xl md:text-5xl font-bold mb-6 text-white">
                Our <span class="text-red-500">Services</span>
            </h2>
            <p class="text-lg md:text-xl text-gray-300 max-w-3xl mx-auto">
                We offer a variety of premium services to make your stay comfortable and memorable
            </p>
            <div class="w-20 h-1 bg-red-500 mx-auto mt-6"></div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 max-w-6xl mx-auto">
            <!-- Card 1 -->
            <div class="bg-gray-800 rounded-xl shadow-lg overflow-hidden transition-transform duration-300 hover:scale-105 hover:shadow-xl">
                <div class="relative overflow-hidden h-64">
                    <img class="w-full h-full object-cover transition-transform duration-500 hover:scale-110" 
                         src="{{ url('/imgs/room.jpg') }}" 
                         alt="Accommodations">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent"></div>
                    <div class="absolute bottom-0 left-0 p-6">
                        <h3 class="text-2xl font-bold text-white">Accommodations</h3>
                    </div>
                </div>
                <div class="p-6">
                    <p class="text-gray-300 mb-6">
                        Cozy rooms and cottages designed for ultimate relaxation with modern amenities and breathtaking nature views.
                    </p>
                    <ul class="space-y-2 mb-6 text-gray-300">
                        <li class="flex items-center">
                            <svg class="w-5 h-5 text-red-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            Air-conditioned rooms
                        </li>
                        <li class="flex items-center">
                            <svg class="w-5 h-5 text-red-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            Private bathrooms
                        </li>
                        <li class="flex items-center">
                            <svg class="w-5 h-5 text-red-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            Mountain views
                        </li>
                    </ul>
                    <a href="{{ route('customer_bookings') }}" class="inline-flex items-center text-red-500 hover:text-red-600 font-medium transition-colors duration-300">
                        View accommodations
                        <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                        </svg>
                    </a>
                </div>
            </div>
            
            <!-- Card 2 -->
            <div class="bg-gray-800 rounded-xl shadow-lg overflow-hidden transition-transform duration-300 hover:scale-105 hover:shadow-xl">
                <div class="relative overflow-hidden h-64">
                    <img class="w-full h-full object-cover transition-transform duration-500 hover:scale-110" 
                         src="{{ url('/imgs/event.jpg') }}" 
                         alt="Event Hosting">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent"></div>
                    <div class="absolute bottom-0 left-0 p-6">
                        <h3 class="text-2xl font-bold text-white">Event Hosting</h3>
                    </div>
                </div>
                <div class="p-6">
                    <p class="text-gray-300 mb-6">
                        Perfect venues for weddings, corporate events, and parties with professional event planning services.
                    </p>
                    <ul class="space-y-2 mb-6 text-gray-300">
                        <li class="flex items-center">
                            <svg class="w-5 h-5 text-red-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            Wedding receptions
                        </li>
                        <li class="flex items-center">
                            <svg class="w-5 h-5 text-red-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            Corporate retreats
                        </li>
                        <li class="flex items-center">
                            <svg class="w-5 h-5 text-red-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            Birthday celebrations
                        </li>
                    </ul>
                    <a href="{{ route('events') }}" class="inline-flex items-center text-red-500 hover:text-red-600 font-medium transition-colors duration-300">
                        Plan your event
                        <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                        </svg>
                    </a>
                </div>
            </div>
            
            <!-- Card 3 -->
            <div class="bg-gray-800 rounded-xl shadow-lg overflow-hidden transition-transform duration-300 hover:scale-105 hover:shadow-xl">
                <div class="relative overflow-hidden h-64">
                    <img class="w-full h-full object-cover transition-transform duration-500 hover:scale-110" 
                         src="{{ url('/imgs/recreational_activities.png') }}" 
                         alt="Recreational Activities">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent"></div>
                    <div class="absolute bottom-0 left-0 p-6">
                        <h3 class="text-2xl font-bold text-white">Recreational Activities</h3>
                    </div>
                </div>
                <div class="p-6">
                    <p class="text-gray-300 mb-6">
                        Enjoy nature walks, swimming, cycling, and other fun activities for all ages and groups.
                    </p>
                    <ul class="space-y-2 mb-6 text-gray-300">
                        <li class="flex items-center">
                            <svg class="w-5 h-5 text-red-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            Swimming pools
                        </li>
                        <li class="flex items-center">
                            <svg class="w-5 h-5 text-red-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            Nature trails
                        </li>
                        <li class="flex items-center">
                            <svg class="w-5 h-5 text-red-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            Picnic areas
                        </li>
                    </ul>
                    <a href="{{ route('Pools_Park') }}" class="inline-flex items-center text-red-500 hover:text-red-600 font-medium transition-colors duration-300">
                        See activities
                        <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                        </svg>
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>


<!-- About Section -->
<section id="about" class="py-16 md:py-24 bg-gray-900">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col lg:flex-row items-center gap-12">
            <!-- Image Gallery -->
            <div class="lg:w-1/2 relative">
                <div class="grid grid-cols-2 gap-4">
                    <div class="rounded-xl overflow-hidden shadow-lg">
                        <img src="{{ url('imgs/welcome_pic.png') }}" alt="Resort View" class="w-full h-64 object-cover transition-transform duration-500 hover:scale-110">
                    </div>
                    <div class="rounded-xl overflow-hidden shadow-lg mt-8">
                        <img src="{{ url('imgs/pool.jpg') }}" alt="Pool Area" class="w-full h-64 object-cover transition-transform duration-500 hover:scale-110">
                    </div>
                    <div class="rounded-xl overflow-hidden shadow-lg -mt-8">
                        <img src="{{ url('imgs/coffeeShop.jpg') }}" alt="Restaurant" class="w-full h-64 object-cover transition-transform duration-500 hover:scale-110">
                    </div>
                    <div class="rounded-xl overflow-hidden shadow-lg">
                        <img src="{{ url('imgs/event.jpg') }}" alt="Event Space" class="w-full h-64 object-cover transition-transform duration-500 hover:scale-110">
                    </div>
                </div>
                <div class="absolute -bottom-6 -right-6 bg-red-500 text-white px-6 py-3 rounded-lg shadow-lg">
                    <span class="font-bold">Since 2020</span>
                </div>
            </div>
            
            <!-- Text Content -->
            <div class="lg:w-1/2">
                <h2 class="text-3xl sm:text-4xl md:text-5xl font-bold mb-6 text-white">
                    About <span class="text-red-500">Mt. ClaRamuel</span>
                </h2>
                <div class="w-20 h-1 bg-red-500 mb-8"></div>
                
                <p class="text-gray-300 mb-6">
                    Nestled in the heart of nature, <span class="font-semibold text-red-400">Mt. ClaRamuel Resort & Events Place</span> offers the perfect blend of relaxation and memorable experiences.
                </p>
                
                <div class="space-y-6 mb-8">
                    <div class="flex items-start">
                        <div class="flex-shrink-0 mt-1">
                            <svg class="w-6 h-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-lg font-semibold text-white">Our Mission</h3>
                            <p class="text-gray-300">
                                ...
                            </p>
                        </div>
                    </div>
                    
                    <div class="flex items-start">
                        <div class="flex-shrink-0 mt-1">
                            <svg class="w-6 h-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-lg font-semibold text-white">Our Vision</h3>
                            <p class="text-gray-300">
                                ...
                            </p>
                        </div>
                    </div>

                </div>
                
                <div class="flex flex-wrap gap-4">
                    <div class="bg-gray-800 px-4 py-3 rounded-lg">
                        <span class="block text-2xl font-bold text-red-500">50+</span>
                        <span class="text-gray-300">Rooms & Cottages</span>
                    </div>
                    <div class="bg-gray-800 px-4 py-3 rounded-lg">
                        <span class="block text-2xl font-bold text-red-500">100+</span>
                        <span class="text-gray-300">Events Hosted</span>
                    </div>
                    <div class="bg-gray-800 px-4 py-3 rounded-lg">
                        <span class="block text-2xl font-bold text-red-500">10K+</span>
                        <span class="text-gray-300">Happy Guests</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Gallery Section -->
<section class="py-16 bg-gray-800">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-3xl sm:text-4xl md:text-5xl font-bold mb-6 text-white">
                Resort <span class="text-red-500">Gallery</span>
            </h2>
            <p class="text-lg md:text-xl text-gray-300 max-w-3xl mx-auto">
                Explore the beauty of Mt. ClaRamuel through our photo gallery
            </p>
            <div class="w-20 h-1 bg-red-500 mx-auto mt-6"></div>
        </div>
        
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
            @foreach([1,222,3,4,5,66,7,8] as $i)
            <a href="{{ url('imgs/gallery/'.$i.'.jpg') }}" class="gallery-item group">
                <div class="aspect-w-1 aspect-h-1 overflow-hidden rounded-lg shadow-md">
                    <img src="{{ url('imgs/gallery/'.$i.'.jpg') }}" 
                         alt="Gallery image {{ $i }}" 
                         class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110">
                    <div class="absolute inset-0 bg-black/20 group-hover:bg-black/40 transition-colors duration-300 flex items-center justify-center">
                        <svg class="w-10 h-10 text-white opacity-0 group-hover:opacity-100 transition-opacity duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7" />
                        </svg>
                    </div>
                </div>
            </a>
            @endforeach
        </div>
        
        <div class="text-center mt-12">
            <a href="#" class="inline-block px-6 py-3 bg-red-500 hover:bg-red-600 text-white font-medium rounded-lg transition-colors duration-300">
                View More Photos
            </a>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="py-20 bg-gradient-to-r from-blue-700 to-blue-900 text-white">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="text-3xl sm:text-4xl md:text-5xl font-bold mb-6 text-gray-800">
            Ready for an Unforgettable Experience?
        </h2>
        <p class="text-xl md:text-2xl mb-8 max-w-3xl mx-auto text-gray-600">
            Book your stay or event today and discover the magic of Mt. ClaRamuel
        </p>
        <div class="flex flex-col sm:flex-row justify-center gap-4">
            <a href="{{ route('customer_bookings') }}" class="inline-block bg-white hover:bg-gray-100 text-blue-800 font-semibold py-4 px-8 rounded-full shadow-lg transition duration-300 transform hover:scale-105">
                Book Now
            </a>
            <a href="tel:+639952901333" class="inline-block border-2 border-white hover:bg-white/20 text-gray-800 font-semibold py-4 px-8 rounded-full shadow-lg transition duration-300 transform hover:scale-105">
                Call Us: +63 995 290 1333
            </a>
        </div>
    </div>
</section>

<!-- Location Section -->
<section id="contact" class="py-16 md:py-24 bg-gray-900">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col lg:flex-row items-center justify-between gap-12">
            <!-- Text Section -->
            <div class="lg:w-1/2 text-center lg:text-left">
                <h2 class="text-3xl sm:text-4xl md:text-5xl font-bold mb-6 text-white">
                    Visit <span class="text-red-500">Us Today</span>
                </h2>
                <div class="w-24 h-1 bg-red-500 mb-6 mx-auto lg:mx-0"></div>

                <div class="space-y-6">
                    <div>
                        <h3 class="text-xl font-semibold text-gray-300 mb-2">Location</h3>
                        <p class="text-lg text-gray-400 leading-relaxed">
                            Narra Street, Brgy. Marana 3rd, Ilagan, 3300 Isabela, Philippines
                        </p>
                    </div>

                    <div>
                        <h3 class="text-xl font-semibold text-gray-300 mb-2">Contact Information</h3>
                        <p class="text-lg text-gray-400 leading-relaxed mb-2">
                            <a href="tel:+639952901333" class="hover:text-red-500 transition-colors duration-300">+63 995 290 1333</a>
                        </p>
                        <p class="text-lg text-gray-400 leading-relaxed">
                            <a href="mailto:mtclaramuelresort@gmail.com" class="hover:text-red-500 transition-colors duration-300">mtclaramuelresort@gmail.com</a>
                        </p>
                    </div>

                    <div>
                        <h3 class="text-xl font-semibold text-gray-300 mb-2">Operating Hours</h3>
                        <p class="text-lg text-gray-400 leading-relaxed">
                            Daily: 8:00 AM - 10:00 PM
                        </p>
                    </div>

                    <div class="pt-4">
                        <h3 class="text-xl font-semibold text-gray-300 mb-4">Follow Us</h3>
                        <div class="flex gap-4 justify-center lg:justify-start">
                            <a href="https://www.facebook.com/mtclaramuelresort" target="_blank"
                                class="bg-blue-600 text-white p-3 rounded-full hover:bg-blue-700 transition-colors duration-300">
                                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                                    <path
                                        d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z" />
                                </svg>
                            </a>

                            <a href="https://www.instagram.com/mt_claramuelresort/" target="_blank"
                                class="bg-gradient-to-r from-purple-500 to-pink-500 text-white p-3 rounded-full hover:from-purple-600 hover:to-pink-600 transition-colors duration-300">
                                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                                    <path
                                        d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z" />
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Map Section -->
            <div class="lg:w-1/2 h-80 sm:h-96 rounded-xl overflow-hidden shadow-xl border-4 border-gray-700">
                <iframe class="w-full h-full border-0"
                    src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d789.1338574963425!2d121.9251491!3d17.142796!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x33856b144a6449f5%3A0xea1ad60f5e068495!2sMt.%20Claramuel%20Resort%20and%20Events%20Place!5e0!3m2!1sen!2sph!4v1711431557"
                    allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade">
                </iframe>
            </div>
        </div>
    </div>
</section>

<!-- Events/History Section -->
@include('history_or_events')

<!-- Footer -->
<footer class="bg-gray-900 pt-16 pb-8">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-12">
            <!-- Logo and Description -->
            <div class="md:col-span-2">
                <div class="flex items-center mb-6">
                    <img src="{{ url('imgs/logo.png') }}" class="h-16 mr-4" alt="Mt. ClaRamuel Logo" />
                    <span class="text-2xl font-bold text-white">Mt. ClaRamuel Resort</span>
                </div>
                <p class="text-gray-400 mb-6">
                    Your perfect getaway destination offering luxury, comfort, and unforgettable experiences in the heart of nature.
                </p>
                <div class="flex space-x-4">
                    <a href="https://www.facebook.com/mtclaramuelresort" target="_blank" class="text-gray-400 hover:text-white transition-colors duration-300">
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z" />
                        </svg>
                    </a>
                    <a href="https://www.instagram.com/mt_claramuelresort/" target="_blank" class="text-gray-400 hover:text-white transition-colors duration-300">
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z" />
                        </svg>
                    </a>
                </div>
            </div>

            <!-- Quick Links -->
            <div>
                <h3 class="text-lg font-semibold text-white mb-6 uppercase">Quick Links</h3>
                <ul class="space-y-3">
                    <li><a href="#hero" class="text-gray-400 hover:text-white transition-colors duration-300">Home</a></li>
                    <li><a href="#about" class="text-gray-400 hover:text-white transition-colors duration-300">About Us</a></li>
                    <li><a href="#services" class="text-gray-400 hover:text-white transition-colors duration-300">Services</a></li>
                    <li><a href="#events" class="text-gray-400 hover:text-white transition-colors duration-300">Events</a></li>
                    <li><a href="#accommodations" class="text-gray-400 hover:text-white transition-colors duration-300">Accommodations</a></li>
                    <li><a href="{{ route('customer_bookings') }}" class="text-gray-400 hover:text-white transition-colors duration-300">Book Now</a></li>
                </ul>
            </div>

            <!-- Contact Info -->
            <div>
                <h3 class="text-lg font-semibold text-white mb-6 uppercase">Contact Us</h3>
                <ul class="space-y-3 text-gray-400">
                    <li class="flex items-start">
                        <svg class="w-5 h-5 text-red-500 mr-3 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        <span>Narra Street, Brgy. Marana 3rd, Ilagan, 3300 Isabela, Philippines</span>
                    </li>
                    <li class="flex items-center">
                        <svg class="w-5 h-5 text-red-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                        </svg>
                        <a href="tel:+639952901333" class="hover:text-white transition-colors duration-300">+63 995 290 1333</a>
                    </li>
                    <li class="flex items-center">
                        <svg class="w-5 h-5 text-red-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                        <a href="mailto:mtclaramuelresort@gmail.com" class="hover:text-white transition-colors duration-300">mtclaramuelresort@gmail.com</a>
                    </li>
                    <li class="flex items-center">
                        <svg class="w-5 h-5 text-red-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>Daily: 8:00 AM - 10:00 PM</span>
                    </li>
                </ul>
            </div>
        </div>

        <div class="border-t border-gray-700 mt-12 pt-8 flex flex-col md:flex-row justify-between items-center">
            <p class="text-gray-400 mb-4 md:mb-0">
                © 2025 Mt. ClaRamuel Resort. All rights reserved.
            </p>

            <div class="flex space-x-6">
                <a href="#" class="text-gray-400 hover:text-white transition-colors duration-300">Privacy Policy</a>
                <a href="#" class="text-gray-400 hover:text-white transition-colors duration-300">Terms of Service</a>
            </div>
        </div>
    </div>
</footer>

<!-- Back to Top Button -->
<button id="back-to-top" class="fixed bottom-8 right-8 bg-red-500 text-white p-3 rounded-full shadow-lg opacity-0 invisible transition-all duration-300 z-50">
    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18" />
    </svg>
</button>

<!-- Scripts -->
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>


<script>
    // Initialize AOS (Animate On Scroll)
    AOS.init({
        duration: 800,
        easing: 'ease-in-out',
        once: true
    });

    // Mobile menu toggle
    const mobileMenuButton = document.getElementById('mobile-menu-button');
    const mobileMenu = document.getElementById('mobile-menu');
    
    mobileMenuButton.addEventListener('click', () => {
        mobileMenu.classList.toggle('hidden');
    });

    // Sticky navbar
    window.addEventListener('scroll', function() {
        const navbar = document.getElementById('navbar');
        if (window.scrollY > 100) {
            navbar.classList.add('shadow-lg');
        } else {
            navbar.classList.remove('shadow-lg');
        }
    });

    // Smooth scrolling for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Close mobile menu if open
            mobileMenu.classList.add('hidden');
            
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

    // Lazy loading for images
    if ('loading' in HTMLImageElement.prototype) {
        const images = document.querySelectorAll('img[loading="lazy"]');
        images.forEach(img => {
            img.src = img.dataset.src;
        });
    } else {
        // Fallback for browsers that don't support lazy loading
        const script = document.createElement('script');
        script.src = 'https://cdn.jsdelivr.net/npm/lozad/dist/lozad.min.js';
        document.body.appendChild(script);
        script.onload = () => {
            const observer = lozad();
            observer.observe();
        };
    }

    // Gallery lightbox
    document.querySelectorAll('.gallery-item').forEach(item => {
        item.addEventListener('click', function(e) {
            e.preventDefault();
            // You can implement a lightbox here using libraries like GLightbox or simple-lightbox
            // For example, if using GLightbox:
            // const lightbox = GLightbox({
            //     href: this.href,
            //     type: 'image'
            // });
            // lightbox.open();
            
            // For now, we'll just open the image in a new tab
            window.open(this.href, '_blank');
        });
    });
</script>

<!-- Include any additional libraries you might need -->
<!-- <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/glightbox/dist/css/glightbox.min.css" /> -->
<!-- <script src="https://cdn.jsdelivr.net/npm/glightbox/dist/js/glightbox.min.js"></script> -->

@endsection