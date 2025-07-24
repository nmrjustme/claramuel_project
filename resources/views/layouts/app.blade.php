<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        
        <title> @yield('title', 'Mt. Claramuel Resort & Events Place') </title>
        
        <!-- Fonts -->
        <link rel="icon" href="{{ asset('/favicon.ico?v=2') }}">

        <script src="https://cdn.tailwindcss.com"></script>
        <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
        <link href="https://cdn.jsdelivr.net/npm/flowbite@3.1.2/dist/flowbite.min.css" rel="stylesheet" />
        
        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    
    @if(View::hasSection('content') && !View::hasSection('dashboard') && !View::hasSection('myBookings'))
        <body class="font-sans antialiased">
            <div class="min-h-screen bg-white">
                <!-- Page Content -->
                <main>
                    @yield('content')
                </main>
            </div>
        </body>
    @endif
    
    @if(View::hasSection('dashboard') && !View::hasSection('content') && !View::hasSection('myBookings'))
        <body class="font-sans antialiased bg-gray-100 dark:bg-gray-800">

            <div class="min-h-screen">
                <x-navigation />
                <!-- Page Content -->
                <main>
                    @yield('dashboard')
                </main>
            </div>
        </body>
    @endif
    
    @if(View::hasSection('myBookings') && !View::hasSection('content') && !View::hasSection('dashboard'))
        <body class="bg-gray-50 dark:bg-gray-800 bg-gray-100 text-gray-800">
            <x-navigation />
            <!-- Page Content -->
            <main>
                @yield('myBookings')
            </main>
        </body>
    @endif
    
    
    
    
    <script src="https://cdn.jsdelivr.net/npm/flowbite@3.1.2/dist/flowbite.min.js"></script>
</html>
