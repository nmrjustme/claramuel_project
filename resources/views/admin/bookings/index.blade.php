@extends('layouts.admin')

@section('title', 'Bookings')

@php
    $active = 'bookings';
@endphp

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex flex-col lg:flex-row gap-6">
        <!-- Main Content -->
        <div class="lg:w-3/4">
            <div class="bg-white rounded-lg shadow-md p-6">
                <h1 class="text-2xl font-bold text-gray-800 mb-6">Bookings</h1>
                
                <!-- Status Tabs -->
                <div class="flex flex-wrap gap-2 mb-6 border-b border-gray-200">
                    <button class="px-4 py-2 bg-red-600 text-white rounded-t-lg font-medium" data-status="paid">Paid</button>
                    <button class="px-4 py-2 text-gray-600 hover:bg-gray-200 rounded-t-lg font-medium" data-status="advance_paid">Advance</button>
                    <button class="px-4 py-2 text-gray-600 hover:bg-gray-200 rounded-t-lg font-medium" data-status="under_verification">Under Verification</button>
                    <button class="px-4 py-2 text-gray-600 hover:bg-gray-200 rounded-t-lg font-medium" data-status="rejected">Rejected</button>
                </div>
                
                <!-- Booking Table -->
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Guest</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Reservation Code</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Check in Date</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Check Out Date</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Payment Collected By</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200" id="bookings-table-body">
                            <!-- Bookings will be loaded here via fetch -->
                        </tbody>
                    </table>
                </div>

                <!-- Pagination and Excel Button -->
                <div class="flex justify-between items-center mt-4">
                    <div id="pagination-info" class="text-sm text-gray-600"></div>
                    <div class="flex gap-2">
                        <button id="prev-page" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg font-medium disabled:opacity-50" disabled>
                            Previous
                        </button>
                        <button id="next-page" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg font-medium disabled:opacity-50" disabled>
                            Next
                        </button>
                        <button id="generate-excel" class="px-4 py-2 bg-green-600 text-white rounded-lg font-medium hover:bg-green-700 flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 384 512" fill="currentColor">
                                <path d="M224 136V0H24C10.7 0 0 10.7 0 24v464c0 13.3 10.7 24 24 24h336c13.3 0 24-10.7 24-24V160H248c-13.2 0-24-10.8-24-24zm60.1 106.5L224 336l60.1 93.5c5.1 8-.6 18.5-10.1 18.5h-34.9c-4.4 0-8.5-2.4-10.6-6.3C208.9 405.5 192 373 192 373c-6.4 14.8-10 20-36.6 68.8-2.1 3.9-6.1 6.3-10.5 6.3H110c-9.5 0-15.2-10.5-10.1-18.5l60.3-93.5-60.3-93.5c-5.2-8 .6-18.5 10.1-18.5h34.8c4.4 0 8.5 2.4 10.6 6.3 26.1 48.8 20 35.6 36.6 68.5 0 0 6.1-11.7 36.6-68.5 2.1-3.9 6.2-6.3 10.6-6.3H274c9.5-.1 15.2 10.4 10.1 18.4zM384 121.9v6.1H256V0h6.1c6.4 0 12.5 2.5 17 7l97.9 98c4.5 4.5 7 10.6 7 16.9z"/>
                            </svg>
                            Excel
                        </button>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Booking Summary Sidebar -->
        <div class="lg:w-1/4">
            <!-- Next Check-in Section -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-2">Next Check-in</h3>
                <p class="text-gray-600 mb-4" id="next-checkin-time">Loading...</p>
                <div class="bg-gray-50 p-4 rounded">
                    <p class="font-medium text-gray-800" id="next-checkin-date">-</p>
                    <p class="text-gray-600" id="next-checkin-nights">-</p>
                    <p class="font-medium text-gray-800 mt-2" id="next-checkin-guest">-</p>
                    <p class="text-gray-600" id="next-checkin-phone">-</p>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-md p-6 sticky top-6">
                <h2 class="text-xl font-bold text-gray-800 mb-4">Booking Summary</h2>
                <div class="border-t border-gray-200 pt-4" id="booking-summary">
                    <div class="text-center py-8 text-gray-400">
                        Select a booking to view details
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('content_js')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // CSRF token for Laravel
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    
    // Headers for fetch requests
    const headers = {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
        'X-CSRF-TOKEN': csrfToken
    };

    // Current status filter and pagination
    let currentStatus = 'paid';
    let currentPage = 1;
    let totalPages = 1;
    const perPage = 10;
    
    // Load initial data
    loadBookings(currentStatus, currentPage);
    loadNextCheckin();
    
    // Tab click handler
    document.querySelectorAll('[data-status]').forEach(tab => {
        tab.addEventListener('click', function() {
            currentStatus = this.dataset.status;
            currentPage = 1;
            
            // Update active tab
            document.querySelectorAll('[data-status]').forEach(t => {
                t.classList.remove('bg-red-600', 'text-white');
                t.classList.add('text-gray-600', 'hover:bg-gray-200');
            });
            this.classList.remove('text-gray-600', 'hover:bg-gray-200');
            this.classList.add('bg-red-600', 'text-white');
            
            // Load bookings for selected status
            loadBookings(currentStatus, currentPage);
        });
    });

    // Pagination handlers
    document.getElementById('prev-page').addEventListener('click', function() {
        if (currentPage > 1) {
            currentPage--;
            loadBookings(currentStatus, currentPage);
        }
    });

    document.getElementById('next-page').addEventListener('click', function() {
        if (currentPage < totalPages) {
            currentPage++;
            loadBookings(currentStatus, currentPage);
        }
    });

    // Generate Excel button click handler
    document.getElementById('generate-excel').addEventListener('click', function() {
        generateExcel(currentStatus);
    });

    function generateExcel(status) {
        const excelBtn = document.getElementById('generate-excel');
        const originalHtml = excelBtn.innerHTML;
        
        // Show loading state
        excelBtn.innerHTML = `
            <svg class="animate-spin -ml-1 mr-2 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            Generating...
        `;
        excelBtn.disabled = true;
    
        fetch(`/admin/bookings/export?status=${status}`, {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            credentials: 'same-origin'
        })
        .then(async (response) => {
            if (response.status === 404) {
                const data = await response.json();
                throw new Error(data.message || 'No data available for export');
            }
    
            if (!response.ok) {
                throw new Error('Failed to generate Excel file');
            }
    
            // Handle successful Excel download
            const blob = await response.blob();
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            
            // Get filename from content-disposition header or use default
            const contentDisposition = response.headers.get('content-disposition');
            let filename = `bookings_${status}_${new Date().toISOString().split('T')[0]}.xlsx`;
            
            if (contentDisposition) {
                const filenameMatch = contentDisposition.match(/filename="(.+)"/);
                if (filenameMatch && filenameMatch[1]) {
                    filename = filenameMatch[1];
                }
            }
            
            a.download = filename;
            document.body.appendChild(a);
            a.click();
            window.URL.revokeObjectURL(url);
            a.remove();
            
            toastr.success('Excel file downloaded successfully');
        })
        .catch(error => {
            console.error('Export Error:', error);
            
            if (error.message.includes('No data available')) {
                // Create empty Excel file with just headers
                const csvContent = "Status,Guest Name,Reservation Code,Check-in Date,Check-out Date,Amount,Payment Collected By,Facilities Booked";
                const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
                const url = URL.createObjectURL(blob);
                const link = document.createElement('a');
                link.href = url;
                link.download = `bookings_${status}_empty_${new Date().toISOString().split('T')[0]}.csv`;
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
                
                toastr.info('No bookings found. Downloaded empty template with headers.');
            } else {
                toastr.error(error.message || 'Failed to generate Excel file');
            }
        })
        .finally(() => {
            excelBtn.innerHTML = originalHtml;
            excelBtn.disabled = false;
        });
    }
    
    const STATUS_CONFIG = {
        'paid': {
            class: 'bg-green-600',
            text: 'PAID'
        },
        'advance_paid': {
            class: 'bg-green-600',
            text: 'PAID'
        },
        'under_verification': {
            class: 'bg-yellow-600',
            text: 'UNDER VERIFICATION'
        },
        'rejected': {
            class: 'bg-red-600',
            text: 'REJECTED'
        },
        'not_paid': {
            class: 'bg-gray-600',
            text: 'NOT PAID'
        }
    };
    // Function to load bookings
    function loadBookings(status, page = 1) {
        const url = new URL(`/get/admin/bookings`, window.location.origin);
        url.searchParams.append('status', status);
        url.searchParams.append('page', page);
        url.searchParams.append('per_page', perPage);
        
        fetch(url, {
            method: 'GET',
            headers: headers,
            credentials: 'same-origin'
        })
        .then(response => {
            if (!response.ok) throw new Error('Network response was not ok');
            return response.json();
        })
        .then(data => {
            const bookings = data.data;
            totalPages = Math.ceil(data.total / perPage);
            
            // Update pagination info
            document.getElementById('pagination-info').textContent = 
                `Showing ${data.from} to ${data.to} of ${data.total} entries`;
            
            // Update pagination buttons
            document.getElementById('prev-page').disabled = currentPage <= 1;
            document.getElementById('next-page').disabled = currentPage >= totalPages;
            
            let html = '';
            
            if (bookings.length === 0) {
                html = '<tr><td colspan="7" class="px-6 py-4 text-center text-gray-500">No bookings found</td></tr>';
            } else {
                bookings.forEach(booking => {
                    const statusInfo = getBookingStatus(booking);
                    
                    html += `
                        <tr class="booking-row cursor-pointer hover:bg-gray-200" data-booking-id="${booking.id}">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full ${statusInfo.class} text-white">
                                    ${statusInfo.text}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">${booking.user?.firstname || 'Guest'} ${booking.user?.lastname || ''}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">${booking.reference}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">${formatDate(booking.details[0]?.checkin_date)}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">${formatDate(booking.details[0]?.checkout_date)}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">${formatCurrency((booking.details[0]?.total_price * 0.5) || 0)}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">${booking.payments?.length > 0 ? (booking.user?.firstname || 'Payment Gateway') : 'N/A'}</div> 
                            </td>
                        </tr>
                    `;
                });
            }
            
            document.getElementById('bookings-table-body').innerHTML = html;
            
            // Add click handler for booking rows
            document.querySelectorAll('.booking-row').forEach(row => {
                row.addEventListener('click', function() {
                    const bookingId = this.dataset.bookingId;
                    loadBookingSummary(bookingId);
                });
            });
        })
        .catch(error => {
            toastr.error('Failed to load bookings');
            console.error('Error:', error);
        });
    }
    function getBookingStatus(booking) {
        // If booking has explicit status (for rejected or pending)
        if (booking.status === 'rejected') return STATUS_CONFIG.rejected;
        if (booking.status === 'pending_confirmation') return STATUS_CONFIG.under_verification;
        
        // Calculate payment status
        const totalPaid = booking.payments?.reduce((sum, payment) => 
            ['paid', 'advance_paid'].includes(payment.status) ? sum + parseFloat(payment.amount) : sum, 0) || 0;
        
        const totalAmount = booking.details?.reduce((sum, detail) => 
            sum + parseFloat(detail.total_price), 0) || 0;
        
        if (totalPaid >= totalAmount) return STATUS_CONFIG.paid;
        if (totalPaid > 0) return STATUS_CONFIG.advance_paid;
        
        return STATUS_CONFIG.not_paid;
    }
    // Function to load next check-in
    function loadNextCheckin() {
        fetch(`/get/bookings/next-checkin`, {
            method: 'GET',
            headers: headers,
            credentials: 'same-origin'
        })
        .then(response => {
            if (!response.ok) {
                return response.json().then(err => { throw err; });
            }
            return response.json();
        })
        .then(data => {
            if (!data.success) {
                throw new Error(data.message || 'Failed to load next check-in');
            }
            
            if (data.data) {
                const booking = data.data;
                const detail = booking.details[0];
                document.getElementById('next-checkin-time').textContent = 
                    `${data.days_until} day${data.days_until !== 1 ? 's' : ''} from now`;
                document.getElementById('next-checkin-date').textContent = formatDate(detail.checkin_date);
                document.getElementById('next-checkin-nights').textContent = 
                    `${getNights(detail.checkin_date, detail.checkout_date)} night${getNights(detail.checkin_date, detail.checkout_date) !== 1 ? 's' : ''}`;
                document.getElementById('next-checkin-guest').textContent = 
                    `${booking.user?.firstname || 'Guest'} ${booking.user?.lastname || ''}`;
                document.getElementById('next-checkin-phone').textContent = booking.user?.phone || 'N/A';
            } else {
                document.getElementById('next-checkin-time').textContent = 'No upcoming check-ins';
                ['next-checkin-date', 'next-checkin-nights', 'next-checkin-guest', 'next-checkin-phone'].forEach(id => {
                    document.getElementById(id).textContent = '-';
                });
            }
        })
        .catch(error => {
            console.error('Next check-in error:', error);
            toastr.error(error.message || 'Failed to load next check-in');
        });
    }
    
    // Function to load booking summary
    async function loadBookingSummary(bookingId) {
        try {
            // Fetch booking data
            const response = await fetch(`/get/show/bookings/${bookingId}`, {
                method: 'GET',
                headers: headers,
                credentials: 'same-origin'
            });

            if (!response.ok) {
                throw new Error('Network response was not ok');
            }

            const data = await response.json();
            const booking = data.data;
            const detail = booking.details[0];
            const statusInfo = getBookingStatus(booking);

            // Calculate payment summary
            const totalPaid = booking.payments?.reduce((sum, payment) => 
                sum + parseFloat(payment.amount), 0) || 0;
            const totalAmount = detail?.total_price || 0;
            const balance = totalAmount - totalPaid;

            // Generate room list HTML
            const roomListHtml = booking.summaries?.length 
                ? booking.summaries.map(summary => {
                    const room = summary.facility;
                    return room ? `
                        <li class="text-sm text-gray-700">
                            ${room.name} — ${formatCurrency(room.price)}
                        </li>` : '';
                }).join('')
                : '<li class="text-sm text-gray-600">No room info available</li>';

            // Generate the HTML template
            const html = `
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <!-- Status & Guest Info -->
                    <div class="mb-6 text-center">
                        <div class="px-4 py-2 inline-flex text-lg leading-5 font-semibold rounded-full ${statusInfo.class} text-white mb-3">
                            ${statusInfo.text}
                        </div>
                        <h3 class="text-xl font-bold text-gray-900">${booking.reference}</h3>
                        <div class="mt-2">
                            <h4 class="text-md font-medium text-gray-800">
                                ${booking.user?.firstname || 'Guest'} ${booking.user?.lastname || ''}
                            </h4>
                            <p class="text-sm text-gray-600 mt-1">
                                <i class="fas fa-envelope mr-1"></i> ${booking.user?.email || 'N/A'}
                            </p>
                            <p class="text-sm text-gray-600">
                                <i class="fas fa-phone mr-1"></i> ${booking.user?.phone || 'N/A'}
                            </p>
                        </div>
                    </div>

                    <!-- Room Information -->
                    <div class="border-t border-gray-200 pt-4 mb-4">
                        <h5 class="text-md font-semibold text-gray-800 mb-3 flex items-center">
                            <i class="fas fa-hotel mr-2 text-blue-500"></i>
                            Room(s) Booked
                        </h5>
                        <ul class="space-y-2 pl-5 list-disc">
                            ${roomListHtml}
                        </ul>
                    </div>

                    <!-- Stay Details -->
                    <div class="border-t border-gray-200 pt-4 mb-4">
                        <h5 class="text-md font-semibold text-gray-800 mb-3 flex items-center">
                            <i class="far fa-calendar-alt mr-2 text-blue-500"></i>
                            Stay Details
                        </h5>
                        <div class="space-y-2">
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-600 flex items-center">
                                    <i class="fas fa-sign-in-alt mr-2 text-gray-400"></i> Check-in:
                                </span>
                                <span class="text-sm font-medium text-gray-800">
                                    ${formatDate(detail?.checkin_date)}
                                </span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-600 flex items-center">
                                    <i class="fas fa-sign-out-alt mr-2 text-gray-400"></i> Check-out:
                                </span>
                                <span class="text-sm font-medium text-gray-800">
                                    ${formatDate(detail?.checkout_date)}
                                </span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-600 flex items-center">
                                    <i class="fas fa-moon mr-2 text-gray-400"></i> Nights:
                                </span>
                                <span class="text-sm font-medium text-gray-800">
                                    ${getNights(detail?.checkin_date, detail?.checkout_date)}
                                </span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Payment Summary -->
                    <div class="border-t border-gray-200 pt-4">
                        <h5 class="text-md font-semibold text-gray-800 mb-3 flex items-center">
                            <i class="fas fa-receipt mr-2 text-blue-500"></i>
                            Payment Summary
                        </h5>
                        <div class="space-y-3">
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-600">Total Amount:</span>
                                <span class="text-sm font-medium text-gray-800">
                                    ${formatCurrency(totalAmount)}
                                </span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-600">Amount Paid:</span>
                                <span class="text-sm font-medium text-green-600">
                                    ${formatCurrency(totalPaid)}
                                </span>
                            </div>
                            <div class="flex justify-between border-t border-gray-100 pt-2">
                                <span class="text-sm font-semibold text-gray-700">Balance:</span>
                                <span class="text-sm font-semibold ${balance > 0 ? 'text-red-600' : 'text-green-600'}">
                                    ${formatCurrency(Math.abs(balance))}
                                    ${balance > 0 ? '(Due)' : '(Overpaid)'}
                                </span>
                            </div>
                            <div class="flex justify-between mt-4 pt-2 border-t border-gray-100">
                                <span class="text-sm text-gray-600">Payment Method:</span>
                                <span class="text-sm font-medium text-gray-800">
                                    ${booking.payments?.[0]?.method || 'N/A'}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            
            // Insert HTML into DOM
            document.getElementById('booking-summary').innerHTML = html;

        } catch (error) {
            toastr.error('Failed to load booking details');
            console.error('Error:', error);
            document.getElementById('booking-summary').innerHTML = `
                <div class="bg-red-50 border-l-4 border-red-400 p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="fas fa-exclamation-circle text-red-400"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-red-700">
                                Failed to load booking details. Please try again later.
                            </p>
                        </div>
                    </div>
                </div>
            `;
        }
    }
    
    // Helper functions
    function formatDate(dateString) {
        if (!dateString) return 'N/A';
        const date = new Date(dateString);
        const options = { weekday: 'short', year: 'numeric', month: 'short', day: 'numeric' };
        return date.toLocaleDateString('en-US', options);
    }
    
    function formatCurrency(amount) {
        return '₱' + parseFloat(amount || 0).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
    }
    
    function getNights(checkin, checkout) {
        if (!checkin || !checkout) return 0;
        const oneDay = 24 * 60 * 60 * 1000;
        const firstDate = new Date(checkin);
        const secondDate = new Date(checkout);
        return Math.round(Math.abs((firstDate - secondDate) / oneDay));
    }
});
</script>
@endsection