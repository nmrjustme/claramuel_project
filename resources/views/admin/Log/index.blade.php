@extends('layouts.admin')
@section('title', 'Inquiries Monitoring')
@php
    $active = 'inquiries';
@endphp

@section('content')
<div class="min-h-screen px-6 py-6">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Request Monitoring</h1>
            <p class="text-gray-600">Manually verify customer GCash payments by reference number</p>
        </div>

        <div class="flex items-center space-x-4">
            <span class="text-sm text-gray-600">
                Total Inquiries: <span id="bookingCount" class="font-bold">0</span>
            </span>
            <span class="text-sm text-gray-600 ml-4">
                <span class="inline-block w-3 h-3 bg-red-200 rounded-full mr-1"></span> Unread requests
            </span>
            <button id="refreshBtn" class="flex items-center text-blue-600 hover:text-blue-800">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M4 2a1 1 0 011 1v2.101a7.002 7.002 0 0111.601 2.566 1 1 0 11-1.885.666A5.002 5.002 0 005.999 7H9a1 1 0 010 2H4a1 1 0 01-1-1V3a1 1 0 011-1zm.008 9.057a1 1 0 011.276.61A5.002 5.002 0 0014.001 13H11a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0v-2.101a7.002 7.002 0 01-11.601-2.566 1 1 0 01.61-1.276z" clip-rule="evenodd" />
                </svg>
                Refresh
            </button>
        </div>
    </div>

    <!-- Search Section -->
    <div class="mb-6">
        <div class="relative max-w-md">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <svg class="h-5 w-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd"></path>
                </svg>
            </div>
            <input id="searchInput" type="text" class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-blue-500 focus:border-blue-500 sm:text-sm" placeholder="Search by name, ID or status">
        </div>
    </div>

    <!-- Booking List -->
    <div class="bg-white rounded-lg overflow-hidden border border-lightGray">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Booking ID</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Read Status</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Booking Status</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Request Time</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody id="bookings-table-body" class="bg-white divide-y divide-gray-200">
                    <!-- Data will be loaded here via JavaScript -->
                </tbody>
            </table>
        </div>
        <!-- Pagination -->
        <div id="pagination" class="bg-white px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6">
            <!-- Pagination will be loaded here -->
        </div>
    </div>
</div>
@endsection

