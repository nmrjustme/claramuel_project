@extends('layouts.admin')
@section('title', 'Booking Calendar')
@php
    $active = 'calendar';
@endphp

@section('content_css')
<style>
    .calendar {
        width: 100%;
        border-collapse: collapse;
    }
    
    .calendar th {
        padding: 0.75rem;
        text-align: center;
        font-weight: 500;
        color: #6b7280;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 0.05em;
    }
    
    .calendar td {
        padding: 0.5rem;
        height: 6rem;
        vertical-align: top;
        border: 1px solid #e5e7eb;
        position: relative;
    }
    
    .calendar-day {
        position: absolute;
        top: 0.25rem;
        right: 0.25rem;
        font-weight: 500;
        color: #111827;
    }
    
    .calendar-day.today {
        background-color: #ef4444;
        color: white;
        border-radius: 9999px;
        width: 1.5rem;
        height: 1.5rem;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .calendar-day.other-month {
        color: #9ca3af;
    }
    
    .booking-badge {
        font-size: 0.75rem;
        padding: 0.125rem 0.25rem;
        border-radius: 0.25rem;
        margin-bottom: 0.25rem;
        display: block;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        cursor: pointer;
    }
    
    .booking-badge:hover {
        opacity: 0.9;
    }
    
    .booking-badge.pending {
        background-color: #fef3c7;
        color: #92400e;
    }
    
    .booking-badge.confirmed {
        background-color: #d1fae5;
        color: #065f46;
    }
    
    .booking-badge.cancelled {
        background-color: #fee2e2;
        color: #991b1b;
    }
    
    .booking-badge.completed {
        background-color: #e0e7ff;
        color: #3730a3;
    }
    
    .more-bookings {
        font-size: 0.75rem;
        color: #6b7280;
        cursor: pointer;
        margin-top: 0.25rem;
    }
    
    .more-bookings:hover {
        text-decoration: underline;
    }
    
    .facility-list {
        margin-top: 0.25rem;
        font-size: 0.7rem;
        color: #4b5563;
    }
    
    .facility-item {
        display: flex;
        align-items: center;
    }
    
    .facility-dot {
        width: 0.4rem;
        height: 0.4rem;
        border-radius: 50%;
        margin-right: 0.25rem;
        background-color: #6b7280;
    }
</style>
@endsection

@section('content')
<div class="min-h-screen p-6">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-3xl font-bold text-white">Booking Calendar</h1>
            <p class="text-gray-200">View bookings by date</p>
        </div>
        <div class="flex space-x-3">
            <button onclick="loadCalendar()" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-800 transition-colors flex items-center">
                Refresh Calendar
            </button>
        </div>
    </div>

    <!-- Calendar Container -->
    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200 mb-8">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-xl font-semibold text-gray-800">Booking Calendar</h2>
            <div class="flex items-center space-x-4">
                <button onclick="previousMonth()" class="p-2 rounded-full hover:bg-gray-100">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </button>
                <h3 class="text-lg font-medium text-gray-700" id="month-year-display"></h3>
                <button onclick="nextMonth()" class="p-2 rounded-full hover:bg-gray-100">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </button>
            </div>
        </div>

        <div class="overflow-x-auto">
            <div id="calendar" class="w-full">
                <!-- Calendar will be loaded here -->
                <div class="text-center py-8">
                    <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-red-600 mx-auto"></div>
                    <p class="mt-2 text-gray-500">Loading calendar...</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Booking Details Modal -->
<div id="booking-details-modal" class="fixed inset-0 z-50 hidden overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 transition-opacity" aria-hidden="true">
            <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
        </div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full">
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <div class="sm:flex sm:items-start">
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                        <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                            Bookings for <span id="modal-date"></span>
                        </h3>
                        <div class="mt-4" id="modal-content">
                            <!-- Booking details will be loaded here -->
                            <div class="text-center py-8">
                                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-red-600 mx-auto"></div>
                                <p class="mt-2 text-gray-500">Loading booking details...</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                <button type="button" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm" onclick="closeDetailsModal()">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('content_js')
<script>
// Global variables
let currentMonth = new Date().getMonth();
let currentYear = new Date().getFullYear();

// Initialize when page loads
document.addEventListener('DOMContentLoaded', function() {
    loadCalendar();
});

// Load calendar data
function loadCalendar() {
    showLoadingState('calendar', 'Loading calendar...');
    
    fetch(`/bookings/calendar?month=${currentMonth + 1}&year=${currentYear}`, {
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            renderCalendar(data.days, data.bookings);
            updateMonthYearDisplay();
        } else {
            showErrorState('calendar', { message: data.message || 'Failed to load calendar' });
        }
    })
    .catch(error => {
        console.error('Error fetching calendar:', error);
        showErrorState('calendar', error);
    });
}

