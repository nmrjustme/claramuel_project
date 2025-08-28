@extends('layouts.admin')
@section('title', 'Booking Details')
@php
$active = 'inquiries';
@endphp

@section('content_css')
<style>
     #sticky-payment {
          position: sticky;
          align-self: flex-start;
     }
     
     #sticky-payment {
          z-index: 10;
     }

     @media (max-width: 1024px) {
          #sticky-payment {
               position: relative;
               top: 0;
          }
     }

     /* Calendar Modal Styles */
     .calendar-modal {
          display: none;
          position: fixed;
          z-index: 100;
          left: 0;
          top: 0;
          width: 100%;
          height: 100%;
          overflow: auto;
          background-color: rgba(0,0,0,0.5);
          backdrop-filter: blur(5px);
     }

     .calendar-modal-content {
          background-color: #fefefe;
          margin: 5% auto;
          padding: 20px;
          border: 1px solid #888;
          width: 90%;
          max-width: 600px;
          border-radius: 12px;
          box-shadow: 0 4px 20px rgba(0,0,0,0.15);
          position: relative;
     }

     .close-calendar {
          color: #aaa;
          float: right;
          font-size: 28px;
          font-weight: bold;
          cursor: pointer;
          position: absolute;
          right: 20px;
          top: 15px;
     }

     .close-calendar:hover,
     .close-calendar:focus {
          color: black;
          text-decoration: none;
     }

     .calendar-container {
          margin-top: 20px;
     }

     .calendar-header {
          display: flex;
          justify-content: space-between;
          align-items: center;
          margin-bottom: 15px;
     }

     .calendar-month {
          font-weight: bold;
          font-size: 18px;
     }

     .calendar-grid {
          display: grid;
          grid-template-columns: repeat(7, 1fr);
          gap: 5px;
     }

     .calendar-day-header {
          text-align: center;
          font-weight: bold;
          padding: 8px 0;
          border-bottom: 1px solid #eee;
     }

     .calendar-day {
          text-align: center;
          padding: 8px 0;
          border-radius: 50%;
          cursor: default;
          height: 35px;
          width: 35px;
          display: flex;
          align-items: center;
          justify-content: center;
          margin: 0 auto;
     }

     .day-available {
          background-color: #f0f9f0;
          color: #0f5132;
     }

     .day-unavailable {
          background-color: #f8d7da;
          color: #842029;
          position: relative;
     }

     .day-unavailable::after {
          content: '';
          position: absolute;
          top: 50%;
          left: 50%;
          width: 70%;
          height: 2px;
          background-color: #842029;
          transform: translate(-50%, -50%) rotate(-45deg);
     }

     .day-current {
          background-color: #cfe2ff;
          color: #084298;
          font-weight: bold;
     }

     .day-outside {
          color: #6c757d;
          opacity: 0.5;
     }

     .calendar-legend {
          display: flex;
          justify-content: center;
          margin-top: 20px;
          gap: 15px;
          flex-wrap: wrap;
     }

     .legend-item {
          display: flex;
          align-items: center;
          font-size: 12px;
     }

     .legend-color {
          width: 15px;
          height: 15px;
          border-radius: 3px;
          margin-right: 5px;
     }

     .view-calendar-btn {
          background: none;
          border: none;
          color: #3b82f6;
          cursor: pointer;
          text-decoration: underline;
          font-size: 13px;
          margin-top: 8px;
          display: inline-flex;
          align-items: center;
     }

     .view-calendar-btn:hover {
          color: #2563eb;
     }

     .view-calendar-btn svg {
          margin-right: 4px;
          width: 14px;
          height: 14px;
     }
</style>
@endsection

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

