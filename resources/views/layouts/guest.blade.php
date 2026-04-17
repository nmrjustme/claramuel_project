<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        
        <title> @yield('title', 'Mt. Claramuel Resort & Events Place') </title>
        
        <!-- Fonts -->
        <link rel="icon" href="{{ asset('/favicon.ico?v=2') }}">
        <!-- Scripts -->
        <script src="https://cdn.tailwindcss.com"></script>
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
        <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
        @vite(['resources/js/app.js'])
        <script>
            tailwind.config = {
                theme: {
                    extend: {
                        colors: {
                            lightGray: '#D3D3D3',
                            darkGray: '#A0A0A0',
                            backgroundLightGray: '#f6f6f6'
                        },
                    }
                }
            }
        </script>
    </head>

        @if(View::hasSection('content') && !View::hasSection('auth') && !View::hasSection('profile'))
            <body class="font-sans antialiased">
                <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0">
                    <div class="w-full sm:max-w-md mt-6 px-6 py-4 rounded-lg lg:border border-lightGray overflow-hidden sm:rounded-lg">
                        @yield('content')
                    </div>
                </div>
            </body>
        @endif
        
        @if(View::hasSection('auth') && !View::hasSection('content') && !View::hasSection('profile'))
            <body class="font-sans antialiased">
                <div class="h-screen flex items-center justify-center">
                    <div class="w-full max-w-4xl flex flex-col lg:flex-row rounded-lg lg:border border-lightGray overflow-hidden">
                        @yield('auth')
                    </div>
                </div>
            </body>
        @endif
        
        @if(View::hasSection('profile') && !View::hasSection('auth') && !View::hasSection('content') && !View::hasSection('admin_profile'))
            <body class="bg-backgroundLightGray font-sans antialiased">
                <div class="py-12">
                    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                            @yield('profile')
                    </div>
                </div>
            </body>
        @endif
        
        <script src="https://cdn.jsdelivr.net/npm/flowbite@3.1.2/dist/flowbite.min.js"></script>
        <script>
            document.addEventListener("DOMContentLoaded", function () {
                const form = document.querySelector("form");
                const loginButton = document.getElementById("button");
                const buttonText = document.getElementById("button-text");
                const spinner = document.getElementById("spinner");
        
                form.addEventListener("submit", function () {
                    loginButton.disabled = true;
                    buttonText.textContent = "Loading...";
                    spinner.classList.remove("hidden");
                });
            });
        </script>
    </body>
</html>
