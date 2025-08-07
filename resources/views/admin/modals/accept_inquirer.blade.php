<style>
    /* Add this new style for the checkbox container */
    .checkbox-container {
        display: flex;
        align-items: center;
        margin-top: 1rem;
        padding: 0.5rem;
        background-color: #f8fafc;
        border-radius: 0.375rem;
    }
    
    .checkbox-container input[type="checkbox"] {
        margin-right: 0.5rem;
    }
    
    .checkbox-container label {
        font-size: 0.875rem;
        color: #4b5563;
    }
    
    /* Modal container styling */
    #bookdetailsmodalContainer {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        background-color: rgba(0, 0, 0, 0.2);
        z-index: 1000;
        overflow-y: auto;
        padding: 1rem;
        opacity: 0;
        pointer-events: none;
        transition: opacity 0.3s ease;
    }
    
    #bookdetailsmodalContainer.active {
        opacity: 1;
        pointer-events: auto;
    }
    
    /* Modal backdrop */
    #bookdetailsmodalBackdrop {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: rgba(0, 0, 0, 0.5);
        z-index: 999;
        backdrop-filter: blur(3px); /* equivalent to Tailwind's backdrop-blur-sm */
    }
    
    
    /* Modal content */
    #bookdetailsmodalContainer .modal-content {
        background-color: white;
        border-radius: 0.75rem;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        width: 100%;
        max-width: 56rem;
        max-height: 90vh;
        display: flex;
        flex-direction: column;
        position: relative;
        z-index: 1001;
        transform: translateY(20px);
        transition: transform 0.3s ease;
    }
    
    #bookdetailsmodalContainer.active .modal-content {
        transform: translateY(0);
    }
    
    /* Modal header */
    .modal-header {
        background: linear-gradient(to right, #dc2626, #b91c1c);
        color: white;
        padding: 1rem;
        border-top-left-radius: 0.75rem;
        border-top-right-radius: 0.75rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        position: sticky;
        top: 0;
        z-index: 10;
    }
    
    /* Modal body */
    .modal-body {
        overflow-y: auto;
        flex: 1;
        padding: 1.5rem;
    }
    
    /* Modal footer */
    .modal-footer {
        background-color: #f9fafb;
        padding: 1rem;
        border-bottom-left-radius: 0.75rem;
        border-bottom-right-radius: 0.75rem;
        border-top: 1px solid #e5e7eb;
        position: sticky;
        bottom: 0;
    }
    
    /* Ensure body doesn't scroll when modal is open */
    body.modal-open {
        overflow: hidden;
        position: fixed;
        width: 100%;
        height: 100%;
    }
    
    /* Info cards styling */
    .info-card {
        background-color: #f9fafb;
        padding: 1rem;
        border-radius: 0.5rem;
        margin-bottom: 1.5rem;
        border: 1px solid #e5e7eb;
    }
    
    /* Facility card styling */
    .facility-card {
        background-color: white;
        padding: 1rem;
        border-radius: 0.5rem;
        margin-bottom: 1.5rem;
        border: 1px solid #e5e7eb;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
    }
    
    /* Meal info styling */
    .meal-info {
        background-color: #f0fdf4;
        padding: 0.75rem;
        border-radius: 0.375rem;
        margin-top: 0.75rem;
        border: 1px solid #dcfce7;
    }
    
    /* Responsive grid */
    .responsive-grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: 1rem;
    }
    
    @media (min-width: 768px) {
        .responsive-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }
    
    /* Button styles */
    .btn {
        padding: 0.5rem 1rem;
        border-radius: 0.375rem;
        font-weight: 500;
        transition: all 0.2s ease;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }
    
    .btn-close {
        background-color: #f3f4f6;
        color: #4b5563;
    }
    
    .btn-close:hover {
        background-color: #e5e7eb;
    }
    
    .btn-confirm {
        background-color: #dc2626;
        color: white;
    }
    
    .btn-confirm:hover {
        background-color: #b91c1c;
    }
    
    .btn-reject {
        background-color: #4b5563;
        color: white;
    }
    
    .btn-reject:hover {
        background-color: #374151;
    }
    
    .btn-disabled {
        background-color: #d1d5db;
        color: #6b7280;
        cursor: not-allowed;
    }
    
    /* Loading spinner */
    .spinner {
        animation: spin 1s linear infinite;
    }
    
    @keyframes spin {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }
    
    /* Status badges */
    .status-badge {
        display: inline-flex;
        align-items: center;
        padding: 0.25rem 0.5rem;
        border-radius: 9999px;
        font-size: 0.75rem;
        font-weight: 500;
    }
    
    .status-confirmed {
        background-color: #dcfce7;
        color: #166534;
    }
    
    .status-pending {
        background-color: #fef9c3;
        color: #854d0e;
    }
    
    .status-rejected {
        background-color: #fee2e2;
        color: #991b1b;
    }
    .facility-category {
        display: inline-block;
        padding: 0.25rem 0.5rem;
        background-color: #f0fdf4;
        border-radius: 0.25rem;
        font-size: 0.75rem;
        color: #166534;
        margin-top: 0.25rem;
    }
