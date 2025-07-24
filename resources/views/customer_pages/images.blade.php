<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tailwind Image Gallery</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .nav-button {
            transition: opacity 0.3s;
        }
        .nav-button:hover {
            opacity: 0.8;
        }
    </style>
</head>
<body class="bg-gray-100 p-8 bg-red-50">
    <div class="container mx-auto">
        <!-- Back button in index -->
        <div class="flex items-center mb-8">
            <button onclick="window.history.back()" class="flex items-center text-blue-600 hover:text-blue-800 mr-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
                </svg>
                Back
            </button>
            <h1 class="text-3xl font-bold">{{ $name->name }}</h1>
        </div>
        
        <!-- Gallery Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
            @forelse ($images as $image)
                <!-- Gallery Item -->
                <div class="group relative overflow-hidden rounded-lg shadow-lg cursor-pointer" onclick="openFullscreen('{{ asset('imgs/facility_img/' . $image->image) }}')">
                    <img src="{{ asset('imgs/facility_img/' . $image->image) }}" alt="Nature" class="w-full h-64 object-cover transition-transform duration-300 group-hover:scale-105">
                    <div class="absolute inset-0 bg-black bg-opacity-30 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                        <span class="text-white text-lg font-semibold">Click to enlarge</span>
                    </div>
                </div>
            @empty
            <p class="text-gray-500 italic">No images available.</p>
            @endforelse
        </div>
    </div>

    <!-- Fullscreen Modal -->
    <div id="fullscreenModal" class="fixed inset-0 bg-black bg-opacity-90 z-50 hidden flex items-center justify-center p-4">
        <div class="relative w-full h-full flex items-center justify-center">
            <!-- Current Image -->
            <img id="fullscreenImage" src="" alt="Fullscreen" class="max-w-full max-h-full object-contain">
            
            <!-- Close Button (Top Right) -->
            <button onclick="closeFullscreen()" class="absolute top-4 right-4 bg-white rounded-full p-2 shadow-lg hover:bg-gray-200 transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
            
            <!-- Navigation Arrows -->
            <button id="prevButton" class="absolute left-4 bg-white rounded-full p-2 shadow-lg hover:bg-gray-200 transition-colors nav-button">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
            </button>
            <button id="nextButton" class="absolute right-4 bg-white rounded-full p-2 shadow-lg hover:bg-gray-200 transition-colors nav-button">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </button>
        </div>
    </div>

    <script>
        // Store all image URLs and current index
        let currentImageIndex = 0;
        let imageUrls = [];
        
        // Get all image URLs from the gallery
        document.addEventListener('DOMContentLoaded', function() {
            const galleryItems = document.querySelectorAll('[onclick^="openFullscreen"]');
            imageUrls = Array.from(galleryItems).map(item => {
                return item.getAttribute('onclick').replace("openFullscreen('", "").replace("')", "");
            });
            
            // Add event listeners for navigation
            document.getElementById('prevButton').addEventListener('click', showPrevImage);
            document.getElementById('nextButton').addEventListener('click', showNextImage);
        });

        function openFullscreen(imageUrl) {
            const modal = document.getElementById('fullscreenModal');
            const fullscreenImage = document.getElementById('fullscreenImage');
            
            // Find the index of the clicked image
            currentImageIndex = imageUrls.indexOf(imageUrl);
            
            fullscreenImage.src = imageUrl;
            modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeFullscreen() {
            const modal = document.getElementById('fullscreenModal');
            modal.classList.add('hidden');
            document.body.style.overflow = 'auto';
        }

        function showPrevImage() {
            if (imageUrls.length === 0) return;
            
            currentImageIndex = (currentImageIndex - 1 + imageUrls.length) % imageUrls.length;
            document.getElementById('fullscreenImage').src = imageUrls[currentImageIndex];
        }

        function showNextImage() {
            if (imageUrls.length === 0) return;
            
            currentImageIndex = (currentImageIndex + 1) % imageUrls.length;
            document.getElementById('fullscreenImage').src = imageUrls[currentImageIndex];
        }

        // Close modal when clicking outside the image
        document.getElementById('fullscreenModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeFullscreen();
            }
        });

        // Close modal with ESC key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeFullscreen();
            }
            // Add keyboard navigation
            else if (e.key === 'ArrowLeft') {
                showPrevImage();
            }
            else if (e.key === 'ArrowRight') {
                showNextImage();
            }
        });
    </script>
</body>
</html>