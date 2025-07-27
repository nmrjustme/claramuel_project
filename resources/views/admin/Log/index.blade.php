@extends('layouts.admin')
@section('title', 'Inquiries Log')
@php
    $active = 'inquiries';
@endphp

@section('content_css')
<style>
    /* Base Styles */
    :root {
        --primary: #3b82f6;
        --primary-dark: #2563eb;
        --secondary: #10b981;
        --danger: #ef4444;
        --warning: #f59e0b;
        --info: #3b82f6;
        --light: #f8fafc;
        --dark: #1e293b;
        --gray: #64748b;
        --gray-light: #e2e8f0;
    }
    
    /* Animation styles */
    @keyframes pop-in {
        0% { transform: scale(0.95); opacity: 0; }
        100% { transform: scale(1); opacity: 1; }
    }
    
    @keyframes fade-in-up {
        0% { transform: translateY(10px); opacity: 0; }
        100% { transform: translateY(0); opacity: 1; }
    }
    
    @keyframes fade-out {
        0% { opacity: 1; }
        100% { opacity: 0; }
    }
    
    @keyframes pulse {
        0%, 100% { background-color: rgba(254, 226, 226, 0.5); }
        50% { background-color: rgba(254, 202, 202, 0.7); }
    }
    
    @keyframes slideIn {
        from { transform: translateX(20px); opacity: 0; }
        to { transform: translateX(0); opacity: 1; }
    }
    
    @keyframes pulseBorder {
        0%, 100% { border-left-color: #ff0000; }
        50% { border-left-color: #ff6666; }
    }
    
    /* Notification styles */
    .animate-pop-in { animation: pop-in 0.2s cubic-bezier(0.175, 0.885, 0.32, 1.275) forwards; }
    .animate-fade-in-up { animation: fade-in-up 0.3s ease-out forwards; }
    .animate-fade-out { animation: fade-out 0.2s ease-out forwards; }
    .animate-pulse { animation: pulse 1.5s cubic-bezier(0.4, 0, 0.6, 1) infinite; }
    
    /* Notification components */
    .app-notification {
        position: fixed;
        right: 20px;
        bottom: 20px;
        max-width: 350px;
        min-width: 300px;
        width: auto;
        padding: 12px 16px;
        border-radius: 12px;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        display: flex;
        align-items: center;
        z-index: 1000;
        transform: translateY(20px);
        opacity: 0;
        animation: slideIn 0.3s forwards;
        color: white;
        font-family: 'Inter', sans-serif;
    }
    
    .notification-content {
        display: flex;
        align-items: center;
        width: 100%;
        gap: 10px;
    }
        
    .notification-icon {
        flex-shrink: 0;
    }
    
    .notification-icon svg {
        width: 20px;
        height: 20px;
    }
    
    .notification-body {
        flex: 1;
        min-width: 0;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .notification-message {
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        flex: 1;
    }
    
    .notification-actions {
        display: flex;
        gap: 8px;
        margin-left: 8px;
    }
    
    .notification-action {
        background: transparent;
        border: none;
        color: white;
        font-size: 13px;
        font-weight: 500;
        text-decoration: underline;
        cursor: pointer;
        padding: 0;
        white-space: nowrap;
    }
    
    .notification-close {
        background: transparent;
        border: none;
        color: white;
        cursor: pointer;
        padding: 0;
        margin-left: 8px;
        flex-shrink: 0;
    }
    
    .notification-close svg {
        width: 16px;
        height: 16px;
    }
    
    .notification-info {
        background-color: var(--info);
    }
    
    .notification-success {
        background-color: var(--secondary);
    }
    
    .notification-warning {
        background-color: var(--warning);
    }
    
    .notification-error {
        background-color: var(--danger);
    }
    
    .priority-critical {
        border-left: 4px solid #ff0000;
        animation: pulseBorder 1.5s infinite;
    }
    
    /* Table Styles */
    table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
        table-layout: fixed;
    }
    
    th {
        position: sticky;
        top: 0;
        background-color: #f8fafc;
        z-index: 10;
        padding: 12px 16px;
        text-align: left;
        font-weight: 600;
        color: var(--dark);
        border-bottom: 1px solid var(--gray-light);
    }
    
    td {
        padding: 12px 16px;
        vertical-align: middle;
        border-bottom: 1px solid var(--gray-light);
        position: relative;
    }

    th:nth-child(1), td:nth-child(1) { width: 5%; }
    th:nth-child(2), td:nth-child(2) { width: 25%; }
    th:nth-child(3), td:nth-child(3) { width: 20%; }
    th:nth-child(4), td:nth-child(4) { width: 20%; }
    th:nth-child(5), td:nth-child(5) { width: 30%; }
    
    /* Rounded corners for first and last cells */
    th:first-child, td:first-child {
        border-top-left-radius: 8px;
        border-bottom-left-radius: 8px;
        padding-left: 20px;
    }
    
    th:last-child, td:last-child {
        border-top-right-radius: 8px;
        border-bottom-right-radius: 8px;
    }
    
    .unread-booking td:first-child::before {
        content: '';
        position: absolute;
        left: 0;
        top: 0;
        bottom: 0;
        width: 4px;
        background-color: var(--danger);
        border-radius: 2px 0 0 2px;
    }
    
    /* New booking highlight */
    .new-booking-highlight {
        animation: highlight-fade 2.5s forwards;
    }
    
    @keyframes highlight-fade {
        0% { background-color: rgba(254, 202, 202, 0.7); }
        100% { background-color: transparent; }
    }
    
    @keyframes flash {
        0%, 100% { background-color: transparent; }
        50% { background-color: rgba(254, 202, 202, 0.5); }
    }
    
    .new-booking-flash {
        animation: flash 0.8s ease-in-out 2;
    }
    
    /* Status badges */
    .status-badge {
        display: inline-flex;
        align-items: center;
        padding: 4px 10px;
        border-radius: 9999px;
        font-size: 12px;
        font-weight: 600;
        line-height: 1;
    }
    
    /* Stats cards */
    .stats-card {
        transition: all 0.2s ease;
        border-radius: 12px;
    }
    
    .stats-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
    }
    
    /* Buttons */
    .btn {
        transition: all 0.2s ease;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-weight: 500;
    }
    
    .btn:hover {
        transform: translateY(-1px);
    }
    
    .btn:active {
        transform: translateY(0);
    }
    
    /* Form elements */
    input[type="text"], 
    input[type="email"],
    input[type="password"],
    select {
        transition: all 0.2s ease;
        border: 1px solid var(--gray-light);
    }
    
    input[type="text"]:focus, 
    input[type="email"]:focus,
    input[type="password"]:focus,
    select:focus {
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.2);
    }
    
    /* Responsive tweaks */
    @media (max-width: 1200px) {
        th:nth-child(1), td:nth-child(1) { width: 7%; }
        th:nth-child(2), td:nth-child(2) { width: 12%; }
    }

    @media (max-width: 992px) {
        th:nth-child(1), td:nth-child(1) { width: 8%; }
        th:nth-child(3), td:nth-child(3) { width: 18%; }
        th:nth-child(4), td:nth-child(4),
        th:nth-child(5), td:nth-child(5) { width: 12%; }
    }

    @media (max-width: 768px) {
        table, thead, tbody, th, td, tr {
            display: block;
        }
        
        thead tr {
            position: absolute;
            top: -9999px;
            left: -9999px;
        }
        
        tr {
            margin-bottom: 1rem;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        
        td {
            border: none;
            position: relative;
            padding-left: 50%;
            width: 100%;
            text-align: right;
        }
        
        td::before {
            content: attr(data-label);
            position: absolute;
            left: 16px;
            width: 45%;
            padding-right: 10px;
            font-weight: 600;
            text-align: left;
        }
        
        .unread-booking td:first-child::before {
            left: 20px;
        }
        
        .unread-booking td:first-child::before {
            display: none;
        }
        
        .unread-booking {
            border-left: 4px solid var(--danger);
        }
    }
</style>
@endsection

@section('content')
<audio id="notificationSound" src="{{ asset('sounds/mixkit-software-interface-back-2575.wav') }}" preload="auto"></audio>

<div class="min-h-screen p-6">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Request Monitoring</h1>
            <p class="text-gray-600">Manage and track all facility bookings</p>
        </div>
        
        <div class="flex space-x-3">
            <button onclick="markAllAsRead()" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                Mark All as Read
            </button>
            
            <div class="relative">
                <button onclick="loadBookings()" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-800 transition-colors flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                    Refresh
                    <span id="new-bookings-count" class="hidden new-bookings-count absolute -top-2 -right-2 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center animate-pulse"></span>
                </button>
            </div>
        </div>
    </div>
    
    <!-- Real-time stats summary -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        
        <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-200 hover-scale relative overflow-hidden glassy-card">
            <div class="absolute -right-6 -top-6 h-16 w-16 rounded-full bg-blue-100 opacity-20"></div>
            <div class="absolute -right-4 -top-4 h-12 w-12 rounded-full bg-blue-200 opacity-30"></div>
            <div class="relative z-10 flex justify-between items-start">
                <div>
                    <p class="text-sm font-medium text-blue-700">Total Inquiries</p>
                    <h3 class="text-2xl font-bold text-blue-900 mt-1" id="total-inquiries">24</h3>
                </div>
                <div class="p-2 bg-white rounded-lg shadow-inner">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-200 hover-scale relative overflow-hidden glassy-card">
            <div class="absolute -right-6 -top-6 h-16 w-16 rounded-full bg-yellow-100 opacity-20"></div>
            <div class="absolute -right-4 -top-4 h-12 w-12 rounded-full bg-yellow-200 opacity-30"></div>
            <div class="relative z-10 flex justify-between items-start">
                <div>
                    <p class="text-sm font-medium text-yellow-700">Pending Request</p>
                    <h3 class="text-2xl font-bold text-yellow-900 mt-1" id="pending-requests">8</h3>
                </div>
                <div class="p-2 bg-white rounded-lg shadow-inner">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-yellow-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-200 hover-scale relative overflow-hidden glassy-card">
            <div class="absolute -right-6 -top-6 h-16 w-16 rounded-full bg-green-100 opacity-20"></div>
            <div class="absolute -right-4 -top-4 h-12 w-12 rounded-full bg-green-200 opacity-30"></div>
            <div class="relative z-10 flex justify-between items-start">
                <div>
                    <p class="text-sm font-medium text-green-700">Confirmed Request</p>
                    <h3 class="text-2xl font-bold text-green-900 mt-1" id="confirmed-requests">11</h3>
                </div>
                <div class="p-2 bg-white rounded-lg shadow-inner">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
        </div>
        
        <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-200 hover-scale relative overflow-hidden glassy-card">
            <div class="absolute -right-6 -top-6 h-16 w-16 rounded-full bg-purple-100 opacity-20"></div>
            <div class="absolute -right-4 -top-4 h-12 w-12 rounded-full bg-purple-200 opacity-30"></div>
            <div class="relative z-10 flex justify-between items-start">
                <div>
                    <p class="text-sm font-medium text-purple-700">Payment Under Verification</p>
                    <h3 class="text-2xl font-bold text-purple-900 mt-1" id="payment_under_verification">5</h3>
                    <p class="text-xs text-purple-600 mt-1">Under Verification</p>
                </div>
                <div class="p-2 bg-white rounded-lg shadow-inner flex items-center justify-center">
                    <span class="text-purple-600 text-2xl font-bold">₱</span>
                </div>
            </div>
        </div>
    
    </div>
        
    <!-- Booking List Table -->
    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200 mb-8">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-xl font-semibold text-gray-800">Inquiries Log</h2>
            <div class="relative">
                <input 
                    type="text" 
                    id="booking-search"
                    class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 sm:text-sm" 
                    placeholder="Search bookings..."
                    onkeyup="filterBookings()"
                >
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
            </div>
        </div>
        
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Id</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Guest</th>
                        <!-- Remove these two th elements -->
                        <!-- <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Check-in</th> -->
                        <!-- <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Check-out</th> -->
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <div class="flex flex-col items-start space-y-1">
                                <span>Request Status</span>
                                <select id="request-status-filter" class="w-full text-xs border border-gray-300 rounded focus:ring-red-500 focus:border-red-500" onchange="filterBookings()">
                                    <option value="">All</option>
                                    <option value="pending_confirmation">Pending</option>
                                    <option value="confirmed">Confirmed</option>
                                    <option value="cancelled">Cancelled</option>
                                    <option value="completed">Completed</option>
                                </select>
                            </div>
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <div class="flex flex-col items-start space-y-1">
                                <span>Payment Status</span>
                                <select id="payment-status-filter" class="w-full text-xs border border-gray-300 rounded focus:ring-red-500 focus:border-red-500" onchange="filterBookings()">
                                    <option value="">All</option>
                                    <option value="not_paid">Not Paid</option>
                                    <option value="advance_paid">Advance Paid</option>
                                    <option value="under_verification">Under Verification</option>
                                    <option value="paid">Paid</option>
                                    <option value="failed">Failed</option>
                                </select>
                            </div>
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody id="bookings-container" class="bg-white divide-y divide-gray-200">
                    <!-- Bookings will be loaded here via AJAX -->
                    <tr>
                        <td colspan="9" class="text-center py-8">
                            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-red-600 mx-auto"></div>
                            <p class="mt-2 text-gray-500">Loading bookings...</p>
                        </td>
                    </tr>
                </tbody>
            </table>
            
            <!-- Pagination Controls -->
            <div id="pagination-controls" class="flex items-center justify-between px-6 py-3 border-t border-gray-200 bg-white">
                <div class="flex-1 flex justify-between sm:hidden">
                    <button onclick="previousPage()" class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                        Previous
                    </button>
                    <button onclick="nextPage()" class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                        Next
                    </button>
                </div>
                <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                    <div>
                        <p class="text-sm text-gray-700" id="pagination-info">
                            Showing <span class="font-medium">1</span> to <span class="font-medium">20</span> of <span class="font-medium">0</span> results
                        </p>
                    </div>
                    <div>
                        <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                            <button onclick="goToPage(1)" class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                                <span class="sr-only">First</span>
                                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                    <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                                    <path fill-rule="evenodd" d="M8.707 5.293a1 1 0 010 1.414L5.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                                </svg>
                            </button>
                            <button onclick="previousPage()" class="relative inline-flex items-center px-2 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                                <span class="sr-only">Previous</span>
                                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                    <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                                </svg>
                            </button>
                            <div id="page-numbers" class="flex">
                                <!-- Page numbers will be inserted here -->
                            </div>
                            <button onclick="nextPage()" class="relative inline-flex items-center px-2 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                                <span class="sr-only">Next</span>
                                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                                </svg>
                            </button>
                            <button onclick="goToPage(lastPage)" class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                                <span class="sr-only">Last</span>
                                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                                    <path fill-rule="evenodd" d="M11.293 14.707a1 1 0 010-1.414L14.586 10l-3.293-3.293a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                                </svg>
                            </button>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Booking Details Modal -->
<div id="booking-modal" class="fixed inset-0 z-50 hidden overflow-y-auto">
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
                            Booking Details
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
                <button type="button" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm" onclick="closeModal()">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>

@include('admin.modals.accept_inquirer')

@endsection

@section('content_js')
<script>
// Global variables
let newBookingsCount = 0;
let currentPage = 1;
let lastPage = 1;
let perPage = 20;
let totalItems = 0;
let originalTitle = document.title;
let tabTitleInterval;
let newBookingsList = [];
let lastPlayedSoundTime = 0;
const soundCooldown = 5000;

// Initialize when page loads
document.addEventListener('DOMContentLoaded', function() {
    originalTitle = document.title;
    loadBookings();
    createNewBookingsPopup();
    
    const inquiryId = sessionStorage.getItem('searchInquiryId');
    
    if (inquiryId) {
        const searchInput = document.getElementById('booking-search');
        if (searchInput) {
            searchInput.value = inquiryId;
        }
        
        setTimeout(() => {
            filterBookings();
            highlightInquiryRow(inquiryId);
        }, 500);
        
        sessionStorage.removeItem('searchInquiryId');
    }
});

function playNotificationSound() {
    const now = Date.now();
    if (now - lastPlayedSoundTime < soundCooldown) {
        return;
    }
    
    const sound = document.getElementById('notificationSound');
    if (sound) {
        sound.currentTime = 0;
        sound.play().catch(e => console.log('Sound play failed:', e));
        lastPlayedSoundTime = now;
    }
}

function highlightInquiryRow(inquiryId) {
    const row = document.querySelector(`tr[data-id="${inquiryId}"]`);
    if (row) {
        row.classList.add('bg-yellow-100');
        row.scrollIntoView({ behavior: 'smooth', block: 'center' });
        
        setTimeout(() => {
            row.classList.remove('bg-yellow-100');
        }, 3000);
    }
}

// Create a popup container for new bookings
function createNewBookingsPopup() {
    const popup = document.createElement('div');
    popup.id = 'new-bookings-popup';
    popup.className = 'fixed bottom-20 right-4 z-50 w-80 bg-white rounded-lg shadow-xl border border-gray-200 hidden flex flex-col';
    popup.innerHTML = `
        <div class="flex justify-between items-center p-3 border-b border-gray-200 bg-gray-50 rounded-t-lg">
            <h3 class="font-medium text-gray-800">New Requests</h3>
            <button onclick="closeNewBookingsPopup()" class="text-gray-500 hover:text-gray-700">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                </svg>
            </button>
        </div>
        <div id="new-bookings-list" class="overflow-y-auto max-h-96">
            <!-- New bookings will be listed here -->
        </div>
        <div class="p-3 border-t border-gray-200 bg-gray-50 rounded-b-lg flex justify-between items-center">
            <span class="text-sm text-gray-600">${newBookingsCount} new bookings</span>
        </div>
    `;
    document.body.appendChild(popup);
}

function showNewBookingsPopup() {
    const popup = document.getElementById('new-bookings-popup');
    if (popup && newBookingsList.length > 0) {
        popup.classList.remove('hidden');
        popup.querySelector('span').textContent = `${newBookingsCount} new bookings`;
        
        const listContainer = document.getElementById('new-bookings-list');
        listContainer.innerHTML = '';
        
        const recentBookings = newBookingsList.slice(0, 5);
        
        recentBookings.forEach(booking => {
            const bookingItem = document.createElement('div');
            bookingItem.className = 'p-3 border-b border-gray-100 hover:bg-gray-50 cursor-pointer';
            bookingItem.onclick = () => viewBookingDetails(booking.id);
            
            const userName = `${booking.user?.firstname || 'Guest'} ${booking.user?.lastname || ''}`.trim();
            const checkinDate = booking.details?.[0]?.checkin_date ? formatDate(booking.details[0].checkin_date) : 'N/A';
            const statusClass = getStatusClass(booking.status);
            const statusText = formatStatusText(booking.status);
            
            bookingItem.innerHTML = `
                <div class="flex justify-between items-start">
                    <div>
                        <h4 class="font-medium text-gray-800">${userName}</h4>
                        <p class="text-sm text-gray-600">Check-in: ${checkinDate}</p>
                    </div>
                    <span class="px-2 py-1 text-xs rounded-full ${statusClass}">
                        ${statusText}
                    </span>
                </div>
                <div class="mt-1 flex justify-between items-center">
                    <span class="text-xs text-gray-500">ID: ${booking.id}</span>
                </div>
            `;
            
            listContainer.appendChild(bookingItem);
        });
        
        setTimeout(() => {
            if (popup.classList.contains('hidden')) return;
            const isHovered = popup.matches(':hover');
            if (!isHovered) {
                popup.classList.add('hidden');
            }
        }, 10000);
    }
}

function closeNewBookingsPopup() {
    const popup = document.getElementById('new-bookings-popup');
    if (popup) {
        popup.classList.add('hidden');
    }
}

function viewBookingDetails(bookingId) {
    closeNewBookingsPopup();
    const button = document.querySelector(`button[data-id="${bookingId}"]`);
    if (button) {
        handleViewBooking(button);
    }
}

// Helper functions
function getStatusClass(status) {
    switch((status || '').toLowerCase()) {
        case 'pending_confirmation':
        case 'Pending Confirmation':
            return 'bg-yellow-100 text-yellow-800';
        case 'confirmed':
        case 'approved':
            return 'bg-green-100 text-green-800';
        case 'completed':
        case 'fulfilled':
            return 'bg-purple-100 text-purple-800';
        case 'cancelled':
        case 'rejected':
            return 'bg-red-100 text-red-800';
        default:
            return 'bg-gray-100 text-gray-800';
    }
}

function formatStatusText(status) {
    if (!status) return 'Unknown';
    return status
        .split('_')
        .map(word => word.charAt(0).toUpperCase() + word.slice(1).toLowerCase())
        .join(' ');
}

function formatDate(dateString) {
    if (!dateString) return 'N/A';
    const options = { year: 'numeric', month: 'short', day: 'numeric' };
    return new Date(dateString).toLocaleDateString(undefined, options);
}

// Update summary counts
function updateSummaryCounts(summary) {
    document.getElementById('total-inquiries').textContent = summary.total || 0;
    document.getElementById('confirmed-requests').textContent = summary.confirmed_requests || 0;
    document.getElementById('pending-requests').textContent = summary.pending_requests || 0;
    document.getElementById('payment_under_verification').textContent = summary.payment_under_verification || 0;
}

// Load bookings via AJAX
function loadBookings(search = '', requestStatus = '', paymentStatus = '', page = 1) {
    currentPage = page;
    const url = `/api/inquiries?search=${encodeURIComponent(search)}&request_status=${requestStatus}&payment_status=${paymentStatus}&page=${page}&per_page=${perPage}`;
    
    showLoadingState('bookings-container', 'Loading bookings...');
    
    fetch(url, {
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            renderBookings(data.inquiries);
            updateSummaryCounts(data.summary || {});
            updateNewBookingsCount(data.newCount || 0);
            updatePaginationControls(data.pagination);
        } else {
            showErrorState('bookings-container', { 
                message: data.message || 'Failed to load bookings' 
            });
        }
    })
    .catch(error => {
        showErrorState('bookings-container', error);
    });
}

