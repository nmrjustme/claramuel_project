<!-- Customer homepage Navigation -->
<nav class="bg-white border-b shadow-sm px-6 py-3 flex items-center justify-between">
  <!-- Left side: Logo + Nav Links -->
    <div class="flex items-center space-x-10">
        <!-- Logo -->
        <div class="flex items-center space-x-">
            <x-logo-icon size="default"/>
            <span class="text-gray-300 sm:text-base lg:text-1xl text-red-300"><a href="{{ route('index') }}">Ｍｔ.ＣＬＡＲＡＭＵＥＬ</a></span>
        </div>
    
        <!-- Navigation Links -->
        <!--<div class="flex space-x-8 text-sm font-medium">
        <a href="#" class="text-gray-900 border-b-2 border-indigo-500 pb-1">Dashboard</a>
        <a href="#" class="text-gray-500 hover:text-gray-900">Team</a>
        <a href="#" class="text-gray-500 hover:text-gray-900">Projects</a>
        <a href="#" class="text-gray-500 hover:text-gray-900">Calendar</a>
        </div>-->
    </div>

  <!-- Right side: Notification + Profile -->
  @auth
    <div class="flex items-center space-x-6">
        <!-- Bell Icon -->
        <button class="text-gray-400 hover:text-gray-600">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6 6 0 00-5-5.917V4a2 2 0 10-4 0v1.083A6 6 0 004 11v3.159c0 .538-.214 1.055-.595 1.436L2 17h5m7 0a3 3 0 11-6 0h6z" />
            </svg>
        </button>

        
        <!-- Profile Dropdown -->
        <div x-data="{ open: false }" class="relative">
            <button @click="open = !open" class="flex items-center space-x-2 focus:outline-none">
                <img src="https://randomuser.me/api/portraits/men/32.jpg" class="h-8 w-8 rounded-full object-cover" alt="Profile" />
                <span class="text-sm font-medium text-gray-700">{{ Auth::user()->firstname }}</span>
    
                <!-- Dropdown Icon -->
                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"
                    xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"></path>
                </svg>
            </button>

            <!-- Dropdown Menu -->
            <div x-show="open" @click.away="open = false"class="absolute right-0 mt-2 w-48 bg-white border border-gray-200 rounded-md shadow-lg py-1 z-50" x-cloak>
                
                <a href="{{ __('profile') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Profile</a>
                
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                    Logout
                    </button>
                </form>
            </div>
        </div>
        
    </div>
    @endauth
</nav>