@extends('layouts.bookings')
@section('title', 'Redirecting...')
@section('bookings')
    <style>
        @keyframes progress {
            0% { width: 0%; }       
            100% { width: 100%; }
        }
        .progress-animation {
            animation: progress 5s linear forwards;
        }
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>

    <div class="flex items-center justify-center min-h-screen">
        <div class="max-w-md w-full bg-white rounded-xl overflow-hidden animate__animated animate__fadeIn border border-lightgray">
            <div class="h-1.5 bg-payment" id="top-bar"></div>
            
            <div class="p-8">
                <div class="mb-8 animate__animated animate__bounceIn">
                    <div class="relative w-32 h-32 mx-auto">
                        <div class="absolute inset-0 rounded-full border-4 border-transparent bg-red-to-r from-red to-secondary p-1">
                            <img src="{{ asset('imgs/logo.png') }}" alt="Logo" class="w-full h-full rounded-full object-cover bg-white p-2">
                        </div>
                    </div>
                </div>
                
                <div class="flex flex-col items-center" id="loading-container">
                    <div class="relative mb-6">
                        <div class="w-16 h-16 rounded-full absolute border-4 border-gray-200"></div>
                        <div class="w-16 h-16 rounded-full animate-spin border-4 border-transparent border-t-payment border-r-payment"></div>
                    </div>
                    
                    <h2 class="text-2xl font-bold text-dark mb-2">Redirecting to payment...</h2>
                    <div class="w-full bg-gray-200 rounded-full h-2 mb-6">
                        <div class="bg-payment h-2 rounded-full progress-animation" id="progress-bar"></div>
                    </div>
                    
                    <div class="text-sm text-gray-500 flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span id="countdown">Redirecting in 5s...</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        const paymentUrl = "{{ route('customer.booking.payments', ['token' => $token]) }}";
        let seconds = 5;
        const countdownElement = document.getElementById('countdown');
        
        const countdownInterval = setInterval(() => {
            seconds--;
            countdownElement.textContent = `Redirecting in ${seconds}s...`;
            
            if (seconds <= 0) {
                clearInterval(countdownInterval);
                window.location.href = paymentUrl;
            }
        }, 1000);

        setTimeout(() => {
            window.location.href = paymentUrl;
        }, 5000);
    </script>
@endsection