// Render initial bookings
function renderBookings(bookings) {
    const container = document.getElementById('bookings-container');
    
    if (!bookings || bookings.length === 0) {
        container.innerHTML = `
            <tr>
                <td colspan="9" class="px-6 py-4 text-center text-gray-500">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round", stroke-linejoin="round", stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                    <p class="mt-2">No bookings found</p>
                </td>
            </tr>
        `;
        return;
    }

    container.innerHTML = '';
    
    bookings.forEach(booking => {
        const bookingDetail = booking.details?.[0] ?? null;
        
        let statusText = booking.status ?? 'unknown';
        let statusClass = 'bg-gray-100 text-gray-800';
        
        switch(statusText.toLowerCase()) {
            case 'pending':
            case 'pending_confirmation':
                statusClass = 'bg-yellow-200 text-yellow-800';
                break;
            case 'confirmed':
            case 'approved':
                statusClass = 'bg-green-600 text-white';
                break;
            case 'completed':
            case 'fulfilled':
                statusClass = 'bg-purple-600 text-purple-800';
                break;
            case 'cancelled':
            case 'rejected':
                statusClass = 'bg-red-600 text-white';
                break;
            default:
                statusClass = 'bg-gray-600 text-gray-800';
        }
        
        statusText = statusText
            .split('_')
            .map(word => word.charAt(0).toUpperCase() + word.slice(1).toLowerCase())
            .join(' ');
            

        const paymentStatus = booking.payments?.[0]?.status || 'N/A';
        let paymentStatusText = paymentStatus;
        let paymentStatusClass = 'bg-gray-100 text-gray-800';
        
        switch(paymentStatus.toLowerCase()) {
            case 'n/a':
                paymentStatusClass = 'bg-gray-100 text-gray-800';
                paymentStatusText = 'N/A';
                break;
            case 'not_paid':
                paymentStatusClass = 'bg-yellow-100 text-yellow-800';
                paymentStatusText = 'Not Paid';
                break;
            case 'paid':
                paymentStatusClass = 'bg-green-100 text-green-800';
                paymentStatusText = 'Paid';
                break;
            case 'advance_paid':
                paymentStatusClass = 'bg-blue-100 text-blue-800';
                paymentStatusText = 'Advance Paid';
                break;
            case 'under_verification':
                paymentStatusClass = 'bg-purple-100 text-purple-800';
                paymentStatusText = 'Under Verification';
                break;
            case 'failed':
            case 'declined':
                paymentStatusClass = 'bg-red-100 text-red-800';
                paymentStatusText = 'Failed';
                break;
            case 'pending':
                paymentStatusClass = 'bg-orange-100 text-orange-800';
                paymentStatusText = 'Pending';
                break;
            default:
                paymentStatusClass = 'bg-gray-100 text-gray-800';
                paymentStatusText = paymentStatus.charAt(0).toUpperCase() + paymentStatus.slice(1).toLowerCase();
        }

        const row = document.createElement('tr');
        row.className = `booking-row ${!booking.is_read ? 'unread-booking' : ''}`;
        row.dataset.id = booking.id;
        
        // In the renderBookings function, modify the row.innerHTML part:
        row.innerHTML = `
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                ${booking.id}
            </td>

            <td class="px-6 py-4 whitespace-nowrap">
                <div class="ml-2">
                    <div class="text-sm font-medium text-gray-900">${booking.user?.firstname || 'Guest'} ${booking.user?.lastname || ''}</div>
                    <div class="text-sm text-gray-500">${booking.user?.email || 'N/A'}</div>
                </div>
            </td>
            <!-- Remove these two td elements -->
            <!-- <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                ${bookingDetail?.checkin_date ? formatDate(bookingDetail.checkin_date) : 'N/A'}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                ${bookingDetail?.checkout_date ? formatDate(bookingDetail.checkout_date) : 'N/A'}
            </td> -->
            <td class="px-6 py-4 whitespace-nowrap">
                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full ${statusClass}">
                    ${statusText}
                </span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full ${paymentStatusClass}">
                    ${paymentStatusText}
                </span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                <button onclick="openModal_accept_inquirer(this)" 
                        data-id="${booking.id}" 
                        class="px-4 py-2 bg-blue-50 hover:bg-blue-100 text-blue-600 hover:text-blue-800 font-medium rounded-lg transition-all duration-200 ease-in-out border border-blue-200 hover:border-blue-300 shadow-sm hover:shadow-md active:scale-95 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50">
                    View Request
                </button>
            </td>
        `;
        
        container.appendChild(row);
    });
}

