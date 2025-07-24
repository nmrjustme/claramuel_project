@extends('layouts.app')
@section('title', 'Book')
@section('dashboard')

<!-- Main Content -->
<div class="container mx-auto px-4 py-8">
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 max-w-6xl mx-auto">
        <!-- Image Gallery -->
        <div class="space-y-4">
            <!-- Main Image -->
            @if($firstImage)
            <div class="relative group overflow-hidden rounded-xl shadow-lg cursor-zoom-in">
                <img src="{{ url('imgs/facility_img/' . $firstImage->image) }}" alt="Main"
                    class="w-full h-96 object-cover transition-transform duration-500 group-hover:scale-105"
                    onclick="showFullscreenImage(this.src)" />
                <div class="absolute inset-0 bg-gradient-to-t from-black/30 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
            </div>
            @endif

            <!-- Thumbnail Grid -->
            @if($remainingImage->count())
            <div class="grid grid-cols-4 gap-3">
                @foreach($remainingImage as $img)
                <div class="relative group overflow-hidden rounded-lg shadow-md">
                    <img src="{{ url('imgs/facility_img/' . $img->image) }}" alt="Gallery image"
                        class="w-full h-24 object-cover transition-all duration-300 group-hover:scale-110"
                        onclick="showFullscreenImage(this.src)" />
                    <div class="absolute inset-0 bg-black/20 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                </div>
                @endforeach
            </div>
            @endif
        </div>

        <!-- Right: Info Panel -->
        <div class="space-y-6 sticky top-4 h-fit">
            <!-- Room Details -->
            <div class="bg-white p-6 rounded-xl shadow-md border border-gray-100">
                @if ($category == 'Room' || $category == 'Village')
                    <h1 class="text-3xl font-bold text-gray-800 mb-2">{{ $facility->name }}</h1>
                    
                    <!-- Price Display -->
                    <div class="flex items-center mb-4">
                        <span class="text-4xl font-extrabold text-indigo-600">₱{{ number_format($facility->price, 2) }}</span>
                        <span class="ml-2 text-gray-500">/ night</span>
                    </div>
                    
                    <!-- Amenities Grid -->
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-3">Amenities</h3>
                        <div class="grid grid-cols-2 gap-3">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                <span>Free WiFi</span>
                            </div>
                            <div class="flex items-center">
                                <svg class="w-5 h-5 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                <span>Air Conditioning</span>
                            </div>
                            <div class="flex items-center">
                                <svg class="w-5 h-5 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                <span>Swimming Pool</span>
                            </div>
                            <div class="flex items-center">
                                <svg class="w-5 h-5 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                <span>Pet Friendly</span>
                            </div>
                            <div class="flex items-center">
                                <svg class="w-5 h-5 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                <span>Free Parking</span>
                            </div>
                            <div class="flex items-center">
                                <svg class="w-5 h-5 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                <span>Cleaning Service</span>
                            </div>
                            <div class="flex items-center">
                                <svg class="w-5 h-5 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                <span>Coffee Place</span>
                            </div>
                            <div class="flex items-center">
                                <svg class="w-5 h-5 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                <span>Fire Safety</span>
                            </div>
                            <div class="flex items-center">
                                <svg class="w-5 h-5 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                <span>First Aid Kit</span>
                            </div>
                            <div class="flex items-center">
                                <svg class="w-5 h-5 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                <span>Smoke Alarm</span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Add to Cart Button -->
                    <button id="addToCartBtn" class="bg-red-600 w-full py-4 px-6 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white font-bold rounded-lg shadow-lg transform hover:scale-[1.02] transition-all duration-200 flex items-center justify-center space-x-2">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                        <span>ADD TO CART</span>
                    </button>
                    
                    <!-- Trust Badges -->
                    <div class="mt-6 flex justify-center space-x-6">
                        <div class="text-center">
                            <svg class="w-10 h-10 mx-auto text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                            </svg>
                            <p class="text-xs text-gray-500 mt-1">Secure Booking</p>
                        </div>
                        <div class="text-center">
                            <svg class="w-10 h-10 mx-auto text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                            </svg>
                            <p class="text-xs text-gray-500 mt-1">No Payment Now</p>
                        </div>
                    </div>
                @endif
            </div>
            
            <!-- Quick Facts -->
            <div class="bg-white p-6 rounded-xl shadow-md border border-gray-100">
                <h3 class="text-xl font-semibold text-gray-800 mb-4">What's Included</h3>
                <ul class="space-y-3">
                    <li class="flex items-start">
                        <svg class="w-5 h-5 text-green-500 mt-0.5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        <span class="text-gray-700">24/7 Customer Support</span>
                    </li>
                    <li class="flex items-start">
                        <svg class="w-5 h-5 text-green-500 mt-0.5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        <span class="text-gray-700">Free Cancellation</span>
                    </li>
                    <li class="flex items-start">
                        <svg class="w-5 h-5 text-green-500 mt-0.5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        <span class="text-gray-700">Complimentary WiFi</span>
                    </li>
                </ul>
            
                <!-- Check-in/Check-out Times Section -->
                <div class="mt-6 pt-6 border-t border-gray-200">
                    <h4 class="text-lg font-medium text-gray-800 mb-3">Arrival & Departure</h4>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <div class="flex items-center mb-2">
                                <svg class="w-6 h-6 text-blue-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                                </svg>
                                <span class="font-semibold text-gray-700">Check-in</span>
                            </div>
                            <p class="text-gray-600">12:00 PM (Noon)</p>
                            <p class="text-sm text-gray-500 mt-1">Please notify property of arrival time in advance</p>
                        </div>
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <div class="flex items-center mb-2">
                                <svg class="w-6 h-6 text-blue-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                                </svg>
                                <span class="font-semibold text-gray-700">Check-out</span>
                            </div>
                            <p class="text-gray-600">11:00 AM</p>
                            <p class="text-sm text-gray-500 mt-1">Late check-out may be available upon request</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Fullscreen Image Viewer -->
