<!DOCTYPE html>
<html lang="en">

<head>
     <meta charset="UTF-8">
     <meta name="viewport" content="width=device-width, initial-scale=1.0">
     <title>Pool and Park | Booking</title>
     <script src="https://cdn.tailwindcss.com"></script>
     <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
     <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
     <meta name="csrf-token" content="{{ csrf_token() }}">
     <script>
          tailwind.config = {
               theme: {
                    extend: {
                         colors: {
                              primary: '#DC2626',
                              secondary: '#B91C1C',
                              dark: '#1F2937',
                              light: '#F9FAFB',
                              accent: '#EA580C',
                         },
                         fontFamily: {
                              sans: ['Inter', 'sans-serif'],
                         },
                    }
               }
          }
     </script>
     <style>
          @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');

          body {
               font-family: 'Inter', sans-serif;
               background-color: #f5f7fa;
          }

          .room-card:hover {
               transform: translateY(-5px);
               box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
          }

          .price-tag {
               position: relative;
               overflow: hidden;
          }

          .price-tag::before {
               content: '';
               position: absolute;
               top: 0;
               left: 0;
               width: 100%;
               height: 100%;
               background: linear-gradient(135deg, rgba(220, 38, 38, 0.1) 0%, rgba(185, 28, 28, 0.1) 100%);
               z-index: -1;
          }

          /* Style for disabled dates */
          input[type="date"]:disabled {
               background-color: #f3f4f6;
               color: #9ca3af;
               cursor: not-allowed;
          }

          .flatpickr-day.disabled, .flatpickr-day.disabled:hover {
               color: #ccc;
               background: transparent;
               cursor: not-allowed;
               border-color: transparent;
          }

          .flatpickr-day.booked, .flatpickr-day.booked:hover {
               color: #fff;
               background: #dc2626;
               border-color: #dc2626;
               cursor: not-allowed;
          }
          
          .guest-type-control {
               display: flex;
               align-items: center;
               justify-content: space-between;
               margin-bottom: 8px;
          }
          
          .guest-type-control input {
               width: 60px;
               text-align: center;
          }
     </style>
</head>

