<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mt. ClaRamuel Resort & Events Place</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#1a365d',
                        secondary: '#e53e3e',
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-white font-sans text-gray-800">
    <!-- Header -->
    <header class="bg-primary text-white fixed w-full shadow-md z-50">
        <div class="container mx-auto px-6 py-4 flex justify-between items-center">
            <div class="flex items-center space-x-2 group">
                <img src="{{ url('imgs/logo.png') }}" class="h-10" alt="Logo">
                <a href="#" class="text-2xl font-bold">
                    <span class="text-white">Mt.</span><span class="text-secondary">ClaRamuel</span>
                </a>
            </div>
            <nav class="hidden md:flex space-x-8 items-center">
                <a href="#home" class="hover:text-secondary transition">Home</a>
                <a href="#about" class="hover:text-secondary transition">About</a>
                <a href="#services" class="hover:text-secondary transition">Services</a>
                <a href="#gallery" class="hover:text-secondary transition">Gallery</a>
                <a href="#contact" class="hover:text-secondary transition">Contact</a>
                <a href="{{ route('login') }}" class="hover:text-secondary transition">Login</a>
                <a href="{{ route('customer_bookings') }}" class="bg-secondary hover:bg-red-700 px-4 py-2 rounded transition flex items-center">
                    <i class="fas fa-calendar-check mr-2"></i> Book Now
                </a>
            </nav>
            <button class="md:hidden text-white focus:outline-none">
                <i class="fas fa-bars text-xl"></i>
            </button>
        </div>
    </header>

    <!-- Hero Section with Video -->
    <section id="home" class="pt-32 pb-20 relative h-screen flex items-center justify-center text-center overflow-hidden">
        <video autoplay loop muted playsinline class="absolute inset-0 w-full h-full object-cover z-0">
            <source src="{{ url('video/welcomeVideo.mp4') }}" type="video/mp4">
        </video>
        <div class="absolute inset-0 bg-black/40 z-10"></div>
        <div class="relative z-20 text-white px-6">
            <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold mb-6">
                <span class="text-secondary">Mt. ClaRamuel</span> Resort & Events Place
            </h1>
            <p class="text-xl md:text-2xl mb-8 max-w-3xl mx-auto">
                Experience a Perfect Getaway with Nature's Serenity and Modern Comfort
            </p>
            <a href="{{ route('customer_bookings') }}" class="inline-block bg-secondary hover:bg-red-700 text-white font-semibold px-8 py-3 rounded-md shadow-lg transition">
                Book Now
            </a>
        </div>
    </section>

    <!-- Highlights Bar -->
    <div class="bg-gray-800 text-white py-4">
        <div class="container mx-auto px-6">
            <div class="flex flex-wrap justify-center items-center gap-6 md:gap-12">
                <div class="flex items-center">
                    <i class="fas fa-clock text-secondary mr-2"></i>
                    <span>Open Daily 8AM-10PM</span>
                </div>
                <div class="flex items-center">
                    <i class="fas fa-home text-secondary mr-2"></i>
                    <span>Accommodations</span>
                </div>
                <div class="flex items-center">
                    <i class="fas fa-calendar-alt text-secondary mr-2"></i>
                    <span>Event Hosting</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Services Section -->
    <section id="services" class="py-20">
        <div class="container mx-auto px-6">
            <div class="text-center mb-16">
                <h2 class="text-3xl md:text-4xl font-bold mb-4">Our <span class="text-secondary">Services</span></h2>
                <div class="w-20 h-1 bg-secondary mx-auto mb-6"></div>
                <p class="text-lg text-gray-600 max-w-2xl mx-auto">
                    We offer a variety of premium services to make your stay comfortable and memorable
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <!-- Service Card 1 -->
                <div class="bg-white rounded-lg shadow-md overflow-hidden transition-transform hover:scale-105">
                    <div class="h-64 overflow-hidden">
                        <img src="{{ url('/imgs/room.jpg') }}" alt="Accommodations" class="w-full h-full object-cover">
                    </div>
                    <div class="p-6">
                        <h3 class="text-xl font-bold mb-3">Accommodations</h3>
                        <p class="text-gray-600 mb-4">
                            Cozy rooms and cottages designed for ultimate relaxation with modern amenities.
                        </p>
                        <ul class="space-y-2 mb-6">
                            <li class="flex items-center">
                                <i class="fas fa-check text-secondary mr-2"></i>
                                Air-conditioned rooms
                            </li>
                            <li class="flex items-center">
                                <i class="fas fa-check text-secondary mr-2"></i>
                                Private bathrooms
                            </li>
                            <li class="flex items-center">
                                <i class="fas fa-check text-secondary mr-2"></i>
                                Mountain views
                            </li>
                        </ul>
                        <a href="{{ route('customer_bookings') }}" class="text-secondary hover:text-red-700 font-medium inline-flex items-center">
                            View details
                            <i class="fas fa-arrow-right ml-2"></i>
                        </a>
                    </div>
                </div>

                <!-- Service Card 2 -->
                <div class="bg-white rounded-lg shadow-md overflow-hidden transition-transform hover:scale-105">
                    <div class="h-64 overflow-hidden">
                        <img src="{{ url('/imgs/event.jpg') }}" alt="Event Hosting" class="w-full h-full object-cover">
                    </div>
                    <div class="p-6">
                        <h3 class="text-xl font-bold mb-3">Event Hosting</h3>
                        <p class="text-gray-600 mb-4">
                            Perfect venues for weddings, corporate events, and parties with professional services.
                        </p>
                        <ul class="space-y-2 mb-6">
                            <li class="flex items-center">
                                <i class="fas fa-check text-secondary mr-2"></i>
                                Wedding receptions
                            </li>
                            <li class="flex items-center">
                                <i class="fas fa-check text-secondary mr-2"></i>
                                Corporate retreats
                            </li>
                            <li class="flex items-center">
                                <i class="fas fa-check text-secondary mr-2"></i>
                                Birthday celebrations
                            </li>
                        </ul>
                        <a href="{{ route('events') }}" class="text-secondary hover:text-red-700 font-medium inline-flex items-center">
                            Plan your event
                            <i class="fas fa-arrow-right ml-2"></i>
                        </a>
                    </div>
                </div>

                <!-- Service Card 3 -->
                <div class="bg-white rounded-lg shadow-md overflow-hidden transition-transform hover:scale-105">
                    <div class="h-64 overflow-hidden">
                        <img src="{{ url('/imgs/recreational_activities.png') }}" alt="Activities" class="w-full h-full object-cover">
                    </div>
                    <div class="p-6">
                        <h3 class="text-xl font-bold mb-3">Recreational Activities</h3>
                        <p class="text-gray-600 mb-4">
                            Enjoy nature walks, swimming, cycling, and other fun activities for all ages.
                        </p>
                        <ul class="space-y-2 mb-6">
                            <li class="flex items-center">
                                <i class="fas fa-check text-secondary mr-2"></i>
                                Swimming pools
                            </li>
                            <li class="flex items-center">
                                <i class="fas fa-check text-secondary mr-2"></i>
                                Nature trails
                            </li>
                            <li class="flex items-center">
                                <i class="fas fa-check text-secondary mr-2"></i>
                                Picnic areas
                            </li>
                        </ul>
                        <a href="{{ route('Pools_Park') }}" class="text-secondary hover:text-red-700 font-medium inline-flex items-center">
                            See activities
                            <i class="fas fa-arrow-right ml-2"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- About Section -->
    <section id="about" class="py-20 bg-gray-50">
        <div class="container mx-auto px-6">
            <div class="flex flex-col lg:flex-row items-center gap-12">
                <div class="lg:w-1/2">
                    <h2 class="text-3xl md:text-4xl font-bold mb-6">About <span class="text-secondary">Mt. ClaRamuel</span></h2>
                    <div class="w-20 h-1 bg-secondary mb-8"></div>
                    
                    <p class="text-gray-600 mb-6">
                        Nestled in the heart of nature, Mt. ClaRamuel Resort & Events Place offers the perfect blend of relaxation and memorable experiences.
                    </p>
                    
                    <div class="space-y-6 mb-8">
                        <div class="flex">
                            <div class="flex-shrink-0 mt-1">
                                <i class="fas fa-check-circle text-secondary mr-3"></i>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold mb-2">Our Mission</h3>
                                <p class="text-gray-600">
                                    To provide exceptional hospitality services while preserving the natural beauty of our surroundings.
                                </p>
                            </div>
                        </div>
                        
                        <div class="flex">
                            <div class="flex-shrink-0 mt-1">
                                <i class="fas fa-check-circle text-secondary mr-3"></i>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold mb-2">Our Vision</h3>
                                <p class="text-gray-600">
                                    To be the premier destination for those seeking tranquility and exceptional event experiences.
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="flex flex-wrap gap-4">
                        <div class="bg-white px-6 py-4 rounded-lg shadow-sm">
                            <span class="block text-2xl font-bold text-secondary">50+</span>
                            <span class="text-gray-600">Rooms & Cottages</span>
                        </div>
                        <div class="bg-white px-6 py-4 rounded-lg shadow-sm">
                            <span class="block text-2xl font-bold text-secondary">100+</span>
                            <span class="text-gray-600">Events Hosted</span>
                        </div>
                    </div>
                </div>
                
                <div class="lg:w-1/2 grid grid-cols-2 gap-4">
                    <img src="{{ url('imgs/welcome_pic.png') }}" alt="Resort" class="rounded-lg shadow-md h-64 w-full object-cover">
                    <img src="{{ url('imgs/pool.jpg') }}" alt="Pool" class="rounded-lg shadow-md h-64 w-full object-cover mt-8">
                    <img src="{{ url('imgs/coffeeShop.jpg') }}" alt="Restaurant" class="rounded-lg shadow-md h-64 w-full object-cover -mt-8">
                    <img src="{{ url('imgs/event.jpg') }}" alt="Event" class="rounded-lg shadow-md h-64 w-full object-cover">
                </div>
            </div>
        </div>
    </section>

    <!-- Gallery Section -->
    <section id="gallery" class="py-20">
        <div class="container mx-auto px-6">
            <div class="text-center mb-16">
                <h2 class="text-3xl md:text-4xl font-bold mb-4">Resort <span class="text-secondary">Gallery</span></h2>
                <div class="w-20 h-1 bg-secondary mx-auto mb-6"></div>
                <p class="text-lg text-gray-600 max-w-2xl mx-auto">
                    Explore the beauty of Mt. ClaRamuel through our photo gallery
                </p>
            </div>
            
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                @foreach([1,222,3,4,5,66,7,8] as $i)
                <a href="{{ url('imgs/gallery/'.$i.'.jpg') }}" class="gallery-item group block relative">
                    <div class="aspect-square overflow-hidden rounded-lg shadow-md hover:shadow-lg transition-shadow duration-300">
                        <img src="{{ url('imgs/gallery/'.$i.'.jpg') }}" 
                             alt="Gallery image {{ $i }}" 
                             class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105">
                        <div class="absolute inset-0 bg-black/20 group-hover:bg-black/30 transition-colors duration-300 flex items-center justify-center">
                            <i class="fas fa-search text-white opacity-0 group-hover:opacity-100 transition-opacity duration-300 transform group-hover:scale-110"></i>
                        </div>
                    </div>
                </a>
                @endforeach
            </div>
            
            <div class="text-center mt-12">
                <a href="#" class="inline-block bg-secondary hover:bg-red-700 text-white px-6 py-3 rounded-md shadow-lg transition">
                    View More Photos
                </a>
            </div>
        </div>
    </section>
    

    <!-- CTA Section -->
    <section class="py-20 bg-primary text-white">
        <div class="container mx-auto px-6 text-center">
            <h2 class="text-3xl md:text-4xl font-bold mb-6">Ready for an Unforgettable Experience?</h2>
            <p class="text-xl mb-8 max-w-2xl mx-auto text-gray-200">
                Book your stay or event today and discover the magic of Mt. ClaRamuel
            </p>
            <div class="flex flex-col sm:flex-row justify-center gap-4">
                <a href="{{ route('customer_bookings') }}" class="inline-block bg-white hover:bg-gray-100 text-secondary font-semibold px-8 py-3 rounded-md shadow-lg transition">
                    Book Now
                </a>
                <a href="tel:+639952901333" class="inline-block border-2 border-white hover:bg-white/10 text-white font-semibold px-8 py-3 rounded-md shadow-lg transition">
                    Call Us: +63 995 290 1333
                </a>
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section id="contact" class="py-20 bg-gray-50">
        <div class="container mx-auto px-6">
            <div class="flex flex-col lg:flex-row gap-12">
                <div class="lg:w-1/2">
                    <h2 class="text-3xl md:text-4xl font-bold mb-6">Visit <span class="text-secondary">Us Today</span></h2>
                    <div class="w-20 h-1 bg-secondary mb-8"></div>
                    
                    <div class="space-y-6">
                        <div>
                            <h3 class="text-xl font-semibold mb-2">Location</h3>
                            <p class="text-gray-600">
                                Narra Street, Brgy. Marana 3rd, Ilagan, 3300 Isabela, Philippines
                            </p>
                        </div>
                        
                        <div>
                            <h3 class="text-xl font-semibold mb-2">Contact Information</h3>
                            <p class="text-gray-600 mb-2">
                                <a href="tel:+639952901333" class="hover:text-secondary transition">+63 995 290 1333</a>
                            </p>
                            <p class="text-gray-600">
                                <a href="mailto:mtclaramuelresort@gmail.com" class="hover:text-secondary transition">mtclaramuelresort@gmail.com</a>
                            </p>
                        </div>
                        
                        <div>
                            <h3 class="text-xl font-semibold mb-2">Operating Hours</h3>
                            <p class="text-gray-600">
                                Daily: 8:00 AM - 10:00 PM
                            </p>
                        </div>
                        
                        <div class="pt-4">
                            <h3 class="text-xl font-semibold mb-4">Follow Us</h3>
                            <div class="flex gap-4">
                                <a href="https://www.facebook.com/mtclaramuelresort" target="_blank" class="bg-blue-600 hover:bg-blue-700 text-white w-10 h-10 rounded-full flex items-center justify-center transition">
                                    <i class="fab fa-facebook-f"></i>
                                </a>
                                <a href="https://www.instagram.com/mt_claramuelresort/" target="_blank" class="bg-gradient-to-r from-purple-500 to-pink-500 hover:from-purple-600 hover:to-pink-600 text-white w-10 h-10 rounded-full flex items-center justify-center transition">
                                    <i class="fab fa-instagram"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="lg:w-1/2 h-96 rounded-xl overflow-hidden shadow-xl border border-gray-200">
                    <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d789.1338574963425!2d121.9251491!3d17.142796!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x33856b144a6449f5%3A0xea1ad60f5e068495!2sMt.%20Claramuel%20Resort%20and%20Events%20Place!5e0!3m2!1sen!2sph!4v1711431557" 
                            width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-800 text-white pt-16 pb-8">
        <div class="container mx-auto px-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-12 mb-12">
                <div>
                    <div class="flex items-center mb-4">
                        <img src="{{ url('imgs/logo.png') }}" class="h-12 mr-3" alt="Logo">
                        <span class="text-2xl font-bold">Mt. ClaRamuel Resort</span>
                    </div>
                    <p class="text-gray-400 mb-4">
                        Your perfect getaway destination offering luxury, comfort, and unforgettable experiences in the heart of nature.
                    </p>
                    <div class="flex gap-4">
                        <a href="https://www.facebook.com/mtclaramuelresort" target="_blank" class="text-gray-400 hover:text-white transition">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a href="https://www.instagram.com/mt_claramuelresort/" target="_blank" class="text-gray-400 hover:text-white transition">
                            <i class="fab fa-instagram"></i>
                        </a>
                    </div>
                </div>
                
                <div>
                    <h3 class="text-lg font-semibold mb-4">Quick Links</h3>
                    <ul class="space-y-2">
                        <li><a href="#home" class="text-gray-400 hover:text-white transition">Home</a></li>
                        <li><a href="#about" class="text-gray-400 hover:text-white transition">About Us</a></li>
                        <li><a href="#services" class="text-gray-400 hover:text-white transition">Services</a></li>
                        <li><a href="#gallery" class="text-gray-400 hover:text-white transition">Gallery</a></li>
                        <li><a href="#contact" class="text-gray-400 hover:text-white transition">Contact</a></li>
                    </ul>
                </div>
                
                <div>
                    <h3 class="text-lg font-semibold mb-4">Services</h3>
                    <ul class="space-y-2">
                        <li><a href="#" class="text-gray-400 hover:text-white transition">Accommodations</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition">Event Hosting</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition">Recreational Activities</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition">Dining</a></li>
                    </ul>
                </div>
                
                <div>
                    <h3 class="text-lg font-semibold mb-4">Contact Us</h3>
                    <ul class="space-y-2 text-gray-400">
                        <li class="flex items-start">
                            <i class="fas fa-map-marker-alt text-secondary mr-3 mt-1"></i>
                            Narra Street, Brgy. Marana 3rd, Ilagan, Isabela
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-phone text-secondary mr-3"></i>
                            <a href="tel:+639952901333" class="hover:text-white transition">+63 995 290 1333</a>
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-envelope text-secondary mr-3"></i>
                            <a href="mailto:mtclaramuelresort@gmail.com" class="hover:text-white transition">mtclaramuelresort@gmail.com</a>
                        </li>
                    </ul>
                </div>
            </div>
            
            <div class="border-t border-gray-700 pt-8 flex flex-col md:flex-row justify-between items-center">
                <p class="text-gray-400 mb-4 md:mb-0">
                    © 2025 Mt. ClaRamuel Resort. All rights reserved.
                </p>
                <div class="flex gap-6">
                    <a href="#" class="text-gray-400 hover:text-white transition">Privacy Policy</a>
                    <a href="#" class="text-gray-400 hover:text-white transition">Terms of Service</a>
                </div>
            </div>
        </div>
    </footer>

    <!-- Back to Top Button -->
    <button id="back-to-top" class="fixed bottom-8 right-8 bg-secondary text-white p-3 rounded-full shadow-lg opacity-0 invisible transition-all z-50">
        <i class="fas fa-arrow-up"></i>
    </button>

    <script>
        // Mobile menu toggle
        const mobileMenuButton = document.querySelector('.md\\:hidden');
        // You would need to add mobile menu functionality here
        
        // Smooth scrolling for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
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
        const soundToggle = document.createElement('button');
        soundToggle.innerHTML = '<i class="fas fa-volume-mute"></i>';
        soundToggle.className = 'absolute bottom-8 right-8 bg-black/50 text-white p-3 rounded-full shadow-lg z-30';
        soundToggle.addEventListener('click', () => {
            video.muted = !video.muted;
            soundToggle.innerHTML = video.muted ? '<i class="fas fa-volume-mute"></i>' : '<i class="fas fa-volume-up"></i>';
        });
        document.querySelector('#home').appendChild(soundToggle);
    </script>
</body>
</html>