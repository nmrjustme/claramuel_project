@extends('layouts.guest')
@section('title','Register')
@section('auth')
<!-- Left Section - Login Form -->
<div class="w-full lg:w-1/2 flex items-center dark:bg-gray-600 justify-center px-6 sm:px-8 py-10">
    <div class="w-full max-w-md">
        <div class="mb-6 justify-self-start">

            
            <!-- New section explaining why we need this info -->
            <div class="mt-4 p-4 bg-blue-50 dark:bg-blue-900 rounded-lg">
                <h3 class="font-medium text-blue-800 dark:text-blue-200">Booking Information</h3>
                <p class="text-sm text-blue-600 dark:text-blue-300 mt-1">
                    We'll need your first name, last name, and phone number when you make a booking. 
                    Providing them now will make your booking process faster later.
                </p>
            </div>
        </div>
        
        <form method="POST" action="{{ route('register') }}">
            @csrf
            
            <!-- Personal Information Section -->
            <div class="mb-6">
                <h3 class="text-lg font-medium text-gray-700 dark:text-gray-100 mb-4">Personal Information</h3>
                
                <div class="flex gap-x-4 mb-4">
                    <div class="w-1/2 flex flex-col">
                        <x-input-label for="firstname" :value="__('First Name')" />
                        <x-text-input type="text" name="firstname" id="firstname" 
                            class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-400" value="{{ old('firstname') }}" 
                            autofocus autocomplete="firstname" placeholder="John"/>
                        <x-input-error :messages="$errors->get('firstname')" class="mt-2" />
                    </div>
        
                    <div class="w-1/2 flex flex-col">
                        <x-input-label for="lastname" :value="__('Last Name')" />
                        <x-text-input type="text" name="lastname" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-400" value="{{ old('lastname') }}" autofocus autocomplete="lastname" placeholder="Doe" />
                        <x-input-error :messages="$errors->get('lastname')" class="mt-2" />
                    </div>
                </div>
                
                <div class="mb-4">
                    <x-input-label for="phone" :value="__('Phone Number')" />
                    <x-text-input 
                        type="text" 
                        name="phone" 
                        id="phone"
                        value="{{ old('phone') }}" 
                        class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" 
                        maxlength="13" {{-- max length to fit "9999 999 999" --}}
                        oninput="formatPhone(this)"
                        placeholder="9999 999 999"
                    />
                    <x-input-error :messages="$errors->get('phone')" class="mt-2" />
                </div>
            </div>
            
            <!-- Account Information Section -->
            <div class="mb-6">
                <h3 class="text-lg font-medium text-gray-700 dark:text-gray-100 mb-4">Account Information</h3>
                
                <div class="mb-4">
                    <x-input-label for="email" :value="__('Email Address')" />
                    <x-text-input type="email" name="email" value="{{ old('email')}}" placeholder="john@gmail.com" 
                        class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" />
                    <x-input-error :messages="$errors->get('email')" class="mt-2" /><x-input-error :messages="$errors->get('email')" class="mt-2" />
                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                        Please enter a working email address. You'll need to verify it to complete registration.
                    </p>
                </div>
                
                <div class="mb-4">
                    <x-input-label for="password" :value="__('Password')" />
                    <x-text-input type="password" name="password" autocomplete="new-password"
                        class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" />
                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                        Your password should be at least 8 characters long and include a mix of letters, numbers, or symbols.
                    </p>
                </div>
            
                <!-- Confirm Password -->
                <div class="mb-4">
                    <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
                    <x-text-input type="password" name="password_confirmation" autocomplete="new-password"
                        class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" />
                    <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                        Please re-enter the same password to confirm. Make sure they match.
                    </p>
                </div>
            </div>
            
            <x-primary-button id="button" class="w-full flex items-center justify-center gap-2">
                @include('auth.spinner')
                <span id="button-text">{{ __('Register') }}</span>
            </x-primary-button>
        </form>
        
        <div class="mt-6 text-center">
            <p class="mt-4 text-center text-gray-600 dark:text-gray-300">Already have an account?
                <a href="{{ route('login') }}" class="dark:text-blue-300 text-blue-700 dark:hover:text-white hover:underline">
                    Login here
                </a>
            </p>
        </div>
    </div>
</div>

<!-- Right Section - Image -->
<div class="w-full lg:w-1/2 hidden lg:flex items-center justify-center bg-no-repeat bg-cover bg-center" style="background-image: url('{{ url('/imgs/auth_img.jpg') }}');">
</div>


<script>
function formatPhone(input) {
    let value = input.value.replace(/\D/g, '').substring(0, 11); // only digits, max 11 numbers
    let formatted = '';
    
    if (value.length > 4) {
        formatted = value.substring(0, 4) + ' ' + value.substring(4, 7);
    } else {
        formatted = value;
    }

    if (value.length > 7) {
        formatted = value.substring(0, 4) + ' ' + value.substring(4, 7) + ' ' + value.substring(7, 10);
    }

    input.value = formatted.trim();
}
</script>
@include('auth.login_register_script')
@include('auth.flowbite_script')

@endsection