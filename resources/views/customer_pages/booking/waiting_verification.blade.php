@extends('layouts.bookings')
@section('title', 'Awaiting Verification')
@section('bookings')
    <x-header />
    <div class="container mx-auto px-4 sm:px-6 py-6 md:py-8 max-w-7xl">
        
        <!-- Progress Steps -->
        <x-progress-step :currentStep="4" :steps="[
            ['label' => 'Select Rooms'],
            ['label' => 'Your Details'],
            ['label' => 'Payment'],
            ['label' => 'Processing']
        ]" />
    
        <div class="w-full max-w-4xl mx-auto rounded-lg md:rounded-xl border border-lightGray overflow-hidden">
            
            <!-- Header Section -->
            <div class="bg-gradient-to-r from-red-600 to-red-700 py-6 md:py-8 px-4 sm:px-8 text-center">
                <div class="heart-beat text-white mb-3 md:mb-4">
                    <i class="fas fa-heart text-3xl sm:text-4xl md:text-5xl"></i>
                </div>
                <h1 class="text-xl sm:text-2xl md:text-3xl font-bold text-white">Thank You for Your Booking!</h1>
                <p class="text-red-100 mt-1 sm:mt-2 text-sm sm:text-base md:text-lg">
                    Mt.Claramuel Resort and Events Place
                </p>
            </div>
            
            <!-- Main Content -->
            <div class="p-4 sm:p-6 md:p-8 space-y-6 md:space-y-8">
                
                <!-- Success Message -->
                <div class="bg-green-50 p-4 md:p-6 rounded-lg border border-green-200">
                    <div class="flex flex-col sm:flex-row items-start sm:items-center gap-3">
                        <div class="bg-green-100 p-2 md:p-3 rounded-lg">
                            <i class="fas fa-info-circle text-green-600 text-lg sm:text-xl md:text-2xl"></i>
                        </div>
                        <div class="flex-1">
                            <h2 class="text-base sm:text-lg md:text-xl font-bold text-green-800">
                                Processing Your Request
                            </h2>
                            <p class="text-gray-700 mt-1 text-sm sm:text-base">
                                Your booking has been received. We will send your booking confirmation to your registered contact details right away.
                            </p>
                        </div>
                    </div>
                </div>
                
                <!-- Next Steps -->
                <div class="bg-blue-50 p-4 md:p-6 rounded-lg border border-blue-200">
                    <h3 class="text-sm sm:text-base md:text-lg font-bold text-blue-800 mb-3">What to Expect Next</h3>
                    <ul class="space-y-2 md:space-y-3">
                        <li class="flex items-start">
                            <i class="fas fa-check-circle text-green-500 mt-1 mr-2 md:mr-3 text-xs sm:text-sm md:text-base"></i>
                            <span class="text-sm sm:text-base">Our team will contact you to confirm your booking details</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check-circle text-green-500 mt-1 mr-2 md:mr-3 text-xs sm:text-sm md:text-base"></i>
                            <span class="text-sm sm:text-base">Booking confirmation sent to your contact information</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check-circle text-green-500 mt-1 mr-2 md:mr-3 text-xs sm:text-sm md:text-base"></i>
                            <span class="text-sm sm:text-base">Special offers for your next visit</span>
                        </li>
                    </ul>
                </div>
                
                <!-- Contact Information -->
                <div class="bg-gray-50 p-4 md:p-6 rounded-lg border border-gray-200">
                    <h3 class="text-sm sm:text-base md:text-lg font-bold text-gray-800 mb-2 md:mb-3">Need Assistance?</h3>
                    <p class="text-gray-600 mb-3 md:mb-4 text-sm sm:text-base">
                        Our team is happy to help with any questions about your booking.
                    </p>
                    <div class="grid gap-2 sm:gap-3">
                        <a href="tel:+639952901333" class="flex items-center text-red-600 hover:text-red-700 text-sm sm:text-base">
                            <i class="fas fa-phone-alt mr-2 sm:mr-3 text-xs sm:text-sm md:text-base"></i>
                            +63 995 290 1333
                        </a>
                        <a href="mailto:mtclaramuelresort@gmail.com" class="flex items-center text-red-600 hover:text-red-700 text-sm sm:text-base">
                            <i class="fas fa-envelope mr-2 sm:mr-3 text-xs sm:text-sm md:text-base"></i>
                            mtclaramuelresort@gmail.com
                        </a>
                    </div>
                </div>
            </div>

            <!-- Browse More -->
            <div class="bg-white p-4 sm:p-6 md:p-8 text-center border-t border-gray-200">
                <h3 class="text-lg sm:text-xl md:text-2xl font-bold text-gray-800 mb-3">
                    Want to explore more?
                </h3>
                {{-- <p class="text-gray-600 text-sm sm:text-base mb-4">
                    Discover more cottages, event spaces, and offers at Mt.Claramuel Resort.
                </p> --}}
                <a href="{{ route('dashboard.bookings') }}" 
                class="inline-block bg-red-600 hover:bg-red-700 text-white font-medium rounded-lg px-5 py-2 sm:px-6 sm:py-3 text-sm sm:text-base transition">
                    Browse More
                </a>
            </div>
            
            
            <!-- Footer -->
            <div class="bg-gray-100 px-4 sm:px-8 py-4 md:py-6 text-center">
                <p class="text-gray-600 text-xs sm:text-sm md:text-base">
                    We look forward to welcoming you to Mt.Claramuel Resort!
                </p>
                <p class="text-[10px] sm:text-xs md:text-sm text-gray-500 mt-1">
                    © {{ date('Y') }} Mt.Claramuel Resort. All rights reserved.
                </p>
            </div>
        </div>
    </div>
@endsection

<style>
    .heart-beat {
        animation: heartBeat 1.5s ease-in-out infinite;
    }
    @keyframes heartBeat {
        0% { transform: scale(1); }
        14% { transform: scale(1.3); }
        28% { transform: scale(1); }
        42% { transform: scale(1.3); }
        70% { transform: scale(1); }
    }
</style>
