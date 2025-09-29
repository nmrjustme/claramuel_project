@extends('layouts.guest')
@section('title', 'Profile')
@section('profile')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="flex flex-col lg:flex-row gap-8">
        
        <!-- Left side (Profile Card) -->
        @auth
        <div class="w-full lg:w-1/3">
            <div class="bg-white rounded-lg border border-lightGray overflow-hidden transition-all duration-300">
                <div class="p-6 sm:p-8 flex flex-col items-center relative">
                    
                    <!-- Back button -->
                    <div class="self-start mb-4">
                        <a href="{{ route('admin.dashboard') }}" class="flex items-center text-indigo-600 hover:text-indigo-800 transition-colors group">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1 group-hover:-translate-x-1 transition-transform" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
                            </svg>
                            Back to Dashboard
                        </a>
                    </div>
                    
                    <!-- Profile image with animated hover effect -->
                    <div class="relative group mb-6">
                        <div class="absolute -inset-2 rounded-full blur opacity-75 group-hover:opacity-100 transition-opacity duration-300"></div>
                        <div class="relative">
                            <img src="{{ url('imgs/profiles/' . (Auth::user()->profile_img ?? 'default.jpg')) }}" 
                                 alt="Profile" 
                                 class="w-32 h-32 rounded-full border-4 border-white shadow-lg object-cover transition-all duration-300 group-hover:scale-105"
                                 id="profile-image-preview">
                            
                            <!-- Upload overlay with animation -->
                            <div class="absolute inset-0 rounded-full flex items-center justify-center bg-black bg-opacity-30 opacity-0 group-hover:opacity-100 transition-opacity duration-300 cursor-pointer"
                                 onclick="document.getElementById('profile-image').click()">
                                <div class="p-3 bg-white rounded-full hover:bg-gray-100 transition-all transform group-hover:scale-110">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Hidden form -->
                        <form action="{{ route('profile.image.update') }}" method="POST" enctype="multipart/form-data" id="profile-image-form">
                            @csrf
                            <input type="file" id="profile-image" name="profile_img" class="hidden" accept="image/*" onchange="validateAndSubmit()">
                        </form>
                        
                    </div>
                    
                    <!-- User info with subtle animations -->
                    <div class="text-center transform transition-all hover:scale-[1.02] duration-300">
                        <h2 class="text-2xl font-bold text-gray-800">{{ Auth::user()->firstname }} {{ Auth::user()->lastname }}</h2>
                        <p class="text-gray-600 mt-1 flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                            {{ Auth::user()->email }}
                        </p>
                        
                        <div class="mt-3 flex justify-center space-x-2">
                            <span class="px-3 py-1 bg-indigo-100 text-indigo-800 text-xs font-medium rounded-full flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                                </svg>
                                {{ Auth::user()->role }}
                            </span>
                            @if (empty(Auth::user()->email_verified_at))
                                <span class="px-3 py-1 bg-yellow-100 text-yellow-800 text-xs font-medium rounded-full flex items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                    </svg>
                                    Unverified
                                </span>
                            @else
                                <span class="px-3 py-1 bg-green-100 text-green-800 text-xs font-medium rounded-full flex items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                    </svg>
                                    Verified
                                </span>
                            @endif
                        </div>
                    </div>
                    
                    <!-- Verification Notice -->
                    @if (empty(Auth::user()->email_verified_at))
                    <div class="w-full mt-6 p-4 bg-gradient-to-r from-yellow-50 to-amber-50 border-l-4 border-yellow-400 rounded-lg shadow-sm transform transition-all hover:scale-[1.01] duration-200">
                        <div class="flex items-start">
                            <div class="flex-shrink-0">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-yellow-500" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2h-1V9z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-yellow-800">Email Verification Required</h3>
                                <div class="mt-1 text-sm text-yellow-700">
                                    <p>Please verify your email.</p>
                                    <form method="POST" action="{{ route('verification.send') }}" class="inline">
                                        @csrf
                                        <button type="submit" class="font-medium text-yellow-700 hover:text-yellow-600 underline">
                                            Click here to resend verification email
                                        </button>
                                    </form>
                                </div>
                                @if (session('status') == 'verification-link-sent')
                                    <div class="mt-2 flex items-center text-sm text-green-600">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                        </svg>
                                        Verification link sent!
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endif
                    
                    <!-- Logout button with animation -->
                    <form method="POST" action="{{ route('logout') }}" class="w-full mt-6">
                        @csrf
                        <button type="submit" class="w-full px-4 py-2 bg-gradient-to-r bg-red-600 hover:bg-red-500 text-white font-medium rounded-lg transition-all duration-300 flex items-center justify-center shadow-md hover:shadow-lg transform hover:-translate-y-0.5">
                            Logout
                        </button>
                    </form>
                </div>
            </div>
            
            <!-- Stats card -->
            <div class="mt-6 bg-white  rounded-lg  border border-lightGray p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Account Stats</h3>
                <div class="grid grid-cols-2 gap-4">
                    <div class="bg-blue-50 p-3 rounded-lg text-center">
                        <p class="text-sm text-blue-600">Member Since</p>
                        <p class="font-bold text-blue-800">{{ Auth::user()->created_at->format('M Y') }}</p>
                    </div>
                    <div class="bg-green-50 p-3 rounded-lg text-center">
                        <p class="text-sm text-green-600">Last Updated</p>
                        <p class="font-bold text-green-800">{{ Auth::user()->updated_at->diffForHumans() }}</p>
                    </div>
                </div>
            </div>
        </div>
        @endauth

        <!-- Right side (Forms Section) -->
        <div class="w-full lg:w-2/3 space-y-6">
            <!-- Profile Information Card -->
            <div class="bg-white rounded-lg border border-lightGray overflow-hidden">
                <div class="px-6 py-4 bg-gradient-to-r from-red-600 to-red-700">
                    <h3 class="text-lg font-medium text-gray-100">Profile Information</h3>
                    <p class="mt-1 text-sm text-gray-200">Update your account's profile information</p>
                </div>
                
                <div class="p-6">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>
            
            <!-- Password Update Card -->
            <div class="bg-white rounded-lg border border-lightGray overflow-hidden">
                <div class="px-6 py-4 bg-gradient-to-r from-red-600 to-red-700">
                    <h3 class="text-lg font-medium text-gray-100">Change Password</h3>
                    <p class="mt-1 text-sm text-gray-200">Secure your account with a strong password</p>
                </div>
                <div class="p-6">
                    @include('profile.partials.update-password-form')
                </div>
            </div>
            
            @auth
                @if (empty(Auth::user()->email_verified_at))
                <!-- Danger Zone Card -->
                <div class="bg-white rounded-lg border border-lightGray overflow-hidden">
                    <div class="px-6 py-4 bg-gradient-to-r from-red-600 to-red-700">
                        <h3 class="text-lg font-medium text-gray-100">Danger Zone</h3>
                        <p class="mt-1 text-sm text-gray-200">Permanent actions that cannot be undone</p>
                    </div>
                    <div class="p-6">
                        @include('profile.partials.delete-user-form')
                    </div>
                </div>
                @endif
            @endauth
        </div>
    </div>