<body class="bg-gradient-to-br from-red-50 to-red-50 min-h-screen">
     <div class="container mx-auto px-4 py-8 max-w-6xl">
          <!-- Progress Steps -->
          <div class="flex justify-between items-center mb-10 relative">
               <div class="absolute top-1/2 left-0 right-0 h-1 bg-gray-200 -z-10"></div>
               <div class="absolute top-1/2 left-0 w-1/2 h-1 bg-primary -z-10"></div>

               <div class="flex flex-col items-center">
                    <div
                         class="w-10 h-10 rounded-full bg-primary text-white flex items-center justify-center font-bold mb-2">
                         1</div>
                    <span class="text-sm font-medium text-primary">Select Facilities</span>
               </div>
               <div class="flex flex-col items-center">
                    <div
                         class="w-10 h-10 rounded-full border-2 border-gray-300 bg-white text-gray-400 flex items-center justify-center font-bold mb-2">
                         2</div>
                    <span class="text-sm font-medium text-gray-500">Payment</span>
               </div>
               <div class="flex flex-col items-center">
                    <div
                         class="w-10 h-10 rounded-full border-2 border-gray-300 bg-white text-gray-400 flex items-center justify-center font-bold mb-2">
                         3</div>
                    <span class="text-sm font-medium text-gray-500">Completed</span>
               </div>
          </div>

          <!-- Main Content -->
          <div class="flex flex-col lg:flex-row gap-6">
               <!-- Left Column -->
               <div class="lg:w-2/3">
                    <!-- Customer Information Card -->
                    <div class="bg-white rounded-xl shadow-sm p-6 mb-6 border border-gray-100">
                         <h2 class="text-xl font-bold text-dark mb-4 flex items-center">
                              <i class="fas fa-user-circle text-primary mr-3"></i>
                              Your Information
                         </h2>
                         <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                              <div>
                                   <label for="firstname" class="block text-sm font-medium text-gray-700 mb-2">First
                                        Name
                                        </label>
                                   <input type="text" id="firstname"
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent"
                                        required>
                              </div>
                              <div>
                                   <label for="lastname" class="block text-sm font-medium text-gray-700 mb-2">Last Name
                                        </label>
                                   <input type="text" id="lastname"
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent"
                                        required>
                              </div>
                              <div>
                                   <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email
                                        </label>
                                   <input type="email" id="email"
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent"
                                        required>
                              </div>
                              <div>
                                   <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">Phone
                                        Number </label>
                                   <input type="tel" id="phone"
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent"
                                        required>
                              </div>
                         </div>
                    </div>

                    <div class="space-y-4">
                         <h3 class="text-lg font-semibold text-dark mb-2">Available Facilities</h3>

                         <div id="facilities-container">
                              <!-- Sample Pool Facility -->
                              <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100 room-card transition duration-300"
                                   data-price="1500" data-room-id="facility-1" data-max-pax="30"
                                   data-images='["/imgs/pool/2.jpg"]' 
                                   data-booked-dates='[]'>

                                   <div class="flex flex-col md:flex-row gap-6">
                                        <!-- Image Section -->
                                        <div class="md:w-1/4">
                                             <img src="/imgs/pool/2.jpg" 
                                                  alt="Luxury Pool"
                                                  class="w-full h-48 object-cover rounded-lg"
                                                  onerror="this.src='https://via.placeholder.com/500x300?text=Image+Not+Found'">
                                        </div>

                                        <!-- Facility Details -->
                                        <div class="md:w-2/4">
                                             <div class="flex justify-between items-start">
                                                  <h3 class="font-bold text-lg text-dark">Pool Access
                                                  </h3>
                                             </div>

                                             <p class="text-gray-600 text-sm mt-1">Enjoy our premium swimming pool with stunning views and comfortable lounge areas.</p>

                                        </div>

                                        <!-- Price and Add to Cart -->
                                        <div class="md:w-1/4 flex flex-col items-end">
                                             <!-- Guest Type Pricing -->
                                             <div class="w-full mt-2">
                                                  <label class="block text-sm text-gray-700 mb-1">Guest Types:</label>
                                                  <div class="space-y-2" id="facility-1-guest-types">
                                                       <div class="guest-type-control">
                                                            <span class="text-sm">Adult (₱500.00)</span>
                                                            <div class="flex items-center">
                                                                 <input type="number" min="0" max="10" value="0" 
                                                                      class="mx-2 px-2 py-1 border border-gray-300 rounded-lg text-center guest-count-input" 
                                                                      data-type="Adult" data-rate="500" data-facility="facility-1">
                                                            </div>
                                                       </div>
                                                       <div class="guest-type-control">
                                                            <span class="text-sm">Child (₱300.00)</span>
                                                            <div class="flex items-center">
                                                                 <input type="number" min="0" max="10" value="0" 
                                                                      class="mx-2 px-2 py-1 border border-gray-300 rounded-lg text-center guest-count-input" 
                                                                      data-type="Child" data-rate="300" data-facility="facility-1">
                                                            </div>
                                                       </div>
                                                       <div class="guest-type-control">
                                                            <span class="text-sm">Senior (₱400.00)</span>
                                                            <div class="flex items-center">
                                                                 <input type="number" min="0" max="10" value="0" 
                                                                      class="mx-2 px-2 py-1 border border-gray-300 rounded-lg text-center guest-count-input" 
                                                                      data-type="Senior" data-rate="400" data-facility="facility-1">
                                                            </div>
                                                       </div>
                                                  </div>
                                             </div>
                                             
                                             <div class="flex items-center mt-2">
                                                  <label class="text-sm text-gray-700 mr-2">Total Persons:</label>
                                                  <span class="text-sm font-medium" id="facility-1-total-persons">0</span>
                                             </div>
                                             <button type="button"
                                                  class="mt-4 bg-primary hover:bg-primary/90 text-white text-sm font-medium py-2 px-4 rounded-lg transition add-to-cart-btn"
                                                  data-room="facility-1">
                                                  Add to Cart
                                             </button>
                                        </div>
                                   </div>
                              </div>

                              <!-- Sample Park Facility -->
                              <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100 room-card transition duration-300 mt-6"
                                   data-price="800" data-room-id="facility-2" data-max-pax="45"
                                   data-images='["/imgs/wwwes.jpg"]' 
                                   data-booked-dates='[]'>

                                   <div class="flex flex-col md:flex-row gap-6">
                                        <!-- Image Section -->
                                        <div class="md:w-1/4">
                                             <img src="/imgs/wwwes.jpg" 
                                                  alt="Premium Park"
                                                  class="w-full h-48 object-cover rounded-lg"
                                                  onerror="this.src='https://via.placeholder.com/500x300?text=Image+Not+Found'">
                                        </div>

                                        <!-- Facility Details -->
                                        <div class="md:w-2/4">
                                             <div class="flex justify-between items-start">
                                                  <h3 class="font-bold text-lg text-dark">Wonders of the World Themed
                                                  </h3>
                                             </div>

                                             <p class="text-gray-600 text-sm mt-1">Beautiful landscaped park with walking trails, picnic areas, and scenic views.</p>
                                        </div>

                                        <!-- Price and Add to Cart -->
                                        <div class="md:w-1/4 flex flex-col items-end">
                                             <!-- Guest Type Pricing -->
                                             <div class="w-full mt-2">
                                                  <label class="block text-sm text-gray-700 mb-1">Guest Types:</label>
                                                  <div class="space-y-2" id="facility-2-guest-types">
                                                       <div class="guest-type-control">
                                                            <span class="text-sm">Adult (₱300.00)</span>
                                                            <div class="flex items-center">
                                                                 <input type="number" min="0" max="15" value="0" 
                                                                      class="mx-2 px-2 py-1 border border-gray-300 rounded-lg text-center guest-count-input" 
                                                                      data-type="Adult" data-rate="300" data-facility="facility-2">
                                                            </div>
                                                       </div>
                                                       <div class="guest-type-control">
                                                            <span class="text-sm">Child (₱150.00)</span>
                                                            <div class="flex items-center">
                                                                 <input type="number" min="0" max="15" value="0" 
                                                                      class="mx-2 px-2 py-1 border border-gray-300 rounded-lg text-center guest-count-input" 
                                                                      data-type="Child" data-rate="150" data-facility="facility-2">
                                                            </div>
                                                       </div>
                                                       <div class="guest-type-control">
                                                            <span class="text-sm">Senior (₱200.00)</span>
                                                            <div class="flex items-center">
                                                                 <input type="number" min="0" max="15" value="0" 
                                                                      class="mx-2 px-2 py-1 border border-gray-300 rounded-lg text-center guest-count-input" 
                                                                      data-type="Senior" data-rate="200" data-facility="facility-2">
                                                            </div>
                                                       </div>
                                                  </div>
                                             </div>
                                             
                                             <div class="flex items-center mt-2">
                                                  <label class="text-sm text-gray-700 mr-2">Total Persons:</label>
                                                  <span class="text-sm font-medium" id="facility-2-total-persons">0</span>
                                             </div>
                                             <button type="button"
                                                  class="mt-4 bg-primary hover:bg-primary/90 text-white text-sm font-medium py-2 px-4 rounded-lg transition add-to-cart-btn"
                                                  data-room="facility-2">
                                                  Add to Cart
                                             </button>
                                        </div>
                                   </div>
                              </div>
                         </div>
                    </div>
               </div>

               <!-- Right Column - Order Summary -->
               <div class="lg:w-1/3 space-y-6 sticky top-6 h-[calc(100vh-1.5rem)] overflow-y-auto">
                    <!-- Date Selection Card -->
                    <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                        <h2 class="text-xl font-bold text-dark mb-4 flex items-center">
                            <i class="far fa-calendar-alt text-primary mr-3"></i>
                            Select Your Date
                        </h2>
                        <div>
                            <label for="checkin" class="block text-sm font-medium text-gray-700 mb-2">
                                Visit Date
                            </label>
                            <input type="text" id="checkin" placeholder="Select date"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent datepicker">
                        </div>
                        <div class="mt-4 flex items-center text-sm text-gray-600">
                            <i class="fas fa-info-circle text-primary mr-2"></i>
                            <span id="nights-display">Select your preferred visit date</span>
                        </div>
                    </div>

                    <!-- Booking Summary Card -->
                    <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-100 hover:shadow-xl transition-shadow duration-300">
                        <h2 class="text-2xl font-bold text-gray-800 mb-5 flex items-center">
                            <i class="fas fa-receipt text-primary mr-3 text-2xl"></i>
                            Booking Summary
                        </h2>
                    
                        <div id="cart-items" class="space-y-4 min-h-[120px]">
                            <!-- Empty cart state -->
                            <div class="text-gray-400 text-center py-6">
                                <i class="fas fa-shopping-cart text-3xl mb-3 opacity-50"></i>
                                <p>Your cart is empty</p>
                            </div>
                            
                            <!-- Sample item (hidden by default) -->
                            <div class="hidden cart-item">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <h4 class="font-medium text-gray-800">Facility Name</h4>
                                        <p class="text-sm text-gray-500">1 person</p>
                                    </div>
                                    <span class="font-semibold text-primary">₱500.00</span>
                                </div>
                            </div>
                        </div>
                    
                        <!-- Simplified total section -->
                        <div class="border-t border-gray-200 pt-4 mt-4">
                            <div class="flex justify-between items-center">
                                <span class="text-lg font-bold text-gray-800">Total</span>
                                <span class="text-3xl font-bold text-primary" id="total-price">₱0.00</span>
                            </div>
                        </div>
                    
                        <button id="checkout-btn" type="button"
                            class="w-full mt-6 bg-gradient-to-r from-primary to-secondary hover:from-primary/90 hover:to-secondary/90 text-white font-bold py-4 px-6 rounded-xl transition-all duration-300 shadow-md hover:shadow-lg flex items-center justify-center disabled:opacity-70 disabled:cursor-not-allowed disabled:transform-none hover:-translate-y-0.5 active:translate-y-0"
                            disabled>
                            <i class="fas fa-lock mr-3"></i>
                            <span>Proceed to Payment</span>
                            <span class="loading-spinner hidden ml-2">
                                <i class="fas fa-spinner fa-spin"></i>
                            </span>
                        </button>
                    
                        <div class="mt-4 text-center">
                            <p class="text-xs text-gray-400 flex items-center justify-center">
                                <i class="fas fa-shield-alt mr-1"></i>
                                Proceed and wait for confirmation before completing the payment.
                            </p>
                        </div>
                    </div>
               </div>
          </div>
     </div>

     <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
     <script>
          class BookingSystem {
               constructor() {
                    this.cart = [];
                    this.roomsData = {};
                    this.bookedDates = {}; // Stores booked dates for each room
                    this.datePicker = null;

                    // Initialize rooms data from DOM
                    document.querySelectorAll('.room-card').forEach(card => {
                         const roomId = card.dataset.roomId;
                         const images = JSON.parse(card.dataset.images);
                         const bookedDates = JSON.parse(card.dataset.bookedDates || '[]');
                         const maxPax = parseInt(card.dataset.maxPax) || 30; // Default to 30 if not specified

                         this.roomsData[roomId] = {
                              name: card.querySelector('h3').textContent.trim(),
                              price: parseFloat(card.dataset.price),
                              images: images,
                              mainImage: images[0] || 'https://via.placeholder.com/500x300?text=No+Image',
                              id: roomId,
                              pax: maxPax,
                              guestTypes: {} // Will store guest type rates
                         };

                         // Initialize guest types for this facility
                         const guestTypeInputs = card.querySelectorAll('.guest-count-input');
                         guestTypeInputs.forEach(input => {
                              const type = input.dataset.type;
                              const rate = parseFloat(input.dataset.rate);
                              this.roomsData[roomId].guestTypes[type] = rate;
                         });

                         this.bookedDates[roomId] = bookedDates;
                    });

                    this.init();
               }

               init() {
                    this.setupEventListeners();
                    this.initDatePickers();
                    this.setDefaultDates();
               }

               initDatePickers() {
                    const self = this;
                    
                    // Initialize checkin datepicker
                    this.checkinPicker = flatpickr("#checkin", {
                         minDate: "today",
                         dateFormat: "Y-m-d",
                         onChange: function(selectedDates, dateStr) {
                              if (selectedDates.length > 0) {
                                   document.getElementById('nights-display').textContent = 
                                        `Visit date: ${self.formatDisplayDate(selectedDates[0])}`;
                              }
                         }
                    });
               }

               setDefaultDates() {
                    const today = new Date();
                    this.checkinPicker.setDate(today);
                    document.getElementById('nights-display').textContent = 
                         `Visit date: ${this.formatDisplayDate(today)}`;
               }

               setupEventListeners() {
                    // Cart button handlers (event delegation)
                    document.addEventListener('click', (e) => {
                         if (e.target.closest('.add-to-cart-btn')) {
                              const button = e.target.closest('.add-to-cart-btn');
                              this.addToCart(button.dataset.room);
                         }

                         if (e.target.closest('.remove-btn')) {
                              const button = e.target.closest('.remove-btn');
                              this.removeFromCart(button.dataset.room);
                         }
                    });
                    
                    // Input change for guest types
                    document.addEventListener('change', (e) => {
                         if (e.target.classList.contains('guest-count-input')) {
                              const input = e.target;
                              const newValue = parseInt(input.value) || 0;
                              const maxPax = this.roomsData[input.dataset.facility].pax;
                              
                              // Calculate total persons
                              let totalPersons = 0;
                              const inputs = document.querySelectorAll(`.guest-count-input[data-facility="${input.dataset.facility}"]`);
                              inputs.forEach(i => {
                                   totalPersons += parseInt(i.value) || 0;
                              });
                              
                              if (totalPersons > maxPax) {
                                   alert(`This facility can only accommodate up to ${maxPax} persons`);
                                   input.value = 0;
                                   this.updateGuestCount(input.dataset.facility, input.dataset.type, 0);
                              } else {
                                   this.updateGuestCount(input.dataset.facility, input.dataset.type, newValue);
                              }
                         }
                    });

                    // Checkout handler
                    document.getElementById('checkout-btn').addEventListener('click', () => {
                         if (this.cart.length > 0) {
                              // Collect all necessary data
                              const bookingData = {
                                   customer: {
                                        firstname: document.getElementById('firstname').value,
                                        lastname: document.getElementById('lastname').value,
                                        email: document.getElementById('email').value,
                                        phone: document.getElementById('phone').value
                                   },
                                   items: this.cart,
                                   total: this.calculateTotal(),
                                   visitDate: this.checkinPicker.selectedDates[0] ? 
                                        this.formatDate(this.checkinPicker.selectedDates[0]) : null
                              };

                              // Here you would typically send this data to your backend
                              console.log('Booking data:', bookingData);
                              
                              // For demo purposes, we'll just show an alert
                              alert('Booking submitted successfully! (Check console for details)');
                         }
                    });
               }
               
               updateGuestCount(facilityId, guestType, count) {
                    // Update the total persons display
                    let totalPersons = 0;
                    const inputs = document.querySelectorAll(`.guest-count-input[data-facility="${facilityId}"]`);
                    inputs.forEach(input => {
                         totalPersons += parseInt(input.value) || 0;
                    });
                    
                    document.getElementById(`${facilityId}-total-persons`).textContent = totalPersons;
                    
                    // Enable/disable add to cart button based on whether there are any guests
                    const addButton = document.querySelector(`.add-to-cart-btn[data-room="${facilityId}"]`);
                    if (addButton) {
                         addButton.disabled = totalPersons === 0;
                    }
               }

               formatDate(date) {
                    if (!(date instanceof Date)) {
                         date = new Date(date);
                    }
                    const year = date.getFullYear();
                    const month = String(date.getMonth() + 1).padStart(2, '0');
                    const day = String(date.getDate()).padStart(2, '0');
                    return `${year}-${month}-${day}`;
               }

               formatDisplayDate(date) {
                    return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
               }

               calculateTotal() {
                    return this.cart.reduce((total, item) => total + item.price, 0);
               }

               addToCart(roomId) {
                    // Check if room exists in roomsData
                    if (!this.roomsData[roomId]) {
                         console.error('Facility not found:', roomId);
                         alert('Facility not available');
                         return;
                    }

                    // Check if already in cart
                    if (this.cart.some(item => item.id === roomId)) {
                         alert('This facility is already in your cart');
                         return;
                    }

                    // Check if date is selected
                    const visitDate = this.checkinPicker.selectedDates[0];
                    
                    if (!visitDate) {
                         alert('Please select a visit date first');
                         return;
                    }

                    // Get guest type counts
                    const guestInputs = document.querySelectorAll(`.guest-count-input[data-facility="${roomId}"]`);
                    const guestCounts = {};
                    let totalPersons = 0;
                    
                    guestInputs.forEach(input => {
                         const type = input.dataset.type;
                         const count = parseInt(input.value) || 0;
                         guestCounts[type] = count;
                         totalPersons += count;
                    });
                    
                    if (totalPersons === 0) {
                         alert('Please select at least one guest');
                         return;
                    }
                    
                    // Check if exceeds max pax
                    const maxPax = this.roomsData[roomId].pax;
                    if (totalPersons > maxPax) {
                         alert(`This facility can only accommodate up to ${maxPax} persons`);
                         return;
                    }

                    // Calculate total price based on guest types
                    let totalPrice = 0;
                    for (const [type, count] of Object.entries(guestCounts)) {
                         if (count > 0) {
                              totalPrice += this.roomsData[roomId].guestTypes[type] * count;
                         }
                    }

                    // Add to cart
                    const facility = this.roomsData[roomId];
                    this.cart.push({
                         id: roomId,
                         name: facility.name,
                         price: totalPrice, // This is now the total price for all guests
                         images: facility.images,
                         mainImage: facility.mainImage,
                         pax: totalPersons,
                         date: this.formatDate(visitDate),
                         guestCounts: guestCounts // Store the breakdown of guest types
                    });

                    this.updateCartDisplay();

                    // Show success message
                    const buttons = document.querySelectorAll(`.add-to-cart-btn[data-room="${roomId}"]`);
                    buttons.forEach(button => {
                         button.textContent = 'Added!';
                         button.classList.replace('bg-primary', 'bg-green-500');
                         button.disabled = true;

                         setTimeout(() => {
                              button.textContent = 'Add to Cart';
                              button.classList.replace('bg-green-500', 'bg-primary');
                              button.disabled = false;
                         }, 2000);
                    });
               }

               updateCartDisplay() {
                    const container = document.getElementById('cart-items');
                    const checkoutBtn = document.getElementById('checkout-btn');
                    const totalElement = document.getElementById('total-price');

                    if (this.cart.length === 0) {
                         container.innerHTML = '<div class="text-gray-400 text-center py-6"><i class="fas fa-shopping-cart text-3xl mb-3 opacity-50"></i><p>Your cart is empty</p></div>';
                         totalElement.textContent = '₱0.00';
                         checkoutBtn.disabled = true;
                         return;
                    }

                    checkoutBtn.disabled = false;

                    let subtotal = 0;
                    let html = '';

                    this.cart.forEach(item => {
                         subtotal += item.price;

                         // Create guest type breakdown text
                         let guestText = '';
                         for (const [type, count] of Object.entries(item.guestCounts)) {
                              if (count > 0) {
                                   guestText += `${count} ${type}, `;
                              }
                         }
                         guestText = guestText.replace(/,\s*$/, ''); // Remove trailing comma
                         
                         // Ensure the image path is correct
                         let imagePath = item.mainImage;
                         if (!imagePath.startsWith('http') && !imagePath.startsWith('/')) {
                              imagePath = '/' + imagePath;
                         }
                         
                         html += `
                              <div class="flex justify-between items-start border-b border-gray-100 pb-4">
                                <div class="flex items-start">
                                  <img src="${imagePath}" alt="${item.name}" class="w-16 h-16 object-cover rounded-lg mr-3" onerror="this.src='https://via.placeholder.com/500x300?text=Image+Not+Found'">
                                  <div>
                                    <h4 class="font-medium text-dark">${item.name}</h4>
                                    <div class="text-sm text-gray-600">${item.pax} person${item.pax !== 1 ? 's' : ''} (${guestText}) • ${item.date}</div>
                                  </div>
                                </div>
                                <div class="text-right">
                                  <div class="font-medium">₱${item.price.toFixed(2)}</div>
                                  <button type="button" class="text-red-500 text-sm hover:text-red-700 transition remove-btn" data-room="${item.id}">
                                    <i class="far fa-trash-alt mr-1"></i> Remove
                                  </button>
                                </div>
                              </div>
                            `;
                    });

                    container.innerHTML = html;
                    totalElement.textContent = `₱${subtotal.toFixed(2)}`;
               }

               removeFromCart(roomId) {
                    this.cart = this.cart.filter(item => item.id !== roomId);
                    this.updateCartDisplay();

                    // Re-enable the add to cart button for this room
                    const buttons = document.querySelectorAll(`.add-to-cart-btn[data-room="${roomId}"]`);
                    buttons.forEach(button => {
                         button.textContent = 'Add to Cart';
                         button.classList.remove('bg-green-500', 'bg-primary');
                         button.classList.add('bg-primary');
                         button.disabled = false;
                    });
               }
          }

          // Initialize the booking system when DOM is loaded
          document.addEventListener('DOMContentLoaded', () => {
               window.bookingSystem = new BookingSystem();
          });
     </script>
</body>

</html>