// Update pagination controls
function updatePaginationControls(pagination) {
    if (!pagination) return;
    
    currentPage = pagination.current_page;
    lastPage = pagination.last_page;
    totalItems = pagination.total;
    perPage = pagination.per_page;
    
    document.getElementById('pagination-info').innerHTML = `
        Showing <span class="font-medium">${(currentPage - 1) * perPage + 1}</span> 
        to <span class="font-medium">${Math.min(currentPage * perPage, totalItems)}</span> 
        of <span class="font-medium">${totalItems}</span> results
    `;
    
    const pageNumbersContainer = document.getElementById('page-numbers');
    pageNumbersContainer.innerHTML = '';
    
    // Always show first page
    addPageNumber(1);
    
    // Show ellipsis if needed
    if (currentPage > 3) {
        const ellipsis = document.createElement('span');
        ellipsis.className = 'relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700';
        ellipsis.textContent = '...';
        pageNumbersContainer.appendChild(ellipsis);
    }
    
    // Show pages around current page
    const startPage = Math.max(2, currentPage - 1);
    const endPage = Math.min(lastPage - 1, currentPage + 1);
    
    for (let i = startPage; i <= endPage; i++) {
        addPageNumber(i);
    }
    
    // Show ellipsis if needed
    if (currentPage < lastPage - 2) {
        const ellipsis = document.createElement('span');
        ellipsis.className = 'relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700';
        ellipsis.textContent = '...';
        pageNumbersContainer.appendChild(ellipsis);
    }
    
    // Always show last page if different from first
    if (lastPage > 1) {
        addPageNumber(lastPage);
    }
}

