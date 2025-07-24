<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/@tailwindcss/browser@4"></script>
    <link href="https://cdn.jsdelivr.net/npm/flowbite@3.1.2/dist/flowbite.min.css" rel="stylesheet" />

    <title>Login</title>
</head>

<body class="flex items-center justify-center min-h-screen bg-gray-100">
    <form class="bg-white p-6 rounded-2xl shadow-md max-w-sm w-full" method="POST" action="{{ route('login') }}">
        @csrf
        <p class="text-2xl font-semibold text-blue-600 mb-2 flex items-center relative">
            Login
        </p>
        <p class="text-gray-600 text-sm mb-4">Welcome back!</p>

        <label class="block relative mt-3">
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" placeholder="Email" :value="old('email')" autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </label>
        
        <label class="block relative mt-3">
            <input required type="password" class="w-full p-3 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:outline-none" placeholder="Password" name="password">
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </label>
        <div class="flex items-start py-5">
            <div class="flex items-start">
                <div class="flex items-center">
                    <input id="remember" name="remember" type="checkbox" 
                        class="w-4 h-4 border border-gray-300 rounded-sm bg-blue-50 text-blue-600 focus:ring-2 focus:ring-blue-500 dark:border-gray-600 dark:focus:ring-blue-600" />
                
                    <label for="remember" class="ml-2 text-sm text-gray-600">{{ __('Remember me') }}</label>
                </div>
                
            </div>
            <a href="{{ route('password.request') }}" class="ms-auto text-sm text-blue-700 hover:underline dark:text-blue-500">Lost Password?</a>
        </div>
        <button class="w-full mt-4 bg-blue-600 text-white p-3 rounded-md hover:bg-blue-700 transition">Submit</button>
        
        <p class="text-center text-sm text-gray-600 mt-3">You don't have an account? <a href="{{ route('register') }}" class="text-blue-600 hover:underline">Register</a></p>
    </form>
</body>

</html>