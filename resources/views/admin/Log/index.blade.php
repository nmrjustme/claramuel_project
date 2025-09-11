@extends('layouts.admin')
@section('title', 'Inquiries Monitoring')
@php
$active = 'inquiries';
@endphp

@section('content_css')
<style>
    .full-height-container {
        display: flex;
        flex-direction: column;
        height: calc(100vh - 12rem);
        /* Adjust based on your header height */
    }

    .table-container {
        flex: 1;
        overflow-y: auto;
    }

    @media (max-width: 1023px) {
        .full-height-container {
            height: auto;
            /* Reset height on smaller screens */
        }
    }
</style>
@endsection

@section('content')
<div class="min-h-screen px-6 py-6">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Request Monitoring</h1>
            <p class="text-gray-600">To track requests before they become actual bookings</p>
        </div>

        <div class="flex items-center space-x-4">
            <button id="refreshBtn" class="flex items-center text-blue-600 hover:text-blue-800">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd"
                        d="M4 2a1 1 0 011 1v2.101a7.002 7.002 0 0111.601 2.566 1 1 0 11-1.885.666A5.002 5.002 0 005.999 7H9a1 1 0 010 2H4a1 1 0 01-1-1V3a1 1 0 011-1zm.008 9.057a1 1 0 011.276.61A5.002 5.002 0 0014.001 13H11a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0v-2.101a7.002 7.002 0 01-11.601-2.566 1 1 0 01.61-1.276z"
                        clip-rule="evenodd" />
                </svg>
                Refresh
            </button>
        </div>
    </div>

    <!-- Search and Filter Section -->
    <div class="mb-6 flex justify-between items-center">
        <div class="relative max-w-md flex items-center gap-2">
            <div class="flex gap-2">
                <button id="filterRead"
                    class="px-4 py-2 rounded-md text-xs font-medium bg-green-100 text-green-800 hover:bg-blue-200 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    Read
                </button>
                <button id="filterUnread"
                    class="px-4 py-2 rounded-md text-xs font-medium bg-red-100 text-red-800 hover:bg-purple-200 focus:outline-none focus:ring-2 focus:ring-purple-500">
                    Unread
                </button>
                <button id="filterAll"
                    class="px-4 py-2 rounded-md text-xs font-medium bg-gray-200 text-gray-800 hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500">
                    All
                </button>
            </div>
        </div>

    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 h-full">
        <!-- Pending Inquiries Container (Right) -->
        <div class="bg-white rounded-lg overflow-hidden shadow-lg full-height-container">
            <div class="bg-gradient-to-r from-yellow-50 to-gray-50 px-6 py-4 border-b border-gray-200 
                flex items-center justify-between">

                <!-- Title -->
                <h2 class="text-lg font-semibold text-gray-800 flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-yellow-600" viewBox="0 0 20 20"
                        fill="currentColor">
                        <path fill-rule="evenodd"
                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z"
                            clip-rule="evenodd" />
                    </svg>
                    Waiting Confirmation
                    <span id="pendingCount"
                        class="ml-2 bg-yellow-100 text-yellow-800 text-xs px-2 py-1 rounded-full">0</span>
                </h2>

                <!-- Search -->
                <div class="w-72 relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z"
                                clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <input id="pendingSearchInput" type="text"
                        class="block w-full pl-10 pr-3 py-2 border border-darkgray rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                        placeholder="Search by name, ID or status">
                </div>
            </div>

            <!-- Table -->
            <div class="table-container">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-yellow-50 sticky top-0 z-10">
                        <tr>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">
                                Record ID
                            </th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">
                                Customer
                            </th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">
                                Read Status
                            </th>
                            <th scope="col"
                                class="px-6 py-3 text-center text-xs font-medium text-gray-700 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody id="pending-table-body" class="bg-white divide-y divide-gray-200">
                        <tr>
                            <td colspan="4" class="px-6 py-4 text-center text-gray-500">
                                Loading pending inquiries...
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div id="pending-pagination"
                class="bg-white px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6">
                <!-- Pagination for pending inquiries -->
            </div>
        </div>

        
        <!-- Confirmed Inquiries Container (Left) -->
        <div class="bg-white rounded-lg overflow-hidden shadow-lg full-height-container">
            <div class="bg-gradient-to-r from-green-50 to-gray-50 px-6 py-4 border-b border-gray-200 
                flex items-center justify-between">
                <h2 class="text-lg font-semibold text-gray-800 flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-green-600" viewBox="0 0 20 20"
                        fill="currentColor">
                        <path fill-rule="evenodd"
                            d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                            clip-rule="evenodd" />
                    </svg>
                    Confirmed Inquiries
                    <span id="confirmedCount"
                        class="ml-2 bg-green-100 text-green-800 text-xs px-2 py-1 rounded-full">0</span>
                </h2>
                <div class="w-72 relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z"
                                clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <input id="confirmedSearchInput" type="text"
                        class="block w-full pl-10 pr-3 py-2 border border-darkgray rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                        placeholder="Search by name, ID or status">
                </div>
            </div>
            <div class="table-container">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-green-50 sticky top-0 z-10">
                        <tr>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">
                                Record ID
                            </th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">
                                Customer
                            </th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">
                                Read Status
                            </th>
                            <th scope="col"
                                class="px-6 py-3 text-center text-xs font-medium text-gray-700 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody id="confirmed-table-body" class="bg-white divide-y divide-gray-200">
                        <!-- Confirmed data will be loaded here -->
                        <tr>
                            <td colspan="4" class="px-6 py-4 text-center text-gray-500">Loading confirmed inquiries...
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div id="confirmed-pagination"
                class="bg-white px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6">
                <!-- Pagination for confirmed inquiries -->
            </div>
        </div>
    </div>
