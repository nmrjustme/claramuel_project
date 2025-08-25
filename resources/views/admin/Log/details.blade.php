@extends('layouts.admin')
@section('title', 'Booking Details')
@php
$active = 'inquiries';
@endphp

@section('content')
<div class="min-h-screen px-6 py-6">
     
     <a href="{{ route('admin.inquiries') }}"
          class="inline-flex items-center justify-center border border-blue-200 mb-4 px-4 py-2 text-blue-700 rounded-xl hover:text-blue-500 font-medium transition-colors duration-200">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24"
               stroke="currentColor">
               <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
          </svg>

          Back to Inquiries
     </a>
     
     
     <div class="text-gray-800 p-6 rounded-t-xl mb-0">
          <div class="flex items-center">
               <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                         d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
               </svg>
               <h1 class="text-2xl font-bold">Booking Details</h1>
          </div>
     </div>
     
     <div class="p-6 grid grid-cols-1 lg:grid-cols-[1.5fr_1fr] gap-6" id="booking-details-content">
          <div class="flex flex-col items-center justify-center min-h-[300px]">
               <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-red-600 mx-auto"></div>
               <p class="mt-4 text-gray-600">Loading booking details...</p>
          </div>
     </div>
</div>

<!-- Payment Information Modal -->
<div id="paymentModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm hidden items-center justify-center z-50 p-4">
     <div class="bg-white rounded-2xl shadow-lg max-w-2xl w-full max-h-[90vh] overflow-y-auto">
          <div class="p-6">
               <div class="flex justify-between items-center mb-6">
                    <h3 class="text-2xl font-bold text-gray-900">Payment Sent Information</h3>
                    <span class="text-gray-500 text-3xl cursor-pointer hover:text-gray-700" onclick="closePaymentModal()">&times;</span>
               </div>
               <div id="paymentModalContent"></div>
          </div>
     </div>
</div>

<!-- Image Modal -->
<div id="imageModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm hidden items-center justify-center z-50">
     <span class="absolute top-5 right-8 text-white text-6xl cursor-pointer" onclick="closeImageModal()">&times;</span>
     <img id="modalImage" src="" class="max-h-[90%] max-w-[90%] rounded-lg shadow-lg">
</div>

<!-- Confirmation Modal -->
<div id="confirmationModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm hidden items-center justify-center z-50 p-4">
     <div class="bg-white rounded-2xl shadow-lg max-w-md w-full">
          <div class="p-6">
               <div class="flex justify-center mb-4">
                    <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-red-600"></div>
               </div>
               <h3 class="text-xl font-bold text-center text-gray-900 mb-2" id="confirmationModalTitle">Processing...</h3>
               <p class="text-gray-600 text-center" id="confirmationModalMessage">Please wait while we process your request.</p>
          </div>
     </div>
</div>

<!-- Result Modal -->
<div id="resultModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm hidden items-center justify-center z-50 p-4">
     <div class="bg-white rounded-2xl shadow-lg max-w-md w-full">
          <div class="p-6 text-center">
               <div id="resultIcon" class="flex justify-center mb-4">
                    <!-- Icon will be inserted here -->
               </div>
               <h3 class="text-xl font-bold text-gray-900 mb-2" id="resultModalTitle">Result</h3>
               <p class="text-gray-600 mb-6" id="resultModalMessage">Operation completed.</p>
               <div class="flex justify-center">
                    <button onclick="closeResultModal()" class="px-6 py-2 bg-red-600 text-white rounded-xl hover:bg-red-700 font-medium transition-colors duration-200">
                         OK
                    </button>
               </div>
          </div>
     </div>
</div>
@endsection