</style>

<!-- Modal Container -->
<div id="bookdetailsmodalContainer" class="hidden">

    <!-- Backdrop -->
    <div id="bookdetailsmodalBackdrop" class="hidden"></div>
    
    <!-- Modal Content -->
    <div class="modal-content">
        <!-- Modal Header -->
        <div class="modal-header">
            <div class="flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                </svg>
                <h2 class="text-xl font-bold">Booking Details</h2>
            </div>
        </div>
        
        <!-- Modal Body -->
        <div class="modal-body">
            <!-- Content will be dynamically inserted here -->
        </div>
        
        <!-- Modal Footer -->
        <div class="modal-footer">
            <!-- Footer content will be dynamically inserted here -->
        </div>
    </div>
</div>

<script>
    // When opening modal
    function showModal() {
        const modal = document.getElementById('bookdetailsmodalContainer');
        const backdrop = document.getElementById('bookdetailsmodalBackdrop');
        
        modal.classList.remove('hidden');
        backdrop.classList.remove('hidden');
        
        // Add slight delay before adding active class for animation
        setTimeout(() => {
            modal.classList.add('active');
            document.body.classList.add('modal-open');
        }, 10);
    }

    // When closing modal
    function closeModal() {
        const modal = document.getElementById('bookdetailsmodalContainer');
        const backdrop = document.getElementById('bookdetailsmodalBackdrop');
        
        modal.classList.remove('active');
        
        // Wait for animation to complete before hiding
        setTimeout(() => {
            modal.classList.add('hidden');
            backdrop.classList.add('hidden');
            document.body.classList.remove('modal-open');
        }, 300);
    }

    // ========================
    // REUSABLE MODAL FUNCTIONS
    // ========================
    function showLoadingModal(message = 'Loading...') {
        const modal = document.getElementById('bookdetailsmodalContainer');
        const modalBody = modal.querySelector('.modal-body');
        const modalFooter = modal.querySelector('.modal-footer');
        
        if (!modal || !modalBody) {
            console.error('Modal elements not found');
            return;
        }

        // Set loading content
        modalBody.innerHTML = `
            <div class="flex flex-col items-center justify-center py-8">
                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-red-600 mx-auto">
                </div>
                <p class="mt-4 text-gray-600">${message}</p>
            </div>
        `;
        
        // Clear footer
        modalFooter.innerHTML = '';
        
        // Show modal
        showModal();
    }

    function showErrorState(error) {
        const modal = document.getElementById('bookdetailsmodalContainer');
        const modalBody = modal.querySelector('.modal-body');
        const modalFooter = modal.querySelector('.modal-footer');
        
        if (!modal || !modalBody) {
            console.error('Modal elements not found');
            return;
        }

        modalBody.innerHTML = `
            <div class="flex flex-col items-center justify-center py-8">
                <div class="text-center text-red-500">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <h3 class="text-xl font-bold mt-2">Error</h3>
                    <p class="mt-2 text-gray-600">${error.message || 'Please try again later'}</p>
                </div>
            </div>
        `;
        
        modalFooter.innerHTML = `
            <div class="flex justify-end">
                <button onclick="closeModal()" class="btn btn-close">
                    Close
                </button>
            </div>
        `;
        
        // Show modal
        showModal();
    }

    function populateModalWithData(data) {
        const modal = document.getElementById('bookdetailsmodalContainer');
        const modalBody = modal.querySelector('.modal-body');
        const modalFooter = modal.querySelector('.modal-footer');

        if (!modal || !modalBody || !modalFooter) {
            console.error('Modal elements not found');
            return;
        }

        // Format date function
        const formatDate = (dateString) => {
            if (!dateString) return 'Not specified';
            const options = { year: 'numeric', month: 'short', day: 'numeric' };
            return new Date(dateString).toLocaleDateString('en-US', options);
        };

        // Format price function
        const formatPrice = (price) => {
            if (price === null || price === undefined || isNaN(price)) return 'Not available';
            return new Intl.NumberFormat('en-PH', {
                style: 'currency',
                currency: 'PHP',
                minimumFractionDigits: 2
            }).format(price).replace('PHP', '₱');
        };

        // Calculate nights between two dates
        const calculateNights = (checkIn, checkOut) => {
            if (!checkIn || !checkOut) return 0;
            
            try {
                // Remove timezone information if present and keep only date part
                const cleanDateString = (dateStr) => {
                    return dateStr.split('T')[0];
                };
                
                const start = new Date(cleanDateString(checkIn));
                const end = new Date(cleanDateString(checkOut));
                
                // Calculate difference in days
                const diffTime = Math.abs(end - start);
                return Math.ceil(diffTime / (1000 * 60 * 60 * 24));
            } catch (e) {
                console.error('Error calculating nights:', e);
                return 0;
            }
        };

        // Get the first facility's dates (they should all be the same)
        const checkInDate = data.facilities?.[0]?.check_in;
        const checkOutDate = data.facilities?.[0]?.check_out;

        // Calculate total nights
        const totalNights = calculateNights(checkInDate, checkOutDate);

        // Calculate overall total price
        const overallTotal = data.total_price || 0;

        // Determine status class
        let statusClass = 'status-pending';
        let statusText = data.status || 'pending';
        
        if (data.status === 'confirmed') {
            statusClass = 'status-confirmed';
            statusText = 'confirmed';
        } else if (data.status === 'rejected') {
            statusClass = 'status-rejected';
            statusText = 'rejected';
        }

        // Create booking details HTML
        const detailsHtml = data.facilities?.map((facility, index) => {
            const facilityNights = calculateNights(facility.check_in, facility.check_out);
            const hasMeal = facility.breakfast && facility.breakfast !== 'None';
            const isConfirmed = data.status === 'confirmed';
            const isRejected = data.status === 'rejected';
            // Generate guest details HTML
            const guestDetailsHtml = facility.guest_details?.length > 0 
                ? facility.guest_details.map(guest => `
                    <li class="flex justify-between text-sm py-1">
                        <span class="text-gray-600">${guest.type}:</span>
                        <span class="font-medium">${guest.quantity} guest(s)</span>
                    </li>
                `).join('')
                : '<li class="text-sm text-gray-500 italic">No guest details available</li>';
            
            return `
                <div class="facility-card">
                    <div class="flex justify-between items-start mb-1">
                        <div>
                            <h4 class="text-lg font-semibold">${facility.facility_name || 'Facility not specified'}</h4>
                            <span class="facility-category">${facility.facility_category || 'No category'}</span>
                            <p class="text-sm mt-1">
                                <span class="font-medium text-gray-600">Room:</span>
                                <span class="ml-2">${facility.room_info.room_number || 'N/A'}</span>
                            </p>
                            <p class="text-sm">
                                <span class="font-medium text-gray-600">Night(s):</span>
                                <span class="ml-2">${facilityNights}</span>
                            </p>
                        </div>
                        <span class="px-3 py-1 bg-blue-100 text-blue-800 text-sm rounded-full">
                            ${formatPrice(facility.room_info.price_per_night)}/night
                        </span>
                    </div>
                    
                    <!-- Guest Composition Section -->
                    <div class="mt-3 border-t pt-3">
                        <h5 class="text-sm font-medium text-gray-700 mb-2">Guest Composition</h5>
                        <ul class="space-y-1">
                            ${guestDetailsHtml}
                        </ul>
                    </div>
                    
                    ${hasMeal ? `
                        <div class="meal-info mt-3">
                            <h5 class="text-sm font-medium text-gray-700 mb-1">Meal Plan</h5>
                            <p class="text-sm">${facility.breakfast}</p>
                            <p class="text-sm mt-1">
                                <span class="font-medium">Price:</span>
                                <span class="ml-2">${formatPrice(facility.breakfast_price)}/morning</span>
                            </p>
                            <p class="text-sm">
                                <span class="font-medium">Total:</span>
                                <span class="ml-2">${formatPrice(facility.breakfast_price * facilityNights)}</span>
                            </p>
                        </div>
                    ` : ''}
                    
                    <div class="mt-3 pt-3 border-t">
                        <p class="text-sm flex justify-between">
                            <span class="font-medium text-gray-600">Subtotal:</span>
                            <span class="font-semibold">${formatPrice(facility.total_price)}</span>
                        </p>
                    </div>
                    
                    <!-- Add checkbox for each room -->
                    <div class="checkbox-container mt-3">
                        <input type="checkbox" id="room-checkbox-${index}" class="room-checkbox" 
                            data-room="${facility.facility_name || 'Room ' + (index + 1)}"
                            ${isConfirmed || isRejected ? 'disabled checked' : ''}>
                        <label for="room-checkbox-${index}">I have reviewed ${facility.facility_name || 'this room'} details</label>
                    </div>
                </div>
            `;
        }).join('') || '<p class="text-gray-500 italic">No facilities booked</p>';
        
        // Check if booking is already confirmed or rejected
        const isConfirmed = data.status === 'confirmed';
        const isRejected = data.status === 'rejected';

        // Main modal HTML
        modalBody.innerHTML = `
            <!-- Booking Dates -->
            <div class="info-card">
                <h3 class="text-lg font-semibold mb-3 text-gray-800">Booking Period</h3>
                <div class="responsive-grid">
                    <div>
                        <p class="text-sm">
                            <span class="font-medium text-gray-600">Check-in:</span>
                            <span class="ml-2">${formatDate(checkInDate)}</span>
                        </p>
                        <p class="text-sm mt-1">
                            <span class="font-medium text-gray-600">Check-out:</span>
                            <span class="ml-2">${formatDate(checkOutDate)}</span>
                        </p>
                        <p class="text-sm mt-1">
                            <span class="font-medium text-gray-600">Total Nights:</span>
                            <span class="ml-2">${totalNights}</span>
                        </p>
                    </div>
                    <div>
                        <p class="text-sm">
                            <span class="font-medium text-gray-600">Overall Total:</span>
                            <span class="ml-2 font-semibold text-lg">${formatPrice(overallTotal)}</span>
                        </p>
                        <p class="text-sm">
                            <span class="font-medium text-gray-600">50% in advance:</span>
                            <span class="ml-2 font-semibold text-lg">${formatPrice(overallTotal * 0.5)}</span>
                        </p>
                    </div>
                </div>
            </div>
            
            <!-- Guest Information -->
            <div class="info-card">
                <h3 class="text-lg font-semibold mb-3 text-gray-800">Guest Information</h3>
                <div class="responsive-grid">
                    <div>
                        <p class="text-sm">
                            <span class="font-medium text-gray-600">Name:</span>
                            <span class="ml-2">${data.user?.firstname || ''} ${data.user?.lastname || ''}</span>
                        </p>
                        <p class="text-sm mt-1">
                            <span class="font-medium text-gray-600">Email:</span>
                            <span class="ml-2">${data.user?.email || 'Not provided'}</span>
                        </p>
                    </div>
                    <div>
                        <p class="text-sm">
                            <span class="font-medium text-gray-600">Phone:</span>
                            <span class="ml-2">${data.user?.phone || 'Not provided'}</span>
                        </p>
                        <p class="text-sm mt-1">
                            <span class="font-medium text-gray-600">Status:</span>
                            <span class="ml-2 status-badge ${statusClass}">
                                ${statusText}
                            </span>
                        </p>
                    </div>
                </div>
            </div>
                
            <!-- Booking Details -->
            <h3 class="text-lg font-semibold mb-3 text-gray-800">Booked Facilities</h3>
            ${detailsHtml}
        `;

        // Footer HTML
        modalFooter.innerHTML = `
            <div class="mb-3 text-sm text-gray-600">
                <p>By confirming or rejecting this booking, an email will be sent to <span class="font-semibold">${data.user?.email || 'the guest'}</span></p>
                <div class="mt-2">
                    <label for="customMessage" class="block text-sm font-medium text-gray-700">Custom Message (optional):</label>
                    <textarea id="customMessage" rows="3" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-red-500 focus:border-red-500 sm:text-sm" placeholder="Add a custom message to include in the email notification"></textarea>
                </div>
            </div>
            <div class="flex justify-end space-x-2">
                <button onclick="closeModal()" class="btn btn-close">
                    Close
                </button>
                <button id="rejectBtn" onclick="handleRejectBooking('${data.id}')" 
                    ${isRejected || isConfirmed ? 'disabled' : ''}
                    class="btn ${isRejected || isConfirmed ? 'btn-disabled' : 'btn-reject'}" ${data.facilities?.length > 0 ? 'disabled' : ''}>
                    ${isRejected ? 'Already Rejected' : 'Reject Booking'}
                </button>
                <button id="confirmBtn" onclick="${isConfirmed ? '' : `handleConfirmBooking('${data.id}')`}" 
                    ${isRejected || isConfirmed ? 'disabled' : ''}
                    class="btn ${isRejected || isConfirmed ? 'btn-disabled' : 'btn-confirm'}" ${data.facilities?.length > 0 ? 'disabled' : ''}>
                    ${isConfirmed ? 'Already Confirmed' : 'Confirm Booking'}
                </button>
            </div>
        `;
        
        // Add event listeners to checkboxes if there are facilities
        if (data.facilities && data.facilities.length > 0) {
            const checkboxes = modalBody.querySelectorAll('.room-checkbox');
            const confirmBtn = modalFooter.querySelector('#confirmBtn');
            const rejectBtn = modalFooter.querySelector('#rejectBtn');
            
            // Function to check if all checkboxes are checked
            function checkAllBoxesChecked() {
                const allChecked = Array.from(checkboxes).every(checkbox => checkbox.checked);
                
                if (allChecked) {
                    confirmBtn.disabled = false;
                    rejectBtn.disabled = false;
                    confirmBtn.classList.remove('btn-disabled');
                    confirmBtn.classList.add('btn-confirm');
                    rejectBtn.classList.remove('btn-disabled');
                    rejectBtn.classList.add('btn-reject');
                } else {
                    confirmBtn.disabled = true;
                    rejectBtn.disabled = true;
                    confirmBtn.classList.remove('btn-confirm');
                    confirmBtn.classList.add('btn-disabled');
                    rejectBtn.classList.remove('btn-reject');
                    rejectBtn.classList.add('btn-disabled');
                }
            }
            
            // Add event listener to each checkbox
            checkboxes.forEach(checkbox => {
                checkbox.addEventListener('change', checkAllBoxesChecked);
            });
            
            // Initially disable buttons if not already confirmed/rejected
            if (!isConfirmed && !isRejected) {
                confirmBtn.disabled = true;
                rejectBtn.disabled = true;
                confirmBtn.classList.remove('btn-confirm');
                confirmBtn.classList.add('btn-disabled');
                rejectBtn.classList.remove('btn-reject');
                rejectBtn.classList.add('btn-disabled');
            }
        }
        
        // Show modal
        showModal();
    }

    // ========================
    // MODAL-SPECIFIC FUNCTIONS
    // ========================

    window.openModal_accept_inquirer = function(button) {
        if (!button) {
            console.error('Button element not provided');
            showErrorState(new Error('Button reference missing'));
            return;
        }
    
        const id = button.getAttribute('data-id');
        if (!id) {
            console.error('No booking ID found');
            showErrorState(new Error('Booking ID missing'));
            return;
        }
    
        markAsRead(id);
        
        showLoadingModal('Loading booking details...');
        
        // Add timeout for the fetch request
        const fetchTimeout = setTimeout(() => {
            showErrorState(new Error('Request timed out. Please try again.'));
        }, 10000); // 10 seconds timeout
    
        // Fetch booking details
        fetchBookingDetails(id)
            .then(data => {
                clearTimeout(fetchTimeout);
                populateModalWithData(data);
            })
            .catch(error => {
                clearTimeout(fetchTimeout);
                console.error('Error loading booking details:', error);
                showErrorState({
                    message: error.message || 'Failed to load booking details',
                    ...error
                });
            });
    };

    // ======================
    // DATA FETCHING FUNCTIONS
    // ======================

    async function fetchBookingDetails(id) {
        try {
            console.log('Fetching booking details for ID:', id);
            const response = await fetch(`/booking-details/${id}`, {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                }
            });
    
            console.log('Response status:', response.status);
            
            if (!response.ok) {
                const errorText = await response.text();
                console.error('Error response text:', errorText);
                throw new Error('Failed to fetch booking details');
            }
    
            const data = await response.json();
            console.log('Full response data:', data);
            
            if (!data.success) {
                throw new Error(data.message || 'Invalid response from server');
            }
    
            // Additional validation
            if (!data.data || typeof data.data !== 'object') {
                throw new Error('Invalid data format received');
            }
    
            return data.data;
        } catch (error) {
            console.error('Detailed fetch error:', {
                error: error.toString(),
                stack: error.stack,
                requestUrl: `/booking-details/${id}`
            });
            throw error;
        }
    }
    
    // Handle confirm booking button click
    window.handleConfirmBooking = function(bookingId) {
        if (!bookingId) {
            showErrorState(new Error('Invalid booking reference'));
            return;
        }
        
        const modalBody = document.querySelector('#bookdetailsmodalContainer .modal-body');
        const modalFooter = document.querySelector('#bookdetailsmodalContainer .modal-footer');
        const customMessage = document.getElementById('customMessage')?.value || '';
        
        if (!modalBody || !modalFooter) {
            console.error('Modal elements not found');
            return;
        }
    
        // Show loading state
        showLoadingModal("Confirming booking and sending email...");
    
        // Make AJAX request to confirm booking
        fetch(`/confirm-booking/${bookingId}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
            },
            body: JSON.stringify({
                send_email: true,
                custom_message: customMessage
            })
        })
        .then(async response => {
            const data = await response.json();
            if (!response.ok) throw new Error(data.message || 'Failed to confirm booking');
            return data;
        })
        .then(data => {
            modalBody.innerHTML = `
                <div class="flex flex-col items-center justify-center py-8">
                    <div class="text-center text-green-500">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        <h3 class="text-xl font-bold mt-2">Booking Confirmed!</h3>
                        <p class="mt-2 text-gray-600">${data.message || 'The booking has been confirmed'}</p>
                    </div>
                </div>
            `;
            
            modalFooter.innerHTML = `
                <div class="flex justify-end">
                    <button onclick="closeModal(); window.location.reload();" class="btn btn-confirm">
                        Close
                    </button>
                </div>
            `;
        })
        .catch(error => {
            console.error('Error:', error);
            showErrorState(error);
        });
    };
    
    window.handleRejectBooking = function(bookingId) {
        if (!bookingId) {
            showErrorState(new Error('Invalid booking reference'));
            return;
        }
        
        const modalBody = document.querySelector('#bookdetailsmodalContainer .modal-body');
        const modalFooter = document.querySelector('#bookdetailsmodalContainer .modal-footer');
        const customMessage = document.getElementById('customMessage')?.value || '';
        
        if (!modalBody || !modalFooter) {
            console.error('Modal elements not found');
            return;
        }
    
        // Show confirmation dialog before rejecting
        const shouldReject = confirm('Are you sure you want to reject this booking? This action cannot be undone.');
        if (!shouldReject) return;
    
        // Show loading state
        showLoadingModal("Rejecting booking...");
    
        // Make AJAX request to reject booking
        fetch(`/reject-booking/${bookingId}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
            },
            body: JSON.stringify({
                custom_message: customMessage
            })
        })
        .then(async response => {
            // First check if the response is JSON
            const contentType = response.headers.get('content-type');
            if (!contentType || !contentType.includes('application/json')) {
                const text = await response.text();
                throw new Error(`Expected JSON but got: ${text.substring(0, 100)}...`);
            }
            
            const data = await response.json();
            if (!response.ok) {
                throw new Error(data.message || 'Failed to reject booking');
            }
            return data;
        })
        .then(data => {
            modalBody.innerHTML = `
                <div class="flex flex-col items-center justify-center py-8">
                    <div class="text-center text-gray-600">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-12.728 12.728M5.636 5.636l12.728 12.728" />
                        </svg>
                        <h3 class="text-xl font-bold mt-2">Booking Rejected</h3>
                        <p class="mt-2 text-gray-600">${data.message || 'The booking has been rejected'}</p>
                    </div>
                </div>
            `;
            
            modalFooter.innerHTML = `
                <div class="flex justify-end">
                    <button onclick="closeModal(); window.location.reload();" class="btn btn-reject">
                        Close
                    </button>
                </div>
            `;
        })
        .catch(error => {
            console.error('Error:', error);
            showErrorState(error);
        });
    };
    
    function markAsRead(bookingId) {
        fetch(`/api/inquiries/mark-read/${bookingId}`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                updateNewBookingsCount(data.newCount);
                
                const bookingItem = document.querySelector(`[data-id="${bookingId}"]`);
                if (bookingItem) {
                    bookingItem.classList.remove('bg-red-50', 'animate-pulse');
                    const unreadBadge = bookingItem.querySelector('.unread-badge');
                    if (unreadBadge) unreadBadge.remove();
                }
            }
        })
        .catch(error => {
            console.error('Error marking as read:', error);
        });
    }

    function updateNewBookingsCount(count) {
        const counterElement = document.getElementById('newBookingsCount');
        if (counterElement) {
            counterElement.textContent = count;
            counterElement.classList.toggle('hidden', count <= 0);
        }
    }
</script>