// Render the calendar
function renderCalendar(days, bookings) {
    const calendarEl = document.getElementById('calendar');
    
    // Create table structure
    let html = `
        <table class="calendar w-full">
            <thead>
                <tr>
                    <th>Sun</th>
                    <th>Mon</th>
                    <th>Tue</th>
                    <th>Wed</th>
                    <th>Thu</th>
                    <th>Fri</th>
                    <th>Sat</th>
                </tr>
            </thead>
            <tbody>
    `;
    
    // Group days by week
    const weeks = [];
    let week = [];
    
    days.forEach((day, index) => {
        week.push(day);
        
        if ((index + 1) % 7 === 0 || index === days.length - 1) {
            weeks.push(week);
            week = [];
        }
    });
    
    // Create rows for each week
    weeks.forEach(week => {
        html += '<tr>';
        
        week.forEach(day => {
            const dateStr = day.date;
            const dayBookings = bookings[dateStr] || [];
            html += renderCalendarDayCell(day, dayBookings);
        });
        
        html += '</tr>';
    });
    
    html += `
            </tbody>
        </table>
    `;
    
    calendarEl.innerHTML = html;
}

// Render a single calendar day cell
function renderCalendarDayCell(day, dayBookings) {
    const bookingsByLog = groupBookingsByLog(dayBookings);
    const isToday = isCurrentDay(day.day, day.isCurrentMonth);
    
    let html = `
        <td class="${!day.isCurrentMonth ? 'bg-gray-50' : ''}">
            <div class="calendar-day ${isToday ? 'today' : ''} ${!day.isCurrentMonth ? 'other-month' : ''}">
                ${day.day}
            </div>
            <div class="mt-6">
    `;
    
    if (bookingsByLog.length > 0) {
        const displayCount = Math.min(bookingsByLog.length, 2);
        
        for (let i = 0; i < displayCount; i++) {
            const booking = bookingsByLog[i];
            
            html += `
                <span class="booking-badge ${getBookingStatusClass(booking.status)}" 
                      onclick="showBookingsForDate('${day.date}')" 
                      title="${booking.user_name} - ${booking.facilities.length} facility(ies)">
                    ${booking.user_name}
                </span>
                <div class="facility-list">
                    ${booking.facilities.slice(0, 3).map(facility => `
                        <div class="facility-item">
                            <span class="facility-dot"></span>
                            ${facility.name}
                        </div>
                    `).join('')}
                    ${booking.facilities.length > 3 ? `<div class="facility-item">+${booking.facilities.length - 3} more</div>` : ''}
                    ${booking.has_breakfast ? '<div class="text-xs text-green-600 mt-1">+ Breakfast</div>' : ''}
                </div>
            `;
        }
        
        if (bookingsByLog.length > 2) {
            html += `
                <span class="more-bookings" onclick="showBookingsForDate('${day.date}')">
                    +${bookingsByLog.length - 2} more bookings
                </span>
            `;
        }
    } else if (day.isCurrentMonth) {
        html += `<span class="text-xs text-gray-400">No bookings</span>`;
    }
    
    html += `
            </div>
        </td>
    `;
    
    return html;
}

