<!-- Modal Backdrop -->
<div id="modalBackdrop" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center p-4">
    <!-- Modal Container -->
    <div class="bg-white rounded-lg shadow-xl w-full max-w-4xl max-h-[90vh] flex flex-col">
        <!-- Modal Header -->
        <div class="flex justify-between items-center p-4 border-b">
            <h3 class="text-xl font-semibold">Image Gallery</h3>
            <button id="closeModal" class="text-gray-500 hover:text-gray-700">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <!-- Gallery Content - Scrollable Area -->
        <div class="overflow-y-auto p-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                <!-- Image Items -->
                <div class="bg-gray-200 rounded-lg overflow-hidden">
                    <img src="https://source.unsplash.com/random/600x400?nature=1" alt="Nature 1"
                        class="w-full h-48 object-cover">
                    <div class="p-2">
                        <p class="text-sm text-gray-700">Beautiful nature landscape</p>
                    </div>
                </div>
                <div class="bg-gray-200 rounded-lg overflow-hidden">
                    <img src="https://source.unsplash.com/random/600x400?nature=2" alt="Nature 2"
                        class="w-full h-48 object-cover">
                    <div class="p-2">
                        <p class="text-sm text-gray-700">Mountain view at sunset</p>
                    </div>
                </div>
                <div class="bg-gray-200 rounded-lg overflow-hidden">
                    <img src="https://source.unsplash.com/random/600x400?nature=3" alt="Nature 3"
                        class="w-full h-48 object-cover">
                    <div class="p-2">
                        <p class="text-sm text-gray-700">Forest pathway</p>
                    </div>
                </div>
                <div class="bg-gray-200 rounded-lg overflow-hidden">
                    <img src="https://source.unsplash.com/random/600x400?nature=4" alt="Nature 4"
                        class="w-full h-48 object-cover">
                    <div class="p-2">
                        <p class="text-sm text-gray-700">Ocean waves</p>
                    </div>
                </div>
                <div class="bg-gray-200 rounded-lg overflow-hidden">
                    <img src="https://source.unsplash.com/random/600x400?nature=5" alt="Nature 5"
                        class="w-full h-48 object-cover">
                    <div class="p-2">
                        <p class="text-sm text-gray-700">Desert dunes</p>
                    </div>
                </div>
                <div class="bg-gray-200 rounded-lg overflow-hidden">
                    <img src="https://source.unsplash.com/random/600x400?nature=6" alt="Nature 6"
                        class="w-full h-48 object-cover">
                    <div class="p-2">
                        <p class="text-sm text-gray-700">Waterfall in the jungle</p>
                    </div>
                </div>
                <div class="bg-gray-200 rounded-lg overflow-hidden">
                    <img src="https://source.unsplash.com/random/600x400?nature=7" alt="Nature 7"
                        class="w-full h-48 object-cover">
                    <div class="p-2">
                        <p class="text-sm text-gray-700">Autumn leaves</p>
                    </div>
                </div>
                <div class="bg-gray-200 rounded-lg overflow-hidden">
                    <img src="https://source.unsplash.com/random/600x400?nature=8" alt="Nature 8"
                        class="w-full h-48 object-cover">
                    <div class="p-2">
                        <p class="text-sm text-gray-700">Snowy peaks</p>
                    </div>
                </div>
                <div class="bg-gray-200 rounded-lg overflow-hidden">
                    <img src="https://source.unsplash.com/random/600x400?nature=9" alt="Nature 9"
                        class="w-full h-48 object-cover">
                    <div class="p-2">
                        <p class="text-sm text-gray-700">Wildlife in habitat</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Footer -->
        <div class="p-4 border-t flex justify-end">
            <button id="closeModal2" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded">
                Close
            </button>
        </div>
    </div>
</div>