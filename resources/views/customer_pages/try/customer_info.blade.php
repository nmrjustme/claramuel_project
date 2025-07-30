@extends('layouts.bookings')
@section('title', 'Customer Information')
@section('bookings')
<style>
     /* Reuse styles from previous page where applicable */
     @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');

     body {
          font-family: 'Inter', sans-serif;
          background-color: #f8fafc;
     }

     .input-group {
          margin-bottom: 1.5rem;
     }

     label {
          display: block;
          margin-bottom: 0.5rem;
          font-weight: 500;
          color: #374151;
     }

     .form-input {
          width: 100%;
          padding: 0.75rem 1rem;
          border: 1px solid #d1d5db;
          border-radius: 0.375rem;
          font-size: 1rem;
          transition: border-color 0.2s, box-shadow 0.2s;
     }

     .form-input:focus {
          outline: none;
          border-color: #DC2626;
          box-shadow: 0 0 0 3px rgba(220, 38, 38, 0.1);
     }

     .input-error {
          border-color: #DC2626 !important;
          background-color: #FEF2F2;
     }

     .error-message {
          color: #DC2626;
          font-size: 0.875rem;
          margin-top: 0.25rem;
          display: none;
     }

     .btn-primary {
          background: linear-gradient(135deg, #DC2626 0%, #B91C1C 100%);
          color: white;
          font-weight: 600;
          padding: 0.75rem 1.5rem;
          border-radius: 0.5rem;
          border: none;
          cursor: pointer;
          transition: all 0.3s ease;
     }

     .btn-primary:hover {
          transform: translateY(-2px);
          box-shadow: 0 6px 12px rgba(220, 38, 38, 0.2);
     }

     .btn-primary:disabled {
          opacity: 0.7;
          cursor: not-allowed;
          transform: none !important;
     }

     .booking-summary-card {
          background-color: white;
          border-radius: 0.75rem;
          box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
          padding: 1.5rem;
          margin-bottom: 1.5rem;
          border: 1px solid #e5e7eb;
     }

     .booking-summary-title {
          font-size: 1.25rem;
          font-weight: 700;
          margin-bottom: 1rem;
          color: #1f2937;
          display: flex;
          align-items: center;
     }

     .booking-summary-title i {
          margin-right: 0.75rem;
          color: #DC2626;
     }

     .booking-item {
          display: flex;
          justify-content: space-between;
          padding: 0.75rem 0;
          border-bottom: 1px solid #f3f4f6;
     }

     .booking-item:last-child {
          border-bottom: none;
     }

     .booking-item-label {
          color: #6b7280;
     }

     .booking-item-value {
          font-weight: 500;
          color: #1f2937;
     }

     .room-item {
          display: flex;
          margin-bottom: 1rem;
          padding-bottom: 1rem;
          border-bottom: 1px dashed #e5e7eb;
     }

     .room-item:last-child {
          border-bottom: none;
          margin-bottom: 0;
          padding-bottom: 0;
     }

     .room-image {
          width: 80px;
          height: 80px;
          border-radius: 0.5rem;
          object-fit: cover;
          margin-right: 1rem;
     }

     .room-details {
          flex: 1;
     }

     .room-name {
          font-weight: 600;
          margin-bottom: 0.25rem;
     }

     .room-price {
          color: #6b7280;
          font-size: 0.875rem;
     }

     .total-section {
          margin-top: 1.5rem;
          padding-top: 1.5rem;
          border-top: 1px solid #e5e7eb;
     }

     .total-row {
          display: flex;
          justify-content: space-between;
          margin-bottom: 0.5rem;
     }

     .total-label {
          font-weight: 600;
     }

     .total-amount {
          font-weight: 700;
          font-size: 1.25rem;
          color: #DC2626;
     }

     .notification {
          position: fixed;
          bottom: 20px;
          right: 20px;
          background-color: #10B981;
          color: white;
          padding: 1rem 1.5rem;
          border-radius: 0.5rem;
          box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
          transform: translateY(100px);
          opacity: 0;
          transition: all 0.3s ease;
          z-index: 1000;
          display: flex;
          align-items: center;
     }

     .notification.show {
          transform: translateY(0);
          opacity: 1;
     }

     .notification.error {
          background-color: #DC2626;
     }

     .notification i {
          margin-right: 0.5rem;
     }

     .loading-spinner {
          display: inline-block;
          width: 1.5rem;
          height: 1.5rem;
          border: 3px solid rgba(255, 255, 255, 0.3);
          border-radius: 50%;
          border-top-color: white;
          animation: spin 1s ease-in-out infinite;
          margin-right: 0.75rem;
     }

     @keyframes spin {
          to {
               transform: rotate(360deg);
          }
     }

     .uppercase-input {
          text-transform: uppercase;
     }
</style>

<x-header />

<div class="container mx-auto px-6 py-8 max-w-6xl">
     <!-- Progress Steps -->
     <x-progress-step 
          :currentStep="2"
          :steps="[
               ['label' => 'Select Rooms'],
               ['label' => 'Customer Info'],
               ['label' => 'Payment'],
               ['label' => 'Completed']
          ]"
     />
     
     <div class="flex flex-col lg:flex-row gap-8">
          <!-- Left Column - Customer Information -->
          <div class="lg:w-2/3">
               <div class="bg-white rounded-xl shadow-sm p-8 border border-gray-100 mb-6">
                    <h2 class="text-2xl font-bold text-dark mb-6 flex items-center">
                         <i class="fas fa-user-circle text-primary mr-3"></i>
                         Your Information
                    </h2>

                    <form id="customer-info-form">
                         <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                              <div class="input-group">
                                   <label for="firstname">First Name <span class="text-red-500">*</span></label>
                                   <input type="text" id="firstname" name="firstname" class="form-input uppercase-input"
                                        required placeholder="JOHN" oninput="this.value = this.value.toUpperCase()">
                                   <div id="firstname-error" class="error-message">First name is required</div>
                              </div>

                              <div class="input-group">
                                   <label for="lastname">Last Name <span class="text-red-500">*</span></label>
                                   <input type="text" id="lastname" name="lastname" class="form-input uppercase-input"
                                        required placeholder="DOE" oninput="this.value = this.value.toUpperCase()">
                                   <div id="lastname-error" class="error-message">Last name is required</div>
                              </div>

                              <div class="input-group">
                                   <label for="email">Email <span class="text-red-500">*</span></label>
                                   <input type="email" id="email" name="email" class="form-input" required
                                        placeholder="john@gmail.com">
                                   <div id="email-error" class="error-message">Please enter a valid email address</div>
                                   <p class="mt-1 text-xs text-gray-500">We'll send your booking confirmation to this
                                        email</p>
                              </div>

                              <div class="input-group">
                                   <label for="phone">Phone Number <span class="text-red-500">*</span></label>
                                   <input type="tel" id="phone" name="phone" class="form-input" maxlength="12"
                                        placeholder="9123 456 789" oninput="formatPhone(this)"
                                        onblur="validatePhone(this)">
                                   <div id="phone-error" class="error-message">Please enter a valid 10-digit phone
                                        number starting with 9 (format: 9123 456 789)</div>
                                   <p class="mt-1 text-xs text-gray-500">We may contact you regarding your booking</p>
                              </div>

                         <div class="input-group md:col-span-2">
                              <h3 class="text-lg font-semibold mb-4">Guest Information</h3>
                              
                              <div id="guest-selection-container">
                                   <!-- This will be populated with guest selection fields for each room -->
                              </div>
                         </div>
                         </div>
                    </form>
               </div>
          </div>

          <!-- Right Column - Booking Summary -->
          <div class="lg:w-1/3">
               <div class="sticky top-6 space-y-6">
                    <div class="booking-summary-card">
                         <h3 class="booking-summary-title">
                              <i class="fas fa-calendar-alt"></i>
                              Booking Summary
                         </h3>

                         <div class="booking-item">
                              <span class="booking-item-label">Check-in</span>
                              <span class="booking-item-value" id="summary-checkin">-</span>
                         </div>
                         <div class="booking-item">
                              <span class="booking-item-label">Check-out</span>
                              <span class="booking-item-value" id="summary-checkout">-</span>
                         </div>
                         <div class="booking-item">
                              <span class="booking-item-label">Nights</span>
                              <span class="booking-item-value" id="summary-nights">-</span>
                         </div>
                    </div>

                    <div class="booking-summary-card">
                         <h3 class="booking-summary-title">
                              <i class="fas fa-bed"></i>
                              Your Rooms
                         </h3>

                         <div id="rooms-list">
                              <!-- Rooms will be populated here -->
                              <div class="text-gray-400 text-center py-4">
                                   <i class="fas fa-shopping-cart text-2xl mb-2 opacity-50"></i>
                                   <p>No rooms selected</p>
                              </div>
                         </div>

                         <div id="breakfast-summary" class="hidden">
                              <div class="booking-item">
                                   <span class="booking-item-label">Breakfast</span>
                                   <span class="booking-item-value" id="breakfast-price">-</span>
                              </div>
                         </div>

                         <div class="total-section">
                              <div class="total-row">
                                   <span class="total-label">Total</span>
                                   <span class="total-amount" id="summary-total">₱0.00</span>
                              </div>
                         </div>
                    </div>

                    <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                         <div class="checkbox-container mb-4">
                              <input type="checkbox" id="terms-checkbox"
                                   class="form-checkbox h-5 w-5 text-primary rounded focus:ring-primary">
                              <label for="terms-checkbox" class="text-sm">
                                   I agree to the <a href="#" class="text-primary hover:underline">terms and
                                        conditions</a> and
                                   <a href="#" class="text-primary hover:underline">privacy policy</a>
                              </label>
                         </div>
                         <div id="terms-error" class="error-message hidden text-sm text-red-500 mb-4">
                              Please accept the terms and conditions to proceed
                         </div>

                         <button id="confirm-booking-btn"
                              class="btn-primary w-full py-3 flex items-center justify-center disabled:opacity-70 disabled:cursor-not-allowed"
                              disabled>
                              <span id="button-text">Confirm Booking</span>
                              <div id="button-spinner" class="loading-spinner hidden"></div>
                         </button>
                    </div>
               </div>
          </div>
     </div>