@section('content_js')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const bookingsTableBody = document.getElementById('bookings-table-body');
    const refreshBtn = document.getElementById('refreshBtn');
    const bookingCount = document.getElementById('bookingCount');
    const searchInput = document.getElementById('searchInput');
    const paginationDiv = document.getElementById('pagination');
    
    let allBookings = [];
    let currentPage = 1;
    let totalPages = 1;
    let currentSearchTerm = '';

    const searchInquiryId = sessionStorage.getItem('searchInquiryId');
    if (searchInquiryId) {
        searchInput.value = searchInquiryId;
        currentSearchTerm = searchInquiryId;
        sessionStorage.removeItem('searchInquiryId'); // Clear it after use
    }
    fetchBookings();

    // Function to format time as "X time ago"
    function timeAgo(dateTime) {
        const seconds = Math.floor((new Date() - new Date(dateTime)) / 1000);
        
        let interval = Math.floor(seconds / 31536000);
        if (interval >= 1) {
            return interval + " year" + (interval === 1 ? "" : "s") + " ago";
        }
        interval = Math.floor(seconds / 2592000);
        if (interval >= 1) {
            return interval + " month" + (interval === 1 ? "" : "s") + " ago";
        }
        interval = Math.floor(seconds / 86400);
        if (interval >= 1) {
            return interval + " day" + (interval === 1 ? "" : "s") + " ago";
        }
        interval = Math.floor(seconds / 3600);
        if (interval >= 1) {
            return interval + " hour" + (interval === 1 ? "" : "s") + " ago";
        }
        interval = Math.floor(seconds / 60);
        if (interval >= 1) {
            return interval + " minute" + (interval === 1 ? "" : "s") + " ago";
        }
        return Math.floor(seconds) + " second" + (Math.floor(seconds) === 1 ? "" : "s") + " ago";
    }

    // Function to get status color and text
    function getStatusInfo(status) {
        switch(status.toLowerCase()) {
            case 'confirmed':
                return { class: 'bg-green-100 text-green-800', text: 'Confirmed' };
            case 'pending_confirmation':
                return { class: 'bg-yellow-100 text-yellow-800', text: 'Pending' };
            case 'rejected':
                return { class: 'bg-red-100 text-red-800', text: 'Rejected' };
            case 'completed':
                return { class: 'bg-blue-100 text-blue-800', text: 'Completed' };
            default:
                return { class: 'bg-gray-100 text-gray-800', text: status };
        }
    }

    // Function to add a new booking to the table with animation
    function addNewBooking(booking) {
        const statusInfo = getStatusInfo(booking.status);
        const row = document.createElement('tr');
        row.className = `hover:bg-gray-50 bg-blue-50 animate-pulse ${booking.is_read ? '' : 'bg-red-50'}`;
        row.innerHTML = `
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">${booking.id}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                ${booking.user.firstname} ${booking.user.lastname}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${booking.is_read ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'}">
                    ${booking.is_read ? 'Read' : 'Unread'}
                </span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${statusInfo.class}">
                    ${statusInfo.text}
                </span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500" title="${new Date(booking.created_at).toLocaleString()}">
                ${timeAgo(booking.created_at)}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline" viewBox="0 0 20 20" fill="currentColor">
                    <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
                    <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd" />
                </svg>
                <button onclick="viewBooking(${booking.id}, this)" data-id="${booking.id}" class="text-blue-600 hover:text-blue-900 mr-3">View</button>
            </td>
        `;
        
        setTimeout(() => {
            row.classList.remove('animate-pulse', 'bg-blue-50');
        }, 2000);
        
        if (bookingsTableBody.firstChild) {
            bookingsTableBody.insertBefore(row, bookingsTableBody.firstChild);
        } else {
            bookingsTableBody.appendChild(row);
        }
        
        bookingCount.textContent = parseInt(bookingCount.textContent || 0) + 1;
    }

    // Function to render bookings to the table
    function renderBookings(bookings, paginationData = null) {
        if (paginationData) {
            currentPage = paginationData.current_page;
            totalPages = paginationData.last_page;
            bookingCount.textContent = paginationData.total;
        } else {
            bookingCount.textContent = bookings.length;
        }
        
        bookingsTableBody.innerHTML = '';
        
        if (bookings.length === 0) {
            bookingsTableBody.innerHTML = `
                <tr>
                    <td colspan="6" class="px-6 py-4 text-center text-gray-500">No bookings found</td>
                </tr>
            `;
            renderPagination();
            return;
        }
        
        bookings.forEach(booking => {
            const statusInfo = getStatusInfo(booking.status);
            const row = document.createElement('tr');
            row.className = `hover:bg-gray-50 ${booking.is_read ? '' : 'bg-red-50'}`;
            row.innerHTML = `
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">${booking.id}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    ${booking.user.firstname} ${booking.user.lastname}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${booking.is_read ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'}">
                        ${booking.is_read ? 'Read' : 'Unread'}
                    </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${statusInfo.class}">
                        ${statusInfo.text}
                    </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500" title="${new Date(booking.created_at).toLocaleString()}">
                    ${timeAgo(booking.created_at)}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline text-blue-600" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
                        <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd" />
                    </svg>
                    <button onclick="viewBooking(${booking.id}, this)" data-id="${booking.id}" class="text-blue-600 hover:text-blue-900 mr-3">View</button>
                </td>
            `;
            bookingsTableBody.appendChild(row);
        });
        
        renderPagination();
    }
    
    // Function to render pagination
    function renderPagination() {
        if (totalPages <= 1) {
            paginationDiv.innerHTML = '';
            return;
        }

        let paginationHTML = `
            <div class="flex-1 flex justify-between sm:hidden">
                <button onclick="changePage(${currentPage > 1 ? currentPage - 1 : 1})" class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white ${currentPage === 1 ? 'opacity-50 cursor-not-allowed' : 'hover:bg-gray-50'}">
                    Previous
                </button>
                <button onclick="changePage(${currentPage < totalPages ? currentPage + 1 : totalPages})" class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white ${currentPage === totalPages ? 'opacity-50 cursor-not-allowed' : 'hover:bg-gray-50'}">
                    Next
                </button>
            </div>
            <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                <div>
                    <p class="text-sm text-gray-700">
                        Showing <span class="font-medium">${(currentPage - 1) * 15 + 1}</span> to <span class="font-medium">${Math.min(currentPage * 15, bookingCount.textContent)}</span> of <span class="font-medium">${bookingCount.textContent}</span> results
                    </p>
                </div>
                <div>
                    <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
        `;

        // Previous button
        paginationHTML += `
            <button onclick="changePage(${currentPage > 1 ? currentPage - 1 : 1})" class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 ${currentPage === 1 ? 'opacity-50 cursor-not-allowed' : 'hover:bg-gray-50'}">
                <span class="sr-only">Previous</span>
                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                </svg>
            </button>
        `;

        // Page numbers
        const maxVisiblePages = 5;
        let startPage, endPage;

        if (totalPages <= maxVisiblePages) {
            startPage = 1;
            endPage = totalPages;
        } else {
            const maxPagesBeforeCurrent = Math.floor(maxVisiblePages / 2);
            const maxPagesAfterCurrent = Math.ceil(maxVisiblePages / 2) - 1;
            
            if (currentPage <= maxPagesBeforeCurrent) {
                startPage = 1;
                endPage = maxVisiblePages;
            } else if (currentPage + maxPagesAfterCurrent >= totalPages) {
                startPage = totalPages - maxVisiblePages + 1;
                endPage = totalPages;
            } else {
                startPage = currentPage - maxPagesBeforeCurrent;
                endPage = currentPage + maxPagesAfterCurrent;
            }
        }

        if (startPage > 1) {
            paginationHTML += `
                <button onclick="changePage(1)" class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50">
                    1
                </button>
                ${startPage > 2 ? '<span class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700">...</span>' : ''}
            `;
        }

        for (let i = startPage; i <= endPage; i++) {
            paginationHTML += `
                <button onclick="changePage(${i})" class="relative inline-flex items-center px-4 py-2 border ${currentPage === i ? 'z-10 bg-blue-50 border-blue-500 text-blue-600' : 'bg-white border-gray-300 text-gray-500 hover:bg-gray-50'} text-sm font-medium">
                    ${i}
                </button>
            `;
        }

        if (endPage < totalPages) {
            paginationHTML += `
                ${endPage < totalPages - 1 ? '<span class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700">...</span>' : ''}
                <button onclick="changePage(${totalPages})" class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50">
                    ${totalPages}
                </button>
            `;
        }

        // Next button
        paginationHTML += `
            <button onclick="changePage(${currentPage < totalPages ? currentPage + 1 : totalPages})" class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 ${currentPage === totalPages ? 'opacity-50 cursor-not-allowed' : 'hover:bg-gray-50'}">
                <span class="sr-only">Next</span>
                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                </svg>
            </button>
            </nav>
            </div>
            </div>
        `;

        paginationDiv.innerHTML = paginationHTML;
    }

    // Function to change page
    window.changePage = function(page) {
        if (page < 1 || page > totalPages || page === currentPage) return;
        currentPage = page;
        fetchBookings();
    }

    // Function to fetch bookings with loading state
    function fetchBookings() {
        // Show loading state
        bookingsTableBody.innerHTML = `
            <tr id="loadingRow">
                <td colspan="6" class="px-6 py-4 text-center">
                    <div class="flex flex-col justify-center items-center gap-2">
                        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-red-600"></div>
                        <span>Loading inquiries...</span>
                    </div>
                </td>
            </tr>
        `;
        
        let url = `/get/mybooking?page=${currentPage}`;
        if (currentSearchTerm) {
            url += `&search=${encodeURIComponent(currentSearchTerm)}`;
        }
        
        fetch(url)
            .then(response => response.json())
            .then(data => {
                allBookings = data.data;
                renderBookings(data.data, {
                    current_page: data.current_page,
                    last_page: data.last_page,
                    total: data.total
                });
            })
            .catch(error => {
                console.error('Error:', error);
                bookingsTableBody.innerHTML = `
                    <tr>
                        <td colspan="6" class="px-6 py-4 text-center text-red-500">
                            Failed to load bookings. Please try again.
                        </td>
                    </tr>
                `;
            });
    }

    // Initial load
    fetchBookings();
    
    // Refresh button
    refreshBtn.addEventListener('click', function() {
        currentPage = 1;
        fetchBookings();
    });

    // Search input event listener
    searchInput.addEventListener('input', function(e) {
        currentSearchTerm = e.target.value.trim();
        currentPage = 1;
        
        // Add a small delay to prevent too many requests while typing
        clearTimeout(this.timer);
        this.timer = setTimeout(() => {
            fetchBookings();
        }, 500);
    });

    // Listen for new booking events
    if (typeof Echo !== 'undefined') {
        window.Echo.channel('bookings')
            .listen('.booking.created', (e) => {
                console.log('New booking received:', e);
                addNewBooking(e.booking);
            });
    }
});


