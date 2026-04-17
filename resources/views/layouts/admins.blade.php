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

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="bg-gray-100 font-sans">
        <div class="flex h-screen overflow-hidden">
            
            @yield('sidebar')

            <!-- Main Content -->
            <div class="flex flex-col flex-1 overflow-hidden">    
                @yield('content')
            </div>
    
            
        </div>
    </body>

    
    <script src="https://cdn.jsdelivr.net/npm/flowbite@3.1.2/dist/flowbite.min.js"></script>
</html>