function addPageNumber(page) {
    const pageNumbersContainer = document.getElementById('page-numbers');
    const button = document.createElement('button');
    button.onclick = () => goToPage(page);
    button.className = `relative inline-flex items-center px-4 py-2 border text-sm font-medium ${
        page === currentPage 
            ? 'z-10 bg-red-50 border-red-500 text-red-600' 
            : 'bg-white border-gray-300 text-gray-500 hover:bg-gray-50'
    }`;
    button.textContent = page;
    pageNumbersContainer.appendChild(button);
}

function goToPage(page) {
    if (page < 1 || page > lastPage || page === currentPage) return;
    
    const search = document.getElementById('booking-search').value;
    const requestStatus = document.getElementById('request-status-filter').value;
    const paymentStatus = document.getElementById('payment-status-filter').value;
    loadBookings(search, requestStatus, paymentStatus, page);
}

function previousPage() {
    goToPage(currentPage - 1);
}

function nextPage() {
    goToPage(currentPage + 1);
}

// Filter bookings
function filterBookings() {
    const search = document.getElementById('booking-search').value;
    const requestStatus = document.getElementById('request-status-filter').value;
    const paymentStatus = document.getElementById('payment-status-filter').value;
    loadBookings(search, requestStatus, paymentStatus, 1);
}

