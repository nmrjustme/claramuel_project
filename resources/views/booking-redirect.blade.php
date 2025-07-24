<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Redirecting - Mt. ClaRamuel Resort</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#DC2626',
                        secondary: '#B91C1C',
                        dark: '#1F2937',
                        light: '#F9FAFB',
                        accent: '#EA580C',
                        payment: '#10B981'
                    },
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    },
                }
            }
        }
    </script>
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
</head>
<body class="bg-red-50 flex flex-col justify-center items-center min-h-screen p-4 text-center">
    <div class="max-w-md w-full bg-white rounded-xl shadow-2xl overflow-hidden animate__animated animate__fadeIn border border-red-200">
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

    <script>
        // Set the payment URL (replace with your actual payment route)
        const paymentUrl = "{{ route('payments', ['booking' => $booking ?? null]) }}";
        let seconds = 5;
        const countdownElement = document.getElementById('countdown');

        // Start the countdown
        const countdownInterval = setInterval(() => {
            seconds--;
            countdownElement.textContent = `Redirecting in ${seconds}s...`;
            
            if (seconds <= 0) {
                clearInterval(countdownInterval);
                window.location.href = paymentUrl;
            }
        }, 1000);

        // Fallback redirect after 5 seconds
        setTimeout(() => {
            window.location.href = paymentUrl;
        }, 5000);
    </script>
</body>
</html>