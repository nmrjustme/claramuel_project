@extends('layouts.bookings')
@section('title', 'Booking Confirmation')
@section('bookings')
    <x-header />
    <div class="container mx-auto px-4 sm:px-6 py-6 md:py-8 max-w-12xl">
        <!-- Progress Steps -->
        <x-progress-steps
            :currentStep="3"
            :progress="'100%'"
            :steps="[['label' => 'Select Rooms'], ['label' => 'Payment'], ['label' => 'Completed']]"
        />
    
        <div class="max-w-4xl mx-auto bg-white rounded-lg md:rounded-xl shadow-md md:shadow-lg overflow-hidden">
            <!-- Header Section -->
            <div class="bg-gradient-to-r from-red-600 to-red-700 py-6 md:py-8 px-6 md:px-8 text-center">
                <div class="heart-beat text-white mb-3 md:mb-4">
                    <i class="fas fa-heart text-4xl md:text-5xl"></i>
                </div>
                <h1 class="text-2xl md:text-3xl font-bold text-white">Thank You for Your Booking!</h1>
                <p class="text-red-100 mt-1 md:mt-2 text-base md:text-lg">Mt.Claramuel Resort and Events Place</p>
            </div>
            
            <!-- Main Content -->
            <div class="p-4 sm:p-6 md:p-8">
                <div class="bg-green-50 p-4 md:p-6 rounded-lg border border-green-200 mb-6 md:mb-8">
                    <div class="flex flex-col sm:flex-row items-start">
                        <div class="bg-green-100 p-2 md:p-3 rounded-lg mb-3 sm:mb-0 sm:mr-4">
                            <i class="fas fa-check-circle text-green-600 text-xl md:text-2xl"></i>
                        </div>
                        <div class="flex-1">
                            <h2 class="text-lg md:text-xl font-bold text-green-800 mb-1 md:mb-2">Payment Received!</h2>
                            <p class="text-gray-700 mb-2 md:mb-4 text-sm sm:text-base">Your booking payment has been received. We've sent the details to your email at <span class="font-semibold">{{ $booking->user->email }}</span>.</p>
                            <p class="text-gray-700 mb-3 md:mb-4 text-sm sm:text-base">You will receive your payment receipt via email once it has been verified</p>
                            <div class="bg-white p-3 md:p-4 rounded border border-green-100">
                                <p class="text-xs md:text-sm text-gray-600 mb-1">Booking Reference:</p>
                                <p class="font-bold text-green-700 text-base md:text-lg">{{ $booking->reference}}</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Next Steps -->
                <div class="bg-blue-50 p-4 md:p-6 rounded-lg border border-blue-200 mb-6 md:mb-8">
                    <h3 class="text-base md:text-lg font-bold text-blue-800 mb-2 md:mb-3">What to Expect Next</h3>
                    <ul class="space-y-2 md:space-y-3">
                        <li class="flex items-start">
                            <i class="fas fa-check-circle text-green-500 mt-0.5 md:mt-1 mr-2 md:mr-3 text-sm md:text-base"></i>
                            <span class="text-sm md:text-base">Booking receipt sent to your email</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check-circle text-green-500 mt-0.5 md:mt-1 mr-2 md:mr-3 text-sm md:text-base"></i>
                            <span class="text-sm md:text-base">Special offers for your next visit</span>
                        </li>
                    </ul>
                </div>
                
                <!-- Contact Information -->
                <div class="bg-gray-50 p-4 md:p-6 rounded-lg border border-gray-200">
                    <h3 class="text-base md:text-lg font-bold text-gray-800 mb-2 md:mb-3">Need Assistance?</h3>
                    <p class="text-gray-600 mb-3 md:mb-4 text-sm md:text-base">Our team is happy to help with any questions about your booking.</p>
                    <div class="space-y-2 md:space-y-3">
                        <a href="tel:+639952901333" class="flex items-center text-red-600 hover:text-red-700 text-sm md:text-base">
                            <i class="fas fa-phone-alt mr-2 md:mr-3 text-sm md:text-base"></i>
                            +63 995 290 1333
                        </a>
                        <a href="mailto:mtclaramuelresort@gmail.com" class="flex items-center text-red-600 hover:text-red-700 text-sm md:text-base">
                            <i class="fas fa-envelope mr-2 md:mr-3 text-sm md:text-base"></i>
                            mtclaramuelresort@gmail.com
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- Footer -->
            <div class="bg-gray-100 px-4 md:px-8 py-4 md:py-6 text-center">
                <p class="text-gray-600 text-sm md:text-base">We look forward to welcoming you to Mt.Claramuel Resort!</p>
                <p class="text-xs md:text-sm text-gray-500 mt-1 md:mt-2">© {{ date('Y') }} Mt.Claramuel Resort. All rights reserved.</p>
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