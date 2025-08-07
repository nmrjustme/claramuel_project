@extends('layouts.guest')
@section('title','Login')
@section('auth')
<!-- Left Section - Login Form -->
<div class="w-full lg:w-1/2 flex items-center justify-center px-6 sm:px-8 py-10">
    <div class="w-full max-w-md">
        <div class="mb-6 justify-self-start">
            <x-logo-icon size="xl"/>
        </div>
        
        <!-- Reminder for customers -->
        <div class="mb-6 p-4 bg-blue-50 dark:bg-blue-900 rounded-lg">
            <p class="text-blue-800 dark:text-blue-200">
                <strong>Note:</strong> This login is for authorized users only. 
                If you're a customer looking to book our services, 
                <a href="{{ route('customer_bookings') }}" class="text-blue-600 dark:text-blue-300 font-semibold underline">click here to book directly</a>.
            </p>
        </div>
        
        <form method="POST" action="{{ route('login') }}">
            @csrf
            
            <div class="mb-4">
                <x-input-label for="email" :value="__('Email address')" />
                <x-text-input type="email" name="email" value="{{ old('email')}}" class="w-full px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>
            <div class="mb-4">
                <x-input-label for="password" :value="__('Password')" />
                <x-text-input type="password" name="password" autocomplete="current-password" class="w-full px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" />
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>
            <div class="flex justify-between items-center mb-4">
                <label class="flex items-center space-x-2">
                    <input type="checkbox" name="remember" class="w-4 h-4 text-blue-600 border border-darkGray rounded focus:ring-blue-500 focus:ring-2">
                    <span class="text-gray-600 text-sm">{{ __('Remember me') }}</span>
                </label>
                <a href="{{ route('password.request') }}" class="text-blue-700 hover:underline text-sm">Forgot password?</a>
            </div>
                    
            <x-primary-button id="button" class="w-full flex items-center justify-center gap-2">
                @include('auth.spinner')
                <span id="button-text">Sign in</span>
            </x-primary-button>
        </form>
    </div>
</div>

<!-- Right Section - Image -->
<div class="w-full lg:w-1/2 hidden lg:flex items-center justify-center">
    <img src="{{ url('/imgs/auth_img.jpg') }}" class="w-full h-full object-cover max-h-screen">
</div>
    @include('auth.login_register_script')
    @include('auth.flowbite_script')

@endsection