<!-- Quick Booking Modal -->
<div id="bookingModal" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-[var(--z-modal)] hidden">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-4xl max-h-[90vh] overflow-y-auto">
        <!-- Modal content will be loaded here -->
    </div>
</div>

<script>
    // Convert PHP facilities data to JavaScript
    const serverFacilities = @json($facilities);
    
    // Sample data for rooms and cottages
    const roomTypes = {
        single: [
            { id: 1, name: "Standard Single", description: "Cozy room with single bed and basic amenities", price: 2000, maxGuests: 1, type: "single" },
            { id: 2, name: "Deluxe Single", description: "Comfortable single room with premium amenities", price: 2500, maxGuests: 1, type: "single" }
        ],
        double: [
            { id: 3, name: "Standard Twin", description: "Two single beds with basic amenities", price: 3000, maxGuests: 2, type: "double" },
            { id: 4, name: "Deluxe Double", description: "Spacious room with queen bed and premium amenities", price: 3500, maxGuests: 2, type: "double" },
            { id: 5, name: "Executive Double", description: "Luxury room with king bed and extra space", price: 4000, maxGuests: 2, type: "double" }
        ],
        suite: [
            { id: 6, name: "Junior Suite", description: "Spacious suite with separate living area", price: 5000, maxGuests: 3, type: "suite" },
            { id: 7, name: "Executive Suite", description: "Luxury suite with premium furnishings", price: 6500, maxGuests: 4, type: "suite" },
            { id: 8, name: "Presidential Suite", description: "Our most luxurious facilities", price: 10000, maxGuests: 4, type: "suite" }
        ]
    };

    const cottages = [
        { id: 101, name: "Garden Cottage", description: "Charming cottage with garden view", price: 5000, maxGuests: 4 },
        { id: 102, name: "Family Cottage", description: "Spacious cottage with two bedrooms", price: 7000, maxGuests: 6 },
        { id: 103, name: "Luxury Cottage", description: "Premium cottage with private hot tub", price: 10000, maxGuests: 4 },
        { id: 104, name: "Beachfront Cottage", description: "Direct beach access with stunning views", price: 12000, maxGuests: 4 }
    ];

    // Use server-provided facilities
    const facilities = serverFacilities.map(facility => ({
        id: facility.id || Math.floor(Math.random() * 1000) + 200,
        name: facility.name,
        category: facility.category,
        price: facility.price,
        description: facility.description || ''
    }));

    // Track selected items
    let selectedRooms = [];
    let selectedCottages = [];
    let selectedFacilities = [];
    let currentFacilitiesType = 'facilities';
    let entranceFees = {
        adults: { count: 0, price: 150 },
        seniors: { count: 0, price: 100 },
        kids: { count: 0, price: 100 }
    };
    let selectedRequests = [];
    let includeBreakfast = false;

    function openBookingModal() {
        showLoadingModal('bookingModal', 'Preparing booking form...');
        
        setTimeout(() => {
            const modal = document.getElementById('bookingModal');
            if (modal) {
                modal.classList.remove('hidden');
                document.body.classList.add('overflow-hidden');
                modal.innerHTML = `
                    <div class="bg-white rounded-xl shadow-2xl w-full max-w-4xl max-h-[90vh] overflow-y-auto">
                        <!-- Modal Header -->
                        <div class="bg-red-600 text-white p-4 rounded-t-xl flex justify-between items-center sticky top-0 z-10">
                            <div class="flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                </svg>
                                <h2 class="text-xl font-bold">Quick Booking</h2>
                            </div>
                            <button onclick="closeModal('bookingModal')" class="text-white hover:text-red-200">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                        
                        <!-- Modal Body -->
                        <div class="p-6">
                            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                                <!-- Left Column - Main Form -->
                                <div class="lg:col-span-2">
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                        <!-- Customer Information -->
                                        <div>
                                            <h4 class="text-md font-medium text-gray-900 mb-3">Customer Information</h4>
                                            <div class="space-y-4">
                                                <div class="grid grid-cols-2 gap-4">
                                                    <div>
                                                        <label for="first-name" class="block text-sm font-medium text-gray-700">First Name *</label>
                                                        <input type="text" id="first-name" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm" value="John" required>
                                                        <div id="first-name-error" class="error-message hidden">First name is required</div>
                                                    </div>
                                                    <div>
                                                        <label for="last-name" class="block text-sm font-medium text-gray-700">Last Name *</label>
                                                        <input type="text" id="last-name" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm" value="Smith" required>
                                                        <div id="last-name-error" class="error-message hidden">Last name is required</div>
                                                    </div>
                                                </div>
                                                <div>
                                                    <label for="customer-email" class="block text-sm font-medium text-gray-700">Email</label>
                                                    <input type="email" id="customer-email" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm" value="john.smith@example.com">
                                                    <div id="email-error" class="error-message hidden">Please enter a valid email address</div>
                                                </div>
                                                <div>
                                                    <label for="customer-phone" class="block text-sm font-medium text-gray-700">Phone *</label>
                                                    <input type="tel" id="customer-phone" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm" value="9123 456 7890" required>
                                                    <div id="phone-error" class="error-message hidden">Phone must start with 9 and be in format 9999 999 9999</div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Booking Dates -->
                                        <div>
                                            <h4 class="text-md font-medium text-gray-900 mb-3" id="dates-title">Booking Dates</h4>
                                            <div class="space-y-4">
                                                <div id="checkin-section">
                                                    <label for="check-in" class="block text-sm font-medium text-gray-700">Check-in *</label>
                                                    <input type="date" id="check-in" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm" required>
                                                    <div id="checkin-error" class="error-message hidden">Check-in date is required</div>
                                                </div>
                                                <div id="checkout-section">
                                                    <label for="check-out" class="block text-sm font-medium text-gray-700">Check-out *</label>
                                                    <input type="date" id="check-out" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm" required>
                                                    <div id="checkout-error" class="error-message hidden">Check-out date is required</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Breakfast Option -->
                                    <div class="mt-6 bg-gray-50 p-4 rounded-lg">
                                        <h4 class="text-md font-medium text-gray-900 mb-3">Breakfast Option</h4>
                                        <div class="flex items-center">
                                            <input type="checkbox" id="include-breakfast" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded" onchange="toggleBreakfast(this.checked)">
                                            <label for="include-breakfast" class="ml-2 block text-sm text-gray-700">
                                                Include breakfast for all rooms (+₱300 per room per night)
                                            </label>
                                        </div>
                                    </div>

                                    <!-- Facilities Type Selection -->
                                    <div class="mt-6">
                                        <h4 class="text-md font-medium text-gray-900 mb-3">Select Facilities Type *</h4>
                                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                            <div id="facilities-option" class="selection-card border rounded-lg p-4 cursor-pointer selected" onclick="selectFacilitiesType('facilities')">
                                                <div class="flex items-center">
                                                    <div class="mr-3">
                                                        <i class="fas fa-hotel text-2xl text-blue-500"></i>
                                                    </div>
                                                    <div>
                                                        <h5 class="font-medium text-gray-900">Facilities</h5>
                                                        <p class="text-sm text-gray-500">Book one or multiple rooms</p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div id="cottage-option" class="selection-card border rounded-lg p-4 cursor-pointer" onclick="selectFacilitiesType('cottage')">
                                                <div class="flex items-center">
                                                    <div class="mr-3">
                                                        <i class="fas fa-home text-2xl text-green-500"></i>
                                                    </div>
                                                    <div>
                                                        <h5 class="font-medium text-gray-900">Cottage</h5>
                                                        <p class="text-sm text-gray-500">Book standalone cottages</p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div id="facility-option" class="selection-card border rounded-lg p-4 cursor-pointer" onclick="selectFacilitiesType('facility')">
                                                <div class="flex items-center">
                                                    <div class="mr-3">
                                                        <i class="fas fa-dumbbell text-2xl text-purple-500"></i>
                                                    </div>
                                                    <div>
                                                        <h5 class="font-medium text-gray-900">Recreational Facilities</h5>
                                                        <p class="text-sm text-gray-500">Book recreational facilities</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div id="facilities-error" class="error-message hidden mt-2">Please select facilities type</div>
                                    </div>

                                    <!-- Room Selection -->
                                    <div id="facilities-selection-section" class="mt-6">
                                        <div class="flex justify-between items-center mb-3">
                                            <h4 class="text-md font-medium text-gray-900">Select Rooms *</h4>
                                            <div class="flex items-center">
                                                <label for="room-filter" class="mr-2 text-sm text-gray-700">Filter by:</label>
                                                <select id="room-filter" class="border border-gray-300 rounded-md shadow-sm py-1 px-2 focus:outline-none focus:ring-blue-500 focus:border-blue-500 text-sm" onchange="filterRooms()">
                                                    <option value="all">All Rooms</option>
                                                    <option value="single">Single</option>
                                                    <option value="double">Double</option>
                                                    <option value="suite">Suite</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4" id="rooms-container">
                                            <!-- Rooms loaded dynamically -->
                                        </div>
                                        <div id="room-error" class="error-message hidden mt-2">Please select at least one room</div>
                                    </div>

                                    <!-- Cottage Selection -->
                                    <div id="cottage-selection-section" class="mt-6 hidden">
                                        <h4 class="text-md font-medium text-gray-900 mb-3">Select Cottages *</h4>
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4" id="cottages-container">
                                            <!-- Cottages loaded dynamically -->
                                        </div>
                                        <div id="cottage-error" class="error-message hidden mt-2">Please select at least one cottage</div>
                                        
                                        <!-- Entrance Fees -->
                                        <div id="entrance-fees-section" class="mt-6 p-4 bg-gray-50 rounded-lg">
                                            <h4 class="text-md font-medium text-gray-900 mb-3">Entrance Fees *</h4>
                                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                                <div class="border rounded-lg p-3">
                                                    <h5 class="font-medium text-gray-900 mb-2">Adults</h5>
                                                    <p class="text-sm text-gray-500 mb-2">₱150 per person</p>
                                                    <div class="flex items-center justify-between">
                                                        <span class="text-sm">Quantity:</span>
                                                        <div class="guest-counter">
                                                            <div class="counter-btn bg-gray-200" onclick="adjustEntranceCount('adults', -1)">-</div>
                                                            <span id="adults-count">0</span>
                                                            <div class="counter-btn bg-gray-200" onclick="adjustEntranceCount('adults', 1)">+</div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="border rounded-lg p-3">
                                                    <h5 class="font-medium text-gray-900 mb-2">Seniors (60+)</h5>
                                                    <p class="text-sm text-gray-500 mb-2">₱100 per person</p>
                                                    <div class="flex items-center justify-between">
                                                        <span class="text-sm">Quantity:</span>
                                                        <div class="guest-counter">
                                                            <div class="counter-btn bg-gray-200" onclick="adjustEntranceCount('seniors', -1)">-</div>
                                                            <span id="seniors-count">0</span>
                                                            <div class="counter-btn bg-gray-200" onclick="adjustEntranceCount('seniors', 1)">+</div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="border rounded-lg p-3">
                                                    <h5 class="font-medium text-gray-900 mb-2">Kids (10 below)</h5>
                                                    <p class="text-sm text-gray-500 mb-2">₱100 per person</p>
                                                    <div class="flex items-center justify-between">
                                                        <span class="text-sm">Quantity:</span>
                                                        <div class="guest-counter">
                                                            <div class="counter-btn bg-gray-200" onclick="adjustEntranceCount('kids', -1)">-</div>
                                                            <span id="kids-count">0</span>
                                                            <div class="counter-btn bg-gray-200" onclick="adjustEntranceCount('kids', 1)">+</div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div id="entrance-error" class="error-message hidden mt-2">Please select at least one guest type</div>
                                        </div>
                                    </div>

                                    <!-- Facility Selection -->
                                    <div id="facility-selection-section" class="mt-6 hidden">
                                        <div class="flex justify-between items-center mb-3">
                                            <h4 class="text-md font-medium text-gray-900">Select Recreational Facilities *</h4>
                                            <div class="flex items-center">
                                                <label for="facility-filter" class="mr-2 text-sm text-gray-700">Filter by:</label>
                                                <select id="facility-filter" class="border border-gray-300 rounded-md shadow-sm py-1 px-2 focus:outline-none focus:ring-blue-500 focus:border-blue-500 text-sm" onchange="filterFacilities()">
                                                    <option value="all">All Facilities</option>
                                                    ${[...new Set(facilities.map(f => f.category))].map(category => `
                                                        <option value="${category}">${category}</option>
                                                    `).join('')}
                                                </select>
                                            </div>
                                        </div>
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4" id="facilities-container">
                                            <!-- Facilities loaded dynamically -->
                                        </div>
                                        <div id="facility-error" class="error-message hidden mt-2">Please select at least one facility</div>
                                    </div>

                                    <!-- Special Requests -->
                                    <div class="mt-6">
                                        <h4 class="text-md font-medium text-gray-900 mb-3">Special Requests</h4>
                                        
                                        <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-4">
                                            <div class="request-option border rounded-lg p-3 text-center" onclick="selectRequestOption(this, 'Early check-in')">
                                                <i class="fas fa-clock text-blue-500 mb-1"></i>
                                                <p class="text-sm">Early Check-in</p>
                                            </div>
                                            <div class="request-option border rounded-lg p-3 text-center" onclick="selectRequestOption(this, 'Late check-out')">
                                                <i class="fas fa-clock text-blue-500 mb-1"></i>
                                                <p class="text-sm">Late Check-out</p>
                                            </div>
                                            <div class="request-option border rounded-lg p-3 text-center" onclick="selectRequestOption(this, 'High floor')">
                                                <i class="fas fa-arrow-up text-blue-500 mb-1"></i>
                                                <p class="text-sm">High Floor</p>
                                            </div>
                                            <div class="request-option border rounded-lg p-3 text-center" onclick="selectRequestOption(this, 'Quiet room')">
                                                <i class="fas fa-volume-mute text-blue-500 mb-1"></i>
                                                <p class="text-sm">Quiet Room</p>
                                            </div>
                                        </div>
                                        
                                        <label for="special-requests" class="block text-sm font-medium text-gray-700">Additional Requests</label>
                                        <textarea id="special-requests" rows="3" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm" placeholder="Please let us know if you have any special requirements...">Please provide a quiet room if possible</textarea>
                                    </div>
                                </div>

                                <!-- Right Column - Summary Card -->
                                <div class="lg:col-span-1">
                                    <div class="summary-card bg-gray-50 rounded-lg p-4 border border-gray-200">
                                        <h4 class="text-md font-medium text-gray-900 mb-3">Booking Summary</h4>
                                        
                                        <div class="mb-4">
                                            <h5 class="text-sm font-medium text-gray-700">Dates</h5>
                                            <p id="summary-dates" class="text-sm text-gray-600">Loading dates...</p>
                                        </div>
                                        
                                        <div class="mb-4">
                                            <h5 class="text-sm font-medium text-gray-700">Guest Information</h5>
                                            <p id="summary-guest" class="text-sm text-gray-600">John Smith</p>
                                            <p id="summary-contact" class="text-sm text-gray-600">john.smith@example.com<br>9123 456 7890</p>
                                        </div>
                                        
                                        <div class="border-t border-gray-200 pt-3 mb-3">
                                            <h5 class="text-sm font-medium text-gray-700 mb-2">Selected Items</h5>
                                            <div id="selected-items-list" class="text-sm text-gray-700 space-y-2">
                                                No items selected yet
                                            </div>
                                        </div>
                                        
                                        <div class="border-t border-gray-200 pt-3">
                                            <div class="flex justify-between items-center">
                                                <span class="text-sm font-medium text-gray-700">Subtotal:</span>
                                                <span id="subtotal-price" class="text-sm">₱0</span>
                                            </div>
                                            <div class="flex justify-between items-center mt-1">
                                                <span class="text-sm font-medium text-gray-700">Tax (10%):</span>
                                                <span id="tax-amount" class="text-sm">₱0</span>
                                            </div>
                                            <div class="flex justify-between items-center mt-2 pt-2 border-t border-gray-200">
                                                <span class="font-medium text-gray-900">Total:</span>
                                                <span id="total-price" class="font-semibold">₱0</span>
                                            </div>
                                        </div>
                                        
                                        <button type="button" id="create-booking-btn" class="mt-4 w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-green-600 text-base font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 sm:text-sm">
                                            Confirm Booking
                                        </button>
                                        
                                        <div class="mt-3 text-center">
                                            <p class="text-xs text-gray-500">By confirming, you agree to our terms and conditions</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
                
                // Initialize the modal
                loadRooms();
                loadCottages();
                loadFacilities();
                selectFacilitiesType('facilities');
                setupPhoneInput();
                setupValidation();
                setupDatePickers();
                updateGuestSummary();
            }
        }, 500);
    }

    function closeBookingModal() {
        closeModal('bookingModal');
    }

    function toggleBreakfast(checked) {
        includeBreakfast = checked;
        updateSelectedItemsSummary();
    }

    function loadRooms() {
        const container = document.getElementById('rooms-container');
        container.innerHTML = '';
        
        const allRooms = [...roomTypes.single, ...roomTypes.double, ...roomTypes.suite];
        
        allRooms.forEach(room => {
            const roomElement = document.createElement('div');
            roomElement.className = 'selection-card border rounded-lg p-4 cursor-pointer';
            roomElement.setAttribute('data-id', room.id);
            roomElement.setAttribute('data-type', room.type);
            roomElement.onclick = () => toggleSelection(roomElement, 'room');
            
            roomElement.innerHTML = `
                <div class="flex justify-between items-start">
                    <div>
                        <h5 class="font-medium text-gray-900">${room.name}</h5>
                        <p class="text-sm text-gray-500">Pax: ${room.maxGuests}</p>
                    </div>
                    <span class="text-lg font-semibold">₱${room.price.toLocaleString()}</span>
                </div>
                <div class="mt-3">
                    <p class="text-sm text-gray-500">${room.description}</p>
                </div>
            `;
            
            container.appendChild(roomElement);
        });
    }

    function loadCottages() {
        const container = document.getElementById('cottages-container');
        container.innerHTML = '';
        
        cottages.forEach(cottage => {
            const cottageElement = document.createElement('div');
            cottageElement.className = 'selection-card border rounded-lg p-4 cursor-pointer';
            cottageElement.setAttribute('data-id', cottage.id);
            cottageElement.onclick = () => toggleSelection(cottageElement, 'cottage');
            
            cottageElement.innerHTML = `
                <div class="flex justify-between items-start">
                    <div>
                        <h5 class="font-medium text-gray-900">${cottage.name}</h5>
                        <p class="text-sm text-gray-500">Pax: ${cottage.maxGuests}</p>
                    </div>
                    <span class="text-lg font-semibold">₱${cottage.price.toLocaleString()}</span>
                </div>
                <div class="mt-3">
                    <p class="text-sm text-gray-500">${cottage.description}</p>
                </div>
            `;
            
            container.appendChild(cottageElement);
        });
    }

    function loadFacilities() {
        const container = document.getElementById('facilities-container');
        container.innerHTML = '';
        
        facilities.forEach(facility => {
            const facilityElement = document.createElement('div');
            facilityElement.className = 'selection-card border rounded-lg p-4 cursor-pointer';
            facilityElement.setAttribute('data-id', facility.id);
            facilityElement.setAttribute('data-category', facility.category);
            facilityElement.onclick = () => toggleSelection(facilityElement, 'facility');
            
            facilityElement.innerHTML = `
                <div class="flex justify-between items-start">
                    <div>
                        <h5 class="font-medium text-gray-900">${facility.name}</h5>
                        <p class="text-sm text-gray-500">Category: ${facility.category}</p>
                    </div>
                    <span class="text-lg font-semibold">₱${facility.price.toLocaleString()}</span>
                </div>
                ${facility.description ? `<div class="mt-3"><p class="text-sm text-gray-500">${facility.description}</p></div>` : ''}
            `;
            
            container.appendChild(facilityElement);
        });
    }

    function filterRooms() {
        const filterValue = document.getElementById('room-filter').value;
        const allRooms = document.querySelectorAll('#rooms-container .selection-card');
        
        allRooms.forEach(room => {
            if (filterValue === 'all' || room.getAttribute('data-type') === filterValue) {
                room.style.display = 'block';
            } else {
                room.style.display = 'none';
            }
        });
    }

    function filterFacilities() {
        const filterValue = document.getElementById('facility-filter').value;
        const allFacilities = document.querySelectorAll('#facilities-container .selection-card');
        
        allFacilities.forEach(facility => {
            if (filterValue === 'all' || facility.getAttribute('data-category') === filterValue) {
                facility.style.display = 'block';
            } else {
                facility.style.display = 'none';
            }
        });
    }

    function selectFacilitiesType(type) {
        currentFacilitiesType = type;
        
        // Update UI for facilities type selection
        document.getElementById('facilities-option').classList.remove('selected');
        document.getElementById('cottage-option').classList.remove('selected');
        document.getElementById('facility-option').classList.remove('selected');
        
        if (type === 'facilities') {
            document.getElementById('facilities-option').classList.add('selected');
            document.getElementById('facilities-selection-section').classList.remove('hidden');
            document.getElementById('cottage-selection-section').classList.add('hidden');
            document.getElementById('facility-selection-section').classList.add('hidden');
            document.getElementById('entrance-fees-section').classList.add('hidden');
            document.getElementById('dates-title').textContent = 'Booking Dates';
            document.getElementById('checkout-section').classList.remove('hidden');
        } else if (type === 'cottage') {
            document.getElementById('cottage-option').classList.add('selected');
            document.getElementById('cottage-selection-section').classList.remove('hidden');
            document.getElementById('facilities-selection-section').classList.add('hidden');
            document.getElementById('facility-selection-section').classList.add('hidden');
            document.getElementById('entrance-fees-section').classList.remove('hidden');
            document.getElementById('dates-title').textContent = 'Booking Date';
            document.getElementById('checkout-section').classList.add('hidden');
            
            // For cottages, set check-out to same as check-in
            const checkin = document.getElementById('check-in');
            const checkout = document.getElementById('check-out');
            checkout.value = checkin.value;
        } else if (type === 'facility') {
            document.getElementById('facility-option').classList.add('selected');
            document.getElementById('facility-selection-section').classList.remove('hidden');
            document.getElementById('facilities-selection-section').classList.add('hidden');
            document.getElementById('cottage-selection-section').classList.add('hidden');
            document.getElementById('entrance-fees-section').classList.add('hidden');
            document.getElementById('dates-title').textContent = 'Booking Date';
            document.getElementById('checkout-section').classList.add('hidden');
            
            // For facilities, set check-out to same as check-in
            const checkin = document.getElementById('check-in');
            const checkout = document.getElementById('check-out');
            checkout.value = checkin.value;
        }
        
        updateDatesSummary();
        updateSelectedItemsSummary();
    }

    function toggleSelection(element, type) {
        element.classList.toggle('selected');
        
        const id = element.getAttribute('data-id');
        const title = element.querySelector('h5').textContent;
        const priceText = element.querySelector('span').textContent;
        const price = parseInt(priceText.replace(/[^\d]/g, ''));
        
        if (type === 'room') {
            if (element.classList.contains('selected')) {
                selectedRooms.push({ id, title, price });
            } else {
                selectedRooms = selectedRooms.filter(room => room.id !== id);
            }
        } else if (type === 'cottage') {
            if (element.classList.contains('selected')) {
                selectedCottages.push({ id, title, price });
            } else {
                selectedCottages = selectedCottages.filter(cottage => cottage.id !== id);
            }
        } else if (type === 'facility') {
            if (element.classList.contains('selected')) {
                selectedFacilities.push({ id, title, price });
            } else {
                selectedFacilities = selectedFacilities.filter(facility => facility.id !== id);
            }
        }
        
        updateSelectedItemsSummary();
    }

    function adjustEntranceCount(type, change) {
        const newCount = entranceFees[type].count + change;
        if (newCount >= 0) {
            entranceFees[type].count = newCount;
            document.getElementById(`${type}-count`).textContent = newCount;
            updateSelectedItemsSummary();
        }
    }

    function updateSelectedItemsSummary() {
        const selectedItemsList = document.getElementById('selected-items-list');
        const subtotalPriceElement = document.getElementById('subtotal-price');
        const taxAmountElement = document.getElementById('tax-amount');
        const totalPriceElement = document.getElementById('total-price');
        
        let html = '';
        let subtotal = 0;
        
        if (currentFacilitiesType === 'facilities') {
            if (selectedRooms.length === 0) {
                html = '<p class="text-gray-500 italic">No rooms selected yet</p>';
            } else {
                html = '<ul class="space-y-2">';
                selectedRooms.forEach(room => {
                    html += `
                        <li class="flex justify-between">
                            <span>${room.title}</span>
                            <span class="font-medium">₱${room.price.toLocaleString()}</span>
                        </li>`;
                    subtotal += room.price;
                });

                if (includeBreakfast && selectedRooms.length > 0) {
                    const breakfastPrice = 300 * selectedRooms.length;
                    html += `
                        <li class="flex justify-between">
                            <span>Breakfast for all rooms</span>
                            <span class="font-medium">₱${breakfastPrice.toLocaleString()}</span>
                        </li>`;
                    subtotal += breakfastPrice;
                }
                html += '</ul>';
            }
        } else if (currentFacilitiesType === 'cottage') {
            if (selectedCottages.length === 0) {
                html = '<p class="text-gray-500 italic">No cottages selected yet</p>';
            } else {
                html = '<ul class="space-y-2">';
                selectedCottages.forEach(cottage => {
                    html += `
                        <li class="flex justify-between">
                            <span>${cottage.title}</span>
                            <span class="font-medium">₱${cottage.price.toLocaleString()}</span>
                        </li>`;
                    subtotal += cottage.price;
                });
                
                if (entranceFees.adults.count > 0 || entranceFees.seniors.count > 0 || entranceFees.kids.count > 0) {
                    html += '<li class="pt-2 mt-2 border-t border-gray-200 font-medium">Entrance Fees</li>';
                    
                    if (entranceFees.adults.count > 0) {
                        const fee = entranceFees.adults.count * entranceFees.adults.price;
                        html += `
                            <li class="flex justify-between">
                                <span>${entranceFees.adults.count} Adult(s)</span>
                                <span>₱${fee.toLocaleString()}</span>
                            </li>`;
                        subtotal += fee;
                    }
                    if (entranceFees.seniors.count > 0) {
                        const fee = entranceFees.seniors.count * entranceFees.seniors.price;
                        html += `
                            <li class="flex justify-between">
                                <span>${entranceFees.seniors.count} Senior(s)</span>
                                <span>₱${fee.toLocaleString()}</span>
                            </li>`;
                        subtotal += fee;
                    }
                    if (entranceFees.kids.count > 0) {
                        const fee = entranceFees.kids.count * entranceFees.kids.price;
                        html += `
                            <li class="flex justify-between">
                                <span>${entranceFees.kids.count} Kid(s)</span>
                                <span>₱${fee.toLocaleString()}</span>
                            </li>`;
                        subtotal += fee;
                    }
                }
                html += '</ul>';
            }
        } else if (currentFacilitiesType === 'facility') {
            if (selectedFacilities.length === 0) {
                html = '<p class="text-gray-500 italic">No facilities selected yet</p>';
            } else {
                html = '<ul class="space-y-2">';
                selectedFacilities.forEach(facility => {
                    html += `
                        <li class="flex justify-between">
                            <span>${facility.title}</span>
                            <span class="font-medium">₱${facility.price.toLocaleString()}</span>
                        </li>`;
                    subtotal += facility.price;
                });
                html += '</ul>';
            }
        }
        
        const tax = subtotal * 0.1;
        const total = subtotal + tax;
        
        selectedItemsList.innerHTML = html;
        subtotalPriceElement.textContent = `₱${subtotal.toLocaleString()}`;
        taxAmountElement.textContent = `₱${tax.toLocaleString()}`;
        totalPriceElement.textContent = `₱${total.toLocaleString()}`;
    }

    function updateGuestSummary() {
        const firstName = document.getElementById('first-name').value;
        const lastName = document.getElementById('last-name').value;
        const email = document.getElementById('customer-email').value;
        const phone = document.getElementById('customer-phone').value;
        
        document.getElementById('summary-guest').textContent = `${firstName} ${lastName}`;
        document.getElementById('summary-contact').innerHTML = `${email}<br>${phone}`;
    }

    function updateDatesSummary() {
        const checkin = document.getElementById('check-in').value;
        const checkout = document.getElementById('check-out').value;
        
        if (checkin) {
            const formattedCheckin = formatDate(checkin);
            
            if (currentFacilitiesType === 'facilities' && checkout) {
                const formattedCheckout = formatDate(checkout);
                document.getElementById('summary-dates').textContent = `${formattedCheckin} to ${formattedCheckout}`;
            } else {
                document.getElementById('summary-dates').textContent = `${formattedCheckin}`;
            }
        }
    }

    function formatDate(dateString) {
        const date = new Date(dateString);
        const day = date.getDate().toString().padStart(2, '0');
        const month = (date.getMonth() + 1).toString().padStart(2, '0');
        const year = date.getFullYear();
        return `${day}/${month}/${year}`;
    }

    function selectRequestOption(element, request) {
        element.classList.toggle('selected');
        
        if (element.classList.contains('selected')) {
            if (!selectedRequests.includes(request)) {
                selectedRequests.push(request);
            }
        } else {
            selectedRequests = selectedRequests.filter(r => r !== request);
        }
        
        updateSpecialRequestsText();
    }

    function updateSpecialRequestsText() {
        const textarea = document.getElementById('special-requests');
        let currentText = textarea.value.trim();
        
        selectedRequests.forEach(request => {
            currentText = currentText.replace(new RegExp(request, 'i'), '').trim();
        });
        
        let newText = selectedRequests.join(', ');
        if (currentText && !selectedRequests.includes(currentText)) {
            newText += (newText ? ', ' : '') + currentText;
        }
        
        textarea.value = newText;
    }

    function setupPhoneInput() {
        const phoneInput = document.getElementById('customer-phone');
        
        phoneInput.addEventListener('input', function(e) {
            let value = this.value.replace(/\D/g, '');
            
            if (value.length > 0 && value[0] !== '9') {
                value = '9' + value.substring(1);
            }
            
            if (value.length > 4) {
                value = value.substring(0, 4) + ' ' + value.substring(4);
            }
            if (value.length > 8) {
                value = value.substring(0, 8) + ' ' + value.substring(8);
            }
            if (value.length > 12) {
                value = value.substring(0, 12);
            }
            
            this.value = value;
            updateGuestSummary();
        });
    }

    function setupValidation() {
        const createBtn = document.getElementById('create-booking-btn');
        
        createBtn.addEventListener('click', function() {
            if (validateForm()) {
                alert('Booking created successfully!');
                closeBookingModal();
            }
        });
    }

    function validateForm() {
        let isValid = true;
        
        // Validate name fields
        const firstName = document.getElementById('first-name');
        const lastName = document.getElementById('last-name');
        if (!firstName.value.trim()) {
            showError(firstName, 'first-name-error');
            isValid = false;
        } else {
            hideError(firstName, 'first-name-error');
        }
        
        if (!lastName.value.trim()) {
            showError(lastName, 'last-name-error');
            isValid = false;
        } else {
            hideError(lastName, 'last-name-error');
        }
        
        // Validate email if provided
        const email = document.getElementById('customer-email');
        if (email.value && !validateEmail(email.value)) {
            showError(email, 'email-error');
            isValid = false;
        } else {
            hideError(email, 'email-error');
        }
        
        // Validate phone
        const phone = document.getElementById('customer-phone');
        const phoneRegex = /^9\d{3} \d{3} \d{4}$/;
        if (!phoneRegex.test(phone.value)) {
            showError(phone, 'phone-error');
            isValid = false;
        } else {
            hideError(phone, 'phone-error');
        }
        
        // Validate dates
        const checkin = document.getElementById('check-in');
        const checkout = document.getElementById('check-out');
        if (!checkin.value) {
            showError(checkin, 'checkin-error');
            isValid = false;
        } else {
            hideError(checkin, 'checkin-error');
        }
        
        if (currentFacilitiesType === 'facilities' && !checkout.value) {
            showError(checkout, 'checkout-error');
            isValid = false;
        } else {
            hideError(checkout, 'checkout-error');
        }
        
        // Validate facilities selection
        if (currentFacilitiesType === 'facilities' && selectedRooms.length === 0) {
            document.getElementById('room-error').classList.remove('hidden');
            isValid = false;
        } else {
            document.getElementById('room-error').classList.add('hidden');
        }
        
        if (currentFacilitiesType === 'cottage' && selectedCottages.length === 0) {
            document.getElementById('cottage-error').classList.remove('hidden');
            isValid = false;
        } else {
            document.getElementById('cottage-error').classList.add('hidden');
        }
        
        if (currentFacilitiesType === 'facility' && selectedFacilities.length === 0) {
            document.getElementById('facility-error').classList.remove('hidden');
            isValid = false;
        } else {
            document.getElementById('facility-error').classList.add('hidden');
        }
        
        // Validate entrance fees for cottages
        if (currentFacilitiesType === 'cottage' && 
            entranceFees.adults.count === 0 && 
            entranceFees.seniors.count === 0 && 
            entranceFees.kids.count === 0) {
            document.getElementById('entrance-error').classList.remove('hidden');
            isValid = false;
        } else {
            document.getElementById('entrance-error').classList.add('hidden');
        }
        
        return isValid;
    }

    function validateEmail(email) {
        const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return re.test(email);
    }

    function showError(field, errorId) {
        field.classList.add('error');
        document.getElementById(errorId).classList.remove('hidden');
    }

    function hideError(field, errorId) {
        field.classList.remove('error');
        document.getElementById(errorId).classList.add('hidden');
    }

    function setupDatePickers() {
        const checkinInput = document.getElementById('check-in');
        const checkoutInput = document.getElementById('check-out');
        
        // Set minimum date to today
        const today = new Date().toISOString().split('T')[0];
        checkinInput.min = today;
        checkoutInput.min = today;
        
        // Set initial check-in to today
        checkinInput.value = today;
        
        // Set initial check-out to tomorrow
        const tomorrow = new Date();
        tomorrow.setDate(tomorrow.getDate() + 1);
        checkoutInput.value = tomorrow.toISOString().split('T')[0];
        
        // Update check-out min date when check-in changes
        checkinInput.addEventListener('change', function() {
            if (this.value) {
                checkoutInput.min = this.value;
                
                if (checkoutInput.value < this.value) {
                    const nextDay = new Date(this.value);
                    nextDay.setDate(nextDay.getDate() + 1);
                    checkoutInput.value = nextDay.toISOString().split('T')[0];
                }
                
                updateDatesSummary();
            }
        });
        
        checkoutInput.addEventListener('focus', function() {
            if (currentFacilitiesType !== 'facilities') {
                this.value = checkinInput.value;
                updateDatesSummary();
            }
        });
        
        updateDatesSummary();
    }

    // Helper functions
    function showLoadingModal(modalId, message) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.innerHTML = `
                <div class="bg-white rounded-xl p-8 max-w-md mx-auto text-center">
                    <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-red-600 mx-auto mb-4"></div>
                    <p class="text-gray-700">${message}</p>
                </div>
            `;
            modal.classList.remove('hidden');
        }
    }

    function closeModal(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
        }
    }
</script>

<!-- Add this CSS in your stylesheet -->
<style>
    .selection-card {
        transition: all 0.2s ease;
    }
    .selection-card.selected {
        border-color: #4f46e5;
        background-color: #f5f3ff;
    }
    .selection-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    }
    .error {
        border-color: #ef4444 !important;
    }
    .error-message {
        color: #ef4444;
        font-size: 0.875rem;
        margin-top: 0.25rem;
    }
    .guest-counter {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    .counter-btn {
        width: 1.5rem;
        height: 1.5rem;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 0.25rem;
        cursor: pointer;
        user-select: none;
    }
    .request-option {
        transition: all 0.2s ease;
    }
    .request-option.selected {
        border-color: #3b82f6;
        background-color: #eff6ff;
    }
    .request-option:hover {
        transform: translateY(-2px);
    }
</style>