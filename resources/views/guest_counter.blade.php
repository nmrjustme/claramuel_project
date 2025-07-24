<div class="relative inline-block text-left w-full max-w-sm items-center justify-center py-4">
    <!-- Dropdown Button -->
    <div>
        <button id="guestDropdownButton" type="button"
            class="inline-flex justify-between w-full rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none">
            Guests
            <svg class="-mr-1 ml-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
            </svg>
        </button>
    </div>

    <!-- Dropdown Menu -->
    <div id="guestDropdownMenu"
        class="hidden origin-top-right absolute right-0 mt-2 w-72 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-50">
        <div class="p-4 space-y-4">
            <!-- Adults -->
            <div class="flex justify-between items-center">
                <span class="text-gray-700 font-medium">Adults</span>
                <div class="flex items-center space-x-2">
                    <button onclick="decrement('adult')"
                        class="w-8 h-8 rounded-full bg-gray-200 text-gray-700 text-lg hover:bg-gray-300">−</button>
                    <span id="adult-count" class="w-6 text-center">0</span>
                    <button onclick="increment('adult')"
                        class="w-8 h-8 rounded-full bg-gray-200 text-gray-700 text-lg hover:bg-gray-300">+</button>
                </div>
            </div>

            <!-- Kids -->
            <div class="flex justify-between items-center">
                <span class="text-gray-700 font-medium">Kids</span>
                <div class="flex items-center space-x-2">
                    <button onclick="decrement('kids')"
                        class="w-8 h-8 rounded-full bg-gray-200 text-gray-700 text-lg hover:bg-gray-300">−</button>
                    <span id="kids-count" class="w-6 text-center">0</span>
                    <button onclick="increment('kids')"
                        class="w-8 h-8 rounded-full bg-gray-200 text-gray-700 text-lg hover:bg-gray-300">+</button>
                </div>
            </div>

            <!-- Seniors -->
            <div class="flex justify-between items-center">
                <span class="text-gray-700 font-medium">Seniors</span>
                <div class="flex items-center space-x-2">
                    <button onclick="decrement('senior')"
                        class="w-8 h-8 rounded-full bg-gray-200 text-gray-700 text-lg hover:bg-gray-300">−</button>
                    <span id="senior-count" class="w-6 text-center">0</span>
                    <button onclick="increment('senior')"
                        class="w-8 h-8 rounded-full bg-gray-200 text-gray-700 text-lg hover:bg-gray-300">+</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Toggle dropdown
    document.getElementById('guestDropdownButton').addEventListener('click', () => {
        document.getElementById('guestDropdownMenu').classList.toggle('hidden');
    });

    // Guest counters
    const counters = {
        adult: 0,
        kids: 0,
        senior: 0
    };

    function increment(type) {
        counters[type]++;
        updateDisplay(type);
    }

    function decrement(type) {
        if (counters[type] > 0) {
            counters[type]--;
            updateDisplay(type);
        }
    }

    function updateDisplay(type) {
        document.getElementById(`${type}-count`).innerText = counters[type];
    }
</script>