</div>

<!-- Toast notification element -->
<div id="toast-notification" class="fixed bottom-4 right-4 hidden">
    <div class="bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg flex items-center transform transition-all duration-300 translate-y-10 opacity-0" id="toast-content">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
        </svg>
        <span id="toast-message">Changes saved successfully!</span>
    </div>
</div>

<script>
function validateAndSubmit() {
    const fileInput = document.getElementById('profile-image');
    const file = fileInput.files[0];

    if (!file) return;

    // Basic validation
    const validTypes = ['image/jpeg', 'image/png', 'image/gif'];
    if (!validTypes.includes(file.type)) {
        showToast('Please upload a valid image file (JPEG, PNG, GIF)', 'error');
        return;
    }

    if (file.size > 2 * 1024 * 1024) { // 2MB limit
        showToast('File size should be less than 2MB', 'error');
        return;
    }

    // Preview image
    const reader = new FileReader();
    reader.onload = function(e) {
        document.getElementById('profile-image-preview').src = e.target.result;
        showToast('Uploading profile picture...', 'success');
    }
    reader.readAsDataURL(file);
    
    // Submit form
    document.getElementById('profile-image-form').submit();
}

function showToast(message, type = 'success') {
    const toast = document.getElementById('toast-notification');
    const toastContent = document.getElementById('toast-content');
    const toastMessage = document.getElementById('toast-message');
    
    // Set message and style based on type
    toastMessage.textContent = message;
    if (type === 'error') {
        toastContent.classList.remove('bg-green-500');
        toastContent.classList.add('bg-red-500');
    } else {
        toastContent.classList.remove('bg-red-500');
        toastContent.classList.add('bg-green-500');
    }
    
    // Show toast
    toast.classList.remove('hidden');
    setTimeout(() => {
        toastContent.classList.remove('translate-y-10', 'opacity-0');
        toastContent.classList.add('translate-y-0', 'opacity-100');
    }, 10);
    
    // Hide after 3 seconds
    setTimeout(() => {
        toastContent.classList.remove('translate-y-0', 'opacity-100');
        toastContent.classList.add('translate-y-10', 'opacity-0');
        setTimeout(() => toast.classList.add('hidden'), 300);
    }, 3000);
}

// Show toast when profile is updated
document.addEventListener('DOMContentLoaded', function() {
    @if (session('status') === 'profile-updated' || session('status') === 'password-updated')
        showToast('Changes saved successfully!');
    @endif
});
</script>

<style>
    /* Smooth transitions for all interactive elements */
    button, a, input, .hover-effect {
        transition: all 0.3s ease;
    }
    /* Hover effect for cards */
    .hover-effect:hover {
        transform: translateY(-2px);
    }
</style>
@endsection