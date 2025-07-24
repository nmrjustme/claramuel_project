<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invalid Verification Link</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-red-50 min-h-screen flex items-center justify-center p-4">
    <div class="max-w-md w-full bg-white rounded-lg shadow-md overflow-hidden">
        <div class="bg-red-600 p-4">
            <h1 class="text-white text-2xl font-bold text-center">Verification Link Error</h1>
        </div>
        
        <div class="p-6 space-y-6">
            <div class="flex justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            
            <p class="text-gray-700 text-center text-lg">
                Invalid or expired verification link.
            </p>
            
            <p class="text-gray-600 text-center">
                Please book your appointment again using the button below.
            </p>
            
            <div class="pt-4">
                <a href="{{ route('customer_bookings') }}" class="w-full flex justify-center py-3 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors">
                    Book Here
                </a>
            </div>
        </div>
    </div>
</body>
</html>