// Show app notification
function showAppNotification(message, options = {}) {
    const {
        type = 'info',
        duration = 5000,
        actions = [],
        priority = 'normal'
    } = options;

    if (priority === 'low' && document.hidden) {
        return;
    }

    const existingNotif = document.querySelector(`.app-notification.notification-${type}`);
    if (existingNotif) existingNotif.remove();

    const notification = document.createElement('div');
    notification.className = `app-notification notification-${type} priority-${priority}`;
    
    notification.innerHTML = `
        <div class="notification-content">
            <div class="notification-icon">
                ${getNotificationIcon(type)}
            </div>
            <div class="notification-body">
                <span class="notification-message">${message}</span>
                ${actions.length ? `
                    <div class="notification-actions">
                        ${actions.map(action => `
                            <button type="button" 
                                    onclick="${action.handler}" 
                                    class="notification-action">
                                ${action.label}
                            </button>
                        `).join('')}
                    </div>
                ` : ''}
            </div>
            <button class="notification-close" onclick="this.parentElement.parentElement.remove()">
                <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                </svg>
            </button>
        </div>
    `;

    document.body.appendChild(notification);
    
    if (duration > 0) {
        setTimeout(() => {
            if (notification.parentNode) {
                notification.classList.add('animate-fade-out');
                setTimeout(() => notification.remove(), 300);
            }
        }, duration);
    }
    
    if (priority === 'critical') {
        flashTabTitle(message);
    }
}