// Group bookings by booking log
function groupBookingsByLog(bookings) {
    const groups = {};
    
    bookings.forEach(booking => {
        const key = booking.booking_log_id;
        
        if (!groups[key]) {
            groups[key] = {
                id: booking.booking_log_id,
                user_name: booking.user_name,
                user_email: booking.user_email,
                status: booking.status,
                payment_status: booking.payment_status,
                facilities: [],
                checkin_date: booking.checkin_date,
                checkout_date: booking.checkout_date,
                total_price: 0,
                amount_paid: booking.amount_paid || 0,
                has_breakfast: false // Initialize breakfast flag
            };
        }
        
        // Add facility to the group
        groups[key].facilities.push({
            name: booking.facility_name,
            price: booking.total_price
        });
        
        // Check if breakfast is included
        if (booking.breakfast) {
            groups[key].has_breakfast = true;
        }
        
        // Accumulate total price
        groups[key].total_price += parseFloat(booking.total_price || 0);
    });
    
    return Object.values(groups);
}

// Check if day is today
function isCurrentDay(day, isCurrentMonth) {
    if (!isCurrentMonth) return false;
    const today = new Date();
    return day === today.getDate() && 
           currentMonth === today.getMonth() && 
           currentYear === today.getFullYear();
}

// Get CSS class for booking status
function getBookingStatusClass(status) {
    switch(status.toLowerCase()) {
        case 'pending':
        case 'pending_confirmation':
            return 'pending';
        case 'confirmed':
        case 'approved':
            return 'confirmed';
        case 'completed':
        case 'fulfilled':
            return 'completed';
        case 'cancelled':
        case 'rejected':
            return 'cancelled';
        default:
            return '';
    }
}

// Update month/year display
function updateMonthYearDisplay() {
    const monthNames = ["January", "February", "March", "April", "May", "June",
                        "July", "August", "September", "October", "November", "December"];
    document.getElementById('month-year-display').textContent = 
        `${monthNames[currentMonth]} ${currentYear}`;
}

// Navigation functions
function previousMonth() {
    currentMonth--;
    if (currentMonth < 0) {
        currentMonth = 11;
        currentYear--;
    }
    loadCalendar();
}

function nextMonth() {
    currentMonth++;
    if (currentMonth > 11) {
        currentMonth = 0;
        currentYear++;
    }
    loadCalendar();
}

// Show bookings for a specific date
function showBookingsForDate(date) {
    const modalDate = document.getElementById('modal-date');
    const modalContent = document.getElementById('modal-content');
    
    // Format the date for display
    const dateObj = new Date(date);
    const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
    modalDate.textContent = dateObj.toLocaleDateString(undefined, options);
    
    // Show loading state
    modalContent.innerHTML = `
        <div class="text-center py-8">
            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-red-600 mx-auto"></div>
            <p class="mt-2 text-gray-500">Loading booking details...</p>
        </div>
    `;
    
    // Fetch bookings for this date
    fetch(`/bookings/by-date?date=${date}`, {
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success && data.bookings.length > 0) {
            renderBookingsModal(data.bookings);
        } else {
            modalContent.innerHTML = `
                <div class="text-center py-8">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round", stroke-linejoin="round", stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                    <p class="mt-2 text-gray-500">No bookings found for this date</p>
                </div>
            `;
        }
    })
    .catch(error => {
        modalContent.innerHTML = `
            <div class="text-center py-8 text-red-500">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round", stroke-linejoin="round", stroke-width="2" d="M12 8v4m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                <p class="mt-2">Failed to load booking details</p>
            </div>
        `;
    });
    
    // Show the modal
    document.getElementById('booking-details-modal').classList.remove('hidden');
}

