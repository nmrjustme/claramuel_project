<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mt. ClaRamuel Resort & Events Place | Luxury Mountain Retreat</title>
    <meta name="description"
        content="Experience luxury accommodations and premier event hosting at Mt. ClaRamuel Resort in Isabela, Philippines.">
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

        .hero-title {
            text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.5);
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

        /* Service card hover animation */
        .service-card {
            transition: all 0.3s ease;
        }

        .service-card:hover {
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

        /* Hero section with highlights bar */
        .hero-container {
            position: relative;
            height: 100vh;
        }

        .highlights-bar {
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            z-index: 20;
        }

        /* Video controls */
        .video-controls {
            position: absolute;
            bottom: 120px;
            /* Adjusted to account for highlights bar */
            right: 20px;
            z-index: 30;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-fadeInUp {
            animation: fadeInUp 0.6s ease-out forwards;
        }

        /* Wavy line styles */
        .wavy-line {
            position: absolute;
            left: 0;
            width: 100%;
            overflow: hidden;
            line-height: 0;
        }

        .wavy-line-top {
            top: 0;
        }

        .wavy-line-bottom {
            bottom: 0;
            transform: rotate(180deg);
        }

        .wavy-line svg {
            position: relative;
            display: block;
            width: calc(100% + 1.3px);
            height: 60px;
        }

        .wavy-line .shape-fill {
            fill: #FFFFFF;
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
                <a href="#home" class="hover:text-gray-300 transition duration-300 font-medium">Home</a>
                <a href="#about" class="hover:text-gray-300  transition duration-300 font-medium">About</a>
                <a href="#services" class="hover:text-gray-300  transition duration-300 font-medium">Services</a>
                <a href="#gallery" class="hover:text-gray-300  transition duration-300 font-medium">Gallery</a>
                <a href="#contact" class="hover:text-gray-300  transition duration-300 font-medium">Contact</a>
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
            <a href="#home"
                class="text-white hover:text-gray-300  text-lg transition border-b border-gray-700 pb-3">Home</a>
            <a href="#about"
                class="text-white hover:text-gray-300  text-lg transition border-b border-gray-700 pb-3">About</a>
            <a href="#services"
                class="text-white hover:text-gray-300  text-lg transition border-b border-gray-700 pb-3">Services</a>
            <a href="#gallery"
                class="text-white hover:text-gray-300  text-lg transition border-b border-gray-700 pb-3">Gallery</a>
            <a href="#contact"
                class="text-white hover:text-gray-300  text-lg transition border-b border-gray-700 pb-3">Contact</a>
            <a href="{{ route('login') }}"
                class="text-white hover:text-gray-300  text-lg transition border-b border-gray-700 pb-3">Login</a>
            <a href="{{ route('dashboard.bookings') }}"
                class="bg-accent hover:bg-darkAccent text-white px-6 py-3 rounded-sm transition flex items-center justify-center text-lg mt-4 btn-pulse">
                <i class="fas fa-calendar-check mr-2"></i> Book Now
            </a>
        </div>
    </div>

    <!-- Hero Section with Highlights Bar -->
    <div class="hero-container">
        <section id="home"
            class="pt-32 pb-20 relative h-full flex items-center justify-center text-center overflow-hidden">
            <video autoplay loop muted playsinline class="absolute inset-0 w-full h-full object-cover z-0">
                <source src="{{ url('video/welcomeVideo.mp4') }}" type="video/mp4">
                <img src="{{ url('imgs/video-backup.jpg') }}" alt="Mt. ClaRamuel Resort"
                    class="absolute inset-0 w-full h-full object-cover z-0">
            </video>
            <div class="absolute inset-0 bg-black/40 z-10"></div>
            <div class="relative z-20 text-white px-6 max-w-6xl mx-auto">
                <div class="text-center px-4 sm:px-6 md:px-12">
                    <h1
                        class="text-2xl sm:text-3xl md:text-5xl lg:text-6xl font-bold mb-4 sm:mb-6 font-serif animate-fadeInUp">
                        Mt. ClaRamuel Resort & Events Place
                    </h1>
                    <p class="text-xs sm:text-sm md:text-lg lg:text-xl mb-6 sm:mb-8 max-w-2xl md:max-w-3xl mx-auto leading-relaxed animate-fadeInUp"
                        style="animation-delay: 0.2s;">
                        A premier mountain retreat offering luxury accommodations and exceptional
                        event venues in the heart of Isabela.
                    </p>
                </div>

                <div class="flex flex-col sm:flex-row justify-center gap-4 animate-fadeInUp"
                    style="animation-delay: 0.4s;">
                    <a href="{{ route('dashboard.bookings') }}"
                        class="inline-block bg-accent hover:bg-darkAccent text-white font-semibold px-8 py-3 rounded-sm shadow-lg transition duration-300 btn-pulse">
                        Book Your Stay
                    </a>
                </div>
            </div>

            <!-- Video sound toggle -->
            <div class="video-controls">
                <button id="sound-toggle"
                    class="bg-black/50 text-white p-3 rounded-full shadow-lg transition hover:bg-black/70">
                    <i class="fas fa-volume-mute"></i>
                </button>
            </div>

            <!-- Scroll indicator -->
            <div class="absolute bottom-28 left-1/2 transform -translate-x-1/2 z-20 animate-bounce">
                <a href="#about" class="text-white">
                    <i class="fas fa-chevron-down text-2xl"></i>
                </a>
            </div>
        </section>

        <!-- Highlights Bar -->
        <div class="highlights-bar">
            <div class="bg-gray-800 text-white py-4 md:py-8 lg:py-12">
                <div class="container mx-auto px-4 sm:px-6">
                    <div class="flex flex-wrap justify-center items-center gap-4 sm:gap-6 md:gap-12 lg:gap-16">
                        <div class="flex items-center space-x-2 sm:space-x-3 md:space-x-4 animate-fadeInUp"
                            style="animation-delay: 0.1s;">
                            <div class="bg-accent p-1.5 sm:p-2 md:p-3 rounded-full">
                                <i class="fas fa-clock text-white text-sm sm:text-base md:text-lg lg:text-xl"></i>
                            </div>
                            <div>
                                <p
                                    class="text-xs sm:text-sm md:text-base lg:text-sm xl:text-base font-semibold truncate">
                                    Open Daily</p>
                                <p
                                    class="text-xs sm:text-sm md:text-base lg:text-sm xl:text-base text-gray-400 truncate">
                                    8 AM - 10 PM</p>
                            </div>
                        </div>
                        <div class="flex items-center space-x-2 sm:space-x-3 md:space-x-4 animate-fadeInUp"
                            style="animation-delay: 0.2s;">
                            <div class="bg-accent p-1.5 sm:p-2 md:p-3 rounded-full">
                                <i class="fas fa-home text-white text-sm sm:text-base md:text-lg lg:text-xl"></i>
                            </div>
                            <div>
                                <p
                                    class="text-xs sm:text-sm md:text-base lg:text-sm xl:text-base font-semibold truncate">
                                    Luxury</p>
                                <p
                                    class="text-xs sm:text-sm md:text-base lg:text-sm xl:text-base text-gray-400 truncate">
                                    Accommodations</p>
                            </div>
                        </div>
                        <div class="flex items-center space-x-2 sm:space-x-3 md:space-x-4 animate-fadeInUp"
                            style="animation-delay: 0.3s;">
                            <div class="bg-accent p-1.5 sm:p-2 md:p-3 rounded-full">
                                <i
                                    class="fas fa-calendar-alt text-white text-sm sm:text-base md:text-lg lg:text-xl"></i>
                            </div>
                            <div>
                                <p
                                    class="text-xs sm:text-sm md:text-base lg:text-sm xl:text-base font-semibold truncate">
                                    Event</p>
                                <p
                                    class="text-xs sm:text-sm md:text-base lg:text-sm xl:text-base text-gray-400 truncate">
                                    Hosting</p>
                            </div>
                        </div>
                        <div class="flex items-center space-x-2 sm:space-x-3 md:space-x-4 animate-fadeInUp"
                            style="animation-delay: 0.4s;">
                            <div class="bg-accent p-1.5 sm:p-2 md:p-3 rounded-full">
                                <i class="fas fa-utensils text-white text-sm sm:text-base md:text-lg lg:text-xl"></i>
                            </div>
                            <div>
                                <p
                                    class="text-xs sm:text-sm md:text-base lg:text-sm xl:text-base font-semibold truncate">
                                    Fine</p>
                                <p
                                    class="text-xs sm:text-sm md:text-base lg:text-sm xl:text-base text-gray-400 truncate">
                                    Dining</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- About Section -->
    <section id="about" class="py-20 bg-white">
        <div class="container mx-auto px-6">
            <div class="flex flex-col lg:flex-row items-center gap-12">
                <div class="lg:w-1/2 animate-fadeInUp">
                    <div class="relative">
                        <img src="{{ url('imgs/welcome_pic.png') }}" alt="Mt. ClaRamuel Resort" class="w-full h-auto rounded-lg shadow-xl
                                max-w-xs mx-auto sm:max-w-sm md:max-w-md lg:max-w-2xl xl:max-w-4xl 2xl:max-w-5xl
                                transition-all duration-300" loading="lazy">
                        <div class="absolute -bottom-6 -right-6 bg-white p-4 shadow-lg rounded-lg hidden md:block">
                            <div class="flex items-center">
                                <div class="bg-accent p-3 rounded-full mr-3">
                                    <i class="fas fa-award text-white text-xl"></i>
                                </div>
                                <div>
                                    <p class="font-bold text-gray-800">Excellence in Hospitality</p>
                                    <p class="text-sm text-gray-600">Since 2010</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="lg:w-1/2">
                    <h2 class="text-2xl md:text-4xl text-darkAccent mb-6 font-serif font-light animate-fadeInUp"
                        style="animation-delay: 0.2s;">
                        𝒜 𝓅𝑒𝒶𝒸𝑒𝒻𝓊𝓁 𝓂𝑜𝓊𝓃𝓉𝒶𝒾𝓃 𝓇𝑒𝓉𝓇𝑒𝒶𝓉 𝒾𝓃 𝐼𝓈𝒶𝒷𝑒𝓁𝒶, 𝑀𝓉.
                        𝒞𝓁𝒶𝑅𝒶𝓂𝓊𝑒𝓁 𝒷𝓁𝑒𝓃𝒹𝓈 𝓃𝒶𝓉𝓊𝓇𝑒 𝒶𝓃𝒹 𝒸𝑜𝓂𝒻𝑜𝓇𝓉 𝒻𝑜𝓇 𝓁𝒶𝓈𝓉𝒾𝓃𝑔
                        𝓂𝑒𝓂𝑜𝓇𝒾𝑒𝓈.
                    </h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                        <div class="bg-gray-50 p-6 rounded-lg border-l-4 border-accent animate-fadeInUp"
                            style="animation-delay: 0.3s;">
                            <h3 class="text-md sm:text-xl xl-text-base font-bold text-gray-800 mb-3">Our Mission</h3>
                            <p class="text-gray-600 text-xs sm:text-sm md:text-base lg:text-sm xl:text-base">
                                To provide exceptional hospitality services while preserving the natural beauty that
                                defines our location, creating memorable experiences for every guest.
                            </p>
                        </div>
                        <div class="bg-gray-50 p-6 rounded-lg border-l-4 border-accent animate-fadeInUp"
                            style="animation-delay: 0.4s;">
                            <h3 class="text-md sm:text-xl xl-text-base font-bold text-gray-800 mb-3">Our Vision</h3>
                            <p class="text-gray-600 text-xs sm:text-sm md:text-base lg:text-sm xl:text-base">
                                To be the premier destination for luxury retreats and exceptional event venues in the
                                region.
                            </p>
                        </div>
                    </div>

                    <div class="flex items-center space-x-6">
                        <div class="text-center animate-fadeInUp" style="animation-delay: 0.5s;">
                            <p class="text-3xl font-bold text-primary">50+</p>
                            <p class="text-gray-600 text-xs sm:text-sm">Luxury Rooms</p>
                        </div>
                        <div class="text-center animate-fadeInUp" style="animation-delay: 0.6s;">
                            <p class="text-3xl font-bold text-primary">100+</p>
                            <p class="text-gray-600 text-xs sm:text-sm">Events Hosted</p>
                        </div>
                        <div class="text-center animate-fadeInUp" style="animation-delay: 0.7s;">
                            <p class="text-3xl font-bold text-primary">5+</p>
                            <p class="text-gray-600 text-xs sm:text-sm">Recreational Areas</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Services Section -->
    <section id="services" class="py-20 bg-gray-50">
        <div class="container mx-auto px-6">
            <div class="text-center mb-16">
                <h2 class="text-2xl md:text-4xl text-darkAccent mb-6 font-serif font-light animate-fadeInUp"
                    style="animation-delay: 0.2s;">
                    𝒟𝒾𝓈𝒸𝑜𝓋𝑒𝓇 𝑜𝓊𝓇 𝒸𝑜𝓂𝓅𝓇𝑒𝒽𝑒𝓃𝓈𝒾𝓋𝑒 𝓇𝒶𝓃𝑔𝑒 𝑜𝒻 𝓅𝓇𝑒𝓂𝒾𝓊𝓂 𝓈𝑒𝓇𝓋𝒾𝒸𝑒𝓈
                </h2>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <!-- Accommodations -->
                <div class="service-card bg-white rounded-lg overflow-hidden shadow-md hover:shadow-xl transition-all duration-300 animate-fadeInUp"
                    style="animation-delay: 0.1s;">
                    <div class="h-64 overflow-hidden">
                        <img src="{{ url('/imgs/room.jpg') }}" alt="Luxury Accommodations"
                            class="w-full h-full object-cover transition-transform duration-500 hover:scale-105">
                    </div>
                    <div class="p-6">
                        <div class="flex items-center mb-4">
                            <div class="bg-accent p-2 rounded-full mr-4">
                                <i class="fas fa-bed text-white"></i>
                            </div>
                            <h3 class="text-md sm:text-xl xl-text-base font-bold text-gray-800">Luxury Accommodations
                            </h3>
                        </div>
                        <p class="text-gray-600 mb-4 text-xs sm:text-sm md:text-base lg:text-sm xl:text-base">
                            Experience unparalleled comfort in our well-appointed rooms and cottages, each designed with
                            your relaxation in mind.
                        </p>
                        <ul class="space-y-2 mb-6 text-xs sm:text-sm md:text-base lg:text-sm xl:text-base">
                            <li class="flex items-center">
                                <i class="fas fa-check text-accent mr-2"></i>
                                Air-conditioned rooms with modern amenities
                            </li>
                            <li class="flex items-center">
                                <i class="fas fa-check text-accent mr-2"></i>
                                Private bathrooms with premium toiletries
                            </li>
                            <li class="flex items-center">
                                <i class="fas fa-check text-accent mr-2"></i>
                                Panoramic mountain views
                            </li>
                        </ul>
                        <a href="{{ route('customer_bookings') }}"
                            class="inline-flex items-center text-primary hover:text-secondary font-medium transition duration-300">
                            View Accommodations
                            <i class="fas fa-arrow-right ml-2"></i>
                        </a>
                    </div>
                </div>

                <!-- Event Hosting -->
                <div class="service-card bg-white rounded-lg overflow-hidden shadow-md hover:shadow-xl transition-all duration-300 animate-fadeInUp"
                    style="animation-delay: 0.2s;">
                    <div class="h-64 overflow-hidden">
                        <img src="{{ url('/imgs/event.jpg') }}" alt="Event Hosting"
                            class="w-full h-full object-cover transition-transform duration-500 hover:scale-105">
                    </div>
                    <div class="p-6">
                        <div class="flex items-center mb-4">
                            <div class="bg-accent p-2 rounded-full mr-4">
                                <i class="fas fa-calendar-alt text-white"></i>
                            </div>
                            <h3 class="text-md sm:text-xl xl-text-base font-bold text-gray-800">Event Hosting</h3>
                        </div>
                        <p class="text-gray-600 mb-4 text-xs sm:text-sm md:text-base lg:text-sm xl:text-base">
                            Our versatile venues and professional event services ensure your special occasion is
                            executed flawlessly.
                        </p>
                        <ul class="space-y-2 mb-6 text-xs sm:text-sm md:text-base lg:text-sm xl:text-base">
                            <li class="flex items-center">
                                <i class="fas fa-check text-accent mr-2"></i>
                                Wedding receptions and ceremonies
                            </li>
                            <li class="flex items-center">
                                <i class="fas fa-check text-accent mr-2"></i>
                                Corporate meetings and retreats
                            </li>
                            <li class="flex items-center">
                                <i class="fas fa-check text-accent mr-2"></i>
                                Birthday and anniversary celebrations
                            </li>
                        </ul>
                        <a href="{{ route('events') }}"
                            class="inline-flex items-center text-primary hover:text-secondary font-medium transition duration-300">
                            Plan Your Event
                            <i class="fas fa-arrow-right ml-2"></i>
                        </a>
                    </div>
                </div>

                <!-- Recreational Activities -->
                <div class="service-card bg-white rounded-lg overflow-hidden shadow-md hover:shadow-xl transition-all duration-300 animate-fadeInUp"
                    style="animation-delay: 0.3s;">
                    <div class="h-64 overflow-hidden">
                        <img src="{{ url('/imgs/recreational_activities.png') }}" alt="Recreational Activities"
                            class="w-full h-full object-cover transition-transform duration-500 hover:scale-105">
                    </div>
                    <div class="p-6">
                        <div class="flex items-center mb-4">
                            <div class="bg-accent p-2 rounded-full mr-4">
                                <i class="fas fa-swimming-pool text-white"></i>
                            </div>
                            <h3 class="text-md sm:text-xl xl-text-base font-bold text-gray-800">Recreational Activities
                            </h3>
                        </div>
                        <p class="text-gray-600 mb-4 text-xs sm:text-sm md:text-base lg:text-sm xl:text-base">
                            Engage in a variety of leisure activities designed to rejuvenate your mind and body.
                        </p>
                        <ul class="space-y-2 mb-6 text-xs sm:text-sm md:text-base lg:text-sm xl:text-base">
                            <li class="flex items-center">
                                <i class="fas fa-check text-accent mr-2"></i>
                                Swimming pools for all ages
                            </li>
                            <li class="flex items-center">
                                <i class="fas fa-check text-accent mr-2"></i>
                                Scenic nature trails
                            </li>
                            <li class="flex items-center">
                                <i class="fas fa-check text-accent mr-2"></i>
                                Picnic areas with mountain views
                            </li>
                        </ul>
                        <a href="{{ route('dashboard.bookings') }}"
                            class="inline-flex items-center text-primary hover:text-secondary font-medium transition duration-300">
                            Book Now
                            <i class="fas fa-arrow-right ml-2"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Gallery Section -->
    <section id="gallery" class="py-20 bg-white">
        <div class="container mx-auto px-6">
            <div class="text-center mb-16">
                <h2 class="text-2xl md:text-4xl text-darkAccent mb-6 font-serif font-light animate-fadeInUp"
                    style="animation-delay: 0.2s;">
                    𝒜 𝓋𝒾𝓈𝓊𝒶𝓁 𝒿𝑜𝓊𝓇𝓃𝑒𝓎 𝓉𝒽𝓇𝑜𝓊𝑔𝒽 𝑜𝓊𝓇 𝑒𝓍𝓆𝓊𝒾𝓈𝒾𝓉𝑒 𝒻𝒶𝒸𝒾𝓁𝒾𝓉𝒾𝑒𝓈 𝒶𝓃𝒹
                    𝒷𝓇𝑒𝒶𝓉𝒽𝓉𝒶𝓀𝒾𝓃𝑔 𝓈𝓊𝓇𝓇𝑜𝓊𝓃𝒹𝒾𝓃𝑔𝓈
                </h2>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                @foreach([1, 222, 3, 4, 5, 66, 7, 8] as $i)
                    <div class="group gallery-item relative overflow-hidden rounded-lg shadow-md hover:shadow-xl transition-all duration-300 animate-fadeInUp"
                        style="animation-delay: {{ $i * 0.1 }}s;">
                        <img src="{{ url('imgs/gallery/' . $i . '.jpg') }}" alt="Gallery image {{ $i }}"
                            class="w-full h-48 sm:h-64 md:h-80 lg:h-96 object-cover transition-transform duration-500 group-hover:scale-110">
                        <div
                            class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-end p-4">
                            <h3
                                class="text-white font-medium translate-y-4 group-hover:translate-y-0 transition-transform duration-300">
                                Resort View {{ $i }}
                            </h3>
                        </div>
                    </div>
                @endforeach
            </div>

        </div>
    </section>

    <!-- Main section with wavy lines -->
    <section class="relative py-20 md:py-80 text-white overflow-hidden">
        <!-- Wavy line top -->
        <div class="wavy-line wavy-line-top">
            <svg data-name="Layer 1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1200 120"
                preserveAspectRatio="none">
                <path
                    d="M321.39,56.44c58-10.79,114.16-30.13,172-41.86,82.39-16.72,168.19-17.73,250.45-.39C823.78,31,906.67,72,985.66,92.83c70.05,18.48,146.53,26.09,214.34,3V0H0V27.35A600.21,600.21,0,0,0,321.39,56.44Z"
                    class="shape-fill"></path>
            </svg>
        </div>

        <!-- Background Image with Overlay -->
        <div class="absolute inset-0 z-0">
            <!-- Background image -->
            <div class="bg-[url('{{ url('imgs/contact_bg.jpg') }}')] bg-cover bg-center bg-no-repeat absolute inset-0">
            </div>
            <!-- Dark overlay -->
            <div class="absolute inset-0 bg-gradient-to-r from-red-900/80 to-red-900/70"></div>
        </div>

        <div class="container mx-auto px-6 text-center relative z-10">
            <h2 class="text-2xl md:text-4xl lg:text-5xl font-bold mb-6 font-serif animate-fadeInUp">
                Ready for an Unforgettable Experience?
            </h2>
            <p class="text-sm md:text-lg lg:text-xl mb-8 max-w-2xl mx-auto text-gray-100 animate-fadeInUp"
                style="animation-delay: 0.2s;">
                Book your stay and discover the magic of Mt. ClaRamuel
            </p>
            <div class="flex flex-col sm:flex-row justify-center gap-4 animate-fadeInUp" style="animation-delay: 0.4s;">
                <a href="#"
                    class="inline-block bg-gradient-to-r from-amber-500 to-orange-500 hover:from-amber-600 hover:to-orange-600 text-white font-semibold px-8 py-4 rounded-lg shadow-lg transition-all duration-300 transform hover:scale-105 hover:shadow-xl">
                    Book Now
                </a>
                <a href="tel:+639952901333"
                    class="inline-block border-2 border-white hover:bg-white/20 text-white font-semibold px-8 py-4 rounded-lg shadow-lg transition-all duration-300 transform hover:scale-105">
                    <i class="fas fa-phone mr-2"></i> +63 995 290 1333
                </a>
            </div>
        </div>

        <!-- Wavy line bottom -->
        <div class="wavy-line wavy-line-bottom">
            <svg data-name="Layer 1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1200 120"
                preserveAspectRatio="none">
                <path
                    d="M321.39,56.44c58-10.79,114.16-30.13,172-41.86,82.39-16.72,168.19-17.73,250.45-.39C823.78,31,906.67,72,985.66,92.83c70.05,18.48,146.53,26.09,214.34,3V0H0V27.35A600.21,600.21,0,0,0,321.39,56.44Z"
                    class="shape-fill"></path>
            </svg>
        </div>
    </section>


    <!-- Contact Section -->
    <section id="contact" class="py-20 bg-white">
        <div class="container mx-auto px-6">
            <div class="flex flex-col lg:flex-row gap-12">
                <div class="lg:w-1/2">
                    <h2
                        class="text-2xl md:text-4xl font-bold text-gray-800 mb-6 section-title font-serif animate-fadeInUp">
                        Contact <span class="text-primary">Information</span>
                    </h2>

                    <div class="space-y-6">
                        <div class="flex items-start animate-fadeInUp" style="animation-delay: 0.2s;">
                            <div class="bg-accent p-3 rounded-full mr-4 flex-shrink-0">
                                <i class="fas fa-map-marker-alt text-white"></i>
                            </div>
                            <div>
                                <h3 class="text-md sm:text-sm md:text-xl lg:text-2xl font-semibold text-gray-800 mb-1">
                                    Location</h3>
                                <p
                                    class="text-gray-600 text-xs sm:text-sm md:text-base lg:text-lg leading-relaxed tracking-wide md:px-0">
                                    Narra Street, Brgy. Marana 3rd, Ilagan, 3300 Isabela, Philippines
                                </p>

                            </div>
                        </div>

                        <div class="flex items-start animate-fadeInUp" style="animation-delay: 0.3s;">
                            <div class="bg-accent p-3 rounded-full mr-4 flex-shrink-0">
                                <i class="fas fa-phone text-white"></i>
                            </div>
                            <div>
                                <h3 class="text-md sm:text-sm md:text-xl lg:text-2xl font-semibold text-gray-800 mb-1">
                                    Contact</h3>
                                <p class="text-gray-600 mb-1">
                                    <a href="tel:+639952901333"
                                        class="hover:text-secondary transition text-gray-600 text-xs sm:text-sm md:text-base lg:text-lg leading-relaxed tracking-wide md:px-0">+63
                                        995 290 1333</a>
                                </p>
                                <p class="text-gray-600">
                                    <a href="mailto:mtclaramuelresort@gmail.com"
                                        class="hover:text-secondary transition text-gray-600 text-xs sm:text-sm md:text-base lg:text-lg leading-relaxed tracking-wide md:px-0">mtclaramuelresort@gmail.com</a>
                                </p>
                            </div>
                        </div>

                        <div class="flex items-start animate-fadeInUp" style="animation-delay: 0.4s;">
                            <div class="bg-accent p-3 rounded-full mr-4 flex-shrink-0">
                                <i class="fas fa-clock text-white"></i>
                            </div>
                            <div>
                                <h3 class="text-md sm:text-sm md:text-xl lg:text-2xl font-semibold text-gray-800 mb-1">
                                    Operating Hours</h3>
                                <p
                                    class="text-gray-600 text-gray-600 text-xs sm:text-sm md:text-base lg:text-lg leading-relaxed tracking-wide md:px-0">
                                    Daily: 8:00 AM - 10:00 PM
                                </p>
                            </div>
                        </div>

                        <div class="pt-4 animate-fadeInUp" style="animation-delay: 0.5s;">
                            <h3 class="text-xl font-semibold text-gray-800 mb-4">Connect With Us</h3>
                            <div class="flex gap-4">
                                <a href="https://www.facebook.com/mtclaramuelresort" target="_blank"
                                    class="bg-blue-600 hover:bg-blue-700 text-white w-12 h-12 rounded-full flex items-center justify-center transition">
                                    <i class="fab fa-facebook-f"></i>
                                </a>
                                <a href="https://www.instagram.com/mt_claramuelresort/" target="_blank"
                                    class="bg-gradient-to-r from-purple-500 to-pink-500 hover:from-purple-600 hover:to-pink-600 text-white w-12 h-12 rounded-full flex items-center justify-center transition">
                                    <i class="fab fa-instagram"></i>
                                </a>
                                <a href="#"
                                    class="bg-green-600 hover:bg-green-700 text-white w-12 h-12 rounded-full flex items-center justify-center transition">
                                    <i class="fab fa-whatsapp"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="lg:w-1/2 h-96 rounded-xl overflow-hidden shadow-xl border border-gray-200 animate-fadeInUp"
                    style="animation-delay: 0.6s;">
                    <iframe
                        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d789.1338574963425!2d121.9251491!3d17.142796!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x33856b144a6449f5%3A0xea1ad60f5e068495!2sMt.%20Claramuel%20Resort%20and%20Events%20Place!5e0!3m2!1sen!2sph!4v1711431557"
                        width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
                </div>
            </div>
        </div>
    </section>

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
                        A premier destination offering luxury accommodations and exceptional event venues in the heart
                        of Isabela's natural beauty.
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
                        <li class="animate-fadeInUp" style="animation-delay: 0.5s;"><a href="#home"
                                class="text-gray-400 hover:text-accent transition">Home</a></li>
                        <li class="animate-fadeInUp" style="animation-delay: 0.6s;"><a href="#about"
                                class="text-gray-400 hover:text-accent transition">About Us</a></li>
                        <li class="animate-fadeInUp" style="animation-delay: 0.7s;"><a href="#services"
                                class="text-gray-400 hover:text-accent transition">Services</a></li>
                        <li class="animate-fadeInUp" style="animation-delay: 0.8s;"><a href="#gallery"
                                class="text-gray-400 hover:text-accent transition">Gallery</a></li>
                        <li class="animate-fadeInUp" style="animation-delay: 0.9s;"><a href="#contact"
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
        // Mobile menu toggle
        const mobileMenuButton = document.getElementById('mobile-menu-button');
        const mobileMenu = document.getElementById('mobile-menu');

        mobileMenuButton.addEventListener('click', () => {
            mobileMenu.classList.toggle('translate-x-0');
            mobileMenu.classList.toggle('translate-x-full');

            // Toggle between hamburger and close icon
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
                    // Scrolling down - hide header
                    header.style.transform = 'translateY(-100%)';
                } else {
                    // Scrolling up - show header
                    header.style.transform = 'translateY(0)';
                }
            } else {
                // At top of page - always show header
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

        // Video sound toggle
        const video = document.querySelector('video');
        const soundToggle = document.getElementById('sound-toggle');
        soundToggle.addEventListener('click', () => {
            video.muted = !video.muted;
            soundToggle.innerHTML = video.muted ? '<i class="fas fa-volume-mute"></i>' : '<i class="fas fa-volume-up"></i>';
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
    </script>
</body>

</html>