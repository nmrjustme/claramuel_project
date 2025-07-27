<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Mt. Claramuel Resort & Events Place')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <meta name="csrf-token" content="{{ csrf_token() }}">
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
                    },
                    fontFamily: {
                        sans: [
                            'Inter', 
                            'system-ui', 
                            '-apple-system', 
                            'BlinkMacSystemFont', 
                            'Segoe UI', 
                            'Roboto', 
                            'sans-serif'
                        ],
                    },
                }
            }
        }
    </script>
    
    <body class="bg-gray-50 min-h-screen">
        @yield('bookings')
    </body>
</head>
