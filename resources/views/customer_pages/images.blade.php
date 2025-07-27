<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tailwind Image Gallery</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Animation classes */
        .slide-in-right {
            animation: slideInRight 0.4s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }
        
        .slide-in-left {
            animation: slideInLeft 0.4s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }
        
        .zoom-in {
            animation: zoomIn 0.3s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }
        
        .fade-in {
            animation: fadeIn 0.3s ease-out forwards;
        }
        
        @keyframes slideInRight {
            from {
                transform: translateX(30px);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }
        
        @keyframes slideInLeft {
            from {
                transform: translateX(-30px);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }
        
        @keyframes zoomIn {
            from {
                transform: scale(0.95);
                opacity: 0;
            }
            to {
                transform: scale(1);
                opacity: 1;
            }
        }
        
        @keyframes fadeIn {
            from {
                opacity: 0;
            }
            to {
                opacity: 1;
            }
        }
        
        /* Gallery item hover effect */
        .gallery-item {
            transition: transform 0.3s ease-out, box-shadow 0.3s ease-out;
            transform-origin: center;
        }
        
        .gallery-item:hover {
            transform: translateY(-5px) scale(1.02);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.15);
        }
        
        .gallery-item::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(to top, rgba(0,0,0,0.7) 0%, rgba(0,0,0,0) 40%);
            opacity: 0;
            transition: opacity 0.3s ease;
            z-index: 1;
        }
        
        .gallery-item:hover::before {
            opacity: 1;
        }
        
        /* Fullscreen image transition */
        #fullscreenImage {
            transition: transform 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        }
        
        #fullscreenImage.zoomed {
            transform: scale(1.5);
            cursor: zoom-out;
        }
        
        /* Loading spinner */
        .spinner {
            animation: spin 1s linear infinite;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }
        
        ::-webkit-scrollbar-track {
            background: #f1f1f1;
        }
        
        ::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 4px;
        }
        
        ::-webkit-scrollbar-thumb:hover {
            background: #555;
        }
        
        /* Thumbnail strip */
        .thumbnail-container {
            scrollbar-width: thin;
            scrollbar-color: #888 #f1f1f1;
        }
    </style>