// View booking function
async function viewBooking(bookingId, buttonElement) {
    try {
        // Show loading state on the button
        const originalText = buttonElement.textContent;
        buttonElement.innerHTML = '<span class="animate-spin">↻</span> Loading...';
        buttonElement.disabled = true;

        // First mark the booking as read
        const markReadResponse = await fetch(`/api/inquiries/mark-read/${bookingId}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        });

        const result = await markReadResponse.json();
        
        if (result.success) {
            // Update the UI to show it's been read
            const row = buttonElement.closest('tr');
            if (row) {
                row.classList.remove('bg-red-50', 'animate-pulse');
                
                // Update the status badge
                const statusBadge = row.querySelector('span.bg-red-100');
                if (statusBadge) {
                    statusBadge.classList.remove('bg-red-200', 'text-red-800');
                    statusBadge.classList.add('bg-green-200', 'text-green-800');
                    statusBadge.textContent = 'Read';
                }
                
                // Remove any unread badge if present
                const unreadBadge = row.querySelector('.unread-badge');
                if (unreadBadge) unreadBadge.remove();
            }
            
            // Update the new bookings count if the element exists
            const newBookingsCount = document.getElementById('newBookingsCount');
            if (newBookingsCount) {
                const currentCount = parseInt(newBookingsCount.textContent) || 0;
                if (currentCount > 0) {
                    newBookingsCount.textContent = currentCount - 1;
                    if (currentCount - 1 <= 0) {
                        newBookingsCount.classList.add('hidden');
                    }
                }
            }
        }

        // Now open the modal to show booking details
        // Create a temporary button with the data-id attribute
        const tempButton = document.createElement('button');
        tempButton.setAttribute('data-id', bookingId);
        window.openModal_accept_inquirer(tempButton);

        // Reset button state after modal is shown
        setTimeout(() => {
            buttonElement.innerHTML = originalText;
            buttonElement.disabled = false;
        }, 1000);
        
    } catch (error) {
        console.error('Error viewing booking:', error);
        buttonElement.textContent = 'Error';
        setTimeout(() => {
            buttonElement.textContent = originalText;
            buttonElement.disabled = false;
        }, 1000);
    }
}
</script>

@include('admin.modals.accept_inquirer')
@endsection