@extends('layouts.admin')

@section('title', 'Bookings')

@php
    $active = 'bookings';
@endphp

@section('content_css')
<style>
    .glass-card {
        background: rgba(255, 255, 255, 0.8);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.2);
        box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.1);
        border-radius: 12px;
    }
    
    .hover-scale {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .status-badge {
        transition: all 0.3s ease;
    }
    
    .status-badge:hover {
        transform: scale(1.05);
    }
    
    .custom-scroll::-webkit-scrollbar {
        width: 6px;
        height: 6px;
    }
    
    .custom-scroll::-webkit-scrollbar-track {
        background: rgba(0, 0, 0, 0.05);
        border-radius: 10px;
    }
    
    .custom-scroll::-webkit-scrollbar-thumb {
        background: rgba(0, 0, 0, 0.1);
        border-radius: 10px;
    }
    
    .custom-scroll::-webkit-scrollbar-thumb:hover {
        background: rgba(0, 0, 0, 0.2);
    }
    
    .animate-pulse {
        animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
    }
    
    @keyframes pulse {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.5; }
    }
    
    .sticky-container {
        position: sticky;
        top: 1.5rem;
        max-height: calc(100vh - 2rem);
        overflow-y: auto;
    }
    
    .tab-active {
        background-color: #dc2626;
        color: white;
        box-shadow: 0 4px 6px -1px rgba(220, 38, 38, 0.1), 0 2px 4px -1px rgba(220, 38, 38, 0.06);
    }
    
    .tab-inactive {
        color: #4b5563;
        background-color: transparent;
    }
    
    .tab-inactive:hover {
        background-color: #f3f4f6;
    }
    
    .fade-in {
        animation: fadeIn 0.3s ease-in;
    }
    
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    .booking-row {
        transition: all 0.2s ease;
    }
    
    .booking-row:hover {
        background-color: #fef2f2 !important;
    }
    
    .booking-row.selected {
        background-color: #fee2e2 !important;
    }