<div id="fullscreenOverlay" class="fixed inset-0 bg-black/90 flex items-center justify-center hidden z-50 backdrop-blur-sm">
    <div class="relative max-w-4xl w-full">
        <img id="fullscreenImage" src="" class="max-w-full max-h-[90vh] rounded-lg shadow-2xl mx-auto">
        <button onclick="closeFullscreenImage()" class="absolute -top-12 right-0 text-white text-4xl font-bold hover:text-gray-300 transition-colors">&times;</button>
    </div>
</div>

<script>
    function showFullscreenImage(src) {
        document.getElementById('fullscreenImage').src = src;
        document.getElementById('fullscreenOverlay').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closeFullscreenImage() {
        document.getElementById('fullscreenOverlay').classList.add('hidden');
        document.body.style.overflow = 'auto';
    }

    // Close when clicking outside image
    document.getElementById('fullscreenOverlay').addEventListener('click', function(e) {
        if (e.target === this) {
            closeFullscreenImage();
        }
    });

    // Add to cart animation
    document.getElementById('addToCartBtn').addEventListener('click', function() {
        this.innerHTML = '<svg class="w-6 h-6 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg><span>ADDING...</span>';
        
        setTimeout(() => {
            this.innerHTML = '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg><span>ADDED TO CART!</span>';
            this.classList.remove('from-indigo-600', 'to-purple-600', 'hover:from-indigo-700', 'hover:to-purple-700');
            this.classList.add('from-green-500', 'to-emerald-600', 'hover:from-green-600', 'hover:to-emerald-700');
            
            // Reset after 2 seconds
            setTimeout(() => {
                this.innerHTML = '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path></svg><span>ADD TO CART</span>';
                this.classList.remove('from-green-500', 'to-emerald-600', 'hover:from-green-600', 'hover:to-emerald-700');
                this.classList.add('from-indigo-600', 'to-purple-600', 'hover:from-indigo-700', 'hover:to-purple-700');
            }, 2000);
        }, 1000);
    });
</script>

<style>
    .hover-effect:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.1);
    }
    .cursor-zoom-in {
        cursor: zoom-in;
    }
</style>

@endsection