<!-- Calendar Modal Template -->
<div id="calendarModalTemplate" 
     class="calendar-modal fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50 hidden">
     <div class="calendar-modal-content bg-white p-6 rounded-2xl shadow-xl max-w-lg w-full">
          <span class="close-calendar absolute top-4 right-6 text-2xl cursor-pointer">&times;</span>
          <h3 class="text-xl font-bold mb-4 calendarModalTitle" id="calendarModalTitle">
               Room Availability Calendar
          </h3>
          <div class="flex gap-6 mb-4 text-gray-700">
               <p><strong>Check-in:</strong> <span class="checkinPlaceholder"></span></p>
               <p><strong>Check-out:</strong> <span class="checkoutPlaceholder"></span></p>
          </div>
          <div class="calendar-container" id="calendarContainer">
               <!-- Calendar will be inserted here -->
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
     // Store unavailable dates globally
     let unavailableDates = {};
     
     document.addEventListener('DOMContentLoaded', function() {
          // Get booking ID from URL
          const urlParams = new URLSearchParams(window.location.search);
          const bookingId = "{{ $bookingId }}";
          
          if (bookingId) {
               // First load unavailable dates, then booking details
               loadUnavailableDates().then(() => {
                    loadBookingDetails(bookingId);
               }).catch(error => {
                    console.error('Error loading unavailable dates:', error);
                    loadBookingDetails(bookingId); // Still try to load booking details
               });
          } else {
               showErrorState({ message: 'No booking ID provided in URL' });
          }
          
          // Setup calendar modal close handlers
          setupCalendarModals();
     });
     
     // Load unavailable dates using the provided method
     async function loadUnavailableDates() {
          try {
               const response = await fetch("{{ route('admin.getUnavailableDates') }}", {
                    headers: {
                         'Accept': 'application/json',
                         'X-Requested-With': 'XMLHttpRequest',
                         'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                    }
               });
               
               if (!response.ok) {
                    throw new Error('Failed to fetch unavailable dates');
               }
               
               const data = await response.json();
               
               if (data.success) {
                    unavailableDates = data.dates || {};
               } else {
                    console.error('Error loading unavailable dates:', data.message);
               }
          } catch (error) {
               console.error('Error loading unavailable dates:', error);
               throw error;
          }
     }
     
     // Setup calendar modal event handlers
     function setupCalendarModals() {
          // Close modal when clicking on X
          document.addEventListener('click', function(e) {
               if (e.target.classList.contains('close-calendar')) {
                    closeCalendarModal(e.target.closest('.calendar-modal'));
               }
          });
          
          // Close modal when clicking outside
          document.addEventListener('click', function(e) {
               if (e.target.classList.contains('calendar-modal')) {
                    closeCalendarModal(e.target);
               }
          });
     }
     
     // Open calendar modal for a specific room
     function openCalendarModal(roomId, roomName, checkin, checkout) {
     // Create or get modal for this room
     let modal = document.getElementById(`calendarModal-${roomId}`);
     
     if (!modal) {
          // Clone the template
          const template = document.getElementById('calendarModalTemplate');
          modal = template.cloneNode(true);
          modal.id = `calendarModal-${roomId}`;
          document.body.appendChild(modal);
     }
     
     // Set the checkin/checkout dates as data attributes on the modal
     modal.dataset.checkIn = checkin;
     modal.dataset.checkOut = checkout;
     
     // Find the title inside THIS modal
     const title = modal.querySelector('.calendarModalTitle');
     if (title) {
          title.textContent = `${roomName} Availability`;
     }
     
     const checkinmodal = modal.querySelector('.checkinPlaceholder');
     const checkoutmodal = modal.querySelector('.checkoutPlaceholder');
     if (checkinmodal && checkoutmodal) {
          checkinmodal.textContent = formatDate(checkin);
          checkoutmodal.textContent = formatDate(checkout);
     }
     
     // Generate calendar
     generateCalendar(roomId, modal);
     
     // Show modal
     modal.style.display = 'block';
     document.body.style.overflow = 'hidden';
     }
     
     
     
     // Close calendar modal
     function closeCalendarModal(modal) {
          if (modal) {
               modal.style.display = 'none';
               document.body.style.overflow = 'auto';
          }
     }
     
     // Generate calendar for a specific room
     function generateCalendar(roomId, modal) {
          const container = modal.querySelector('#calendarContainer');
          const now = new Date();
          const currentMonth = now.getMonth();
          const currentYear = now.getFullYear();

          const checkInDate = modal.dataset.checkIn;
          const checkOutDate = modal.dataset.checkOut;
          
          // Get unavailable dates for this room
          const roomUnavailableDates = unavailableDates[roomId] || [];
          
          // Generate calendar for current month
          let calendarHTML = `
               <div class="calendar-header">
                    <button class="prev-month" onclick="changeCalendarMonth(${roomId}, ${currentMonth-1}, ${currentYear})">←</button>
                    <div class="calendar-month">${getMonthName(currentMonth)} ${currentYear}</div>
                    <button class="next-month" onclick="changeCalendarMonth(${roomId}, ${currentMonth+1}, ${currentYear})">→</button>
               </div>
               <div class="calendar-grid">
          `;
          
          // Add day headers
          const days = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
          days.forEach(day => {
               calendarHTML += `<div class="calendar-day-header">${day}</div>`;
          });
          
          // Get first day of month and total days
          const firstDay = new Date(currentYear, currentMonth, 1).getDay();
          const daysInMonth = new Date(currentYear, currentMonth + 1, 0).getDate();
          
          // Add empty cells for days before the first day of the month
          for (let i = 0; i < firstDay; i++) {
               calendarHTML += `<div class="calendar-day day-outside"></div>`;
          }
          
          // Add days of the month
          const today = new Date();
          for (let day = 1; day <= daysInMonth; day++) {
               const dateStr = `${currentYear}-${String(currentMonth + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
               const isToday = today.getDate() === day && today.getMonth() === currentMonth && today.getFullYear() === currentYear;
               const isUnavailable = isDateUnavailable(dateStr, roomUnavailableDates);
               
               let dayClass = 'calendar-day';
               if (isToday) dayClass += ' day-current';
               if (isUnavailable) dayClass += ' day-unavailable';
               else dayClass += ' day-available';
               
               calendarHTML += `<div class="${dayClass}">${day}</div>`;
          }
          
          calendarHTML += `</div>`;
          
          // Add legend
          calendarHTML += `
               <div class="calendar-legend">
                    <div class="legend-item">
                         <div class="legend-color" style="background-color: #f0f9f0; border: 1px solid #d1e7dd;"></div>
                         <span>Available</span>
                    </div>
                    <div class="legend-item">
                         <div class="legend-color" style="background-color: #f8d7da; border: 1px solid #f1aeb5;"></div>
                         <span>Booked</span>
                    </div>
                    <div class="legend-item">
                         <div class="legend-color" style="background-color: #cfe2ff; border: 1px solid #9ec5fe;"></div>
                         <span>Today</span>
                    </div>
               </div>
          `;
          
          container.innerHTML = calendarHTML;
     }
     
     // Change calendar month (for navigation)
     function changeCalendarMonth(roomId, month, year) {
          // Handle year rollover
          if (month < 0) {
               month = 11;
               year--;
          } else if (month > 11) {
               month = 0;
               year++;
          }
          
          const modal = document.getElementById(`calendarModal-${roomId}`);
          if (modal) {
               const container = modal.querySelector('#calendarContainer');
               const roomUnavailableDates = unavailableDates[roomId] || [];
               
               // Regenerate calendar for the new month
               let calendarHTML = `
                    <div class="calendar-header">
                         <button class="prev-month" onclick="changeCalendarMonth(${roomId}, ${month-1}, ${year})">←</button>
                         <div class="calendar-month">${getMonthName(month)} ${year}</div>
                         <button class="next-month" onclick="changeCalendarMonth(${roomId}, ${month+1}, ${year})">→</button>
                    </div>
                    <div class="calendar-grid">
               `;
               
               // Add day headers
               const days = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
               days.forEach(day => {
                    calendarHTML += `<div class="calendar-day-header">${day}</div>`;
               });
               
               // Get first day of month and total days
               const firstDay = new Date(year, month, 1).getDay();
               const daysInMonth = new Date(year, month + 1, 0).getDate();
               
               // Add empty cells for days before the first day of the month
               for (let i = 0; i < firstDay; i++) {
                    calendarHTML += `<div class="calendar-day day-outside"></div>`;
               }
               
               // Add days of the month
               const today = new Date();
               for (let day = 1; day <= daysInMonth; day++) {
                    const dateStr = `${year}-${String(month + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
                    const isToday = today.getDate() === day && today.getMonth() === month && today.getFullYear() === year;
                    const isUnavailable = isDateUnavailable(dateStr, roomUnavailableDates);
                    
                    let dayClass = 'calendar-day';
                    if (isToday) dayClass += ' day-current';
                    if (isUnavailable) dayClass += ' day-unavailable';
                    else dayClass += ' day-available';
                    
                    calendarHTML += `<div class="${dayClass}">${day}</div>`;
               }
               
               calendarHTML += `</div>`;
               
               // Add legend
               calendarHTML += `
                    <div class="calendar-legend">
                         <div class="legend-item">
                              <div class="legend-color" style="background-color: #f0f9f0; border: 1px solid #d1e7dd;"></div>
                              <span>Available</span>
                         </div>
                         <div class="legend-item">
                              <div class="legend-color" style="background-color: #f8d7da; border: 1px solid #f1aeb5;"></div>
                              <span>Booked</span>
                         </div>
                         <div class="legend-item">
                              <div class="legend-color" style="background-color: #cfe2ff; border: 1px solid #9ec5fe;"></div>
                              <span>Today</span>
                         </div>
                    </div>
               `;
               
               container.innerHTML = calendarHTML;
          }
     }
     
     // Check if a date is unavailable
     function isDateUnavailable(dateStr, unavailableDates) {
          for (const range of unavailableDates) {
               if (dateStr >= range.checkin_date && dateStr <= range.checkout_date) {
                    return true;
               }
          }
          return false;
     }
     
     // Get month name
     function getMonthName(month) {
          const months = ['January', 'February', 'March', 'April', 'May', 'June', 
                         'July', 'August', 'September', 'October', 'November', 'December'];
          return months[month];
     }
     
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

          document.addEventListener('click', function(e) {
               if (e.target.closest('.view-calendar-btn')) {
                    const button = e.target.closest('.view-calendar-btn');
                    const roomId = button.dataset.roomId;
                    const roomName = button.dataset.roomName;
                    const checkin = button.dataset.checkin;
                    const checkout = button.dataset.checkout;
                    openCalendarModal(roomId, roomName, checkin, checkout);
               }
          });

          
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
               const isCheckedin = data.status === 'checked_in';
               const isCheckedout = data.status === 'checked_out';
               const isCancelled = data.status === 'cancelled';
               const isNoshow = data.status === 'no_show';
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
                         
                         <!-- View Calendar Button -->
                         <button class="view-calendar-btn" 
                                   data-room-id="${facility.facility_id}" 
                                   data-room-name="${facility.facility_name}"
                                   data-checkin="${checkInDate}"
                                   data-checkout="${checkOutDate}"
                                   >
                              <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                   <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                              </svg>
                              View Availability Calendar
                         </button>
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

                    <label for="room-checkbox-${index}" class="flex items-center mt-4 p-3 bg-gray-50 rounded-lg border border-lightgray cursor-pointer">
                         <input type="checkbox" id="room-checkbox-${index}"
                                   class="mr-2 room-checkbox h-4 w-4 text-red-600 focus:ring-red-500 border-gray-300 rounded"
                                   data-room="${facility.facility_name || 'Room ' + (index + 1)}"
                                   ${isConfirmed || isRejected || isCheckedin || isCheckedout || isCancelled || isNoshow ? 'disabled checked' : ''}>
                         <span class="text-sm text-gray-700">
                              I have reviewed <span class="font-medium">${facility.facility_name || 'this room'}</span> details
                         </span>
                    </label>
               
               </div>
               
               `;
          }).join('') || '<p class="text-gray-500 italic">No facilities booked</p>';
          
          // Check if booking is already confirmed or rejected
          const isConfirmed = data.status === 'confirmed';
          const isRejected = data.status === 'rejected';
               
          const isCheckedin = data.status === 'checked_in';
          const isCheckedout = data.status === 'checked_out';
          const isCancelled = data.status === 'cancelled';
          const isNoshow = data.status === 'no_show';
          
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
          
          let paymentHtml = '';
          if (data.payment && data.payment.amount !== null && data.payment.amount !== undefined) {
               paymentHtml = `
               <div class="bg-green-50 p-6 rounded-2xl mb-6 border border-green-200 shadow-sm sticky top-4" id="sticky-payment">
                    <h3 class="text-2xl font-bold text-gray-900 mb-6 flex items-center">Payment Advance Sent Information</h3>
                    <div class="grid grid-cols-1 lg:grid-cols-1 gap-2">
                         <div class="space-y-3">
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
                                   <span class="font-medium text-gray-600">To be paid:</span>
                                   <span class="font-semibold text-green-800">${formatPrice(data.payment.amount)}</span>
                              </div>

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
                                        ${data.payment.amount_paid ? 'disabled' : ''}
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
                    <h3 class="text-lg font-semibold mb-3 text-gray-800">Payment Advance Sent Information</h3>
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
                         <svg class="w-6 h-6 text-blue-500 mr-2" fill="none" stroke='currentColor' stroke-width="2"
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
                                   <span class="font-semibold text-gray-900">${data.user?.email || 'the guest'} </span> and
                                   <span class="font-semibold text-gray-900">${data.user?.phone || 'No Phone Number'} </span>
                         </p>
                         <div class="mt-4">
                              <label for="customMessage" class="block text-sm font-medium text-gray-700 mb-2">
                              Custom Message (optional)
                              <span class="text-gray-400 font-normal ml-1">- will be included in email notification</span>
                              </label>
                              <textarea 
                                   id="customMessage" 
                                   name="customMessage"
                                   rows="3"
                                   placeholder="Type your custom message here..."
                                   class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-red-500 focus:ring-2 focus:ring-red-500 focus:ring-opacity-50 
                                             shadow-sm transition duration-150 ease-in-out
                                             disabled:bg-gray-100 disabled:text-gray-500 disabled:placeholder-gray-400 disabled:cursor-not-allowed
                                             placeholder-gray-400 text-gray-700"
                                   ${isConfirmed || isRejected || isCheckedin || isCheckedout || isCancelled || isNoshow ? 'disabled' : ''}
                              ></textarea>
                              <p class="mt-1 text-xs text-gray-500">Maximum 250 characters</p>
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
               
               // Function to check if all checkboxes are checked AND status is pending_confirmation
               function checkAllBoxesChecked() {
                    const allChecked = Array.from(checkboxes).every(checkbox => checkbox.checked);
                    const isPendingConfirmation = data.status === 'pending_confirmation';
                    
                    if (allChecked && isPendingConfirmation) {
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
                         send_notifier: true,
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