</style>
@endsection

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex flex-col lg:flex-row gap-6">
        <!-- Main Content -->
        <div class="lg:w-3/4">
            <div class="glass-card p-6 hover-scale">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6 gap-4">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-800">Bookings Management</h1>
                        <p class="text-gray-600 mt-1">Manage all guest reservations and payments</p>
                    </div>
                    
                    <!-- Search Bar -->
                    <div class="relative w-full md:w-64">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        <input id="search-input" type="text" class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg leading-5 bg-white/50 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 sm:text-sm" placeholder="Search bookings...">
                    </div>
                </div>
                
                <!-- Status Tabs -->
                <div class="flex flex-wrap gap-2 mb-6">
                    <button class="px-4 py-2 rounded-lg font-medium tab-active" data-status="fully_paid">
                        Fully Paid
                    </button>
                    <button class="px-4 py-2 rounded-lg font-medium tab-inactive" data-status="verified">
                        Advance
                    </button>
                    <button class="px-4 py-2 rounded-lg font-medium tab-inactive" data-status="under_verification">
                        Under Verification
                    </button>
                    <button class="px-4 py-2 rounded-lg font-medium tab-inactive" data-status="rejected">
                        Rejected
                    </button>
                </div>
                
                <!-- Booking Table -->
                <div class="overflow-x-auto custom-scroll">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Guest</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Reservation Code</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Check-in</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Check-out</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Collected By</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200" id="bookings-table-body">
                            <!-- Loading state -->
                            <tr>
                                <td colspan="7" class="px-6 py-8 text-center">
                                    <div class="flex justify-center">
                                        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-red-600"></div>
                                    </div>
                                    <p class="mt-2 text-gray-500">Loading bookings...</p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination and Excel Button -->
                <div class="flex flex-col sm:flex-row justify-between items-center mt-6 gap-4">
                    <div id="pagination-info" class="text-sm text-gray-600"></div>
                    <div class="flex gap-2">
                        <button id="prev-page" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg font-medium hover:bg-gray-200 transition-colors disabled:opacity-50 disabled:cursor-not-allowed" disabled>
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                            </svg>
                        </button>
                        <button id="next-page" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg font-medium hover:bg-gray-200 transition-colors disabled:opacity-50 disabled:cursor-not-allowed" disabled>
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                            </svg>
                        </button>
                        <button id="generate-excel" class="bg-green-600 text-white rounded-lg font-medium hover:from-green-700 hover:to-green-800 transition-colors flex items-center gap-2 shadow-md hover:shadow-lg px-4 py-2 ">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 384 512" fill="currentColor">
                                <path d="M224 136V0H24C10.7 0 0 10.7 0 24v464c0 13.3 10.7 24 24 24h336c13.3 0 24-10.7 24-24V160H248c-13.2 0-24-10.8-24-24zm60.1 106.5L224 336l60.1 93.5c5.1 8-.6 18.5-10.1 18.5h-34.9c-4.4 0-8.5-2.4-10.6-6.3C208.9 405.5 192 373 192 373c-6.4 14.8-10 20-36.6 68.8-2.1 3.9-6.1 6.3-10.5 6.3H110c-9.5 0-15.2-10.5-10.1-18.5l60.3-93.5-60.3-93.5c-5.2-8 .6-18.5 10.1-18.5h34.8c4.4 0 8.5 2.4 10.6 6.3 26.1 48.8 20 35.6 36.6 68.5 0 0 6.1-11.7 36.6-68.5 2.1-3.9 6.2-6.3 10.6-6.3H274c9.5-.1 15.2 10.4 10.1 18.4zM384 121.9v6.1H256V0h6.1c6.4 0 12.5 2.5 17 7l97.9 98c4.5 4.5 7 10.6 7 16.9z"/>
                            </svg>
                            Export Excel
                        </button>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Booking Summary Sidebar -->
        <div class="lg:w-1/4">
            <!-- Sticky Wrapper -->
            <div class="sticky-container space-y-6">
                <!-- Next Check-in Section -->
                <div class="glass-card p-6 hover-scale">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-800">Next Check-in</h3>
                        <div class="bg-red-100 text-red-800 text-xs font-medium px-2 py-1 rounded-full animate-pulse">
                            Upcoming
                        </div>
                    </div>
                    <p class="text-gray-600 mb-4" id="next-checkin-time">Loading...</p>
                    <div class="bg-gradient-to-r from-red-50 to-red-100 p-4 rounded-lg border border-red-100">
                        <div class="flex items-center mb-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-red-500 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd" />
                            </svg>
                            <p class="font-medium text-gray-800" id="next-checkin-date">-</p>
                        </div>
                        <div class="flex items-center mb-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-red-500 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd" />
                            </svg>
                            <p class="text-gray-600" id="next-checkin-nights">-</p>
                        </div>
                        <div class="flex items-center mb-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-red-500 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                            </svg>
                            <p class="font-medium text-gray-800" id="next-checkin-guest">-</p>
                        </div>
                        <div class="flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-red-500 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z" />
                            </svg>
                            <p class="text-gray-600" id="next-checkin-phone">-</p>
                        </div>
                    </div>
                </div>
                
                <!-- Booking Summary -->
                <div class="glass-card overflow-hidden hover-scale">
                    <div class="bg-gradient-to-r from-red-600 to-red-800 p-4 text-white">
                        <h2 class="text-xl font-bold flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z" />
                            </svg>
                            Booking Details
                        </h2>
                    </div>
                    <div class="p-0 fade-in" id="booking-summary">
                        <div class="text-center py-10 px-4 text-gray-400">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto mb-4 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                            <p>Select a booking to view details</p>
                        </div>
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
    let currentStatus = 'fully_paid';
    let currentPage = 1;
    let totalPages = 1;
    const perPage = 10;
    let searchQuery = '';
    
    // Load initial data
    loadBookings(currentStatus, currentPage);
    loadNextCheckin();
    
    // Search input handler with debounce
    const searchInput = document.getElementById('search-input');
    let searchTimeout;
    
    searchInput.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        searchQuery = this.value.trim();
        
        // Debounce the search to avoid too many requests
        searchTimeout = setTimeout(() => {
            currentPage = 1;
            loadBookings(currentStatus, currentPage);
        }, 500);
    });
    
    // Tab click handler
    document.querySelectorAll('[data-status]').forEach(tab => {
        tab.addEventListener('click', function() {
            currentStatus = this.dataset.status;
            currentPage = 1;
            searchQuery = ''; // Reset search when changing tabs
            searchInput.value = ''; // Clear search input
            
            // Update active tab
            document.querySelectorAll('[data-status]').forEach(t => {
                t.classList.remove('tab-active');
                t.classList.add('tab-inactive');
            });
            this.classList.remove('tab-inactive');
            this.classList.add('tab-active');
            
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
    
        fetch(`/admin/bookings/export?status=${status}${searchQuery ? `&search=${encodeURIComponent(searchQuery)}` : ''}`, {
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
            
            showToast('success', 'Excel file downloaded successfully');
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
                
                showToast('info', 'No bookings found. Downloaded empty template with headers.');
            } else {
                showToast('error', error.message || 'Failed to generate Excel file');
            }
        })
        .finally(() => {
            excelBtn.innerHTML = originalHtml;
            excelBtn.disabled = false;
        });
    }
    
    const STATUS_CONFIG = {
        'fully_paid': {
            class: 'bg-blue-600',
            text: 'FULLY PAID'
        },
        'verified': {
            class: 'bg-green-600',
            text: 'VERIFIED'
        },
        'under_verification': {
            class: 'bg-yellow-600',
            text: 'UNDER VERIFICATION'
        },
        'rejected': {
            class: 'bg-red-600',
            text: 'REJECTED'
        }
    };

    // Function to load bookings
    function loadBookings(status, page = 1) {
        const url = new URL(`/get/admin/bookings`, window.location.origin);
        url.searchParams.append('status', status);
        url.searchParams.append('page', page);
        url.searchParams.append('per_page', perPage);
        if (searchQuery) {
            url.searchParams.append('search', searchQuery);
        }
        
        // Show loading state
        document.getElementById('bookings-table-body').innerHTML = `
            <tr>
                <td colspan="7" class="px-6 py-8 text-center">
                    <div class="flex justify-center">
                        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-red-600"></div>
                    </div>
                    <p class="mt-2 text-gray-500">Loading bookings...</p>
                </td>
            </tr>
        `;
        
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
                html = `
                    <tr>
                        <td colspan="7" class="px-6 py-8 text-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <p class="mt-2 text-gray-500">No bookings found</p>
                        </td>
                    </tr>
                `;
            } else {
                bookings.forEach(booking => {
                    // Get the payment status from the first payment or use booking status as fallback
                    const paymentStatus = booking.payments?.[0]?.status || booking.status;
                    const statusInfo = STATUS_CONFIG[paymentStatus] || STATUS_CONFIG.under_verification;
                    
                    html += `
                        <tr class="booking-row cursor-pointer" data-booking-id="${booking.id}">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full ${statusInfo.class} text-white status-badge">
                                    ${statusInfo.text}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">${booking.user?.firstname || 'Guest'} ${booking.user?.lastname || ''}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900 font-medium">${booking.reference}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">${formatDate(booking.details[0]?.checkin_date)}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">${formatDate(booking.details[0]?.checkout_date)}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900 font-medium">${formatCurrency((booking.details[0]?.total_price * 0.5) || 0)}</div>
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
                    
                    // Highlight selected row
                    document.querySelectorAll('.booking-row').forEach(r => {
                        r.classList.remove('selected');
                    });
                    this.classList.add('selected');
                });
            });
        })
        .catch(error => {
            showToast('error', 'Failed to load bookings');
            console.error('Error:', error);
            document.getElementById('bookings-table-body').innerHTML = `
                <tr>
                    <td colspan="7" class="px-6 py-8 text-center">
                        <div class="bg-red-50 border-l-4 border-red-400 p-4">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-red-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm text-red-700">
                                        Failed to load bookings. Please try again later.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>
            `;
        });
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
            showToast('error', error.message || 'Failed to load next check-in');
        });
    }
    
    // Function to load booking summary
    async function loadBookingSummary(bookingId) {
        try {
            // Show loading state
            document.getElementById('booking-summary').innerHTML = `
                <div class="text-center py-10 px-4">
                    <div class="flex justify-center">
                        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-red-600"></div>
                    </div>
                    <p class="mt-2 text-gray-500">Loading booking details...</p>
                </div>
            `;
            
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
            const paymentStatus = booking.payments?.[0]?.status || booking.status;
            const statusInfo = STATUS_CONFIG[paymentStatus] || STATUS_CONFIG.under_verification;
            
            // Calculate payment summary using amount_paid
            const totalPaid = booking.payments?.reduce((sum, payment) => 
                sum + (payment.amount_paid ? parseFloat(payment.amount_paid) : 0), 0) || 0;
            
            const totalAmount = detail?.total_price || 0;
            const balance = totalAmount - totalPaid;

            // Generate room list HTML
            const roomListHtml = booking.summaries?.length 
                ? booking.summaries.map(summary => {
                    const room = summary.facility;
                    return room ? `
                        <li class="flex justify-between py-2 border-b border-gray-100 last:border-0">
                            <span class="text-sm text-gray-700">${room.name}</span>
                            <span class="text-sm font-medium text-gray-800">${formatCurrency(room.price)}</span>
                        </li>` : '';
                }).join('')
                : '<li class="text-sm text-gray-600 py-2">No room info available</li>';

            // Generate payment history HTML - updated to show amount_paid
            const paymentHistoryHtml = booking.payments?.length
                ? booking.payments.map(payment => `
                    <li class="flex justify-between py-2 border-b border-gray-100 last:border-0">
                        <div>
                            <span class="text-sm font-medium text-gray-800">${formatDate(payment.payment_date)}</span>
                            <span class="block text-xs text-gray-500">${payment.method || 'N/A'}</span>
                        </div>
                        <span class="text-sm font-medium text-green-600">${formatCurrency(payment.amount_paid || 0)}</span>
                    </li>
                `).join('')
                : '<li class="text-sm text-gray-600 py-2">No payment history</li>';

            // Generate the HTML template - updated payment summary section
            const html = `
                <div class="divide-y divide-gray-200 fade-in">
                    <!-- Header with Status and Guest Info -->
                    <div class="px-4 py-5 bg-white">
                        <div class="flex justify-between items-start">
                            <div>
                                <h3 class="text-lg font-bold text-gray-900">${booking.reference}</h3>
                                <p class="text-sm text-gray-500 mt-1">Booking ID: ${booking.id}</p>
                            </div>
                            <span class="px-3 py-1 rounded-full text-lg font-semibold ${statusInfo.class} text-white status-badge">
                                ${statusInfo.text}
                            </span>
                        </div>
                        
                        <div class="mt-4">
                            <h4 class="text-md font-semibold text-gray-800 flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                                </svg>
                                Guest Information
                            </h4>
                            <div class="mt-2 pl-7">
                                <p class="text-sm font-medium text-gray-800">${booking.user?.firstname || 'Guest'} ${booking.user?.lastname || ''}</p>
                                <p class="text-sm text-gray-600 mt-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 inline mr-1" viewBox="0 0 20 20" fill="currentColor">
                                        <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z" />
                                        <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z" />
                                    </svg>
                                    ${booking.user?.email || 'N/A'}
                                </p>
                                <p class="text-sm text-gray-600">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 inline mr-1" viewBox="0 0 20 20" fill="currentColor">
                                        <path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z" />
                                    </svg>
                                    ${booking.user?.phone || 'N/A'}
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Stay Details -->
                    <div class="px-4 py-5 bg-white">
                        <h4 class="text-md font-semibold text-gray-800 flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd" />
                            </svg>
                            Stay Details
                        </h4>
                        <div class="mt-3 pl-7 space-y-3">
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-600">Check-in:</span>
                                <span class="text-sm font-medium text-gray-800">
                                    ${formatDate(detail?.checkin_date)}
                                </span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-600">Check-out:</span>
                                <span class="text-sm font-medium text-gray-800">
                                    ${formatDate(detail?.checkout_date)}
                                </span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-600">Nights:</span>
                                <span class="text-sm font-medium text-gray-800">
                                    ${getNights(detail?.checkin_date, detail?.checkout_date)}
                                </span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Rooms Booked -->
                    <div class="px-4 py-5 bg-white">
                        <h4 class="text-md font-semibold text-gray-800 flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z" />
                            </svg>
                            Rooms Booked
                        </h4>
                        <ul class="mt-3 pl-7">
                            ${roomListHtml}
                        </ul>
                    </div>
                    
                    <!-- Payment Summary -->
                    <div class="px-4 py-5 bg-white">
                        <h4 class="text-md font-semibold text-gray-800 flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M8.433 7.418c.155-.103.346-.196.567-.267v1.698a2.305 2.305 0 01-.567-.267C8.07 8.34 8 8.114 8 8c0-.114.07-.34.433-.582zM11 12.849v-1.698c.22.071.412.164.567.267.364.243.433.468.433.582 0 .114-.07.34-.433.582a2.305 2.305 0 01-.567.267z" />
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-13a1 1 0 10-2 0v.092a4.535 4.535 0 00-1.676.662C6.602 6.234 6 7.009 6 8c0 .99.602 1.765 1.324 2.246.48.32 1.054.545 1.676.662v1.941c-.391-.127-.68-.317-.843-.504a1 1 0 10-1.51 1.31c.562.649 1.413 1.076 2.353 1.253V15a1 1 0 102 0v-.092a4.535 4.535 0 001.676-.662C13.398 13.766 14 12.991 14 12c0-.99-.602-1.765-1.324-2.246A4.535 4.535 0 0011 9.092V7.151c.391.127.68.317.843.504a1 1 0 101.511-1.31c-.563-.649-1.413-1.076-2.354-1.253V5z" clip-rule="evenodd" />
                            </svg>
                            Payment Summary
                        </h4>
                        <div class="mt-3 pl-7 space-y-3">
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
                            <div class="flex justify-between pt-2 border-t border-gray-100">
                                <span class="text-sm font-semibold text-gray-700">Balance:</span>
                                <span class="text-sm font-semibold ${balance > 0 ? 'text-red-600' : 'text-green-600'}">
                                    ${formatCurrency(Math.abs(balance))}
                                    ${balance > 0 ? '(Due)' : '(Overpaid)'}
                                </span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Payment History -->
                    <div class="px-4 py-5 bg-white">
                        <h4 class="text-md font-semibold text-gray-800 flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4zm6 4a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd" />
                            </svg>
                            Payment History
                        </h4>
                        <ul class="mt-3 pl-7">
                            ${paymentHistoryHtml}
                        </ul>
                    </div>
                    
                    <!-- Actions -->
                    <div class="px-4 py-4 bg-gray-50 flex justify-end space-x-3">
                        <button class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-300 transition-colors">
                            Edit
                        </button>
                        <button class="px-4 py-2 bg-red-600 text-white rounded-lg text-sm font-medium hover:bg-red-700 transition-colors">
                            Cancel Booking
                        </button>
                    </div>
                </div>
            `;
            
            // Insert HTML into DOM
            document.getElementById('booking-summary').innerHTML = html;

        } catch (error) {
            console.error('Error:', error);
            document.getElementById('booking-summary').innerHTML = `
                <div class="p-4">
                    <div class="bg-red-50 border-l-4 border-red-400 p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-red-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-red-700">
                                    Failed to load booking details. Please try again later.
                                </p>
                            </div>
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
    
    function showToast(type, message) {
        // Create toast element
        const toast = document.createElement('div');
        toast.className = `fixed top-4 right-4 z-50 px-4 py-3 rounded-lg shadow-lg text-white ${
            type === 'success' ? 'bg-green-600' : 
            type === 'error' ? 'bg-red-600' : 
            type === 'info' ? 'bg-blue-600' : 'bg-gray-600'
        }`;
        toast.innerHTML = `
            <div class="flex items-center">
                <svg class="h-6 w-6 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="${
                        type === 'success' ? 'M5 13l4 4L19 7' : 
                        type === 'error' ? 'M6 18L18 6M6 6l12 12' : 
                        'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z'
                    }" />
                </svg>
                <span>${message}</span>
            </div>
        `;
        
        // Add to DOM
        document.body.appendChild(toast);
        
        // Remove after delay
        setTimeout(() => {
            toast.classList.add('opacity-0', 'transition-opacity', 'duration-300');
            setTimeout(() => {
                toast.remove();
            }, 300);
        }, 3000);
    }
});
</script>
@endsection