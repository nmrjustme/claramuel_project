<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QR Code In Use Indicator</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center p-4">
    <div class="bg-gray-200 p-8 rounded-xl shadow-lg max-w-md w-full">
        <div class="text-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800">QR Code Status</h1>
            <p class="text-gray-600 mt-1">Scanning system indicator</p>
        </div>

        <!-- QR Code Container -->
        <div class="bg-white p-4 rounded-lg border-4 border-red-400 shadow-inner mb-6 flex flex-col items-center">
            <!-- QR Code Placeholder (replace with your actual QR code) -->
               <div class="w-48 h-48 bg-gray-100 flex items-center justify-center mb-3 relative overflow-hidden rounded">
               <!-- Replace "path/to/your-qr-code.png" with your actual image path -->
               
               <img src="{{ $qrPath }}" 
                    alt="QR Code" 
                    class="w-full h-full object-contain p-2">
               
               <!-- Overlay text (optional) -->
               <span class="absolute bottom-2 text-gray-400 text-xs bg-white/80 px-2 py-1 rounded">IN USE</span>
               </div>
            
            <!-- Status Indicator -->
            <div class="bg-red-500 text-white px-5 py-2 rounded-full flex items-center animate-pulse">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                </svg>
                <span class="font-bold tracking-wide">CURRENTLY IN USE</span>
            </div>
        </div>

        <!-- Warning Message -->
        <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded-r-lg">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-red-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-red-700">
                        This QR code is currently active. Please wait until the current session ends or contact the system administrator if this persists.
                    </p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>