</head>
<body class="bg-gray-50 p-4 md:p-8">
    <div class="container mx-auto">
        <!-- Back button and title -->
        <div class="flex items-center mb-6 md:mb-8">
            <button onclick="window.history.back()" class="flex items-center text-blue-600 hover:text-blue-800 transition-colors mr-4 group">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1 group-hover:-translate-x-1 transition-transform" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
                </svg>
                <span class="group-hover:underline">Back</span>
            </button>
            <h1 class="text-2xl md:text-3xl font-bold text-gray-800">{{ $name->name }}</h1>
        </div>
        
        <!-- Gallery Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 md:gap-6">
            @forelse ($images as $image)
                <!-- Gallery Item -->
                <div class="gallery-item relative overflow-hidden rounded-xl shadow-md cursor-pointer group"
                     onclick="openFullscreen('{{ asset('imgs/facility_img/' . $image->image) }}', {{ $loop->index }})">
                    <img src="{{ asset('imgs/facility_img/' . $image->image) }}" alt="Gallery image" 
                         class="w-full h-64 object-cover transition-transform duration-300 group-hover:scale-105"
                         loading="lazy">
                    <div class="absolute inset-0 flex items-end p-4 z-10 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                        <span class="text-white text-sm font-medium bg-black bg-opacity-50 px-3 py-1 rounded-full">
                            <i class="fas fa-search-plus mr-2"></i>View
                        </span>
                    </div>
                </div>
            @empty
                <div class="col-span-full py-12 text-center">
                    <div class="text-gray-400 mb-4">
                        <i class="fas fa-images text-5xl"></i>
                    </div>
                    <p class="text-gray-500 text-lg">No images available</p>
                    <p class="text-gray-400 text-sm mt-2">Upload some images to get started</p>
                </div>
            @endforelse
        </div>
        
        <!-- Image count -->
        @if(count($images) > 0)
        <div class="mt-6 text-right text-gray-500 text-sm">
            Showing {{ count($images) }} image{{ count($images) > 1 ? 's' : '' }}
        </div>
        @endif
    </div>

    <!-- Fullscreen Modal -->
    <div id="fullscreenModal" class="fixed inset-0 bg-black bg-opacity-95 z-50 hidden flex flex-col items-center justify-center p-4">
        <!-- Top controls -->
        <div class="w-full flex justify-between items-center mb-4 z-50">
            <!-- Close Button -->
            <button onclick="closeFullscreen()" class="text-white hover:bg-white hover:bg-opacity-10 rounded-full p-2 transition-all">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
            
            <!-- Image Counter -->
            <div class="bg-black bg-opacity-50 text-white px-4 py-2 rounded-full">
                <span id="currentIndex">1</span> / <span id="totalImages">0</span>
            </div>
            
            <!-- Download Button -->
            <button id="downloadButton" class="text-white hover:bg-white hover:bg-opacity-10 rounded-full p-2 transition-all">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                </svg>
            </button>
        </div>
        
        <!-- Main image container -->
        <div class="relative flex-1 w-full flex items-center justify-center overflow-hidden">
            <!-- Loading spinner -->
            <div id="loadingSpinner" class="absolute inset-0 flex items-center justify-center hidden">
                <div class="spinner border-4 border-white border-t-transparent rounded-full h-12 w-12"></div>
            </div>
            
            <!-- Current Image Container -->
            <div class="relative w-full h-full flex items-center justify-center">
                <img id="fullscreenImage" src="" alt="Fullscreen" 
                     class="max-w-full max-h-full object-contain cursor-zoom-in"
                     onclick="toggleZoom()">
            </div>
            
            <!-- Navigation Arrows -->
            <button id="prevButton" class="absolute left-4 text-white hover:bg-white hover:bg-opacity-10 rounded-full p-3 transition-all nav-button">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
            </button>
            <button id="nextButton" class="absolute right-4 text-white hover:bg-white hover:bg-opacity-10 rounded-full p-3 transition-all nav-button">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </button>
        </div>
        
        <!-- Thumbnail strip -->
        <div id="thumbnailContainer" class="w-full mt-4 py-2 overflow-x-auto thumbnail-container hidden">
            <div class="flex space-x-2 px-4">
                @foreach ($images as $image)
                    <img src="{{ asset('imgs/facility_img/' . $image->image) }}" 
                         alt="Thumbnail" 
                         class="h-16 w-16 object-cover rounded cursor-pointer border-2 border-transparent hover:border-white transition-all opacity-70 hover:opacity-100"
                         data-index="{{ $loop->index }}"
                         onclick="navigateToThumbnail({{ $loop->index }})">
                @endforeach
            </div>
        </div>
    </div>

    <script>
        // Store all image URLs and current index
        let currentImageIndex = 0;
        let imageUrls = [];
        let isZoomed = false;
        let touchStartX = 0;
        let touchStartY = 0;
        
        // Get all image URLs from the gallery
        document.addEventListener('DOMContentLoaded', function() {
            const galleryItems = document.querySelectorAll('[onclick^="openFullscreen"]');
            imageUrls = Array.from(galleryItems).map(item => {
                return item.getAttribute('onclick').split("'")[1];
            });
            
            // Update total images count
            document.getElementById('totalImages').textContent = imageUrls.length;
            
            // Add event listeners for navigation
            document.getElementById('prevButton').addEventListener('click', showPrevImage);
            document.getElementById('nextButton').addEventListener('click', showNextImage);
            
            // Add download functionality
            document.getElementById('downloadButton').addEventListener('click', downloadCurrentImage);
            
            // Show thumbnail strip if more than 1 image
            if (imageUrls.length > 1) {
                document.getElementById('thumbnailContainer').classList.remove('hidden');
            }
            
            // Add swipe support for touch devices
            setupSwipe();
            
            // Preload adjacent images for better performance
            if (imageUrls.length > 0) {
                preloadAdjacentImages(0);
            }
        });

        function openFullscreen(imageUrl, index) {
            const modal = document.getElementById('fullscreenModal');
            const fullscreenImage = document.getElementById('fullscreenImage');
            const loadingSpinner = document.getElementById('loadingSpinner');
            
            // Show loading spinner
            loadingSpinner.classList.remove('hidden');
            fullscreenImage.classList.add('opacity-0');
            
            // Find the index of the clicked image
            currentImageIndex = index;
            
            // Reset zoom state
            isZoomed = false;
            
            // Set image source
            fullscreenImage.onload = function() {
                loadingSpinner.classList.add('hidden');
                fullscreenImage.classList.remove('opacity-0');
                fullscreenImage.classList.add('zoom-in');
                
                // Remove zoom-in class after animation completes
                setTimeout(() => {
                    fullscreenImage.classList.remove('zoom-in');
                }, 300);
            };
            
            fullscreenImage.src = imageUrl;
            
            // Show modal
            modal.classList.remove('hidden');
            modal.classList.add('fade-in');
            
            // Update counter and thumbnail selection
            updateCounter();
            updateThumbnailSelection();
            
            // Preload adjacent images
            preloadAdjacentImages(currentImageIndex);
            
            document.body.style.overflow = 'hidden';
            
            // Hide UI elements after 3 seconds of inactivity
            startInactivityTimer();
        }

        function closeFullscreen() {
            const modal = document.getElementById('fullscreenModal');
            modal.classList.add('hidden');
            document.body.style.overflow = 'auto';
            
            // Clear inactivity timer
            clearInactivityTimer();
        }

        function showPrevImage() {
            if (imageUrls.length === 0) return;
            
            // Reset zoom state when navigating
            resetZoom();
            
            const newIndex = (currentImageIndex - 1 + imageUrls.length) % imageUrls.length;
            navigateToImage(newIndex, 'left');
        }

        function showNextImage() {
            if (imageUrls.length === 0) return;
            
            // Reset zoom state when navigating
            resetZoom();
            
            const newIndex = (currentImageIndex + 1) % imageUrls.length;
            navigateToImage(newIndex, 'right');
        }
        
        function resetZoom() {
            isZoomed = false;
            document.getElementById('fullscreenImage').classList.remove('zoomed');
        }
        
        function navigateToImage(newIndex, direction) {
            const fullscreenImage = document.getElementById('fullscreenImage');
            const loadingSpinner = document.getElementById('loadingSpinner');
            
            // Show loading spinner
            loadingSpinner.classList.remove('hidden');
            fullscreenImage.classList.add('opacity-0');
            
            // Set appropriate animation class based on direction
            fullscreenImage.classList.remove('slide-in-left', 'slide-in-right');
            fullscreenImage.classList.add(direction === 'left' ? 'slide-in-left' : 'slide-in-right');
            
            // Update image source
            fullscreenImage.onload = function() {
                loadingSpinner.classList.add('hidden');
                fullscreenImage.classList.remove('opacity-0');
                
                // Remove animation class after animation completes
                setTimeout(() => {
                    fullscreenImage.classList.remove('slide-in-left', 'slide-in-right');
                }, 400);
            };
            
            fullscreenImage.src = imageUrls[newIndex];
            
            // Update current index
            currentImageIndex = newIndex;
            
            // Update counter and thumbnail selection
            updateCounter();
            updateThumbnailSelection();
            
            // Preload adjacent images
            preloadAdjacentImages(newIndex);
            
            // Reset inactivity timer
            startInactivityTimer();
        }
        
        function navigateToThumbnail(index) {
            if (index === currentImageIndex) return;
            
            const direction = index > currentImageIndex ? 'right' : 'left';
            navigateToImage(index, direction);
        }
        
        function updateCounter() {
            document.getElementById('currentIndex').textContent = currentImageIndex + 1;
        }
        
        function updateThumbnailSelection() {
            const thumbnails = document.querySelectorAll('#thumbnailContainer img');
            thumbnails.forEach((thumb, index) => {
                if (index === currentImageIndex) {
                    thumb.classList.add('border-white', 'opacity-100');
                    thumb.classList.remove('border-transparent', 'opacity-70');
                    
                    // Scroll thumbnail into view
                    thumb.scrollIntoView({
                        behavior: 'smooth',
                        block: 'nearest',
                        inline: 'center'
                    });
                } else {
                    thumb.classList.remove('border-white', 'opacity-100');
                    thumb.classList.add('border-transparent', 'opacity-70');
                }
            });
        }
        
        function toggleZoom() {
            const fullscreenImage = document.getElementById('fullscreenImage');
            isZoomed = !isZoomed;
            
            if (isZoomed) {
                fullscreenImage.classList.add('zoomed');
            } else {
                fullscreenImage.classList.remove('zoomed');
            }
            
            // Reset inactivity timer when zooming
            startInactivityTimer();
        }
        
        function setupSwipe() {
            const modal = document.getElementById('fullscreenModal');
            let touchStartX = 0;
            let touchStartY = 0;
            let touchEndX = 0;
            let touchEndY = 0;
            
            modal.addEventListener('touchstart', (e) => {
                touchStartX = e.changedTouches[0].screenX;
                touchStartY = e.changedTouches[0].screenY;
            }, false);
            
            modal.addEventListener('touchmove', (e) => {
                // Prevent scrolling when zoomed
                if (isZoomed) {
                    e.preventDefault();
                }
            }, { passive: false });
            
            modal.addEventListener('touchend', (e) => {
                touchEndX = e.changedTouches[0].screenX;
                touchEndY = e.changedTouches[0].screenY;
                handleSwipe();
            }, false);
            
            function handleSwipe() {
                // Only handle horizontal swipes when not zoomed
                if (isZoomed) return;
                
                const xDiff = touchStartX - touchEndX;
                const yDiff = touchStartY - touchEndY;
                
                // Only consider horizontal swipes with minimal vertical movement
                if (Math.abs(xDiff) > Math.abs(yDiff) && Math.abs(xDiff) > 50) {
                    if (xDiff > 0) {
                        // Swipe left - next image
                        showNextImage();
                    } else {
                        // Swipe right - previous image
                        showPrevImage();
                    }
                }
            }
        }
        
        function downloadCurrentImage() {
            if (imageUrls.length === 0) return;
            
            const link = document.createElement('a');
            link.href = imageUrls[currentImageIndex];
            link.download = `image-${currentImageIndex + 1}.jpg`;
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }
        
        function preloadAdjacentImages(currentIndex) {
            // Preload next and previous images for smoother navigation
            const preloadIndices = [
                (currentIndex - 1 + imageUrls.length) % imageUrls.length,
                (currentIndex + 1) % imageUrls.length
            ];
            
            preloadIndices.forEach(index => {
                const img = new Image();
                img.src = imageUrls[index];
            });
        }
        
        // UI visibility timeout
        let inactivityTimer;
        
        function startInactivityTimer() {
            clearInactivityTimer();
            
            // Only start timer if not zoomed
            if (!isZoomed) {
                inactivityTimer = setTimeout(() => {
                    hideUIElements();
                }, 3000);
            }
        }
        
        function clearInactivityTimer() {
            if (inactivityTimer) {
                clearTimeout(inactivityTimer);
            }
            showUIElements();
        }
        
        function hideUIElements() {
            const modal = document.getElementById('fullscreenModal');
            modal.querySelectorAll('button, #thumbnailContainer, #currentIndex').forEach(el => {
                el.classList.add('opacity-0');
            });
        }
        
        function showUIElements() {
            const modal = document.getElementById('fullscreenModal');
            modal.querySelectorAll('button, #thumbnailContainer, #currentIndex').forEach(el => {
                el.classList.remove('opacity-0');
            });
        }

        // Close modal when clicking outside the image
        document.getElementById('fullscreenModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeFullscreen();
            } else {
                // Show UI elements when interacting with modal
                clearInactivityTimer();
            }
        });

        // Handle keyboard events
        document.addEventListener('keydown', function(e) {
            const modal = document.getElementById('fullscreenModal');
            if (modal.classList.contains('hidden')) return;
            
            switch(e.key) {
                case 'Escape':
                    closeFullscreen();
                    break;
                case 'ArrowLeft':
                    showPrevImage();
                    break;
                case 'ArrowRight':
                    showNextImage();
                    break;
                case ' ':
                    e.preventDefault();
                    toggleZoom();
                    break;
                case 'd':
                    downloadCurrentImage();
                    break;
                case 't':
                    // Toggle thumbnail strip
                    document.getElementById('thumbnailContainer').classList.toggle('hidden');
                    break;
                case 'h':
                    // Toggle UI visibility
                    if (document.querySelector('#fullscreenModal button:first-child').classList.contains('opacity-0')) {
                        showUIElements();
                        startInactivityTimer();
                    } else {
                        hideUIElements();
                        clearInactivityTimer();
                    }
                    break;
            }
        });
        
        // Mouse movement detection for UI visibility
        document.getElementById('fullscreenModal').addEventListener('mousemove', () => {
            if (!isZoomed) {
                showUIElements();
                startInactivityTimer();
            }
        });
    </script>
</body>
</html>