
<div class="flex items-center space-x-3">
    <x-logo-icon size="default" />
    <span class="text-gray-300 text-base md:text-3xl text-red-300"><a href="#">Ｍｔ.ＣＬＡＲＡＭＵＥＬ</a></span>
</div>
<div class="relative">

    <div class="relative group">
        @auth
        <button class="flex items-center gap-2 focus:outline-none">
            <img src="{{ url('imgs/profiles/' . Auth::user()->profile_img) }}"
                class="h-8 w-8 rounded-full object-cover" alt="Profile" />
            <span class="text-sm font-medium text-gray-700 dark:text-white">{{ Auth::user()->firstname }}</span>
            <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" stroke-width="2"
                viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
            </svg>
        </button>
        @endauth


        <!-- Dropdown Menu -->
        <div
            class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-md shadow-lg py-1 z-50 opacity-0 scale-95 group-focus-within:opacity-100 group-focus-within:scale-100 group-focus-within:block transition-all ease-in-out duration-200 hidden">
            <a href="{{ route('profile.edit') }}"
                class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-600">Profile</a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                    class="w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-600">Logout</button>
            </form>
        </div>
    </div>

</div>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const headerProfileDropdownToggle = document.getElementById('headerProfileDropdownToggle');
        const headerProfileDropdown = document.getElementById('headerProfileDropdown');
        headerProfileDropdownToggle.addEventListener('click', function () {
            headerProfileDropdown.classList.toggle('hidden');
        });
        document.addEventListener('click', function (e) {
            if (!headerProfileDropdownToggle.contains(e.target) && !headerProfileDropdown.contains(e.target)) {
                headerProfileDropdown.classList.add('hidden');
            }
        });
    });
</script>