</div>
@endsection

@section('content_js')
<script>
    document.addEventListener('DOMContentLoaded', function() {
    const confirmedTableBody = document.getElementById('confirmed-table-body');
    const pendingTableBody = document.getElementById('pending-table-body');
    const refreshBtn = document.getElementById('refreshBtn');
    const confirmedCount = document.getElementById('confirmedCount');
    const pendingCount = document.getElementById('pendingCount');
    const confirmedSearchInput = document.getElementById('confirmedSearchInput');
    const pendingSearchInput = document.getElementById('pendingSearchInput');
    const confirmedPaginationDiv = document.getElementById('confirmed-pagination');
    const pendingPaginationDiv = document.getElementById('pending-pagination');
    const filterRead = document.getElementById('filterRead');
    const filterUnread = document.getElementById('filterUnread');
    const filterAll = document.getElementById('filterAll');
    
    let currentConfirmedPage = 1;
    let currentPendingPage = 1;
    let totalConfirmedPages = 1;
    let totalPendingPages = 1;
    let confirmedSearchTerm = '';
    let pendingSearchTerm = '';
    let confirmedReadFilter = 'all'; // 'all', 'read', 'unread'
    let pendingReadFilter = 'all';   // 'all', 'read', 'unread'

    const searchInquiryId = sessionStorage.getItem('searchInquiryId');
    if (searchInquiryId) {
        confirmedSearchInput.value = searchInquiryId;
        pendingSearchInput.value = searchInquiryId;
        confirmedSearchTerm = searchInquiryId;
        pendingSearchTerm = searchInquiryId;
        sessionStorage.removeItem('searchInquiryId'); // Clear it after use
    }
    
    fetchConfirmedInquiries();
    fetchPendingInquiries();

    // Function to format time as "X time ago"
    function timeAgo(dateTime) {
        const now = new Date();
        const pastDate = new Date(dateTime);
        const seconds = Math.floor((now - pastDate) / 1000);
        
        if (seconds < 0) {
            return "just now";
        }
        
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
    
    function getDateCreated(dateInput) {
        const date = dateInput instanceof Date ? dateInput : new Date(dateInput);
        return date.toLocaleDateString("en-US", {
            month: "long",
            day: "numeric",
            year: "numeric"
        });
    }
    
    // Function to add a new booking to the appropriate table with animation
    function addNewBooking(booking) {
        const isConfirmed = booking.status === 'confirmed';
        const tableBody = isConfirmed ? confirmedTableBody : pendingTableBody;
        const countElement = isConfirmed ? confirmedCount : pendingCount;

        const row = document.createElement('tr');
        row.className = `hover:bg-gray-50 bg-blue-50 animate-pulse ${booking.is_read ? '' : 'bg-red-50'}`;
        row.innerHTML = `
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">${booking.id}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                ${booking.user.firstname} ${booking.user.lastname}
                <div class="text-sm text-gray-500">
                    ${booking.user.email}
                </div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${booking.is_read ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'}">
                    ${booking.is_read ? 'Read' : 'Unread'}
                </span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-center">
                <button onclick="viewBookingDetails(${booking.id}, this)" 
                    class="text-blue-600 hover:text-blue-900 inline-flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
                        <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 0 018 0z" clip-rule="evenodd" />
                    </svg>
                    Full Details
                </button>
            </td>
        `;
        
        setTimeout(() => {
            row.classList.remove('animate-pulse', 'bg-blue-50');
        }, 2000);
        
        if (tableBody.firstChild) {
            tableBody.insertBefore(row, tableBody.firstChild);
        } else {
            tableBody.appendChild(row);
        }
        
        countElement.textContent = parseInt(countElement.textContent || 0) + 1;
    }

    // Function to render confirmed inquiries
    function renderConfirmedInquiries(bookings, paginationData = null) {
        if (paginationData) {
            confirmedCount.textContent = paginationData.total;
            totalConfirmedPages = paginationData.last_page;
        } else {
            confirmedCount.textContent = bookings.length;
        }
        
        confirmedTableBody.innerHTML = '';
        
        if (bookings.length === 0) {
            confirmedTableBody.innerHTML = `
                <tr>
                    <td colspan="4" class="px-6 py-4 text-center text-gray-500">No confirmed inquiries found</td>
                </tr>
            `;
            renderConfirmedPagination();
            return;
        }
        
        bookings.forEach(booking => {
            const row = document.createElement('tr');
            row.className = `hover:bg-gray-50 ${booking.is_read ? '' : 'bg-red-50'}`;
            row.innerHTML = `
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">${booking.id}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                    ${booking.user.firstname} ${booking.user.lastname}
                    <div class="text-sm text-gray-500">
                        ${booking.user.email}
                    </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${booking.is_read ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'}">
                        ${booking.is_read ? 'Read' : 'Unread'}
                    </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-center">
                    <button onclick="viewBookingDetails(${booking.id}, this)" 
                        class="text-blue-600 hover:text-blue-900 inline-flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" viewBox="0 0 20 20" fill="currentColor">
                            <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
                            <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 0 018 0z" clip-rule="evenodd" />
                        </svg>
                        Full Details
                    </button>
                </td>
            `;
            confirmedTableBody.appendChild(row);
        });
        
        renderConfirmedPagination();
    }
    
    // Function to render pending inquiries
    function renderPendingInquiries(bookings, paginationData = null) {
        if (paginationData) {
            pendingCount.textContent = paginationData.total;
            totalPendingPages = paginationData.last_page;
        } else {
            pendingCount.textContent = bookings.length;
        }
        
        pendingTableBody.innerHTML = '';
        
        if (bookings.length === 0) {
            pendingTableBody.innerHTML = `
                <tr>
                    <td colspan="4" class="px-6 py-4 text-center text-gray-500">No pending inquiries found</td>
                </tr>
            `;
            renderPendingPagination();
            return;
        }
        
        bookings.forEach(booking => {
            const row = document.createElement('tr');
            row.className = `hover:bg-gray-50 ${booking.is_read ? '' : 'bg-red-50'}`;
            row.innerHTML = `
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">${booking.id}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                    ${booking.user.firstname} ${booking.user.lastname}
                    <div class="text-sm text-gray-500">
                        ${booking.user.email}
                    </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${booking.is_read ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'}">
                        ${booking.is_read ? 'Read' : 'Unread'}
                    </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-center">
                    <button onclick="viewBookingDetails(${booking.id}, this)" 
                        class="text-blue-600 hover:text-blue-900 inline-flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" viewBox="0 0 20 20" fill="currentColor">
                            <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
                            <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd" />
                        </svg>
                        Full Details
                    </button>
                </td>
            `;
            pendingTableBody.appendChild(row);
        });
        
        renderPendingPagination();
    }
    
    // Function to render pagination for confirmed inquiries
    function renderConfirmedPagination() {
        if (totalConfirmedPages <= 1) {
            confirmedPaginationDiv.innerHTML = '';
            return;
        }

        let paginationHTML = `
            <div class="flex-1 flex justify-between sm:hidden">
                <button onclick="changeConfirmedPage(${currentConfirmedPage > 1 ? currentConfirmedPage - 1 : 1})" class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white ${currentConfirmedPage === 1 ? 'opacity-50 cursor-not-allowed' : 'hover:bg-gray-50'}">
                    Previous
                </button>
                <button onclick="changeConfirmedPage(${currentConfirmedPage < totalConfirmedPages ? currentConfirmedPage + 1 : totalConfirmedPages})" class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white ${currentConfirmedPage === totalConfirmedPages ? 'opacity-50 cursor-not-allowed' : 'hover:bg-gray-50'}">
                    Next
                </button>
            </div>
            <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                <div>
                    <p class="text-sm text-gray-700">
                        Showing <span class="font-medium">${(currentConfirmedPage - 1) * 15 + 1}</span> to <span class="font-medium">${Math.min(currentConfirmedPage * 15, confirmedCount.textContent)}</span> of <span class="font-medium">${confirmedCount.textContent}</span> results
                    </p>
                </div>
                <div>
                    <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
        `;

        // Previous button
        paginationHTML += `
            <button onclick="changeConfirmedPage(${currentConfirmedPage > 1 ? currentConfirmedPage - 1 : 1})" class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 ${currentConfirmedPage === 1 ? 'opacity-50 cursor-not-allowed' : 'hover:bg-gray-50'}">
                <span class="sr-only">Previous</span>
                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                </svg>
            </button>
        `;

        // Page numbers
        const maxVisiblePages = 5;
        let startPage, endPage;

        if (totalConfirmedPages <= maxVisiblePages) {
            startPage = 1;
            endPage = totalConfirmedPages;
        } else {
            const maxPagesBeforeCurrent = Math.floor(maxVisiblePages / 2);
            const maxPagesAfterCurrent = Math.ceil(maxVisiblePages / 2) - 1;
            
            if (currentConfirmedPage <= maxPagesBeforeCurrent) {
                startPage = 1;
                endPage = maxVisiblePages;
            } else if (currentConfirmedPage + maxPagesAfterCurrent >= totalConfirmedPages) {
                startPage = totalConfirmedPages - maxVisiblePages + 1;
                endPage = totalConfirmedPages;
            } else {
                startPage = currentConfirmedPage - maxPagesBeforeCurrent;
                endPage = currentConfirmedPage + maxPagesAfterCurrent;
            }
        }

        if (startPage > 1) {
            paginationHTML += `
                <button onclick="changeConfirmedPage(1)" class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50">
                    1
                </button>
                ${startPage > 2 ? '<span class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700">...</span>' : ''}
            `;
        }

        for (let i = startPage; i <= endPage; i++) {
            paginationHTML += `
                <button onclick="changeConfirmedPage(${i})" class="relative inline-flex items-center px-4 py-2 border ${currentConfirmedPage === i ? 'z-10 bg-blue-50 border-blue-500 text-blue-600' : 'bg-white border-gray-300 text-gray-500 hover:bg-gray-50'} text-sm font-medium">
                    ${i}
                </button>
            `;
        }

        if (endPage < totalConfirmedPages) {
            paginationHTML += `
                ${endPage < totalConfirmedPages - 1 ? '<span class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700">...</span>' : ''}
                <button onclick="changeConfirmedPage(${totalConfirmedPages})" class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50">
                    ${totalConfirmedPages}
                </button>
            `;
        }

        // Next button
        paginationHTML += `
            <button onclick="changeConfirmedPage(${currentConfirmedPage < totalConfirmedPages ? currentConfirmedPage + 1 : totalConfirmedPages})" class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 ${currentConfirmedPage === totalConfirmedPages ? 'opacity-50 cursor-not-allowed' : 'hover:bg-gray-50'}">
                <span class="sr-only">Next</span>
                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                </svg>
            </button>
            </nav>
            </div>
            </div>
        `;

        confirmedPaginationDiv.innerHTML = paginationHTML;
    }
    
    // Function to render pagination for pending inquiries
    function renderPendingPagination() {
        if (totalPendingPages <= 1) {
            pendingPaginationDiv.innerHTML = '';
            return;
        }

        let paginationHTML = `
            <div class="flex-1 flex justify-between sm:hidden">
                <button onclick="changePendingPage(${currentPendingPage > 1 ? currentPendingPage - 1 : 1})" class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white ${currentPendingPage === 1 ? 'opacity-50 cursor-not-allowed' : 'hover:bg-gray-50'}">
                    Previous
                </button>
                <button onclick="changePendingPage(${currentPendingPage < totalPendingPages ? currentPendingPage + 1 : totalPendingPages})" class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white ${currentPendingPage === totalPendingPages ? 'opacity-50 cursor-not-allowed' : 'hover:bg-gray-50'}">
                    Next
                </button>
            </div>
            <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                <div>
                    <p class="text-sm text-gray-700">
                        Showing <span class="font-medium">${(currentPendingPage - 1) * 15 + 1}</span> to <span class="font-medium">${Math.min(currentPendingPage * 15, pendingCount.textContent)}</span> of <span class="font-medium">${pendingCount.textContent}</span> results
                    </p>
                </div>
                <div>
                    <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
        `;

        // Previous button
        paginationHTML += `
            <button onclick="changePendingPage(${currentPendingPage > 1 ? currentPendingPage - 1 : 1})" class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 ${currentPendingPage === 1 ? 'opacity-50 cursor-not-allowed' : 'hover:bg-gray-50'}">
                <span class="sr-only">Previous</span>
                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                </svg>
            </button>
        `;

        // Page numbers
        const maxVisiblePages = 5;
        let startPage, endPage;

        if (totalPendingPages <= maxVisiblePages) {
            startPage = 1;
            endPage = totalPendingPages;
        } else {
            const maxPagesBeforeCurrent = Math.floor(maxVisiblePages / 2);
            const maxPagesAfterCurrent = Math.ceil(maxVisiblePages / 2) - 1;
            
            if (currentPendingPage <= maxPagesBeforeCurrent) {
                startPage = 1;
                endPage = maxVisiblePages;
            } else if (currentPendingPage + maxPagesAfterCurrent >= totalPendingPages) {
                startPage = totalPendingPages - maxVisiblePages + 1;
                endPage = totalPendingPages;
            } else {
                startPage = currentPendingPage - maxPagesBeforeCurrent;
                endPage = currentPendingPage + maxPagesAfterCurrent;
            }
        }

        if (startPage > 1) {
            paginationHTML += `
                <button onclick="changePendingPage(1)" class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50">
                    1
                </button>
                ${startPage > 2 ? '<span class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700">...</span>' : ''}
            `;
        }

        for (let i = startPage; i <= endPage; i++) {
            paginationHTML += `
                <button onclick="changePendingPage(${i})" class="relative inline-flex items-center px-4 py-2 border ${currentPendingPage === i ? 'z-10 bg-blue-50 border-blue-500 text-blue-600' : 'bg-white border-gray-300 text-gray-500 hover:bg-gray-50'} text-sm font-medium">
                    ${i}
                </button>
            `;
        }

        if (endPage < totalPendingPages) {
            paginationHTML += `
                ${endPage < totalPendingPages - 1 ? '<span class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700">...</span>' : ''}
                <button onclick="changePendingPage(${totalPendingPages})" class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50">
                    ${totalPendingPages}
                </button>
            `;
        }

        // Next button
        paginationHTML += `
            <button onclick="changePendingPage(${currentPendingPage < totalPendingPages ? currentPendingPage + 1 : totalPendingPages})" class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 ${currentPendingPage === totalPendingPages ? 'opacity-50 cursor-not-allowed' : 'hover:bg-gray-50'}">
                <span class="sr-only">Next</span>
                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                </svg>
            </button>
            </nav>
            </div>
            </div>
        `;

        pendingPaginationDiv.innerHTML = paginationHTML;
    }

    // Function to change confirmed page
    window.changeConfirmedPage = function(page) {
        if (page < 1 || page > totalConfirmedPages || page === currentConfirmedPage) return;
        currentConfirmedPage = page;
        fetchConfirmedInquiries();
    }
    
    // Function to change pending page
    window.changePendingPage = function(page) {
        if (page < 1 || page > totalPendingPages || page === currentPendingPage) return;
        currentPendingPage = page;
        fetchPendingInquiries();
    }

    // Function to fetch confirmed inquiries with loading state
    function fetchConfirmedInquiries() {
        // Show loading state
        confirmedTableBody.innerHTML = `
            <tr id="loadingRow">
                <td colspan="4" class="px-6 py-4 text-center">
                    <div class="flex flex-col justify-center items-center gap-2">
                        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-green-600"></div>
                        <span>Loading confirmed inquiries...</span>
                    </div>
                </td>
            </tr>
        `;
        
        let url = `/get/inquiries/confirmed?page=${currentConfirmedPage}`;
        if (confirmedSearchTerm) {
            url += `&search=${encodeURIComponent(confirmedSearchTerm)}`;
        }
        
        // Add read status filter to the URL
        if (confirmedReadFilter !== 'all') {
            url += `&read_status=${confirmedReadFilter}`;
        }
        
        fetch(url)
            .then(response => response.json())
            .then(data => {
                renderConfirmedInquiries(data.data, {
                    current_page: data.current_page,
                    last_page: data.last_page,
                    total: data.total
                });
            })
            .catch(error => {
                console.error('Error:', error);
                confirmedTableBody.innerHTML = `
                    <tr>
                        <td colspan="4" class="px-6 py-4 text-center text-red-500">
                            Failed to load confirmed inquiries. Please try again.
                        </td>
                    </tr>
                `;
            });
    }
    
    // Function to fetch pending inquiries with loading state
    function fetchPendingInquiries() {
        // Show loading state
        pendingTableBody.innerHTML = `
            <tr id="loadingRow">
                <td colspan="4" class="px-6 py-4 text-center">
                    <div class="flex flex-col justify-center items-center gap-2">
                        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-yellow-600"></div>
                        <span>Loading pending inquiries...</span>
                    </div>
                </td>
            </tr>
        `;
        
        let url = `/get/inquiries/pending?page=${currentPendingPage}`;
        if (pendingSearchTerm) {
            url += `&search=${encodeURIComponent(pendingSearchTerm)}`;
        }
        
        // Add read status filter to the URL
        if (pendingReadFilter !== 'all') {
            url += `&read_status=${pendingReadFilter}`;
        }
        
        fetch(url)
            .then(response => response.json())
            .then(data => {
                renderPendingInquiries(data.data, {
                    current_page: data.current_page,
                    last_page: data.last_page,
                    total: data.total
                });
            })
            .catch(error => {
                console.error('Error:', error);
                pendingTableBody.innerHTML = `
                    <tr>
                        <td colspan="4" class="px-6 py-4 text-center text-red-500">
                            Failed to load pending inquiries. Please try again.
                        </td>
                    </tr>
                `;
            });
    }

    // Initial load
    fetchConfirmedInquiries();
    fetchPendingInquiries();
    
    // Refresh button
    refreshBtn.addEventListener('click', function() {
        currentConfirmedPage = 1;
        currentPendingPage = 1;
        fetchConfirmedInquiries();
        fetchPendingInquiries();
    });

    // Search input event listeners - SEPARATE FOR EACH TABLE
    confirmedSearchInput.addEventListener('input', function(e) {
        confirmedSearchTerm = e.target.value.trim();
        currentConfirmedPage = 1;
        
        // Add a small delay to prevent too many requests while typing
        clearTimeout(this.timer);
        this.timer = setTimeout(() => {
            fetchConfirmedInquiries();
        }, 500);
    });

    pendingSearchInput.addEventListener('input', function(e) {
        pendingSearchTerm = e.target.value.trim();
        currentPendingPage = 1;
        
        // Add a small delay to prevent too many requests while typing
        clearTimeout(this.timer);
        this.timer = setTimeout(() => {
            fetchPendingInquiries();
        }, 500);
    });

    // Filter button event listeners - SEPARATE FOR EACH TABLE
    filterRead.addEventListener('click', function() {
        confirmedReadFilter = 'read';
        pendingReadFilter = 'read';
        currentConfirmedPage = 1;
        currentPendingPage = 1;
        updateActiveFilter();
        fetchConfirmedInquiries();
        fetchPendingInquiries();
    });

    filterUnread.addEventListener('click', function() {
        confirmedReadFilter = 'unread';
        pendingReadFilter = 'unread';
        currentConfirmedPage = 1;
        currentPendingPage = 1;
        updateActiveFilter();
        fetchConfirmedInquiries();
        fetchPendingInquiries();
    });

    filterAll.addEventListener('click', function() {
        confirmedReadFilter = 'all';
        pendingReadFilter = 'all';
        currentConfirmedPage = 1;
        currentPendingPage = 1;
        updateActiveFilter();
        fetchConfirmedInquiries();
        fetchPendingInquiries();
    });

    // Function to update active filter button styles
    function updateActiveFilter() {
        // Reset all buttons
        [filterRead, filterUnread, filterAll].forEach(btn => {
            btn.classList.remove('ring-2', 'ring-offset-2');
            // Restore original classes
            btn.className = 'px-4 py-2 rounded-md text-xs font-medium focus:outline-none focus:ring-2';
            
            // Add back the specific color classes for each button
            if (btn === filterRead) {
                btn.classList.add('bg-green-100', 'text-green-800', 'hover:bg-blue-200', 'focus:ring-blue-500');
            } else if (btn === filterUnread) {
                btn.classList.add('bg-red-100', 'text-red-800', 'hover:bg-purple-200', 'focus:ring-purple-500');
            } else if (btn === filterAll) {
                btn.classList.add('bg-gray-100', 'text-gray-800', 'hover:bg-gray-200', 'focus:ring-gray-500');
            }
        });
                
        // Highlight active button
        let activeButton;
        const currentFilter = confirmedReadFilter; // Both tables use the same filter
        switch(currentFilter) {
            case 'read':
                activeButton = filterRead;
                break;
            case 'unread':
                activeButton = filterUnread;
                break;
            default:
                activeButton = filterAll;
        }
        
        activeButton.classList.add('ring-2', 'ring-offset-2');
        if (activeButton === filterRead) activeButton.classList.add('ring-blue-500');
        if (activeButton === filterUnread) activeButton.classList.add('ring-purple-500');
        if (activeButton === filterAll) activeButton.classList.add('ring-gray-500');
    }

    // Initialize active filter
    updateActiveFilter();

    // Listen for new booking events
    if (typeof Echo !== 'undefined') {
        window.Echo.channel('bookings')
            .listen('.booking.created', (e) => {
                console.log('New booking received:', e);
                console.log('Booking status:', e.booking.status);
                addNewBooking(e.booking);
            });
    }
});

async function viewBookingDetails(bookingId, buttonElement) {
    // Show loading state on the button
    const originalContent = buttonElement.innerHTML;
    buttonElement.innerHTML = '<span class="animate-spin">↻ </span> Redirecting...';
    buttonElement.disabled = true;

    try {
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
            // Update UI
            const row = buttonElement.closest('tr');
            if (row) {
                row.classList.remove('bg-red-50', 'animate-pulse');

                const statusBadge = row.querySelector('span.bg-red-100');
                if (statusBadge) {
                    statusBadge.classList.remove('bg-red-200', 'text-red-800');
                    statusBadge.classList.add('bg-green-200', 'text-green-800');
                    statusBadge.textContent = 'Read';
                }

                const unreadBadge = row.querySelector('.unread-badge');
                if (unreadBadge) unreadBadge.remove();
            }

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

            // ✅ Redirect to booking details page after marking as read
            window.location.href = `/admin/booking-details/${bookingId}`;
        }
    } catch (error) {
        console.error("Error:", error);
        buttonElement.innerHTML = originalContent; // restore button
        buttonElement.disabled = false;
    }
}
</script>
@endsection