<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Premium Image Gallery</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
        
        body { font-family: 'Inter', sans-serif; }

        /* Smooth dynamic transitions */
        .gallery-item {
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .gallery-item:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }

        /* Glassmorphism UI */
        .glass-ui {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        /* Navigation Button Hover */
        .nav-button {
            transition: all 0.3s ease;
            background: rgba(0, 0, 0, 0.3);
        }
        .nav-button:hover {
            background: rgba(255, 255, 255, 0.9);
            color: black;
        }

        /* Fullscreen Image Behavior */
        #fullscreenImage {
            transition: transform 0.4s cubic-bezier(0.16, 1, 0.3, 1), opacity 0.3s ease;
            will-change: transform, opacity;
        }

        #fullscreenImage.zoomed {
            transform: scale(2);
            cursor: zoom-out;
        }

        /* Active Thumbnail Styling */
        .thumb-active {
            border-color: #3b82f6 !important;
            opacity: 1 !important;
            transform: scale(1.1);
        }

        /* Modern Loading Bar */
        .progress-bar {
            height: 2px;
            background: #3b82f6;
            position: absolute;
            top: 0;
            left: 0;
            transition: width 0.3s ease;
        }
    </style>
</head>

<body class="bg-gray-50 text-gray-900">
    <div class="container mx-auto px-4 py-12 max-w-7xl">
        
        <div class="flex flex-col md:flex-row md:items-center justify-between mb-10 gap-4">
            <div class="flex items-center space-x-6">
                <button onclick="window.history.back()" 
                    class="h-10 w-10 flex items-center justify-center rounded-full bg-white shadow-sm border border-gray-200 text-gray-600 hover:text-blue-600 hover:border-blue-200 transition-all group">
                    <i class="fas fa-arrow-left group-hover:-translate-x-1 transition-transform"></i>
                </button>
                <div>
                    <h1 class="text-3xl font-extrabold tracking-tight text-gray-900">{{ $name->name }}</h1>
                </div>
            </div>
            
            @if(count($images) > 0)
            <div class="inline-flex items-center px-4 py-2 rounded-lg bg-blue-50 text-blue-700 text-sm font-semibold">
                <i class="far fa-image mr-2"></i> {{ count($images) }} Photos
            </div>
            @endif
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
            @forelse ($images as $image)
                <div class="gallery-item group relative aspect-[4/3] overflow-hidden rounded-2xl bg-gray-200 cursor-pointer"
                    onclick="openFullscreen('{{ asset('imgs/facility_img/' . $image->image) }}', {{ $loop->index }})">
                    
                    <img src="{{ asset('imgs/facility_img/' . $image->image) }}" 
                        alt="Facility Gallery"
                        class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-110"
                        loading="lazy">
                    
                    <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                        <div class="translate-y-4 group-hover:translate-y-0 transition-transform duration-300">
                            <span class="bg-white/90 text-black px-5 py-2 rounded-full text-sm font-bold shadow-xl">
                                <i class="fas fa-expand mr-2"></i> View Large
                            </span>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full py-20 text-center bg-white rounded-3xl border-2 border-dashed border-gray-200">
                    <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-camera-retro text-3xl text-gray-300"></i>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900">No images found</h3>
                    <p class="text-gray-500">This facility hasn't uploaded any photos yet.</p>
                </div>
            @endforelse
        </div>
    </div>

    <div id="fullscreenModal" class="fixed inset-0 bg-black/95 z-[100] hidden flex flex-col items-center justify-center opacity-0 transition-opacity duration-300">
        <div id="loadingBar" class="progress-bar" style="width: 0%"></div>

        <div id="modalUI" class="absolute top-0 inset-x-0 p-6 flex items-center justify-between z-[110] transition-opacity duration-300">
            <button onclick="closeFullscreen()" class="h-12 w-12 flex items-center justify-center rounded-full glass-ui text-white hover:bg-white hover:text-black transition-all">
                <i class="fas fa-times text-xl"></i>
            </button>

            <div class="flex items-center space-x-2 glass-ui px-4 py-2 rounded-2xl text-white font-medium text-sm">
                <span id="currentIndex" class="text-blue-400 font-bold">1</span>
                <span class="opacity-40">/</span>
                <span id="totalImages">0</span>
            </div>

            <div class="flex items-center space-x-3">
                <button id="downloadButton" class="h-12 w-12 flex items-center justify-center rounded-full glass-ui text-white hover:bg-white hover:text-black transition-all" title="Download (D)">
                    <i class="fas fa-download"></i>
                </button>
            </div>
        </div>

        <button id="prevButton" class="absolute left-6 top-1/2 -translate-y-1/2 h-16 w-16 rounded-full flex items-center justify-center text-white z-[110] nav-button opacity-0 sm:opacity-100">
            <i class="fas fa-chevron-left text-2xl"></i>
        </button>
        <button id="nextButton" class="absolute right-6 top-1/2 -translate-y-1/2 h-16 w-16 rounded-full flex items-center justify-center text-white z-[110] nav-button opacity-0 sm:opacity-100">
            <i class="fas fa-chevron-right text-2xl"></i>
        </button>

        <div class="w-full h-full flex items-center justify-center p-4 md:p-12 overflow-hidden select-none" onclick="handleBackgroundClick(event)">
            <img id="fullscreenImage" src="" alt="Fullscreen View" 
                class="max-w-full max-h-full object-contain cursor-zoom-in drop-shadow-2xl shadow-black/50">
        </div>

        <div id="thumbUI" class="absolute bottom-0 inset-x-0 p-6 z-[110] transition-opacity duration-300">
            <div id="thumbnailContainer" class="flex items-center justify-center space-x-3 overflow-x-auto pb-2 scrollbar-hide max-w-4xl mx-auto">
                @foreach ($images as $image)
                    <img src="{{ asset('imgs/facility_img/' . $image->image) }}" 
                        class="h-14 w-14 md:h-20 md:w-20 object-cover rounded-lg cursor-pointer border-2 border-transparent opacity-40 hover:opacity-100 transition-all flex-shrink-0 thumb-nav-item"
                        data-index="{{ $loop->index }}" 
                        onclick="navigateToThumbnail({{ $loop->index }})">
                @endforeach
            </div>
        </div>

        <div class="absolute bottom-4 left-4 text-[10px] text-white/30 hidden md:block uppercase tracking-widest">
            Esc to close • Arrows to navigate • Space to zoom
        </div>
    </div>

    <script>
        let currentImageIndex = 0;
        let imageUrls = [];
        let isZoomed = false;
        let inactivityTimeout;

        document.addEventListener('DOMContentLoaded', () => {
            // Collect all images
            const images = document.querySelectorAll('.gallery-item img');
            imageUrls = Array.from(images).map(img => img.src);
            document.getElementById('totalImages').textContent = imageUrls.length;

            // Setup listeners
            document.getElementById('prevButton').onclick = (e) => { e.stopPropagation(); showPrevImage(); };
            document.getElementById('nextButton').onclick = (e) => { e.stopPropagation(); showNextImage(); };
            document.getElementById('downloadButton').onclick = downloadCurrentImage;
            
            setupSwipe();
        });

        function openFullscreen(url, index) {
            const modal = document.getElementById('fullscreenModal');
            const img = document.getElementById('fullscreenImage');
            
            currentImageIndex = index;
            resetZoom();
            
            modal.classList.remove('hidden');
            setTimeout(() => modal.classList.add('opacity-100'), 10);
            
            loadImage(url);
            updateUI();
            document.body.style.overflow = 'hidden';
            resetInactivityTimer();
        }

        function loadImage(url) {
            const img = document.getElementById('fullscreenImage');
            const bar = document.getElementById('loadingBar');
            
            bar.style.width = '30%';
            img.style.opacity = '0';
            img.style.transform = 'scale(0.95)';

            const highRes = new Image();
            highRes.src = url;
            highRes.onload = () => {
                bar.style.width = '100%';
                img.src = url;
                img.style.opacity = '1';
                img.style.transform = 'scale(1)';
                setTimeout(() => bar.style.width = '0%', 400);
            };
        }

        function showNextImage() {
            currentImageIndex = (currentImageIndex + 1) % imageUrls.length;
            loadImage(imageUrls[currentImageIndex]);
            updateUI();
        }

        function showPrevImage() {
            currentImageIndex = (currentImageIndex - 1 + imageUrls.length) % imageUrls.length;
            loadImage(imageUrls[currentImageIndex]);
            updateUI();
        }

        function updateUI() {
            document.getElementById('currentIndex').textContent = currentImageIndex + 1;
            
            // Update Thumbnails
            document.querySelectorAll('.thumb-nav-item').forEach((thumb, idx) => {
                if (idx === currentImageIndex) {
                    thumb.classList.add('thumb-active');
                    thumb.scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: 'center' });
                } else {
                    thumb.classList.remove('thumb-active');
                }
            });
        }

        function navigateToThumbnail(idx) {
            currentImageIndex = idx;
            loadImage(imageUrls[idx]);
            updateUI();
        }

        function toggleZoom() {
            isZoomed = !isZoomed;
            const img = document.getElementById('fullscreenImage');
            img.classList.toggle('zoomed', isZoomed);
            img.classList.toggle('cursor-zoom-out', isZoomed);
            img.classList.toggle('cursor-zoom-in', !isZoomed);
        }

        function resetZoom() {
            isZoomed = false;
            const img = document.getElementById('fullscreenImage');
            img.classList.remove('zoomed', 'cursor-zoom-out');
            img.classList.add('cursor-zoom-in');
        }

        function closeFullscreen() {
            const modal = document.getElementById('fullscreenModal');
            modal.classList.remove('opacity-100');
            setTimeout(() => modal.classList.add('hidden'), 300);
            document.body.style.overflow = 'auto';
            clearTimeout(inactivityTimeout);
        }

        function handleBackgroundClick(e) {
            if (e.target.id === 'fullscreenImage' && !isZoomed) {
                toggleZoom();
            } else if (isZoomed) {
                resetZoom();
            }
        }

        // Inactivity Timer
        function resetInactivityTimer() {
            const uiElements = [document.getElementById('modalUI'), document.getElementById('thumbUI'), document.getElementById('prevButton'), document.getElementById('nextButton')];
            uiElements.forEach(el => el.classList.remove('opacity-0'));
            
            clearTimeout(inactivityTimeout);
            if (!isZoomed) {
                inactivityTimeout = setTimeout(() => {
                    uiElements.forEach(el => el.classList.add('opacity-0'));
                }, 3000);
            }
        }

        document.getElementById('fullscreenModal').addEventListener('mousemove', resetInactivityTimer);

        // Keyboard support
        document.addEventListener('keydown', (e) => {
            const modal = document.getElementById('fullscreenModal');
            if (modal.classList.contains('hidden')) return;

            if (e.key === 'ArrowRight') showNextImage();
            if (e.key === 'ArrowLeft') showPrevImage();
            if (e.key === 'Escape') closeFullscreen();
            if (e.key === ' ') { e.preventDefault(); toggleZoom(); }
            if (e.key.toLowerCase() === 'd') downloadCurrentImage();
        });

        function downloadCurrentImage() {
            const a = document.createElement('a');
            a.href = imageUrls[currentImageIndex];
            a.download = `Facility-${currentImageIndex + 1}.jpg`;
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
        }

        function setupSwipe() {
            let startX = 0;
            const modal = document.getElementById('fullscreenModal');
            
            modal.addEventListener('touchstart', e => startX = e.touches[0].clientX);
            modal.addEventListener('touchend', e => {
                if (isZoomed) return;
                const endX = e.changedTouches[0].clientX;
                const diff = startX - endX;
                if (Math.abs(diff) > 50) {
                    diff > 0 ? showNextImage() : showPrevImage();
                }
            });
        }
    </script>
</body>
</html>