function getNotificationIcon(type) {
    const icons = {
        info: `
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
        `,
        success: `
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
        `,
        warning: `
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
        `,
        error: `
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
        `
    };
    return icons[type] || icons.info;
}

// Update new bookings count with notifications
function updateNewBookingsCount(count) {
    const prevCount = newBookingsCount;
    newBookingsCount = count;
    const counterElement = document.getElementById('new-bookings-count');
    
    if (count > 0) {
        counterElement.textContent = count > 99 ? '99+' : count;
        counterElement.classList.remove('hidden');
        
        if (count > prevCount) {
            const diff = count - prevCount;
            flashTabTitle(`(${diff}) New Bookings`);
            
            if (diff >= 5) {
                showAppNotification(`You have ${diff} new bookings waiting for your response!`, {
                    type: 'warning',
                    priority: 'high',
                });
            }
        }
    } else {
        counterElement.classList.add('hidden');
        restoreTabTitle();
    }
}

// Tab title flashing
function flashTabTitle(message) {
    clearInterval(tabTitleInterval);
    originalTitle = document.title.replace(/^\(\d+\)\s*/, '');
    
    let count = 0;
    tabTitleInterval = setInterval(() => {
        document.title = (count++ % 2) ? `${message} - ${originalTitle}` : originalTitle;
    }, 1000);
    
    setTimeout(() => {
        clearInterval(tabTitleInterval);
        document.title = originalTitle;
    }, 15000);
}

