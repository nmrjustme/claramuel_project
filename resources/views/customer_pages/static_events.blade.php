<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Packages We Offer</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            background-color: white !important;
            background-attachment: fixed;
            min-height: 100vh;
        }
        
        .package-card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        
        .package-card:hover {
            transform: translateY(-5px);
        }
        
        .image-container {
            height: 200px;
            position: relative;
            overflow: hidden;
        }
        
        .image-container::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 30%;
            background: linear-gradient(to top, rgba(0,0,0,0.1), transparent);
        }
    </style>
</head>
<body class="bg-white">
    <div class="container mx-auto px-4 py-8">
        <!-- Back Button -->
        <div class="mb-6">
            <a href="{{ url()->previous() }}" class="inline-flex items-center text-red-500 hover:text-red-700 transition duration-300">
                <i class="fas fa-arrow-left mr-2"></i> Back
            </a>
        </div>

        <!-- Header -->
        <header class="mb-12 text-center">
            <h1 class="text-4xl font-bold text-red-500 mb-4">Event Packages We Offer</h1>
            <p class="text-xl text-gray-600">Plan your perfect event with our customizable packages</p>
        </header>

        <!-- Events Grid -->
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8 mb-12">
            <!-- Birthday Celebration Package -->
            <div class="package-card bg-white rounded-lg overflow-hidden shadow-md hover:shadow-lg border border-gray-200">
                <div class="image-container bg-[url('https://images.unsplash.com/photo-1513151233558-d860c5398176?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1470&q=80')] bg-cover bg-center"></div>
                <div class="p-6">
                    <div class="flex justify-between items-start mb-3">
                        <span class="bg-red-600 text-white text-sm px-3 py-1 rounded-full">Birthday</span>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-3">Birthday Party Package</h3>
                    <p class="text-gray-600 mb-4">Celebrate any age with personalized themes, vibrant decorations, and full-service catering.</p>
                </div>
            </div>

            <!-- Wedding Celebration Package -->
            <div class="package-card bg-white rounded-lg overflow-hidden shadow-md hover:shadow-lg border border-gray-200">
                <div class="image-container bg-[url('https://images.unsplash.com/photo-1583939003579-730e3918a45a?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1587&q=80')] bg-cover bg-center"></div>
                <div class="p-6">
                    <div class="flex justify-between items-start mb-3">
                        <span class="bg-red-600 text-white text-sm px-3 py-1 rounded-full">Wedding</span>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-3">Wedding Celebration Package</h3>
                    <p class="text-gray-600 mb-4">Comprehensive wedding services including venue setup, catering, coordination, and photography.</p>
                </div>
            </div>

            <!-- Corporate Event Package -->
            <div class="package-card bg-white rounded-lg overflow-hidden shadow-md hover:shadow-lg border border-gray-200">
                <div class="image-container bg-[url('https://images.unsplash.com/photo-1549488344-1f9b8d2bd1f3?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1470&q=80')] bg-cover bg-center"></div>
                <div class="p-6">
                    <div class="flex justify-between items-start mb-3">
                        <span class="bg-red-600 text-white text-sm px-3 py-1 rounded-full">Corporate</span>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-3">Corporate Event Package</h3>
                    <p class="text-gray-600 mb-4">Professional event solutions with meeting facilities, team-building activities, and premium catering.</p>
                </div>
            </div>

            <!-- Seminar & Workshop Package -->
            <div class="package-card bg-white rounded-lg overflow-hidden shadow-md hover:shadow-lg border border-gray-200">
                <div class="image-container bg-[url('https://images.unsplash.com/photo-1431540015161-0bf868a2d407?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1470&q=80')] bg-cover bg-center"></div>
                <div class="p-6">
                    <div class="flex justify-between items-start mb-3">
                        <span class="bg-red-600 text-white text-sm px-3 py-1 rounded-full">Seminar</span>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-3">Seminar & Workshop Package</h3>
                    <p class="text-gray-600 mb-4">Perfect for educational and training events with AV equipment and comfortable seating.</p>
                </div>
            </div>

            <!-- Valentine's Day Romantic Package -->
            <div class="package-card bg-white rounded-lg overflow-hidden shadow-md hover:shadow-lg border border-gray-200">
                <div class="image-container bg-[url('https://images.unsplash.com/photo-1518199266791-5375a83190b7?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1470&q=80')] bg-cover bg-center"></div>
                <div class="p-6">
                    <div class="flex justify-between items-start mb-3">
                        <span class="bg-red-600 text-white text-sm px-3 py-1 rounded-full">Valentine's</span>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-3">Valentine's Day Romantic Package</h3>
                    <p class="text-gray-600 mb-4">Create memorable moments with romantic dinners, private rooms, and couple's activities.</p>

                </div>
            </div>

            <!-- Group Gathering Package -->
            <div class="package-card bg-white rounded-lg overflow-hidden shadow-md hover:shadow-lg border border-gray-200">
                <div class="image-container bg-[url('https://images.unsplash.com/photo-1527525443983-6e60c75fff46?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1702&q=80')] bg-cover bg-center"></div>
                <div class="p-6">
                    <div class="flex justify-between items-start mb-3">
                        <span class="bg-red-600 text-white text-sm px-3 py-1 rounded-full">Gathering</span>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-3">Group Gathering Package</h3>
                    <p class="text-gray-600 mb-4">Perfect for family reunions, social events, or company outings for groups of 20 to 100 guests.</p>
                </div>
            </div>
        </div>

        <!-- Booking Information -->
        <div class="bg-white rounded-lg p-8 shadow-md border border-gray-200 max-w-3xl mx-auto">
            <!-- Back Button -->
            <div class="mb-6">
                <a href="{{ url()->previous() }}" class="inline-flex items-center text-red-500 hover:text-red-700 transition duration-300">
                    <i class="fas fa-arrow-left mr-2"></i> Back
                </a>
            </div>
            <h2 class="text-2xl font-bold text-gray-800 mb-6 text-center">How to Book</h2>
            <div class="grid md:grid-cols-2 gap-8">
                <div>
                    <h3 class="text-lg font-semibold text-red-500 mb-4 flex items-center">
                        <i class="fas fa-info-circle mr-2"></i> Contact Information
                    </h3>
                    <ul class="space-y-4">
                        <li class="flex items-center">
                            <i class="fas fa-phone-alt text-red-500 mr-3 text-lg"></i>
                            <span class="text-gray-800">+63 995 290 1333</span>
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-envelope text-red-500 mr-3 text-lg"></i>
                            <span class="text-gray-800">mtclaramuelresort@gmail.com</span>
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-map-marker-alt text-red-500 mr-3 text-lg"></i>
                            <span class="text-gray-800">Marana 3rd, City of Ilagan, Isabela</span>
                        </li>
                    </ul>
                </div>
   
                <div>
                    <h3 class="text-lg font-semibold text-red-500 mb-4 flex items-center">
                        <i class="fas fa-share-alt mr-2"></i> Connect With Us
                    </h3>
                    <div class="flex space-x-4 mb-4">
                        <a href="https://www.facebook.com/mtclaramuelresort" target="_blank" rel="noopener noreferrer"
                            class="bg-blue-600 hover:bg-blue-500 w-10 h-10 rounded-full flex items-center justify-center text-white transition duration-300">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a href="https://www.instagram.com/mt_claramuelresort/" target="_blank" rel="noopener noreferrer"
                        class="bg-pink-600 hover:bg-pink-500 w-10 h-10 rounded-full flex items-center justify-center text-white transition duration-300">
                            <i class="fab fa-instagram"></i>
                        </a>
                    </div>
                    <p class="text-gray-500 text-sm">Message us on social media to inquire or book</p>
                </div>
            </div>
            <div class="mt-8 text-center bg-gray-100 py-3 rounded-lg">
                <p class="text-gray-600 font-medium">
                    <i class="far fa-clock text-red-500 mr-2"></i> Available for booking Monday-Saturday, 9AM-5PM
                </p>
            </div>
        </div>
    </div>
</body>
</html>