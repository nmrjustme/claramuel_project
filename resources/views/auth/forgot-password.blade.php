@extends('layouts.guest')
@section('title', 'Forgot Password')
@section('content')
    
    <div class="mb-4 text-sm text-gray-600">
        {{ __('Forgot your password? No problem. Just let us know your email address and we will email you a password reset link that will allow you to choose a new one.') }}
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}">
        @csrf
        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" type="email" name="email" :value="old('email')" autofocus />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

         <div class="flex items-center justify-end mt-6">
            <x-primary-button id="button"
                class="w-full flex items-center justify-center gap-2">
                @include('auth.spinner') {{-- Assumes spinner is hidden initially --}}
                <span id="button-text">{{ __('Email Password Reset Link') }}</span>
            </x-primary-button>
        </div>
    </form>
@endsection