function restoreTabTitle() {
    clearInterval(tabTitleInterval);
    document.title = originalTitle;
}

// Mark all bookings as read
function markAllAsRead() {
    fetch('/api/inquiries/mark-all-read', {
        method: 'POST',
        headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            updateNewBookingsCount(0);
            newBookingsList = [];
            
            document.querySelectorAll('.booking-row').forEach(row => {
                row.classList.remove('unread-booking');
            });
            
            showAppNotification('All bookings marked as read', {
                type: 'success',
                duration: 3000
            });
            restoreTabTitle();
            
            closeNewBookingsPopup();
        }
    })
    .catch(error => {
        console.error('Error marking all bookings as read:', error);
        showAppNotification('Failed to mark all as read', {
            type: 'error',
            duration: 3000
        });
    });
}

// UI helper functions
function showLoadingState(containerId, message) {
    const container = document.getElementById(containerId);
    if (container) {
        container.innerHTML = `
            <tr>
                <td colspan="9" class="text-center py-8">
                    <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-red-600 mx-auto"></div>
                    <p class="mt-2 text-gray-500">${message}</p>
                </td>
            </tr>
        `;
    }
}

function showErrorState(containerId, error) {
    const container = document.getElementById(containerId);
    if (container) {
        container.innerHTML = `
            <tr>
                <td colspan="9" class="p-4 text-center text-red-500">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round", stroke-linejoin="round", stroke-width="2" d="M12 8v4m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    <p class="mt-2">${error.message || 'Failed to load data. Please try again.'}</p> 
                    <button onclick="filterBookings()" class="mt-4 px-4 py-2 bg-red-100 text-red-700 rounded hover:bg-red-200">
                        Retry
                    </button>
                </td>
            </tr>
        `;
    }
}

// View payment details
function viewPaymentDetails(bookingId) {
    console.log('View payment details for booking:', bookingId);
}
</script>
@endsection