@section('content_js')
<script>
     document.addEventListener('DOMContentLoaded', function() {
               // Get booking ID from URL
               const urlParams = new URLSearchParams(window.location.search);
               const bookingId = "{{ $bookingId }}";
               
               if (bookingId) {
                    loadBookingDetails(bookingId);
               } else {
                    showErrorState({ message: 'No booking ID provided in URL' });
               }
          });
          
          // Format date function
          function formatDate(dateString) {
               if (!dateString) return 'Not specified';
               const options = { year: 'numeric', month: 'short', day: 'numeric' };
               return new Date(dateString).toLocaleDateString('en-US', options);
          }
          
          // Format price function
          function formatPrice(price) {
               if (price === null || price === undefined || isNaN(price)) return 'Not available';
               return new Intl.NumberFormat('en-PH', {
                    style: 'currency',
                    currency: 'PHP',
                    minimumFractionDigits: 2
               }).format(price).replace('PHP', '₱');
          }
          
          // Calculate nights between two dates
          function calculateNights(checkIn, checkOut) {
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
          }
          
          function showLoadingState(message = 'Loading...') {
               const contentDiv = document.getElementById('booking-details-content');
               contentDiv.innerHTML = `
                    <div class="flex flex-col items-center justify-center min-h-[300px]">
                         <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-red-600 mx-auto"></div>
                         <p class="mt-4 text-gray-600">${message}</p>
                    </div>
               `;
          }
          
          function showErrorState(error) {
               const contentDiv = document.getElementById('booking-details-content');
               contentDiv.innerHTML = `
                    <div class="flex flex-col items-center justify-center min-h-[300px] text-center">
                         <div class="text-center text-red-500">
                              <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                              </svg>
                         <h3 class="text-xl font-bold mt-2">Error</h3>
                              <p class="mt-2 text-gray-600">${error.message || 'Please try again later'}</p>
                         </div>
                         <a href="{{ route('admin.inquiries') }}"
                              class="inline-flex items-center justify-center border border-blue-200 px-5 py-2.5 text-blue-700 rounded-xl hover:text-blue-500 font-medium transition-colors duration-200">
                              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                              </svg>
                              
                              Back to Inquiries
                         </a>
                    </div>
               `;
          }
          
          async function loadBookingDetails(bookingId) {
               showLoadingState('Loading booking details...');
               
               try {
                    const response = await fetch(`/booking-details/${bookingId}`, {
                         headers: {
                         'Accept': 'application/json',
                         'X-Requested-With': 'XMLHttpRequest',
                         'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                         }
                    });
                    
                    if (!response.ok) {
                         const errorText = await response.text();
                         throw new Error('Failed to fetch booking details');
                    }
                    
                    const data = await response.json();
                    
                    if (!data.success) {
                         throw new Error(data.message || 'Invalid response from server');
                    }
                    
                    // Additional validation
                    if (!data.data || typeof data.data !== 'object') {
                         throw new Error('Invalid data format received');
                    }
                    
                    populatePageWithData(data.data);
               } catch (error) {
                    console.error('Error loading booking details:', error);
                    showErrorState({
                         message: error.message || 'Failed to load booking details',
                         ...error
                    });
               }
          }
          
          function populatePageWithData(data) {
               const contentDiv = document.getElementById('booking-details-content');
               
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
               
               // Get the first facility's dates (they should all be the same)
               const checkInDate = data.facilities?.[0]?.check_in;
               const checkOutDate = data.facilities?.[0]?.check_out;
               const reservationCode = data.code;
               
               // Calculate total nights
               const totalNights = calculateNights(checkInDate, checkOutDate);

               // Calculate overall total price
               const overallTotal = data.total_price || 0;

               // Determine status class
               let statusClass = 'bg-yellow-100 text-yellow-800';
               let statusText = data.status || 'pending';
               
               if (data.status === 'confirmed') {
                    statusClass = 'bg-green-100 text-green-800';
                    statusText = 'confirmed';
               } else if (data.status === 'rejected') {
                    statusClass = 'bg-red-100 text-red-800';
                    statusText = 'rejected';
               } else if (data.status === 'pending_confirmation') {
                    statusClass = 'bg-yellow-100 text-yellow-800';
                    statusText = 'Pending';
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
                    <div class="bg-white p-6 rounded-2xl shadow-md border border-gray-100 mb-6">
                         <!-- Header -->
                         <div class="flex justify-between items-start mb-4">
                         <div>
                              <h4 class="text-xl font-bold text-gray-900">
                              ${facility.facility_name || 'Facility not specified'}
                              </h4>
                              <span class="inline-block mt-2 px-2.5 py-1 bg-green-100 text-green-700 rounded-full text-xs font-medium">
                              ${facility.facility_category || 'No category'}
                              </span>

                              <div class="mt-3 space-y-1">
                              <p class="text-sm">
                              <span class="font-medium text-gray-600">Room:</span>
                              <span class="ml-2 text-gray-800">${facility.room_info.room_number || 'N/A'}</span>
                              </p>
                              <p class="text-sm">
                              <span class="font-medium text-gray-600">Night(s):</span>
                              <span class="ml-2 text-gray-800">${facilityNights}</span>
                              </p>
                              </div>
                         </div>

                         <!-- Price per night -->
                         <span class="px-4 py-1 bg-blue-50 text-blue-700 text-sm font-semibold rounded-full shadow-sm">
                              ${formatPrice(facility.room_info.price_per_night)}/night
                         </span>
                         </div>
                         
                         <!-- Guest Composition -->
                         <div class="mt-4 border-t pt-4">
                         <h5 class="text-sm font-semibold text-gray-700 mb-2 uppercase tracking-wide">
                              Guest Composition
                         </h5>
                         <ul class="space-y-1 text-sm text-gray-800">
                              ${guestDetailsHtml}
                         </ul>
                         </div>

                         <!-- Meal Plan (if applicable) -->
                         ${hasMeal ? `
                         <div class="mt-4 bg-green-50 p-4 rounded-xl border border-green-200">
                              <h5 class="text-sm font-semibold text-gray-700 mb-2">Meal Plan</h5>
                              <p class="text-sm text-gray-800">${facility.breakfast}</p>
                              <p class="text-sm mt-1">
                                        <span class="font-medium text-gray-600">Price:</span>
                                        <span class="ml-2">${formatPrice(facility.breakfast_price)}/morning</span>
                              </p>
                              <p class="text-sm">
                                        <span class="font-medium text-gray-600">Total:</span>
                                        <span class="ml-2 font-medium">${formatPrice(facility.breakfast_price * facilityNights)}</span>
                              </p>
                         </div>
                         ` : ''}
                         
                         <!-- Subtotal -->
                         <div class="mt-4 pt-4 border-t border-ligtgray flex justify-between items-center">
                              <span class="text-sm font-medium text-gray-600">Subtotal</span>
                              <span class="text-lg font-bold text-gray-900">${formatPrice(facility.total_price)}</span>
                         </div>

                         <!-- Review Checkbox -->
                         <div class="flex items-center mt-4 p-3 bg-gray-50 rounded-lg border border-lightgray">
                              <input type="checkbox" id="room-checkbox-${index}"
                                        class="mr-2 room-checkbox h-4 w-4 text-red-600 focus:ring-red-500 border-gray-300 rounded"
                                        data-room="${facility.facility_name || 'Room ' + (index + 1)}"
                                        ${isConfirmed || isRejected ? 'disabled checked' : ''}>
                              <label for="room-checkbox-${index}" class="text-sm text-gray-700">
                                        I have reviewed <span class="font-medium">${facility.facility_name || 'this room'}</span> details
                              </label>
                         </div>
                    </div>
                    
                    `;
               }).join('') || '<p class="text-gray-500 italic">No facilities booked</p>';
               
               // Check if booking is already confirmed or rejected
               const isConfirmed = data.status === 'confirmed';
               const isRejected = data.status === 'rejected';
               
               // Create receipt HTML
               let receiptHtml = '';
               if (data.payment && data.payment.receipt_path) {
                    receiptHtml = `
                         <div class="flex flex-col">
                              ${data.payment && data.payment.receipt_path ? `
                                   <img src="${window.location.origin}/${data.payment.receipt_path}"
                                        alt="Payment Receipt"
                                        class="w-full max-h-[550px] object-contain rounded-md cursor-pointer"
                                        onclick="openImageModal('${window.location.origin}/${data.payment.receipt_path}')">
                              ` : `
                                   <div class="flex items-center justify-center h-48 bg-gray-100 rounded-md border border-gray-200">
                                        <p class="text-gray-500 italic">No receipt uploaded</p>
                                   </div>
                              `}
                         </div>
                    `;
               } else {
                    receiptHtml = `
                         <div class="flex flex-col">
                              <h4 class="text-sm font-medium text-gray-700 mb-2">Receipt</h4>
                              <img src="${window.location.origin}/${data.payment.receipt_path}"
                                   alt="Payment Receipt"
                                   class="w-full max-h-[350px] object-contain rounded-md border cursor-pointer hover:shadow-lg transition"
                                   onclick="openImageModal('${window.location.origin}/${data.payment.receipt_path}')">
                         </div>
                    `;
               }
               
               // Create payment modal content
               let paymentModalContent = '';
               if (data.payment && data.payment.amount !== null && data.payment.amount !== undefined) {
               paymentModalContent = `
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                         
                         <!-- Payment Information -->
                         <div class="space-y-4">
                              <div class="bg-gray-50 p-4 rounded-lg">
                                   <p class="text-sm text-gray-600">To be paid:</p>
                                   <p class="text-xl font-bold text-green-800">${formatPrice(data.payment.amount)}</p>
                              </div>
                              
                              ${data.payment.gcash_number ? `
                              <div class="bg-gray-50 p-4 rounded-lg">
                                   <p class="text-sm text-gray-600">GCash Number:</p>
                                   <p class="text-lg font-semibold text-gray-800">${data.payment.gcash_number}</p>
                              </div>
                              ` : ''}

                              ${data.payment.reference_no ? `
                              <div class="bg-gray-50 p-4 rounded-lg">
                                   <p class="text-sm text-gray-600">Reference No:</p>
                                   <p class="text-lg font-semibold text-gray-800">${data.payment.reference_no}</p>
                              </div>
                              ` : ''}

                              ${data.payment.payment_date ? `
                              <div class="bg-gray-50 p-4 rounded-lg">
                                   <p class="text-sm text-gray-600">Payment Date:</p>
                                   <p class="text-lg font-semibold text-gray-800">${formatDate(data.payment.payment_date)}</p>
                              </div>
                              ` : ''}
                         </div>

                         <!-- Receipt -->
                         <div>
                              <h4 class="text-lg font-semibold mb-2">Receipt</h4>
                              ${data.payment.receipt_path ? `
                              <img src="${window.location.origin}/${data.payment.receipt_path}"
                                   alt="Payment Receipt"
                                   class="w-full max-h-[600px] object-contain rounded-md cursor-pointer"
                                   onclick="openImageModal('${window.location.origin}/${data.payment.receipt_path}')">
                              ` : `
                              <div class="flex items-center justify-center h-64 bg-gray-100 rounded-md border border-gray-200">
                                   <p class="text-gray-500 italic">No receipt uploaded</p>
                              </div>
                              `}
                         </div>
                    </div>
               `;
               } else {
               paymentModalContent = `
                    <div class="text-center py-8">
                         <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto text-gray-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                         </svg>
                         <p class="text-gray-500 text-lg">No payment information available</p>
                    </div>
               `;
               }
               
               
               // Store payment modal content for later use
               window.paymentModalContent = paymentModalContent;
               
               let paymentHtml = '';
               if (data.payment && data.payment.amount !== null && data.payment.amount !== undefined) {
                    paymentHtml = `
                    <div class="bg-green-50 p-6 rounded-2xl mb-6 border border-green-200 shadow-sm sticky top-4">
                         <h3 class="text-2xl font-bold text-gray-900 mb-6 flex items-center">Payment Sent Information</h3>
                         <div class="grid grid-cols-1 lg:grid-cols-2 gap-2">
                              <div class="space-y-3">
                                   <div class="flex justify-between pb-3 border-b border-gray-200">
                                   <span class="font-medium text-gray-600">To be paid:</span>
                                   <span class="font-semibold text-green-800">${formatPrice(data.payment.amount)}</span>
                                   </div>
                                   ${data.payment.gcash_number ? `
                                   <div class="flex justify-between pb-3 border-b border-gray-200">
                                   <span class="font-medium text-gray-600">GCash Number:</span>
                                   <span class="font-semibold text-green-800">${data.payment.gcash_number}</span>
                                   </div>
                                   ` : ''}
                                   ${data.payment.reference_no ? `
                                   <div class="flex justify-between pb-3 border-b border-gray-200">
                                   <span class="font-medium text-gray-600">Reference No:</span>
                                   <span class="font-semibold text-green-800">${data.payment.reference_no}</span>
                                   </div>
                                   ` : ''}
                                   ${data.payment.payment_date ? `
                                   <div class="flex justify-between pb-3 border-b border-gray-200">
                                   <span class="font-medium text-gray-600">Payment Date:</span>
                                   <span class="font-semibold text-green-800">${formatDate(data.payment.payment_date)}</span>
                                   </div>
                                   ` : ''}
                                   <div class="flex justify-between pb-3 border-b border-gray-200">
                                        <label class="font-medium text-gray-600" for="amountPaid">Amount Paid:</label>
                                        <input 
                                             type="number" 
                                             id="amountPaid"
                                             class="font-semibold text-green-800 border rounded px-2 py-1 w-32 text-right"
                                             value="${data.payment.amount_paid ? data.payment.amount_paid : ''}"
                                             placeholder="0.00"
                                             min="0"
                                             step="0.01"
                                        >
                                   </div>
                              
                              </div>
                              ${receiptHtml}
                         </div>
                    </div>
                    `;
               } else {
                    paymentHtml = `
                    <div class="bg-blue-50 p-6 rounded-xl mb-6 border border-blue-200 shadow-sm sticky top-4">
                         <h3 class="text-lg font-semibold mb-3 text-gray-800">Payment Sent Information</h3>
                         <p class="text-gray-500 italic">No payment information available</p>
                         ${receiptHtml}
                    </div>
                    `;
               }
               
               // Main page HTML with two columns
               contentDiv.innerHTML = `
                    <div class="pr-6">   

                         <div class="bg-gradient-to-br from-red-600 to-red-800 p-8 rounded-2xl shadow-xl border border-red-300 mb-6 relative overflow-hidden">
                         <!-- Decorative elements -->
                         <div class="absolute -top-4 -right-4 w-24 h-24 rounded-full bg-red-400 opacity-20"></div>
                         <div class="absolute bottom-0 left-0 w-16 h-16 rounded-full bg-red-300 opacity-10"></div>
                         
                         <!-- Section Title -->
                         <h3 class="text-2xl font-bold text-white mb-6 flex items-center relative z-10">
                              <svg class="w-7 h-7 text-red-200 mr-3" fill="none" stroke="currentColor" stroke-width="2"
                                   viewBox="0 0 24 24">
                                   <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M5.121 17.804A13.937 13.937 0 0112 15c2.5 0 4.847.655 6.879 1.804M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                              </svg>
                              Guest Information
                         </h3>
                         
                         <!-- Grid Layout -->
                         <div class="grid grid-cols-1 md:grid-cols-2 gap-8 relative z-10">
                              <!-- Left Column -->
                              <div class="space-y-6">
                                   <div class="bg-red-500/20 p-4 rounded-lg backdrop-blur-sm border border-red-400/30">
                                        <p class="text-sm text-red-100 font-medium">Name</p>
                                        <p class="text-lg font-semibold text-white mt-1">
                                             ${data.user?.firstname || ''} ${data.user?.lastname || ''}
                                        </p>
                                   </div>
                                   <div class="bg-red-500/20 p-4 rounded-lg backdrop-blur-sm border border-red-400/30">
                                        <p class="text-sm text-red-100 font-medium">Email</p>
                                        <p class="text-lg font-medium text-white mt-1">
                                             ${data.user?.email || 'Not provided'}
                                        </p>
                                   </div>
                              </div>
                              
                              <!-- Right Column -->
                              <div class="space-y-6">
                                   <div class="bg-red-500/20 p-4 rounded-lg backdrop-blur-sm border border-red-400/30">
                                        <p class="text-sm text-red-100 font-medium">Phone</p>
                                        <p class="text-lg font-medium text-white mt-1">
                                             ${data.user?.phone || 'Not provided'}
                                        </p>
                                   </div>
                                   <div class="bg-red-500/20 p-4 rounded-lg backdrop-blur-sm border border-red-400/30">
                                        <p class="text-sm text-red-100 font-medium">Status</p>
                                        <span class="inline-flex items-center px-4 py-1.5 rounded-full text-sm font-semibold ${statusClass} mt-1 border border-red-300/30">
                                             ${statusText}
                                        </span>
                                   </div>
                              </div>
                         </div>
                         </div>

                         <!-- Reservation Code -->
                         <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 mb-6">
                              <!-- Section Title -->
                              <h3 class="text-2xl font-bold text-gray-900 mb-4 flex items-center gap-2">
                                   <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                             d="M9 12h6m2 0a2 2 0 100-4H7a2 2 0 100 4h10zm0 0a2 2 0 110 4H7a2 2 0 110-4h10z" />
                                   </svg>
                                   Reservation Code
                              </h3>
                              
                              <div class="mt-4">
                                   <p class="mt-2 text-3xl font-mono font-bold tracking-widest text-center text-blue-700 bg-blue-50 px-6 py-4 rounded-xl border border-blue-300 shadow-sm">
                                        ${reservationCode}
                                   </p>
                              </div>
                         </div>
                         
                         
                         <!-- Booking Dates -->
                         <div class="bg-white p-6 rounded-2xl shadow-md border border-gray-100 mb-6">
                              <!-- Section Title -->
                              <h3 class="text-2xl font-bold text-gray-900 mb-6 flex items-center">
                              <svg class="w-6 h-6 text-emerald-500 mr-2" fill="none" stroke="currentColor" stroke-width="2"
                                   viewBox="0 0 24 24">
                                   <path stroke-linecap="round" stroke-linejoin="round"
                                             d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                              </svg>
                              Booking Period
                              </h3>

                              <!-- Grid Layout -->
                              <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                                   <!-- Left Column -->
                                   <div class="space-y-4">
                                        <div>
                                        <p class="text-sm text-gray-500">Check-in</p>
                                        <p class="text-lg font-semibold text-gray-900">
                                        ${formatDate(checkInDate)}
                                        </p>
                                        </div>
                                        <div>
                                        <p class="text-sm text-gray-500">Check-out</p>
                                        <p class="text-lg font-semibold text-gray-900">
                                        ${formatDate(checkOutDate)}
                                        </p>
                                        </div>
                                        <div>
                                        <p class="text-sm text-gray-500">Total Nights</p>
                                        <p class="text-lg font-medium text-gray-800">
                                        ${totalNights}
                                        </p>
                                        </div>
                                   </div>

                                   <!-- Right Column -->
                                   <div class="space-y-4">
                                        <div>
                                        <p class="text-sm text-gray-500">Overall Total</p>
                                        <p class="text-xl font-bold text-gray-900">
                                        ${formatPrice(overallTotal)}
                                        </p>
                                        </div>
                                        <div>
                                        <p class="text-sm text-gray-500">50% in Advance</p>
                                        <p class="text-xl font-semibold text-emerald-600">
                                        ${formatPrice(overallTotal * 0.5)}
                                        </p>
                                        </div>
                                   </div>
                              </div>
                         </div>
                         
                         <!-- Booking Details -->
                         <!-- Title -->
                         <h3 class="text-2xl font-bold text-gray-900 mb-6 flex items-center">
                              <svg class="w-6 h-6 text-blue-500 mr-2" fill="none" stroke="currentColor" stroke-width="2"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                             d="M4 6h16M4 10h16M4 14h10M4 18h6" />
                              </svg>
                              Booked Facilities
                         </h3>

                         <!-- Facilities List -->
                         <div class="space-y-3">
                         ${detailsHtml}
                         </div>

                         <!-- Custom Message -->
                         <div class="mt-8">
                              <p class="text-sm text-gray-600">
                                        By confirming or rejecting this booking, an email will be sent to 
                                        <span class="font-semibold text-gray-900">${data.user?.email || 'the guest'}</span>.
                              </p>
                              <div class="mt-3">
                                        <label for="customMessage" class="block text-sm font-medium text-gray-700">
                                        Custom Message (optional)
                                        </label>
                                        <textarea id="customMessage" rows="3"
                                        class="mt-2 block w-full border border-gray-300 rounded-xl shadow-sm py-2 px-3 text-sm focus:outline-none focus:ring-red-500 focus:border-red-500"
                                        placeholder="Add a custom message to include in the email notification"></textarea>
                              </div>
                         </div>

                         <!-- Action Buttons -->
                         <div class="flex justify-end gap-3 mt-10 pt-6 border-t border-gray-200">
                              <a href="{{ route('admin.inquiries') }}"
                                        class="inline-flex items-center justify-center border border-blue-200 px-5 py-2.5 text-blue-700 rounded-xl hover:text-blue-500 font-medium transition-colors duration-200">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                             <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                                        </svg>
                                        
                                        Back to Inquiries
                              </a>
                              <button id="rejectBtn"
                                        onclick="handleRejectBooking('${data.id}')"
                                        ${isRejected || isConfirmed ? 'disabled' : ''}
                                        class="inline-flex items-center justify-center px-5 py-2.5 rounded-xl font-semibold transition-colors duration-200
                                        ${isRejected || isConfirmed 
                                        ? 'bg-gray-200 text-gray-500 cursor-not-allowed' 
                                        : 'bg-gray-600 text-white hover:bg-gray-700'}">
                                        ${isRejected ? 'Already Rejected' : 'Reject Booking'}
                              </button>
                              <button id="confirmBtn"
                                        onclick="${isConfirmed ? '' : `handleConfirmBooking('${data.id}')`}"
                                        ${isRejected || isConfirmed ? 'disabled' : ''}
                                        class="inline-flex items-center justify-center px-5 py-2.5 rounded-xl font-semibold transition-colors duration-200
                                        ${isRejected || isConfirmed 
                                        ? 'bg-gray-200 text-gray-500 cursor-not-allowed' 
                                        : 'bg-red-600 text-white hover:bg-red-700'}">
                                        ${isConfirmed ? 'Already Confirmed' : 'Confirm Booking'}
                              </button>
                         </div>
                    
                    </div>
                    
                    <div class="rounded-xl self-start">
                         ${paymentHtml}
                    </div>
               `;
               
               // Add event listeners to checkboxes if there are facilities
               if (data.facilities && data.facilities.length > 0) {
                    const checkboxes = contentDiv.querySelectorAll('.room-checkbox');
                    const confirmBtn = contentDiv.querySelector('#confirmBtn');
                    const rejectBtn = contentDiv.querySelector('#rejectBtn');
                    
                    // Function to check if all checkboxes are checked
                    function checkAllBoxesChecked() {
                    const allChecked = Array.from(checkboxes).every(checkbox => checkbox.checked);
                    
                    if (allChecked) {
                    confirmBtn.disabled = false;
                    rejectBtn.disabled = false;
                    confirmBtn.classList.remove('bg-gray-300', 'text-gray-500', 'cursor-not-allowed');
                    confirmBtn.classList.add('bg-red-600', 'text-white', 'hover:bg-red-700');
                    rejectBtn.classList.remove('bg-gray-300', 'text-gray-500', 'cursor-not-allowed');
                    rejectBtn.classList.add('bg-gray-600', 'text-white', 'hover:bg-gray-700');
                    } else {
                    confirmBtn.disabled = true;
                    rejectBtn.disabled = true;
                    confirmBtn.classList.remove('bg-red-600', 'text-white', 'hover:bg-red-700');
                    confirmBtn.classList.add('bg-gray-300', 'text-gray-500', 'cursor-not-allowed');
                    rejectBtn.classList.remove('bg-gray-600', 'text-white', 'hover:bg-gray-700');
                    rejectBtn.classList.add('bg-gray-300', 'text-gray-500', 'cursor-not-allowed');
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
                    confirmBtn.classList.remove('bg-red-600', 'text-white', 'hover:bg-red-700');
                    confirmBtn.classList.add('bg-gray-300', 'text-gray-500', 'cursor-not-allowed');
                    rejectBtn.classList.remove('bg-gray-600', 'text-white', 'hover:bg-gray-700');
                    rejectBtn.classList.add('bg-gray-300', 'text-gray-500', 'cursor-not-allowed');
                    }
               }
          }

          // Show confirmation modal
          function showConfirmationModal(title, message) {
               const modal = document.getElementById('confirmationModal');
               const titleElement = document.getElementById('confirmationModalTitle');
               const messageElement = document.getElementById('confirmationModalMessage');
               
               titleElement.textContent = title;
               messageElement.textContent = message;
               
               modal.classList.remove('hidden');
               modal.classList.add('flex');
               document.body.style.overflow = 'hidden';
          }
          
          // Close confirmation modal
          function closeConfirmationModal() {
               const modal = document.getElementById('confirmationModal');
               modal.classList.remove('flex');
               modal.classList.add('hidden');
               document.body.style.overflow = 'auto';
          }
          
          // Show result modal
          function showResultModal(title, message, isSuccess) {
               const modal = document.getElementById('resultModal');
               const titleElement = document.getElementById('resultModalTitle');
               const messageElement = document.getElementById('resultModalMessage');
               const iconElement = document.getElementById('resultIcon');
               
               titleElement.textContent = title;
               messageElement.textContent = message;
               
               // Set appropriate icon based on success/failure
               if (isSuccess) {
                    iconElement.innerHTML = `
                         <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                         </svg>
                    `;
               } else {
                    iconElement.innerHTML = `
                         <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                         </svg>
                    `;
               }
               
               modal.classList.remove('hidden');
               modal.classList.add('flex');
               document.body.style.overflow = 'hidden';
          }
          
          // Close result modal
          function closeResultModal() {
               const modal = document.getElementById('resultModal');
               modal.classList.remove('flex');
               modal.classList.add('hidden');
               document.body.style.overflow = 'auto';
               
               // Reload the page to reflect the updated status
               window.location.reload();
          }

          // Handle confirm booking button click
          async function handleConfirmBooking(bookingId) {
               if (!bookingId) {
                    alert('Invalid booking reference');
                    return;
               }

               const amountPaidInput = document.getElementById('amountPaid');
               const amountPaid = amountPaidInput ? parseFloat(amountPaidInput.value) : 0;
               const customMessage = document.getElementById('customMessage')?.value || '';
               if (!amountPaid || amountPaid <= 0 || isNaN(amountPaid)) {
                         alert('Please enter a valid amount paid before confirming the booking.');
                         if (amountPaidInput) {
                              amountPaidInput.focus();
                              amountPaidInput.classList.add('border-red-500');
                         }
                         return;
               }
               if (!confirm('Are you sure you want to confirm this booking?')) {
                    return;
               }
               
               // Show confirmation modal
               showConfirmationModal("Confirming Booking", "Confirming booking and sending email...");
               
               try {
                    // Make AJAX request to confirm booking
                    const response = await fetch(`/bookings/${bookingId}/verify-with-receipt`, {
                    method: 'POST',
                         headers: {
                              'Content-Type': 'application/json',
                              'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                         },
                         body: JSON.stringify({
                              send_email: true,
                              custom_message: customMessage,
                              amount_paid: amountPaid
                         })
                    });
                    
                    const data = await response.json();
                    
                    if (!response.ok) {
                    throw new Error(data.message || 'Failed to confirm booking');
                    }
                    
                    // Close confirmation modal and show success modal
                    closeConfirmationModal();
                    showResultModal(
                         "Booking Confirmed!", 
                         data.message || 'The booking has been confirmed and the guest has been notified.',
                         true
                    );
               } catch (error) {
                    console.error('Error:', error);
                    // Close confirmation modal and show error modal
                    closeConfirmationModal();
                    showResultModal(
                         "Error", 
                         error.message || 'Failed to confirm booking. Please try again.',
                         false
                    );
               }
          }

     async function handleRejectBooking(bookingId) {
          if (!bookingId) {
               alert('Invalid booking reference');
               return;
          }
          
          const customMessage = document.getElementById('customMessage')?.value || '';
          
          if (!confirm('Are you sure you want to reject this booking? This action cannot be undone.')) {
               return;
          }
          
          // Show confirmation modal
          showConfirmationModal("Rejecting Booking", "Rejecting booking and notifying guest...");
     
          try {
               // Make AJAX request to reject booking
               const response = await fetch(`/reject-booking/${bookingId}`, {
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
               });
               
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
               
               // Close confirmation modal and show success modal
               closeConfirmationModal();
               showResultModal(
                    "Booking Rejected", 
                    data.message || 'The booking has been rejected and the guest has been notified.',
                    true
               );
          } catch (error) {
               console.error('Error:', error);
               // Close confirmation modal and show error modal
               closeConfirmationModal();
               showResultModal(
                    "Error", 
                    error.message || 'Failed to reject booking. Please try again.',
                    false
               );
          }
     }
     
     // Image Modal Functions
     function openImageModal(imageSrc) {
          const modal = document.getElementById('imageModal');
          const modalImage = document.getElementById('modalImage');
          
          modalImage.src = imageSrc;
          modal.classList.remove('hidden');
          modal.classList.add('flex');
          document.body.style.overflow = 'hidden';
     }
     
     function closeImageModal() {
          const modal = document.getElementById('imageModal');
          modal.classList.remove('flex');
          modal.classList.add('hidden');
          document.body.style.overflow = 'auto';
     }
     
     // Close image modal when clicking outside
     document.getElementById('imageModal').addEventListener('click', function(e) {
          if (e.target === this) {
               closeImageModal();
          }
     });
</script>
@endsection