</div>

<!-- Notification element -->
<div id="notification" class="notification hidden">
     <i class="fas fa-check-circle"></i>
     <span id="notification-message"></span>
</div>

<script>
     
     document.addEventListener('DOMContentLoaded', function() {
        // Retrieve booking data from sessionStorage
          const bookingData = JSON.parse(sessionStorage.getItem('bookingData'));
          
          if (!bookingData) {
               // Redirect back if no booking data found
               showNotification('No booking data found. Please select rooms first.', true);
               setTimeout(() => {
                    window.location.href = '/bookings';
               }, 2000);
               return;
          }
          
          // Display booking summary
          displayBookingSummary(bookingData);
          
          // Setup form validation
          setupFormValidation(bookingData);
          
          setupGuestSelection(bookingData);
     });
     
     // Add this to the customer-info.blade.php script section
     function setupGuestSelection(bookingData) {
          const container = document.getElementById('guest-selection-container');
          container.innerHTML = '';
          
          fetch('/customer/guest-types', {
               method: 'GET',
               headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
               },
               credentials: 'same-origin'
          })
          .then(response => {
               if (!response.ok) {
                    throw new Error(`Server responded with ${response.status}`);
               }
               return response.json();
          })
          .then(responseData => {  
               // Extract guest types from the response structure
               const guestTypes = responseData.data || responseData;
               
               // Filter unique types or handle duplicates (example: taking first occurrence)
               const uniqueTypes = [];
               const seenTypes = new Set();
               
               guestTypes.forEach(type => {
                    if (!seenTypes.has(type.type)) {
                         seenTypes.add(type.type);
                         uniqueTypes.push(type);
                    }
               });

               console.log('Processed guest types:', uniqueTypes); // Debug log
               
               bookingData.facilities.forEach((room, index) => {
                    const roomDiv = document.createElement('div');
                    roomDiv.className = 'mb-6 p-4 border border-gray-200 rounded-lg';

                    roomDiv.innerHTML = `
                         <div class="mb-4">
                              <h3 class="text-lg font-semibold text-gray-800">Guest Selection</h3>
                         </div>
                         <h4 class="font-medium mb-2">${room.name} (Max ${room.pax} guests)</h4>
                         <div class="grid grid-cols-1 md:grid-cols-3 gap-4" id="guest-selection-room-${index}">
                              ${uniqueTypes.map(type => `
                              <div class="guest-type-group">
                              <label for="guest-type-${index}-${type.id}" class="block text-sm font-medium text-gray-700 mb-1">
                                   ${type.type}
                              </label>
                              <input type="number"
                                   id="guest-type-${index}-${type.id}"
                                   name="guest_types[${room.facility_id}][${type.id}]"
                                   class="guest-quantity form-input w-full"
                                   min="0"
                                   max="${room.pax}"
                                   value="0"
                                   data-room-index="${index}"
                                   data-room-id="${room.facility_id}"
                                   data-room-pax="${room.pax}"
                                   data-guest-type-id="${type.id}">
                              </div>
                              `).join('')}
                         </div>
                         <div class="mt-2 text-sm text-gray-600">
                              <span id="guest-count-${index}">0</span> / ${room.pax} guests selected
                         </div>
                    `;

                    container.appendChild(roomDiv);
               });

               // Attach change listeners to guest quantity inputs
               document.querySelectorAll('.guest-quantity').forEach(input => {
                    input.addEventListener('change', updateGuestCounts);
               });
          })
          .catch(error => {
               console.error('Error loading guest types:', error);
               container.innerHTML = `
                    <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4">
                         <div class="flex">
                              <div class="flex-shrink-0">
                              <svg class="h-5 w-5 text-yellow-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                   <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                              </svg>
                              </div>
                              <div class="ml-3">
                              <p class="text-sm text-yellow-700">
                                   Could not load guest types. ${error.message}
                                   <button onclick="window.location.reload()" class="mt-2 text-yellow-600 hover:text-yellow-500 font-medium">
                                        Try Again
                                   </button>
                              </p>
                              </div>
                         </div>
                    </div>
               `;
          });
     }
     
     
     function updateGuestCounts() {
          const roomIndex = this.dataset.roomIndex;
          const roomPax = parseInt(this.dataset.roomPax);
          const roomId = this.dataset.roomId;
          
          // Get all inputs for this room
          const roomInputs = document.querySelectorAll(`.guest-quantity[data-room-index="${roomIndex}"]`);
          
          // Calculate total selected guests
          let totalGuests = 0;
          roomInputs.forEach(input => {
               totalGuests += parseInt(input.value) || 0;
          });
          
          // Update the display
          document.getElementById(`guest-count-${roomIndex}`).textContent = totalGuests;
          
          // Enforce max pax limit
          if (totalGuests > roomPax) {
               showNotification(`Maximum ${roomPax} guests allowed for this room`, true);
               this.value = Math.max(0, parseInt(this.value) - (totalGuests - roomPax));
               updateGuestCounts.call(this); // Recursive call to update again
          }
     }
    
    function displayBookingSummary(bookingData) {
        // Format dates
        const checkinDate = new Date(bookingData.checkin_date);
        const checkoutDate = new Date(bookingData.checkout_date);
        const nights = Math.ceil((checkoutDate - checkinDate) / (1000 * 60 * 60 * 24));

        document.getElementById('summary-checkin').textContent = formatDisplayDate(checkinDate);
        document.getElementById('summary-checkout').textContent = formatDisplayDate(checkoutDate);
        document.getElementById('summary-nights').textContent = `${nights} night${nights !== 1 ? 's' : ''}`;
        document.getElementById('summary-total').textContent = `₱${bookingData.total_price.toFixed(2)}`;

        // Display rooms
        const roomsList = document.getElementById('rooms-list');
        if (bookingData.facilities.length > 0) {
            roomsList.innerHTML = '';
            bookingData.facilities.forEach(room => {
                const roomElement = document.createElement('div');
                roomElement.className = 'room-item';
                roomElement.innerHTML = `
                    <img src="${room.mainImage}" alt="${room.name}" class="room-image" onerror="this.src='https://via.placeholder.com/500x300?text=Room+Image'">
                    <div class="room-details">
                        <div class="room-name">${room.name}</div>
                        <div class="room-price">${nights} night${nights !== 1 ? 's' : ''} × ₱${room.price.toFixed(2)}</div>
                        <div class="room-price">₱${(room.price * nights).toFixed(2)}</div>
                    </div>
                `;
                roomsList.appendChild(roomElement);
            });
        }

        // Display breakfast if included
        if (bookingData.breakfast_included) {
            document.getElementById('breakfast-summary').classList.remove('hidden');
            document.getElementById('breakfast-price').textContent = `₱${bookingData.breakfast_price.toFixed(2)}`;
        } else {
            document.getElementById('breakfast-summary').classList.add('hidden');
        }
    }

    function setupFormValidation(bookingData) {
        const form = document.getElementById('customer-info-form');
        const confirmBtn = document.getElementById('confirm-booking-btn');
        const termsCheckbox = document.getElementById('terms-checkbox');
        const termsError = document.getElementById('terms-error');

        // Validate fields on blur
        document.getElementById('firstname').addEventListener('blur', validateNameField);
        document.getElementById('lastname').addEventListener('blur', validateNameField);
        document.getElementById('email').addEventListener('blur', validateEmailField);
        document.getElementById('phone').addEventListener('blur', validatePhoneField);

        // Validate terms checkbox
        termsCheckbox.addEventListener('change', function() {
            termsError.classList.add('hidden');
            validateForm();
        });

        // Confirm booking button click handler
        confirmBtn.addEventListener('click', function() {
            if (!termsCheckbox.checked) {
                termsError.classList.remove('hidden');
                return;
            }

            if (validateForm()) {
                submitBooking(bookingData);
            }
        });

        function validateForm() {
            const isValid = 
                validateNameField({ target: document.getElementById('firstname') }) &&
                validateNameField({ target: document.getElementById('lastname') }) &&
                validateEmailField({ target: document.getElementById('email') }) &&
                validatePhoneField({ target: document.getElementById('phone') }) &&
                termsCheckbox.checked;

            confirmBtn.disabled = !isValid;
            return isValid;
        }
    }

    function validateNameField(e) {
        const field = e.target;
        const errorElement = document.getElementById(`${field.id}-error`);
        
        if (!field.value.trim()) {
            field.classList.add('input-error');
            errorElement.style.display = 'block';
            return false;
        }
        
        field.classList.remove('input-error');
        errorElement.style.display = 'none';
        return true;
    }

    function validateEmailField(e) {
        const field = e.target;
        const errorElement = document.getElementById(`${field.id}-error`);
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        
        if (!field.value.trim() || !emailRegex.test(field.value)) {
            field.classList.add('input-error');
            errorElement.style.display = 'block';
            return false;
        }
        
        field.classList.remove('input-error');
        errorElement.style.display = 'none';
        return true;
    }

    function validatePhoneField(e) {
        return validatePhone(e.target);
    }

    function formatDisplayDate(date) {
        if (typeof date === 'string') {
            date = new Date(date);
        }
        return date.toLocaleDateString('en-US', { 
            weekday: 'short', 
            month: 'short', 
            day: 'numeric',
            year: 'numeric'
        });
    }

    function submitBooking(bookingData) {
        const button = document.getElementById('confirm-booking-btn');
        const buttonText = document.getElementById('button-text');
        const spinner = document.getElementById('button-spinner');

        // Show loading state
        button.disabled = true;
        buttonText.textContent = 'Processing...';
        spinner.classList.remove('hidden');

        // Collect form data
        const formData = {
            firstname: document.getElementById('firstname').value.trim(),
            lastname: document.getElementById('lastname').value.trim(),
            email: document.getElementById('email').value.trim(),
            phone: document.getElementById('phone').value.trim(),
            ...bookingData
        };

        // Submit to server
        fetch('/bookings/confirm', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            },
            body: JSON.stringify(formData)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Redirect to payment page on success
                // /WaitForConfirmation?email=${encodeURIComponent(bookingData.email)}
                window.location.href = `/bookings/payment/${data.booking_id}`;
            } else {
                showNotification(data.message || 'Booking failed. Please try again.', true);
                button.disabled = false;
                buttonText.textContent = 'Confirm Booking';
                spinner.classList.add('hidden');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('An error occurred. Please try again.', true);
            button.disabled = false;
            buttonText.textContent = 'Confirm Booking';
            spinner.classList.add('hidden');
        });
    }

    // Helper function to show notifications
    function showNotification(message, isError = false) {
        const notification = document.getElementById('notification');
        const notificationMessage = document.getElementById('notification-message');
        
        notificationMessage.textContent = message;
        notification.className = isError ? 'notification error show' : 'notification show';
        
        // Clear any existing timeout to prevent hiding a new notification prematurely
        if (notification.timeoutId) {
            clearTimeout(notification.timeoutId);
        }
        
        notification.timeoutId = setTimeout(() => {
            notification.className = notification.className.replace('show', '');
        }, 5000);
    }

    // Phone number validation
    function validatePhone(input) {
        const phoneRegex = /^9\d{3}\s\d{3}\s\d{3}$/;
        const phone = input.value;
        
        if (!phoneRegex.test(phone)) {
            document.getElementById('phone-error').style.display = 'block';
            input.classList.add('input-error');
            return false;
        }
        
        document.getElementById('phone-error').style.display = 'none';
        input.classList.remove('input-error');
        return true;
    }

    // Phone number formatting
    function formatPhone(input) {
        let phone = input.value.replace(/\D/g, '');
        
        if (phone.length > 4) {
            phone = phone.substring(0, 4) + ' ' + phone.substring(4);
        }
        if (phone.length > 8) {
            phone = phone.substring(0, 8) + ' ' + phone.substring(8);
        }
        
        input.value = phone.substring(0, 12);
    }
</script>
@endsection