// Render bookings in the modal
function renderBookingsModal(bookings) {
    const modalContent = document.getElementById('modal-content');
    const bookingsByLog = groupBookingsByLog(bookings);
    
    let html = `
        <div class="space-y-4">
            ${bookingsByLog.map(booking => {
                const statusClass = getBookingStatusClass(booking.status);
                const statusText = booking.status.split('_').map(word => 
                    word.charAt(0).toUpperCase() + word.slice(1).toLowerCase()
                ).join(' ');
                
                return `
                    <div class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 rounded-lg">
                        <div class="bg-gray-50 px-4 py-3 border-b border-gray-200">
                            <div class="flex justify-between items-center">
                                <div>
                                    <h4 class="font-medium text-gray-900">${booking.user_name}</h4>
                                    <p class="text-sm text-gray-500">${booking.user_email}</p>
                                </div>
                                <div>
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full ${statusClass}">
                                        ${statusText}
                                    </span>
                                    <div class="mt-1 text-xs ${booking.payment_status === 'paid' ? 'text-green-600' : 'text-yellow-600'}">
                                        ${booking.payment_status || 'N/A'}
                                    </div>
                                </div>
                            </div>
                            <div class="mt-2 flex items-center text-sm text-gray-500">
                                <svg class="flex-shrink-0 mr-1.5 h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd" />
                                </svg>
                                ${formatDate(booking.checkin_date)} - ${formatDate(booking.checkout_date)}
                                ${booking.has_breakfast ? '<span class="ml-3 text-xs bg-green-100 text-green-800 px-2 py-0.5 rounded-full">Breakfast Included</span>' : ''}
                            </div>
                        </div>
                        <table class="min-w-full divide-y divide-gray-300">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900">Facility</th>
                                    <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Price</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 bg-white">
                                ${booking.facilities.map(facility => `
                                    <tr>
                                        <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900">
                                            ${facility.name}
                                        </td>
                                        <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                            ₱${parseFloat(facility.price).toLocaleString('en-PH')}
                                        </td>
                                    </tr>
                                `).join('')}
                            </tbody>
                        </table>
                        <div class="bg-gray-50 px-4 py-3 border-t border-gray-200 text-right">
                            <div class="text-sm font-medium text-gray-900">
                                Total: ₱${booking.total_price.toLocaleString('en-PH')}
                                ${booking.amount_paid ? `<span class="text-xs ml-2">(Paid: ₱${booking.amount_paid.toLocaleString('en-PH')})</span>` : ''}
                            </div>
                        </div>
                    </div>
                `;
            }).join('')}
        </div>
    `;
    
    modalContent.innerHTML = html;
}


// View full booking details
function viewBookingDetails(bookingId) {
    if (typeof window.openModal_accept_inquirer === 'function') {
        const button = document.createElement('button');
        button.setAttribute('data-id', bookingId);
        window.openModal_accept_inquirer(button);
    } else {
        alert('Booking ID: ' + bookingId);
    }
    closeDetailsModal();
}

// Close modal
function closeDetailsModal() {
    document.getElementById('booking-details-modal').classList.add('hidden');
}

// UI helper functions
function showLoadingState(containerId, message) {
    const container = document.getElementById(containerId);
    if (container) {
        container.innerHTML = `
            <div class="text-center py-8">
                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-red-600 mx-auto"></div>
                <p class="mt-2 text-gray-500">${message}</p>
            </div>
        `;
    }
}

function showErrorState(containerId, error) {
    const container = document.getElementById(containerId);
    if (container) {
        container.innerHTML = `
            <div class="text-center py-8 text-red-500">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round", stroke-linejoin="round", stroke-width="2" d="M12 8v4m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                <p class="mt-2">${error.message || 'Failed to load data. Please try again.'}</p>
                <button onclick="loadCalendar()" class="mt-4 px-4 py-2 bg-red-100 text-red-700 rounded hover:bg-red-200">
                    Retry
                </button>
            </div>
        `;
    }
}

// Date formatting helper
function formatDate(dateString) {
    if (!dateString) return 'N/A';
    const options = { month: 'short', day: 'numeric' };
    return new Date(dateString).toLocaleDateString(undefined, options);
}

// Close modal when clicking outside
window.addEventListener('click', function(event) {
    if (event.target === document.getElementById('booking-details-modal')) {
        closeDetailsModal();
    }
});
</script>
@endsection
