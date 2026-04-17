<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Processing - Mt.Claramuel Resort</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .progress-bar {
            height: 4px;
            background: linear-gradient(90deg, #f87171 0%, #ef4444 50%, #dc2626 100%);
            background-size: 200% 100%;
            animation: progressAnimation 2s ease-in-out infinite;
        }
        @keyframes progressAnimation {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }
        .card-hover {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .card-hover:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        }
        .pulse {
            animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }
        .float {
            animation: float 3s ease-in-out infinite;
        }
        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-5px); }
        }
    </style>
</head>
<body class="bg-gray-50 min-h-screen flex items-center justify-center p-4 sm:p-6">
    <div class="bg-white rounded-lg shadow-md w-full max-w-md overflow-hidden card-hover">
        <!-- Animated progress bar -->
        <div class="progress-bar"></div>
        
        <!-- Header -->
        <div class="bg-gradient-to-r from-red-600 to-red-700 py-5 sm:py-6 px-4 sm:px-6 text-center">
            <div class="flex items-center justify-center space-x-2 sm:space-x-3">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 sm:h-6 sm:w-6 text-white float" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <h1 class="text-lg sm:text-xl font-semibold text-white">Processing Your Booking</h1>
            </div>
            <p class="text-red-100 text-xs sm:text-sm mt-1">Mt.Claramuel Resort</p>
        </div>
        
        <!-- Main content -->
        <div class="p-4 sm:p-6">
            <h2 class="text-lg sm:text-xl font-semibold text-gray-800 text-center mb-3 sm:mb-4">Booking Received!</h2>
            
            <div class="text-gray-600 mb-4 sm:mb-6 space-y-3 sm:space-y-4">                
                <!-- Summary Card -->
                <div class="bg-gray-50 p-3 sm:p-4 rounded-lg border border-gray-200">
                    <div class="flex items-start">
                        <div class="bg-red-100 p-1.5 sm:p-2 rounded-lg mr-2 sm:mr-3">
                            <i class="fas fa-info-circle text-red-600 text-sm sm:text-base"></i>
                        </div>
                        <div class="text-xs sm:text-sm">
                            <p class="font-medium text-gray-800 mb-1 sm:mb-2">What's happening now:</p>
                            <ul class="space-y-1.5 sm:space-y-2">
                                <li class="flex items-start">
                                    <i class="fas fa-check text-green-500 text-2xs sm:text-xs mt-0.5 sm:mt-1 mr-1.5 sm:mr-2"></i>
                                    <span>Verifying your booking details</span>
                                </li>
                                <li class="flex items-start">
                                    <i class="far fa-clock text-amber-500 text-2xs sm:text-xs mt-0.5 sm:mt-1 mr-1.5 sm:mr-2"></i>
                                    <span>Sending confirmation to you</span>
                                </li>
                            </ul>
                            <div class="mt-2 sm:mt-3 bg-white p-1.5 sm:p-2 rounded border border-gray-200">
                                <p class="text-2xs sm:text-xs text-gray-500">Confirmation will be sent to:</p>
                                <p class="font-medium text-red-600 truncate text-sm sm:text-base">{{ $email }}</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Important Notes -->
                <div class="bg-amber-50 p-3 sm:p-4 rounded-lg border border-amber-200 text-xs sm:text-sm">
                    <div class="flex items-start">
                        <div class="bg-amber-100 p-1.5 sm:p-2 rounded-lg mr-2 sm:mr-3">
                            <i class="fas fa-exclamation-triangle text-amber-600 text-sm sm:text-base"></i>
                        </div>
                        <div>
                            <p class="font-medium text-amber-700 mb-1 sm:mb-2">Important:</p>
                            <ul class="space-y-1 sm:space-y-1.5">
                                <li class="flex items-start">
                                    <i class="fas fa-circle text-amber-500 text-3xs sm:text-2xs mt-1 sm:mt-1.5 mr-1 sm:mr-2"></i>
                                    <span>Check spam/junk folder if message doesn't arrive</span>
                                </li>
                                <li class="flex items-start">
                                    <i class="fas fa-circle text-amber-500 text-3xs sm:text-2xs mt-1 sm:mt-1.5 mr-1 sm:mr-2"></i>
                                    <span>Processing may take up to 2 hours during peak times</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Contact Info -->
            <div class="pt-3 sm:pt-4 border-t border-gray-200">
                <p class="text-xs sm:text-sm text-gray-600 mb-2 sm:mb-3 text-center">Need immediate assistance?</p>
                <div class="flex flex-col space-y-1 sm:space-y-2">
                    <a href="tel:+631234567890" class="text-red-600 hover:text-red-700 text-xs sm:text-sm flex items-center justify-center">
                        <i class="fas fa-phone-alt mr-1.5 sm:mr-2"></i>
                        +63 995 290 1333
                    </a>
                    <a href="mailto:reservations@mtclaramuelresort.com" class="text-red-600 hover:text-red-700 text-xs sm:text-sm flex items-center justify-center">
                        <i class="fas fa-envelope mr-1.5 sm:mr-2"></i>
                        mtclaramuelresort@gmail.com
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Simulate progress updates
        setTimeout(() => {
            document.querySelectorAll('.flex.justify-between.items-center.mb-8 div:nth-child(2) div').innerHTML = '<i class="fas fa-check text-xs"></i>';
            document.querySelectorAll('.flex.justify-between.items-center.mb-8 div:nth-child(2) div').classList.remove('pulse');
            document.querySelectorAll('.flex.justify-between.items-center.mb-8 div:nth-child(2) div').classList.add('bg-red-600', 'text-white');
            document.querySelectorAll('.flex.justify-between.items-center.mb-8 div:nth-child(3) div').classList.add('pulse');
            document.querySelectorAll('.flex.justify-between.items-center.mb-8 div:nth-child(3) div').innerHTML = '<i class="fas fa-spinner text-xs animate-spin"></i>';
        }, 2000);
        
        setTimeout(() => {
            document.querySelectorAll('.flex.justify-between.items-center.mb-8 div:nth-child(3) div').innerHTML = '<i class="fas fa-check text-xs"></i>';
            document.querySelectorAll('.flex.justify-between.items-center.mb-8 div:nth-child(3) div').classList.remove('pulse');
            document.querySelectorAll('.flex.justify-between.items-center.mb-8 div:nth-child(3) div').classList.add('bg-red-600', 'text-white');
            document.querySelectorAll('.flex.justify-between.items-center.mb-8 div:nth-child(4) div').classList.add('pulse');
            document.querySelectorAll('.flex.justify-between.items-center.mb-8 div:nth-child(4) div').innerHTML = '<i class="fas fa-spinner text-xs animate-spin"></i>';
        }, 4000);
        
        setTimeout(() => {
            document.querySelectorAll('.flex.justify-between.items-center.mb-8 div:nth-child(4) div').innerHTML = '<i class="fas fa-check text-xs"></i>';
            document.querySelectorAll('.flex.justify-between.items-center.mb-8 div:nth-child(4) div').classList.remove('pulse');
            document.querySelectorAll('.flex.justify-between.items-center.mb-8 div:nth-child(4) div').classList.add('bg-red-600', 'text-white');
            document.querySelector('h2').textContent = 'Booking Confirmed!';
            document.querySelector('h2').classList.add('text-green-600');
        }, 6000);
    </script>
</body>
</html>