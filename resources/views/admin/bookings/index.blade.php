@extends('layouts.admin')

@section('title', 'Bookings')

@php
    $active = 'bookings';
@endphp

@section('content_css')
    <style>
        /* Existing styles remain unchanged except for sticky removal */
        .hover-scale {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .status-badge {
            transition: all 0.3s ease;
            font-size: 0.7rem;
            padding: 0.25rem 0.5rem;
        }

        .status-badge:hover {
            transform: scale(1.05);
        }

        .custom-scroll::-webkit-scrollbar {
            width: 4px;
            height: 4px;
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

            0%,
            100% {
                opacity: 1;
            }

            50% {
                opacity: 0.5;
            }
        }

        /* REMOVED STICKY CONTAINER STYLES */
        .summary-sidebar {
            width: 100%;
            display: flex;
            flex-direction: column;
        }

        .booking-summary-card {
            width: 100%;
            display: flex;
            flex-direction: column;
        }

        /* Responsive adjustments */
        @media (max-width: 1024px) {

            .lg\:w-2\/3,
            .lg\:w-1\/3 {
                width: 100%;
            }

            .flex-col.lg\:flex-row {
                flex-direction: column;
            }

            .booking-summary-card {
                margin-bottom: 1rem;
            }
        }

        @media (max-width: 640px) {
            .glass-card {
                padding: 1rem;
            }

            .booking-summary-card>div {
                padding: 0.75rem;
            }
        }

        .fade-in {
            animation: fadeIn 0.3s ease-in;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .booking-row {
            transition: all 0.2s ease;
        }

        .booking-row:hover {
            background-color: #faf7f7 !important;
        }

        .booking-row.selected {
            background-color: #eeecec !important;
        }

        .action-btn-details {
            padding: 0.5rem 0.75rem;
            border-radius: 0.375rem;
            font-size: 0.75rem;
            font-weight: 500;
            transition: all 0.2s;
            margin: 0.1rem;
            border: none;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .btn-confirm {
            background-color: #10B981;
            color: white;
        }

        .btn-confirm:hover {
            background-color: #059669;
        }

        .btn-checkin {
            background-color: #3B82F6;
            color: white;
        }

        .btn-checkin:hover {
            background-color: #2563EB;
        }

        .btn-checkout {
            background-color: #8B5CF6;
            color: white;
        }

        .btn-checkout:hover {
            background-color: #7C3AED;
        }

        .btn-details {
            background-color: #6B7280;
            color: white;
        }

        .btn-details:hover {
            background-color: #4B5563;
        }

        /* Compact table styles */
        .compact-table td,
        .compact-table th {
            padding: 0.5rem 0.75rem;
            white-space: nowrap;
        }

        .summary-card {
            border-left: 4px solid #e53e3e;
            background: linear-gradient(to right, #fdf2f2, #fff);
        }

        .summary-icon {
            width: 18px;
            height: 18px;
            margin-right: 0.5rem;
        }

        .progress-bar {
            height: 8px;
            border-radius: 4px;
            overflow: hidden;
            background-color: #edf2f7;
        }

        .progress-fill {
            height: 100%;
            border-radius: 4px;
            background: linear-gradient(to right, #e53e3e, #f56565);
            transition: width 0.5s ease;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 0.75rem;
        }

        .stat-item {
            background-color: #f8fafc;
            border-radius: 0.5rem;
            padding: 0.75rem;
            border: 1px solid #e2e8f0;
        }

        .action-buttons {
            display: flex;
            flex-wrap: wrap;
            gap: 0.2rem;
        }

        /* Fix for the main container */
        .glass-card {
            width: 100%;
            box-sizing: border-box;
        }

        /* Table container fixes */
        .table-container {
            width: 100%;
            overflow-x: auto;
            position: relative;
        }

        /* Ensure proper spacing */
        .flex-col.lg\:flex-row {
            align-items: stretch;
            gap: 1rem;
        }

        @media (min-width: 1024px) {
            .summary-container {
                width: 50%;
                /* Changed from 33.333333% */
                min-width: 380px;
                /* Increased from 320px */
                max-width: 600px;
                /* Increased from 400px */
                flex-shrink: 0;
            }

            .main-content {
                width: 50%;
                /* Changed from 66.666667% */
                flex: 1;
                min-width: 0;
            }
        }

        .summary-container {
            width: 100%;
            position: relative;
        }

        /* Action buttons in sidebar */
        .sidebar-actions {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
            margin-top: 1rem;
            padding-top: 1rem;
            border-top: 1px solid #e5e7eb;
        }

        .sidebar-btn {
            padding: 0.75rem 1rem;
            border-radius: 0.5rem;
            font-weight: 500;
            text-align: center;
            transition: all 0.2s;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }

        .sidebar-btn svg {
            width: 1.25rem;
            height: 1.25rem;
        }

        /* Responsive adjustments */
        @media (max-width: 1024px) {

            .summary-container,
            .main-content {
                width: 100%;
            }

            .flex-col.lg\:flex-row {
                flex-direction: column;
            }
        }

        /* QR Modal Styles */
        .qr-modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.8);
            z-index: 1000;
            justify-content: center;
            align-items: center;
        }

        .qr-modal.active {
            display: flex;
        }

        .qr-modal-content {
            background-color: #1f2937;
            border-radius: 0.5rem;
            width: 90%;
            max-width: 500px;
            padding: 1.5rem;
            position: relative;
            opacity: 0.9;
        }

        .qr-modal-close {
            position: absolute;
            top: 1rem;
            right: 1rem;
            background: none;
            border: none;
            color: white;
            font-size: 1.5rem;
            cursor: pointer;
        }

        .spinner {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 3px solid rgba(255, 255, 255, .3);
            border-radius: 50%;
            border-top-color: #fff;
            animation: spin 1s ease-in-out infinite;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        /* Preloader styles */
        .btn-preloader {
            display: inline-block;
            width: 16px;
            height: 16px;
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            border-top-color: #fff;
            animation: spin 1s ease-in-out infinite;
            margin-right: 8px;
        }

        .sidebar-btn .btn-preloader {
            width: 18px;
            height: 18px;
        }

        .sidebar-btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }

        #resultModal {
            backdrop-filter: blur(5px);
            -webkit-backdrop-filter: blur(5px);
            background-color: rgba(0, 0, 0, 0.4);
        }

        #resultModal .modal-content {
            background: white;
            border-radius: 0.5rem;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
            transform: translateY(-20px);
            transition: transform 0.3s ease-out;
        }

        #resultModal.active .modal-content {
            transform: translateY(0);
        }

        .sidebar-btn:disabled,
        .sidebar-btn[disabled],
        .sidebar-btn[aria-disabled="true"] {
            opacity: 0.6;
            cursor: not-allowed;
            background-color: #9ca3af !important;
        }

        .sidebar-btn:disabled:hover,
        .sidebar-btn[disabled]:hover,
        .sidebar-btn[aria-disabled="true"]:hover {
            background-color: #9ca3af !important;
        }

        .pointer-events-none {
            pointer-events: none;
        }

        .new-booking-notification {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background: #4CAF50;
            color: white;
            padding: 10px 15px;
            border-radius: 4px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
            z-index: 1000;
            animation: slideIn 0.3s ease;
        }

        @keyframes slideIn {
            from {
                transform: translateX(100px);
                opacity: 0;
            }

            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        .booking-row.pending {
            background-color: #fff3cd !important;
            /* Light yellow background */
            border-left: 4px solid #ffc107;
            /* Yellow accent border */
        }

        .booking-row.pending:hover {
            background-color: #ffeaa7 !important;
            /* Slightly darker yellow on hover */
        }

        .button-hint {
            font-size: 0.7rem;
            color: #6b7280;
            margin-top: 0.25rem;
            display: block;
            font-style: italic;
        }

        /* Guest Modal Styles */
        .guest-modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.8);
            z-index: 1000;
            justify-content: center;
            align-items: center;
        }

        .guest-modal.active {
            display: flex;
        }

        .guest-modal-content {
            background-color: white;
            border-radius: 0.5rem;
            width: 90%;
            max-width: 500px;
            padding: 1.5rem;
            position: relative;
        }

        .guest-modal-close {
            position: absolute;
            top: 1rem;
            right: 1rem;
            background: none;
            border: none;
            font-size: 1.5rem;
            cursor: pointer;
        }

        .guest-types {
            margin-bottom: 1.5rem;
        }

        .guest-type-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1rem;
        }

        .guest-type-label {
            flex: 1;
            font-weight: 500;
        }

        .guest-price-input {
            width: 80px;
            margin: 0 1rem;
            padding: 0.5rem;
            border: 1px solid #ddd;
            border-radius: 0.25rem;
        }

        .quantity-controls {
            display: flex;
            align-items: center;
        }

        .quantity-btn {
            width: 30px;
            height: 30px;
            background-color: #f3f4f6;
            border: 1px solid #d1d5db;
            border-radius: 0.25rem;
            cursor: pointer;
            font-weight: bold;
        }

        .quantity-btn:hover {
            background-color: #e5e7eb;
        }

        .quantity-value {
            margin: 0 0.5rem;
            min-width: 20px;
            text-align: center;
        }

        .guest-summary {
            border-top: 1px solid #e5e7eb;
            padding-top: 1rem;
            margin-bottom: 1.5rem;
        }

        .guest-summary-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 0.5rem;
        }

        .guest-summary-total {
            display: flex;
            justify-content: space-between;
            margin-top: 1rem;
            padding-top: 0.5rem;
            border-top: 1px solid #e5e7eb;
            font-weight: bold;
        }

        .modal-action-buttons {
            display: flex;
            justify-content: flex-end;
            gap: 0.5rem;
        }

        .modal-btn {
            padding: 0.5rem 1rem;
            border-radius: 0.375rem;
            cursor: pointer;
            font-weight: 500;
        }

        .modal-btn-cancel {
            background-color: #f3f4f6;
            border: 1px solid #d1d5db;
        }

        .modal-btn-cancel:hover {
            background-color: #e5e7eb;
        }

        .modal-btn-save {
            background-color: #10B981;
            color: white;
            border: none;
        }

        .modal-btn-save:hover {
            background-color: #059669;
        }

        .guest-addons-section {
            margin-top: 1rem;
            padding-top: 1rem;
        }

        .guest-addons-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 0.5rem;
        }

        .guest-addons-title {
            font-size: 0.875rem;
            font-weight: 600;
            color: #374151;
            display: flex;
            align-items: center;
        }

        .guest-addons-title svg {
            width: 1rem;
            height: 1rem;
            margin-right: 0.5rem;
            color: #6b7280;
        }

        .guest-addons-list {
            background-color: #f9fafb;
            border-radius: 0.375rem;
            padding: 0.75rem;
        }

        .guest-addon-item {
            display: flex;
            justify-content: space-between;
            padding: 0.5rem 0;
            border-bottom: 1px solid #e5e7eb;
        }

        .guest-addon-item:last-child {
            border-bottom: none;
        }

        .guest-addon-name {
            font-size: 0.875rem;
            color: #374151;
        }

        .guest-addon-price {
            font-size: 0.875rem;
            font-weight: 600;
            color: #059669;
        }

        .no-addons {
            font-size: 0.875rem;
            color: #6b7280;
            font-style: italic;
            text-align: center;
            padding: 0.5rem;
        }

        .add-addon-btn {
            background-color: #10b981;
            color: white;
            border: none;
            border-radius: 0.25rem;
            padding: 0.25rem 0.5rem;
            font-size: 0.75rem;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 0.25rem;
        }

        .add-addon-btn:hover {
            background-color: #059669;
        }

        .delete-addon-btn {
            opacity: 0.7;
            transition: opacity 0.2s ease;
            padding: 0.25rem;
            border-radius: 0.25rem;
        }

        .delete-addon-btn:hover {
            opacity: 1;
            background-color: rgba(239, 68, 68, 0.1);
        }
    </style>
@endsection

@section('content')
    <div class="min-h-screen px-6 py-6">
        <div class="flex flex-col mb-10">
        <!-- Container -->
        <div class="bg-white rounded-lg border border-lightgray">
            
            <!-- Title -->
            <div class="px-4 pt-6 pb-4 border-b border-gray-200">
                <h2 class="text-xl font-bold text-gray-800">
                    SEARCH TODAY'S GUEST CHECK-IN OR CHECKOUT
                </h2>
            </div>

            <!-- Content -->
            <div class="flex flex-col md:flex-row gap-8 p-8">
                
                <!-- Manual Search Fields -->
                <div class="flex flex-col md:flex-row md:items-end flex-wrap gap-6 flex-1">
                    
                    <div class="flex flex-col min-w-[200px] flex-1">
                        <label for="search-firstname" class="mb-2 text-sm font-semibold text-gray-700 uppercase tracking-wide">First Name</label>
                        <input type="text" id="search-firstname" placeholder="Enter first name"
                            class="w-full px-4 py-2.5 text-base border border-gray-300 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-red-500 transition shadow-sm hover:shadow-md">
                    </div>

                    <div class="flex flex-col min-w-[200px] flex-1">
                        <label for="search-lastname" class="mb-2 text-sm font-semibold text-gray-700 uppercase tracking-wide">Last Name</label>
                        <input type="text" id="search-lastname" placeholder="Enter last name"
                            class="w-full px-4 py-2.5 text-base border border-gray-300 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-red-500 transition shadow-sm hover:shadow-md">
                    </div>

                    <div class="flex flex-col min-w-[200px] flex-1">
                        <label for="search-date" class="mb-2 text-sm font-semibold text-gray-700 uppercase tracking-wide">Date <span
                                class="text-xs text-gray-500 font-normal">(Adjust if needed)</span></label>
                        <input type="date" id="search-date" value="{{ date('Y-m-d') }}"
                            class="w-full px-4 py-2.5 text-base border border-gray-300 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-red-500 transition shadow-sm hover:shadow-md">
                    </div>

                    <!-- Check-in/Check-out Toggle -->
                    <div class="flex flex-col">
                        <label class="text-sm font-semibold text-gray-700 uppercase tracking-wide mb-3">Search Type</label>
                        <div class="flex bg-gray-100 rounded-xl p-1 shadow-inner">
                            <input type="radio" id="search-checkin" name="search-type" value="checkin"
                                class="hidden peer/checkin" checked>
                            <label for="search-checkin"
                                class="px-5 py-2 rounded-lg text-sm font-medium cursor-pointer transition-all duration-200 peer-checked/checkin:bg-red-600 peer-checked/checkin:text-white peer-checked/checkin:shadow-md">
                                Check-in
                            </label>

                            <input type="radio" id="search-checkout" name="search-type" value="checkout"
                                class="hidden peer/checkout">
                            <label for="search-checkout"
                                class="px-5 py-2 rounded-lg text-sm font-medium cursor-pointer transition-all duration-200 peer-checked/checkout:bg-red-600 peer-checked/checkout:text-white peer-checked/checkout:shadow-md">
                                Check-out
                            </label>
                        </div>
                    </div>

                    <!-- Search and Clear Buttons -->
                    <div class="flex flex-col md:flex-row md:items-end gap-3 mt-2 md:mt-0">
                        <button id="search-button"
                            class="px-6 py-2.5 bg-green-600 cursor-pointer text-white rounded-xl hover:bg-green-700 transition-all duration-200 text-base font-semibold shadow-md hover:shadow-lg active:scale-95">
                            Search
                        </button>
                        <button id="clear-button"
                            class="px-6 py-2.5 bg-gray-200 cursor-pointer text-gray-700 rounded-xl hover:bg-gray-300 transition-all duration-200 text-base font-semibold shadow-md hover:shadow-lg active:scale-95">
                            Reset
                        </button>
                    </div>
                </div>

                <!-- Divider with OR -->
                <div class="flex items-center justify-center">
                    <div class="hidden md:flex items-center h-12">
                        <div class="border-t border-gray-300 w-10"></div>
                        <span class="mx-3 text-sm font-semibold text-gray-500">OR</span>
                        <div class="border-t border-gray-300 w-10"></div>
                    </div>
                </div>

                <!-- QR Scanner Button -->
                <div class="flex flex-col min-w-[220px]">
                    <label class="mb-2 text-sm font-semibold text-gray-700 uppercase tracking-wide">Quick Access</label>
                    <button id="qr-scanner-btn"
                        class="flex items-center justify-center gap-2 px-6 py-3 bg-gradient-to-r from-red-600 to-red-700 text-white rounded-xl hover:from-red-700 hover:to-red-800 shadow-md transition hover:shadow-lg active:scale-95 cursor-pointer">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 7V5a2 2 0 012-2h2m10 0h2a2 2 0 012 2v2m0 10v2a2 2 0 01-2 2h-2m-10 0H5a2 2 0 01-2-2v-2" />
                        </svg>
                        Scan QR Code
                    </button>
                </div>
            </div>
        </div>
    </div>



        <div class="flex flex-col lg:flex-row gap-4">
            <!-- Main Content - Fixed width and overflow -->
            <div class="main-content lg:w-1/2 w-full">
                <div class="glass-card p-4 hover-scale bg-white rounded-lg border border-lightgray">

                    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6 gap-4">
                        <!-- Left: Title -->
                        <div>
                            <h2 class="text-xl font-bold text-gray-800">
                                Bookings Management
                            </h2>
                            <p class="text-gray-600 text-base md:text-lg mt-1">
                                Manage all guest reservations with ease
                            </p>
                        </div>

                        <!-- Right: Search + Filter + Refresh -->
                        <div class="flex flex-col md:flex-row items-center gap-4 w-full md:w-auto">

                            <!-- Search Bar -->
                            <div class="relative w-full md:w-64">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-4 w-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z"
                                            clip-rule="evenodd"></path>
                                    </svg>
                                </div>
                                <input id="search-input" type="text"
                                    class="block w-full pl-9 pr-10 py-2 text-sm md:text-base border border-gray-300 rounded-lg bg-white/70 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500"
                                    placeholder="Search reservations...">
                            </div>

                            <!-- Status Filter -->
                            <div class="flex items-center gap-2">
                                <label for="status-filter" class="text-sm font-medium text-gray-700 whitespace-nowrap">
                                    Status:
                                </label>
                                <select id="status-filter"
                                    class="w-full md:w-auto pl-3 pr-10 py-2 text-sm cursor-pointer border border-gray-300 rounded-lg focus:ring-red-500 focus:border-red-500">
                                    <option value="all" selected>All Statuses</option>
                                    <option value="pending_confirmation">Pending</option>
                                    <option value="confirmed">Confirmed</option>
                                    <option value="checked_in">Checked In</option>
                                    <option value="checked_out">Checked Out</option>
                                </select>
                            </div>

                            <!-- Refresh Button -->
                            <button id="refreshBtn"
                                class="flex items-center text-blue-600 hover:text-blue-800 text-sm font-semibold cursor-pointer transition-colors">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd"
                                        d="M4 2a1 1 0 011 1v2.101a7.002 7.002 0 0111.601 2.566 1 1 0 11-1.885.666A5.002 5.002 0 005.999 7H9a1 1 0 010 2H4a1 1 0 01-1-1V3a1 1 0 011-1zm.008 9.057a1 1 0 011.276.61A5.002 5.002 0 0014.001 13H11a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0v-2.101a7.002 7.002 0 01-11.601-2.566 1 1 0 01.61-1.276z"
                                        clip-rule="evenodd" />
                                </svg>
                                Refresh
                            </button>
                        </div>
                    </div>



                    <hr class="border-gray-300 my-3">

                    <!-- Booking Table - Fixed container -->
                    <div class="table-container">
                        <div class="overflow-x-auto custom-scroll">
                            <table class="min-w-full divide-y divide-gray-200 compact-table">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th
                                            class="px-3 py-2 text-left text-sm font-medium text-gray-500 uppercase tracking-wider">
                                            ID
                                        </th>
                                        <th
                                            class="px-3 py-2 text-left text-sm font-medium text-gray-500 uppercase tracking-wider">
                                            Status
                                        </th>
                                        <th
                                            class="px-3 py-2 text-left text-sm font-medium text-gray-500 uppercase tracking-wider">
                                            Guest
                                        </th>
                                        <th
                                            class="px-3 py-2 text-left text-sm font-medium text-gray-500 uppercase tracking-wider">
                                            Check-in
                                        </th>
                                        <th
                                            class="px-3 py-2 text-left text-sm font-medium text-gray-500 uppercase tracking-wider">
                                            Action
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200" id="bookings-table-body">
                                    <!-- Loading state -->
                                    <tr>
                                        <td colspan="5" class="px-6 py-6 text-center">
                                            <div class="flex justify-center">
                                                <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-red-600">
                                                </div>
                                            </div>
                                            <p class="mt-2 text-lg text-gray-500">Loading bookings...</p>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Pagination -->
                    <div class="flex flex-col lg:flex-row justify-between items-center mt-4 gap-3">
                        <div id="pagination-info" class="text-sm text-gray-600"></div>
                        <div class="flex gap-1">
                            <button id="prev-page"
                                class="px-3 py-1.5 bg-gray-100 text-gray-700 rounded-md text-lg font-medium hover:bg-gray-200 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                                disabled>
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20"
                                    fill="currentColor">
                                    <path fill-rule="evenodd"
                                        d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z"
                                        clip-rule="evenodd" />
                                </svg>
                            </button>
                            <button id="next-page"
                                class="px-3 py-1.5 bg-gray-100 text-gray-700 rounded-md text-lg font-medium hover:bg-gray-200 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                                disabled>
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20"
                                    fill="currentColor">
                                    <path fill-rule="evenodd"
                                        d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                                        clip-rule="evenodd" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Booking Summary Sidebar - REMOVED STICKY BEHAVIOR -->
            <div class="summary-container lg:w-1/2 w-full">
                <!-- Simple container without sticky positioning -->
                <div class="summary-sidebar">
                    <!-- Booking Summary -->
                    <div
                        class="glass-card booking-summary-card bg-white rounded-lg border border-lightgray overflow-hidden hover-scale w-full">
                        <div class="bg-gradient-to-r from-red-600 to-red-800 p-3 text-white">
                            <h2 class="text-lg font-bold flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 6h16M4 12h16M4 18h16" />
                                </svg>

                                BOOKING DETAILS
                            </h2>
                        </div>
                        <div class="p-0 fade-in w-full" id="booking-summary">
                            <div class="text-center py-6 px-3 text-gray-400">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 mx-auto mb-3 text-gray-300"
                                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                </svg>
                                <p class="text-lg">Select a booking to view details</p>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <div id="resultModal" class="fixed inset-0 flex items-center justify-center z-50 hidden">
        <div class="modal-content w-11/12 md:w-1/3">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 id="resultModalTitle" class="text-xl font-semibold text-gray-800"></h2>
            </div>
            <div class="px-6 py-4">
                <p id="resultModalMessage" class="text-gray-700"></p>
            </div>
            <div class="px-6 py-4 border-t border-gray-200 bg-gray-50 flex justify-end">
                <button id="resultModalClose"
                    class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 transition-colors">
                    Close
                </button>
            </div>
        </div>
    </div>

    <!-- QR Scanner Modal -->
    <div id="qr-scanner-modal" class="qr-modal">
        <div class="qr-modal-content">
            <button id="qr-modal-close" class="qr-modal-close">&times;</button>
            <h1 class="text-white text-2xl font-bold mb-4">Scan Guest QR Code</h1>

            <div class="flex-1 flex flex-col items-center justify-center">
                <video id="qrVideo" class="w-full max-w-4xl h-full mb-4 border-4 border-white rounded-lg">
                </video>

                <div id="qrResult" class="text-white text-center mb-6"></div>

                <!-- Welcome message container (initially hidden) -->
                <div id="welcomeMessage" class="hidden text-center mb-6 p-4 bg-gray-800 rounded-lg max-w-md">
                    <h2 class="text-xl font-bold text-green-400 mb-2" id="welcomeTitle">Welcome!</h2>
                    <p class="text-white" id="customerDetails"></p>
                </div>

                <button id="qr-cancel-btn"
                    class="px-6 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition-colors">
                    Cancel
                </button>
            </div>
        </div>
    </div>

    <!-- Guest Composition Modal -->
    <div id="guest-modal" class="guest-modal">
        <div class="guest-modal-content">
            <button id="guest-modal-close" class="guest-modal-close">&times;</button>
            <h2 class="text-xl font-semibold text-gray-800 mb-4">Add Guest Composition</h2>

            <div class="guest-types">
                <div class="guest-type-row">
                    <span class="guest-type-label">Kids (0-12 years)</span>
                    <input type="number" min="0" step="0.01" class="guest-price-input" id="kid-price" placeholder="Price">
                    <div class="quantity-controls">
                        <button class="quantity-btn" data-type="kid" data-action="decrease">-</button>
                        <span class="quantity-value" id="kid-quantity">0</span>
                        <button class="quantity-btn" data-type="kid" data-action="increase">+</button>
                    </div>
                </div>

                <div class="guest-type-row">
                    <span class="guest-type-label">Adults (13-59 years)</span>
                    <input type="number" min="0" step="0.01" class="guest-price-input" id="adult-price" placeholder="Price">
                    <div class="quantity-controls">
                        <button class="quantity-btn" data-type="adult" data-action="decrease">-</button>
                        <span class="quantity-value" id="adult-quantity">0</span>
                        <button class="quantity-btn" data-type="adult" data-action="increase">+</button>
                    </div>
                </div>

                <div class="guest-type-row">
                    <span class="guest-type-label">Seniors (60+ years)</span>
                    <input type="number" min="0" step="0.01" class="guest-price-input" id="senior-price"
                        placeholder="Price">
                    <div class="quantity-controls">
                        <button class="quantity-btn" data-type="senior" data-action="decrease">-</button>
                        <span class="quantity-value" id="senior-quantity">0</span>
                        <button class="quantity-btn" data-type="senior" data-action="increase">+</button>
                    </div>
                </div>
            </div>

            <div class="guest-summary">
                <h3 class="text-lg font-medium text-gray-700 mb-2">Guest Summary</h3>

                <div class="guest-summary-item">
                    <span>Kids:</span>
                    <span id="kid-summary">0 x ₱0.00 = ₱0.00</span>
                </div>

                <div class="guest-summary-item">
                    <span>Adults:</span>
                    <span id="adult-summary">0 x ₱0.00 = ₱0.00</span>
                </div>

                <div class="guest-summary-item">
                    <span>Seniors:</span>
                    <span id="senior-summary">0 x ₱0.00 = ₱0.00</span>
                </div>

                <div class="guest-summary-total">
                    <span>Total:</span>
                    <span id="guest-total">₱0.00</span>
                </div>
            </div>

            <div class="modal-action-buttons">
                <button id="guest-modal-cancel" class="modal-btn modal-btn-cancel">Cancel</button>
                <button id="guest-modal-save" class="modal-btn modal-btn-save">Save Guest Composition</button>
            </div>
        </div>
    </div>
@endsection

@section('content_js')
    <!-- CSRF token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <script src="https://cdn.jsdelivr.net/npm/jsqr@1.4.0/dist/jsQR.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // CSRF token for Laravel
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            // Headers for fetch requests
            const headers = {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': csrfToken
            };

            // QR Scanner variables
            const qrModal = document.getElementById('qr-scanner-modal');
            const qrOpenBtn = document.getElementById('qr-scanner-btn');
            const qrCloseBtn = document.getElementById('qr-modal-close');
            const qrCancelBtn = document.getElementById('qr-cancel-btn');
            const video = document.getElementById("qrVideo");
            const resultContainer = document.getElementById("qrResult");
            const welcomeMessage = document.getElementById("welcomeMessage");
            const welcomeTitle = document.getElementById("welcomeTitle");
            const customerDetails = document.getElementById("customerDetails");

            const addGuestBtn = document.getElementById('add-guest-btn');
            const guestModal = document.getElementById('guest-modal');
            const guestModalClose = document.getElementById('guest-modal-close');
            const guestModalCancel = document.getElementById('guest-modal-cancel');
            const guestModalSave = document.getElementById('guest-modal-save');
            let currentFacilityBookingLogId = null;

            const guestTypes = {
                kid: { price: 0, quantity: 0, element: 'kid' },
                adult: { price: 0, quantity: 0, element: 'adult' },
                senior: { price: 0, quantity: 0, element: 'senior' }
            };

            document.addEventListener('click', function (e) {
                if (e.target && e.target.id === 'add-guest-btn') {
                    openGuestModal();
                }
                // Also handle clicks on elements inside the button
                if (e.target && e.target.closest('#add-guest-btn')) {
                    openGuestModal();
                }
            });

            guestModalClose.addEventListener('click', closeGuestModal);
            guestModalCancel.addEventListener('click', closeGuestModal);

            guestModal.addEventListener('click', function (e) {
                if (e.target === guestModal) {
                    closeGuestModal();
                }
            });

            document.querySelectorAll('.quantity-btn').forEach(btn => {
                btn.addEventListener('click', function () {
                    const type = this.dataset.type;
                    const action = this.dataset.action;

                    if (action === 'increase') {
                        guestTypes[type].quantity++;
                    } else if (action === 'decrease' && guestTypes[type].quantity > 0) {
                        guestTypes[type].quantity--;
                    }

                    updateQuantityDisplay(type);
                    calculateSummary();
                });
            });

            document.querySelectorAll('.guest-price-input').forEach(input => {
                input.addEventListener('input', function () {
                    const type = this.id.replace('-price', '');
                    guestTypes[type].price = parseFloat(this.value) || 0;
                    calculateSummary();
                });
            });

            // Save guest composition
            guestModalSave.addEventListener('click', function () {
                saveGuestComposition();
            });

            function openGuestModal() {
                // Store the current booking ID for use when saving
                if (!currentBookingId) {
                    showToast('error', 'No booking selected');
                    return;
                }

                currentFacilityBookingLogId = currentBookingId;

                // Reset form
                Object.keys(guestTypes).forEach(type => {
                    guestTypes[type].price = 0;
                    guestTypes[type].quantity = 0;
                    document.getElementById(`${type}-price`).value = '';
                    updateQuantityDisplay(type); // Use the function instead of direct DOM manipulation
                });

                calculateSummary();
                guestModal.classList.add('active');
            }

            function closeGuestModal() {
                guestModal.classList.remove('active');
            }

            function updateQuantityDisplay(type) {
                const element = document.getElementById(`${type}-quantity`);
                if (element) {
                    element.textContent = guestTypes[type].quantity;
                }
            }


            function calculateSummary() {
                let total = 0;

                Object.keys(guestTypes).forEach(type => {
                    const typeData = guestTypes[type];
                    const typeTotal = typeData.price * typeData.quantity;

                    document.getElementById(`${type}-summary`).textContent =
                        `${typeData.quantity} x ₱${typeData.price.toFixed(2)} = ₱${typeTotal.toFixed(2)}`;

                    total += typeTotal;
                });

                document.getElementById('guest-total').textContent = `₱${total.toFixed(2)}`;
            }

            async function saveGuestComposition() {
                // Validate at least one guest type has quantity > 0
                const hasGuests = Object.values(guestTypes).some(type => type.quantity > 0);

                if (!hasGuests) {
                    showToast('error', 'Please add at least one guest');
                    return;
                }

                // Validate prices are set for selected guest types
                const missingPrices = Object.entries(guestTypes)
                    .filter(([_, data]) => data.quantity > 0 && data.price <= 0)
                    .map(([type]) => type);

                if (missingPrices.length > 0) {
                    showToast('error', `Please set prices for: ${missingPrices.join(', ')}`);
                    return;
                }

                try {
                    // Show loading state
                    guestModalSave.disabled = true;
                    guestModalSave.innerHTML = '<div class="btn-preloader"></div> Saving...';

                    // Prepare data for API
                    const guestData = Object.entries(guestTypes)
                        .filter(([_, data]) => data.quantity > 0)
                        .map(([type, data]) => ({
                            type: type,
                            cost: data.price,
                            quantity: data.quantity,
                            total_cost: data.price * data.quantity
                        }));

                    // Send to server - use the correct route
                    const response = await fetch(`/admin/guest-addons`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify({
                            facility_booking_log_id: currentFacilityBookingLogId,
                            guests: guestData
                        })
                    });

                    const result = await response.json();
                    if (!response.ok || !result.success) {
                        throw new Error(result.message || 'Failed to save guest composition');
                    }

                    // Show success message
                    showToast('success', 'Guest composition saved successfully');

                    // Close modal
                    closeGuestModal();

                    // Reload booking summary to show updated guest information
                    if (currentBookingId) {
                        loadBookingSummary(currentBookingId);
                    }

                } catch (error) {
                    console.error('Error saving guest composition:', error);
                    showToast('error', error.message || 'Failed to save guest composition');
                } finally {
                    // Reset button state
                    guestModalSave.disabled = false;
                    guestModalSave.textContent = 'Save Guest Composition';
                }
            }

            let qrStream = null;
            let isProcessing = false;
            let qrScanning = false;
            let scanAnimationFrame = null;

            let searchType = 'checkin';

            // Current status filter and pagination
            let currentStatus = 'all';
            let currentPage = 1;
            let totalPages = 1;
            const perPage = 18; // Increased for more compact view
            let searchQuery = '';
            let currentBookingId = null;

            const searchInquiryId = sessionStorage.getItem('searchInquiryId');

            if (searchInquiryId) {
                // Clear the stored ID immediately
                sessionStorage.removeItem('searchInquiryId');

                // Set the search query to the ID and trigger search
                searchQuery = searchInquiryId;
                currentPage = 1;

                // Load bookings with the search ID
                loadBookings(currentStatus, currentPage);
                highlightBookingRow(searchInquiryId);
                loadBookingSummary(searchInquiryId);
            }

            document.querySelectorAll('input[name="search-type"]').forEach(radio => {
                radio.addEventListener('change', function () {
                    searchType = this.value;
                    updateDateLabel();
                    // performSearch(); // Optionally trigger search when changing type
                });
            });

            function updateDateLabel() {

                const dateLabel = document.querySelector('label[for="search-date"]');
                if (dateLabel) {
                    if (searchType === 'checkin') {
                        dateLabel.innerHTML = 'Check-in Date <span class="text-sm text-gray-500 font-normal">(Adjust if needed)</span>';
                    } else {
                        dateLabel.innerHTML = 'Check-out Date <span class="text-sm text-gray-500 font-normal">(Adjust if needed)</span>';
                    }
                }
            }

            updateDateLabel();

            // Search and Clear button functionality
            document.getElementById('search-button').addEventListener('click', function () {
                performSearch();
            });

            document.getElementById('clear-button').addEventListener('click', function () {
                clearSearch();
            });

            // Add event listeners for Enter key in search fields
            document.getElementById('search-firstname').addEventListener('keypress', function (e) {
                if (e.key === 'Enter') {
                    performSearch();
                }
            });

            document.getElementById('search-lastname').addEventListener('keypress', function (e) {
                if (e.key === 'Enter') {
                    performSearch();
                }
            });

            document.getElementById('search-date').addEventListener('keypress', function (e) {
                if (e.key === 'Enter') {
                    performSearch();
                }
            });

            function performSearch() {
                const firstName = document.getElementById('search-firstname').value.trim();
                const lastName = document.getElementById('search-lastname').value.trim();
                const date = document.getElementById('search-date').value;
                
                // Build search parameters with proper validation
                let searchParams = new URLSearchParams();
                
                if (firstName) searchParams.append('firstname', firstName);
                if (lastName) searchParams.append('lastname', lastName);
                if (date) {
                    if (searchType === 'checkin') {
                        searchParams.append('checkin_date', date);
                    } else {
                        searchParams.append('checkout_date', date);
                    }
                }
                searchParams.append('date_type', searchType);
                
                currentPage = 1;
                loadBookings(currentStatus, currentPage, searchParams.toString());
            }

            function clearSearch() {
                document.getElementById('search-firstname').value = '';
                document.getElementById('search-lastname').value = '';
                document.getElementById('search-date').value = '{{ date('Y-m-d') }}';

                // Reset search type to default (checkin)
                document.getElementById('search-checkin').checked = true;
                searchType = 'checkin';
                updateDateLabel();

                searchQuery = ''; // Reset to empty string
                currentPage = 1;
                loadBookings(currentStatus, currentPage);
            }


            // Function to show the result modal
            function showResultModal(title, message, isSuccess = true) {
                const modal = document.getElementById('resultModal');
                const modalTitle = document.getElementById('resultModalTitle');
                const modalMessage = document.getElementById('resultModalMessage');

                // Set title and message
                modalTitle.textContent = title;
                modalMessage.textContent = message;

                // Style based on success/error
                if (isSuccess) {
                    modalTitle.classList.add('text-green-600');
                    modalTitle.classList.remove('text-red-600');
                } else {
                    modalTitle.classList.add('text-red-600');
                    modalTitle.classList.remove('text-green-600');
                }

                // Show the modal with blur effect
                modal.classList.remove('hidden');
                document.body.style.overflow = 'hidden'; // Prevent scrolling
            }


            // Function to hide the result modal
            function hideResultModal() {
                const modal = document.getElementById('resultModal');
                modal.classList.add('hidden');
                document.body.style.overflow = 'auto'; // Re-enable scrolling
            }

            // Add event listener to close button
            document.getElementById('resultModalClose').addEventListener('click', hideResultModal);

            // Close modal when clicking outside
            document.getElementById('resultModal').addEventListener('click', function (e) {
                if (e.target === this) {
                    hideResultModal();
                }
            });

            // QR Scanner Modal Functions
            function openQRScanner() {
                qrModal.classList.add('active');
                startQRScanner();
            }

            function closeQRScanner() {
                qrModal.classList.remove('active');
                stopQRScanner();
                // Clear any ongoing processing
                isProcessing = false;

                // IMPORTANT: Re-enable page functionality
                enablePageFunctionality();
            }

            function handleVideoError() {
                console.error("Video element error");
                resultContainer.innerHTML = "Camera error. Please try again or refresh the page.";
                stopQRScanner();
            }

            function startQRScanner() {
                if (qrScanning) return;

                qrScanning = true;
                resultContainer.innerHTML = "Initializing camera...";
                welcomeMessage.classList.add('hidden');

                if (navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
                    navigator.mediaDevices.getUserMedia({ video: { facingMode: "environment" } })
                        .then(function (stream) {
                            qrStream = stream;
                            video.srcObject = stream;
                            video.setAttribute('playsinline', true);

                            // Update status message when video starts playing
                            video.onplaying = function () {
                                resultContainer.innerHTML = "Ready to scan...";
                            };

                            video.play()
                                .then(() => {
                                    scanAnimationFrame = requestAnimationFrame(scanQR);
                                })
                                .catch(function (err) {
                                    console.error("Video play error:", err);
                                    resultContainer.innerHTML = "Could not start camera. Please try again.";
                                    stopQRScanner();
                                });
                        })
                        .catch(function (err) {
                            console.error("Camera access error:", err);
                            resultContainer.innerHTML = "Could not access camera. Please grant permission.";
                            qrScanning = false;

                            if (err.name === 'NotAllowedError') {
                                resultContainer.innerHTML += "<br>Please allow camera access in your browser settings.";
                            } else if (err.name === 'NotFoundError' || err.name === 'OverconstrainedError') {
                                resultContainer.innerHTML += "<br>No camera found or camera doesn't meet requirements.";
                            }
                        });
                } else {
                    resultContainer.innerHTML = "Camera not supported in this browser.";
                    qrScanning = false;
                }
            }

            function stopQRScanner() {
                qrScanning = false;
                isProcessing = false;

                if (scanAnimationFrame) {
                    cancelAnimationFrame(scanAnimationFrame);
                    scanAnimationFrame = null;
                }

                if (qrStream) {
                    qrStream.getTracks().forEach(track => track.stop());
                    qrStream = null;
                }

                video.srcObject = null;
                resultContainer.innerHTML = "";
                welcomeMessage.classList.add('hidden');
            }

            function scanQR() {
                if (!qrScanning || isProcessing) {
                    if (qrScanning) {
                        scanAnimationFrame = requestAnimationFrame(scanQR);
                    }
                    return;
                }

                if (video.readyState === video.HAVE_ENOUGH_DATA) {
                    const canvas = document.createElement("canvas");
                    canvas.width = video.videoWidth;
                    canvas.height = video.videoHeight;
                    const context = canvas.getContext("2d");
                    context.drawImage(video, 0, 0, canvas.width, canvas.height);

                    const imageData = context.getImageData(0, 0, canvas.width, canvas.height);
                    const code = jsQR(imageData.data, imageData.width, imageData.height);

                    if (code) {
                        isProcessing = true;
                        resultContainer.innerHTML = "QR Code detected! Verifying...";
                        processQRCode(code.data);
                    } else {
                        scanAnimationFrame = requestAnimationFrame(scanQR);
                    }
                } else {
                    scanAnimationFrame = requestAnimationFrame(scanQR);
                }
            }

            async function processQRCode(qrData) {
                try {
                    console.log("📦 QR Data:", qrData);

                    if (!qrData || typeof qrData !== 'string') {
                        throw new Error("Invalid QR code data");
                    }

                    resultContainer.innerHTML = "<div class='spinner'></div> Processing QR code...";

                    const response = await fetch('/decode-qr-booking', {
                        method: 'POST',
                        headers: headers,
                        body: JSON.stringify({ qr_data: qrData })
                    });

                    if (!response.ok) {
                        const errorData = await response.json().catch(() => null);
                        throw new Error(errorData?.message || `Server error: ${response.status}`);
                    }

                    const result = await response.json();

                    if (!result.success) {
                        throw new Error(result.message || "QR code processing failed");
                    }

                    const bookingId = result.booking_id;

                    resultContainer.innerHTML = "✅ QR code processed successfully!";
                    welcomeMessage.classList.remove('hidden');
                    welcomeTitle.textContent = "Welcome!";
                    customerDetails.textContent = `Booking ID: ${bookingId} processed`;

                    highlightBookingRow(bookingId);
                    closeQRScanner();

                    searchQuery = bookingId.toString();
                    currentPage = 1;
                    // Wait for bookings to load before highlighting
                    await loadBookings(currentStatus, currentPage);

                    loadBookingSummary(bookingId);

                } catch (error) {
                    console.error("QR processing error:", error);
                    resultContainer.innerHTML = `❌ ${error.message || "An error occurred"}`;
                    isProcessing = false;

                    setTimeout(() => {
                        if (qrScanning) {
                            resultContainer.innerHTML = "Ready to scan...";
                        }
                        isProcessing = false;
                        scanAnimationFrame = requestAnimationFrame(scanQR);
                    }, 2000);
                }
            }

            function enablePageFunctionality() {
                // Reattach event listeners if needed
                document.querySelectorAll('.booking-row').forEach(row => {
                    row.addEventListener('click', function (e) {
                        // Don't trigger if clicking on a button
                        if (e.target.tagName === 'BUTTON') return;

                        const bookingId = this.dataset.bookingId;
                        loadBookingSummary(bookingId);
                        highlightBookingRow(bookingId);
                    });
                });

                // Reattach action button listeners
                document.querySelectorAll('.action-btn-details').forEach(btn => {
                    btn.addEventListener('click', function (e) {
                        const bookingId = this.dataset.bookingId;
                        const action = this.dataset.action;

                        switch (action) {
                            case 'details':
                                loadBookingSummary(bookingId);
                                highlightBookingRow(bookingId);
                                break;
                        }
                    });
                });

                // Refresh the bookings to ensure everything is in sync
                loadBookings(currentStatus, currentPage);
            }


            // QR Modal Event Listeners
            qrOpenBtn.addEventListener('click', openQRScanner);
            qrCloseBtn.addEventListener('click', closeQRScanner);
            qrCancelBtn.addEventListener('click', closeQRScanner);

            // Refresh button
            document.getElementById('refreshBtn').addEventListener('click', function () {
                loadBookings(currentStatus, currentPage);
            });

            document.getElementById('status-filter').addEventListener('change', function () {
                currentStatus = this.value;
                currentPage = 1;
                loadBookings(currentStatus, currentPage);
            });

            // Load initial data
            loadBookings(currentStatus, currentPage);

            // Search input handler with debounce
            const searchInput = document.getElementById('search-input');
            let searchTimeout;

            searchInput.addEventListener('input', function () {
                clearTimeout(searchTimeout);
                const value = this.value.trim();

                // Only set searchQuery if it's a string
                if (typeof value === 'string') {
                    searchQuery = value;
                } else {
                    searchQuery = '';
                }

                // Debounce the search to avoid too many requests
                searchTimeout = setTimeout(() => {
                    currentPage = 1;
                    loadBookings(currentStatus, currentPage);
                }, 500);
            });

            // Pagination handlers
            document.getElementById('prev-page').addEventListener('click', function () {
                if (currentPage > 1) {
                    currentPage--;
                    loadBookings(currentStatus, currentPage);
                }
            });

            document.getElementById('next-page').addEventListener('click', function () {
                if (currentPage < totalPages) {
                    currentPage++;
                    loadBookings(currentStatus, currentPage);
                }
            });

            const STATUS_CONFIG = {
                'pending_confirmation': {
                    class: 'bg-yellow-600',
                    text: 'PENDING'
                },
                'confirmed': {
                    class: 'bg-green-600',
                    text: 'CONFIRMED'
                },
                'checked_in': {
                    class: 'bg-blue-600',
                    text: 'CHECKED IN'
                },
                'checked_out': {
                    class: 'bg-purple-600',
                    text: 'CHECKED OUT'
                },
            };

            // Function to load bookings
            function loadBookings(status, page = 1, searchParams = '') {
                return new Promise((resolve, reject) => {
                    const url = new URL(`/get/mybooking`, window.location.origin);

                    if (searchParams) {
                        const params = new URLSearchParams(searchParams);
                        params.forEach((value, key) => {
                            url.searchParams.append(key, value);
                        });
                    }
                    // Only add status parameter if it's not "all"
                    if (status !== 'all') {
                        url.searchParams.append('status', status);
                    }

                    url.searchParams.append('page', page);
                    url.searchParams.append('per_page', perPage);
                    url.searchParams.append('date_type', searchType);

                    // Handle searchQuery - check if it's a string before trying to split
                    if (searchQuery && typeof searchQuery === 'string' && searchQuery.trim() !== '') {
                        // Check if it's a simple numeric ID (booking ID)
                        if (/^\d+$/.test(searchQuery.trim())) {
                            url.searchParams.append('id', searchQuery.trim());
                        }
                        // Check if searchQuery contains field-specific queries
                        else if (searchQuery.includes(':')) {
                            // Extract search parameters from the searchQuery string
                            const searchParams = {};
                            searchQuery.split(' ').forEach(term => {
                                const [key, value] = term.split(':');
                                if (key && value) {
                                    searchParams[key] = value;
                                }
                            });

                            // Add individual search parameters to the URL
                            if (searchParams.firstname) {
                                url.searchParams.append('firstname', searchParams.firstname);
                            }
                            if (searchParams.lastname) {
                                url.searchParams.append('lastname', searchParams.lastname);
                            }
                            if (searchParams.checkin_date) {
                                url.searchParams.append('checkin_date', searchParams.checkin_date);
                            }
                            if (searchParams.checkout_date) {
                                url.searchParams.append('checkout_date', searchParams.checkout_date);
                            }
                            if (searchParams.id) {
                                url.searchParams.append('id', searchParams.id);
                            }
                        } else {
                            // Simple search - treat as general search term
                            url.searchParams.append('search', searchQuery);
                        }
                    }

                    // Show loading state
                    document.getElementById('bookings-table-body').innerHTML = `
                                                                    <tr>
                                                                        <td colspan="5" class="px-6 py-6 text-center">
                                                                            <div class="flex justify-center">
                                                                                <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-red-600"></div>
                                                                            </div>
                                                                            <p class="mt-2 text-lg text-gray-500">Loading bookings...</p>
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
                            document.getElementById('pagination-info').textContent = `Showing ${data.from} to ${data.to} of ${data.total} entries`;

                            // Update pagination buttons
                            document.getElementById('prev-page').disabled = currentPage <= 1;
                            document.getElementById('next-page').disabled = currentPage >= totalPages;

                            let html = '';

                            if (bookings.length === 0) {
                                html = `
                                                                            <tr>
                                                                                <td colspan="5" class="px-6 py-6 text-center">
                                                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 mx-auto text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                                                    </svg>
                                                                                    <p class="mt-2 text-lg text-gray-500">No bookings found</p>
                                                                                </td>
                                                                            </tr>
                                                                        `;
                            } else {
                                bookings.forEach(booking => {
                                    const detail = booking.details?.[0];
                                    // Use the status directly from the API response
                                    const displayStatus = booking.status;
                                    const statusInfo = STATUS_CONFIG[displayStatus] || { class: 'bg-yellow-600', text: displayStatus.toUpperCase() };
                                    const isPending = displayStatus === 'pending_confirmation';
                                    html += `
                                                                                <tr class="booking-row ${isPending ? 'pending' : ''} cursor-pointer" data-booking-id="${booking.id}">
                                                                                    <td class="px-3 py-2">
                                                                                        <div class="text-sm text-gray-900 font-medium">${booking.id}</div>
                                                                                    </td>
                                                                                    <td class="px-3 py-2">
                                                                                        <span class="px-2 inline-flex text-sm leading-5 font-semibold rounded-full ${statusInfo.class} text-white status-badge">
                                                                                            ${statusInfo.text}
                                                                                        </span>
                                                                                    </td>
                                                                                    <td class="px-3 py-2">
                                                                                        <div class="text-lg text-gray-900">${booking.user?.firstname || 'Guest'} ${booking.user?.lastname || ''}</div>
                                                                                        <div class="text-sm text-gray-500">${booking.user?.phone || 'No phone'}</div>
                                                                                    </td>
                                                                                    <td class="px-3 py-2">
                                                                                        <div class="text-sm text-gray-900">${detail ? formatDate(detail.checkin_date) : 'N/A'}</div>
                                                                                        <div class="text-sm text-gray-500">${detail ? getNights(detail.checkin_date, detail.checkout_date) + ' nights' : 'N/A'}</div>
                                                                                    </td>
                                                                                    <td class="px-3 py-2">
                                                                                        <div class="flex flex-wrap gap-1 justify-start">
                                                                                            <button class="action-btn-details btn-details" data-booking-id="${booking.id}" data-action="details">
                                                                                                Details
                                                                                            </button>
                                                                                        </div>
                                                                                    </td>
                                                                                </tr>
                                                                            `;
                                });
                            }

                            document.getElementById('bookings-table-body').innerHTML = html;

                            // Add click handlers for action buttons
                            document.querySelectorAll('.action-btn-details').forEach(btn => {
                                btn.addEventListener('click', function (e) {
                                    const bookingId = this.dataset.bookingId;
                                    const action = this.dataset.action;

                                    // Handle different actions
                                    switch (action) {
                                        case 'details':
                                            loadBookingSummary(bookingId);
                                            highlightBookingRow(bookingId);
                                            break;
                                    }
                                });
                            });

                            // Add click handlers for table rows
                            document.querySelectorAll('.booking-row').forEach(row => {
                                row.addEventListener('click', function (e) {
                                    // Don't trigger if clicking on a button
                                    if (e.target.tagName === 'BUTTON') return;

                                    const bookingId = this.dataset.bookingId;
                                    loadBookingSummary(bookingId);
                                    highlightBookingRow(bookingId);
                                });
                            });

                            // Highlight the scanned booking if it exists in the results
                            if (window.scannedBookingId) {
                                setTimeout(() => {
                                    highlightBookingRow(window.scannedBookingId);
                                    // Clear the stored ID after highlighting
                                    delete window.scannedBookingId;
                                }, 100);
                            }

                            resolve(data);
                        })
                        .catch(error => {
                            showToast('error', 'Failed to load bookings');
                            console.error('Error:', error);
                            document.getElementById('bookings-table-body').innerHTML = `
                                                                        <tr>
                                                                            <td colspan="5" class="px-6 py-6 text-center">
                                                                                <div class="bg-red-50 border-l-4 border-red-400 p-3">
                                                                                    <div class="flex">
                                                                                        <div class="flex-shrink-0">
                                                                                            <svg class="h-5 w-5 text-red-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                                                                            </svg>
                                                                                        </div>
                                                                                        <div class="ml-3">
                                                                                            <p class="text-lg text-red-700">
                                                                                                Failed to load bookings. Please try again later.
                                                                                            </p>
                                                                                        </div>
                                                                                    </div>
                                                                                    <button onclick="loadBookings(currentStatus, currentPage)" class="mt-2 px-3 py-1 bg-red-600 text-white rounded text-sm hover:bg-red-700">
                                                                                        Retry
                                                                                    </button>
                                                                                </div>
                                                                            </td>
                                                                        </tr>
                                                                    `;
                            reject(error);
                        });
                });
            }

            // Function to highlight the selected booking row

            function highlightBookingRow(bookingId) {
                console.log("Attempting to highlight booking:", bookingId);

                // First remove any existing highlights
                document.querySelectorAll('.booking-row.selected').forEach(row => {
                    row.classList.remove('selected');
                });

                // Try to find and highlight the matching row
                const rows = document.querySelectorAll('.booking-row');
                let found = false;

                rows.forEach(row => {
                    const rowBookingId = row.dataset.bookingId;
                    if (rowBookingId === bookingId.toString()) {
                        row.classList.add('selected');
                        // Scroll to the row with lgooth animation
                        row.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                        found = true;
                        console.log("Successfully highlighted booking:", bookingId);
                    }
                });

                // If not found immediately, try again after a short delay
                if (!found) {
                    console.log("Booking row not found immediately, retrying...");
                    setTimeout(() => {
                        const retryRows = document.querySelectorAll('.booking-row');
                        retryRows.forEach(row => {
                            const rowBookingId = row.dataset.bookingId;
                            if (rowBookingId === bookingId.toString()) {
                                row.classList.add('selected');
                                row.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                                console.log("Highlighted booking on retry:", bookingId);
                            }
                        });
                    }, 500); // Wait 500ms before retrying
                }
            }


            // Function to load booking summary
            async function loadBookingSummary(bookingId) {
                console.log(`Loading booking summary for ID: ${bookingId}`);
                currentBookingId = bookingId;

                try {
                    // Show loading state
                    document.getElementById('booking-summary').innerHTML = `
                                                                    <div class="text-center py-6 px-3">
                                                                        <div class="flex justify-center">
                                                                            <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-red-600"></div>
                                                                        </div>
                                                                        <p class="mt-2 text-lg text-gray-500">Loading booking details...</p>
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
                    const detail = booking.details?.[0];
                    const guestAddonsHtml = generateGuestAddonsHtml(booking.guest_addons || []);
                    const checkinDate = detail.checkin_date;

                    // Use booking status directly (removed payment-based status logic)
                    const bookingStatus = booking.status;
                    const statusInfo = STATUS_CONFIG[bookingStatus] || { class: 'bg-yellow-600', text: bookingStatus.toUpperCase() };

                    const isStatusDisabled = bookingStatus === 'checked_in' || bookingStatus === 'checked_out';

                    // PAYMENT COMPARISON LOGIC STARTS HERE
                    const advancePaid = parseFloat(booking.payments?.[0]?.amount) || 0;

                    const totalAmount = booking.details?.reduce((sum, detail) => {
                        return sum + parseFloat(detail.total_price || 0);
                    }, 0) || 0;

                    const checkinPaid = parseFloat(booking.payments?.[0]?.checkin_paid) || 0;

                    // Total amount customer has paid so far
                    const totalPayment = advancePaid + checkinPaid;

                    const paymentScheme = booking.payments?.[0]?.method || 'Unknown';

                    const reference = booking.payments?.[0]?.reference_no || 'Unknown';
                    // Calculate remaining balance (positive = owed, negative = overpaid)
                    const balance = totalAmount - totalPayment;

                    // Calculate payment completion percentage
                    const paidPercentage = totalAmount > 0 ? (totalPayment / totalAmount) * 100 : 0;
                    // PAYMENT COMPARISON LOGIC ENDS HERE

                    // Generate room list HTML
                    const roomListHtml = booking.summaries?.length
                        ? booking.summaries.map(summary => {
                            const room = summary.facility;
                            // Use summary.facility_price or room.price depending on your data structure
                            const price = summary.facility_price || 0;

                            return room ? `
                                                                            <li class="flex justify-between py-1.5 border-b border-gray-100 last:border-0">
                                                                                <span class="text-sm text-gray-700">${room.name}</span>
                                                                                <span class="text-sm font-medium text-gray-800">${formatCurrency(price)}</span>
                                                                            </li>` : '';
                        }).join('')
                        : '<li class="text-sm text-gray-600 py-1.5">No room info available</li>';

                    // Generate guest composition HTML with proper type conversion
                    const guestCompositionHtml = booking.summaries?.length
                        ? booking.summaries.map(summary => {
                            const room = summary.facility;
                            if (!room) return '';

                            const roomId = Number(room.id);
                            console.log(`Processing room ${room.name} (ID: ${roomId})`);
                            console.log('booking.guest_details:', booking.guest_details);

                            // Get all guest details for this facility with type conversion
                            const guestsForRoom = (booking.guest_details || []).filter(g => {
                                const guestFacilityId = Number(g.facility_id);
                                console.log(`Comparing room ${roomId} with guest facility ${guestFacilityId}`);
                                return guestFacilityId === roomId;
                            });

                            console.log(`Found ${guestsForRoom.length} guest records for room ${roomId}`);

                            // Group by guest type and sum quantities
                            const guestTypeCounts = guestsForRoom.reduce((acc, guest) => {
                                const type = guest.guest_type?.type || 'Unknown';
                                const quantity = Number(guest.quantity) || 1;
                                acc[type] = (acc[type] || 0) + quantity;
                                return acc;
                            }, {});

                            console.log('Guest type counts:', guestTypeCounts);

                            // Create guest items HTML
                            const guestItems = Object.entries(guestTypeCounts).length
                                ? `
                                                                                <table class="w-full border-collapse mt-1">
                                                                                    <tbody>
                                                                                        ${Object.entries(guestTypeCounts).map(([type, quantity]) => `
                                                                                            <tr>
                                                                                                <td class="p-0.5 border-b border-gray-200 text-sm">${type}</td>
                                                                                                <td class="p-0.5 border-b border-gray-200 text-sm text-right">${quantity} guest${quantity !== 1 ? 's' : ''}</td>
                                                                                            </tr>
                                                                                        `).join('')}
                                                                                        <tr class="font-semibold">
                                                                                            <td class="p-0.5 text-sm">Total Guests</td>
                                                                                            <td class="p-0.5 text-sm text-right">${Object.values(guestTypeCounts).reduce((a, b) => a + b, 0)}</td>
                                                                                        </tr>
                                                                                    </tbody>
                                                                                </table>
                                                                            `
                                : '<p class="text-sm text-gray-500 italic mt-1">No guest details recorded</p>';

                            return `
                                                                            <div class="mb-3 p-2 bg-gray-50 rounded">
                                                                                <h4 class="text-sm font-semibold text-gray-800">${room.name}</h4>
                                                                                ${guestItems}
                                                                            </div>
                                                                        `;
                        }).join('')
                        : '<p class="text-sm text-gray-500 italic">No room information available</p>';

                    // NEW BREAKFAST LOGIC: Check if breakfast is included per facility summary
                    const hasBreakfast = booking.summaries?.some(summary => {
                        return summary.breakfast_id !== null && summary.breakfast_id !== undefined;
                    });

                    // Find summaries with breakfast for display
                    const breakfastSummaries = booking.summaries?.filter(summary => {
                        return summary.breakfast_id !== null && summary.breakfast_id !== undefined;
                    }) || [];

                    // Generate breakfast HTML if available
                    const breakfastHtml = hasBreakfast ? `
                                                                    <!-- Breakfast Information -->
                                                                    <div class="px-3 py-3 bg-white">
                                                                        <h4 class="text-sm font-semibold text-gray-800 flex items-center mb-1">                           
                                                                            Breakfast Included
                                                                        </h4>
                                                                        <div class="mt-1 pl-5">
                                                                        ${breakfastSummaries.map(summary => `
                                                                            <div class="flex justify-between mb-1">
                                                                                <span class="text-sm text-gray-600">${summary.facility.name} Breakfast:</span>
                                                                                <span class="text-sm font-medium text-gray-800">
                                                                                    ${summary.breakfast_price ? formatCurrency(summary.breakfast_price) : 'Included'}
                                                                                </span>
                                                                            </div>
                                                                        `).join('')}

                                                                        </div>
                                                                    </div>
                                                                ` : '';
                    // Generate action buttons HTML based on booking status
                    let actionButtonsHtml = '';

                    // Get button states based on booking status
                    const buttonStates = getButtonStates(bookingStatus, checkinDate);

                    const checkoutUrl = `/check-out/receipt/${bookingId}`;

                    actionButtonsHtml = `
                                                                    <button class="sidebar-btn bg-green-600 text-white hover:bg-green-700 ${buttonStates.confirm.disabled ? 'opacity-60 cursor-not-allowed' : ''}" 
                                                                        data-action="confirm" data-booking-id="${bookingId}" ${buttonStates.confirm.disabled ? 'disabled' : ''}>
                                                                        ${buttonStates.confirm.loading ? '<div class="btn-preloader"></div>' :
                            '<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>'}
                                                                        Confirm Booking
                                                                    </button>
                                                                    <button class="sidebar-btn bg-blue-600 text-white hover:bg-blue-700 ${buttonStates.checkin.disabled ? 'opacity-60 cursor-not-allowed' : ''}" 
                                                                        data-action="checkin" data-booking-id="${bookingId}" ${buttonStates.checkin.disabled ? 'disabled' : ''}>
                                                                        ${buttonStates.checkin.loading ? '<div class="btn-preloader"></div>' :
                            '<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-3" fill="none" viewBox="0 0 24 24"stroke="currentColor"> <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M7.5 10.5l4.5 4.5m0 0l4.5-4.5m-4.5 4.5V3" /> </svg>'}
                                                                        Check-in Guest
                                                                    </button>
                                                                    <button class="sidebar-btn bg-purple-600 text-white hover:bg-purple-700 ${buttonStates.checkout.disabled ? 'opacity-60 cursor-not-allowed' : ''}" 
                                                                        data-action="checkout" data-booking-id="${bookingId}" ${buttonStates.checkout.disabled ? 'disabled' : ''}>
                                                                        ${buttonStates.checkout.loading ? '<div class="btn-preloader"></div>' :
                            '<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 7l4 4m0 0l-4 4m4-4H7" /></svg>'}
                                                                        Check-out Guest
                                                                    </button>

                                                                `;

                    // Generate the HTML template
                    const html = `
                            <div class="divide-y divide-gray-200 fade-in">
                                <!-- Header with Status and Guest Info -->
                                <div class="px-3 py-3 bg-white">
                                    <div class="flex justify-between items-start mb-2">
                                        <div>
                                            <h3 class="text-lg font-bold text-gray-900">${booking.code || 'N/A'}</h3>
                                            <p class="text-sm text-gray-500 mt-0.5">Record ID: ${booking.id || 'N/A'}</p>
                                        </div>
                                        <span class="px-2 py-0.5 rounded-full text-xl font-semibold ${statusInfo.class} text-white status-badge">
                                            ${statusInfo.text}
                                        </span>
                                    </div>

                                    <!-- Payment Progress Bar -->
                                    <div class="mb-3">
                                        <div class="flex justify-between text-2xl lg:text-sm mb-1">
                                            <span class="text-gray-600">Payment Progress</span>
                                            <span class="font-medium">${Math.round(paidPercentage)}%</span>
                                        </div>
                                        <div class="progress-bar">
                                            <div class="progress-fill" style="width: ${paidPercentage}%"></div>
                                        </div>
                                    </div>

                                    <div class="mb-2">
                                        <h4 class="text-sm font-semibold text-gray-800 flex items-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 mr-1.5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                                            </svg>

                                            Guest Information
                                        </h4>
                                        <div class="mt-1 pl-5">
                                            <p class="text-sm font-medium text-gray-800">${booking.user?.firstname || 'Guest'} ${booking.user?.lastname || ''}</p>
                                            <p class="text-sm text-gray-600 mt-0.5">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 inline mr-1" viewBox="0 0 20 20" fill="currentColor">
                                                    <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z" />
                                                    <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z" />
                                                </svg>
                                                ${booking.user?.email || 'N/A'}
                                            </p>
                                            <p class="text-sm text-gray-600">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 inline mr-1" viewBox="0 0 20 20" fill="currentColor">
                                                    <path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z" />
                                                </svg>
                                                ${booking.user?.phone || 'N/A'}
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Stay Details -->
                                <div class="px-3 py-3 bg-white">
                                    <h4 class="text-sm font-semibold text-gray-800 flex items-center mb-1">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 mr-1.5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm.5-12a.5.5 0 00-1 0v4.25l3.5 2.1a.5.5 0 10.5-.86l-3-1.8V6z" clip-rule="evenodd" />
                                        </svg>
                                        Stay Details
                                    </h4>
                                    <div class="mt-1 pl-5 space-y-1">
                                        <div class="flex justify-between">
                                            <span class="text-sm text-gray-600">Check-in:</span>
                                            <span class="text-sm font-medium text-gray-800">
                                                ${detail ? formatDate(detail.checkin_date) : 'N/A'}
                                            </span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-sm text-gray-800">Check-out:</span>
                                            <span class="text-sm font-medium text-gray-800">
                                                ${detail ? formatDate(detail.checkout_date) : 'N/A'}
                                            </span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-sm text-gray-600">Nights:</span>
                                            <span class="text-sm font-medium text-gray-800">
                                                ${detail ? getNights(detail.checkin_date, detail.checkout_date) : 'N/A'}
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Guest Composition -->
                                <div class="px-3 py-3 bg-white">
                                    <div class="flex items-center justify-between mb-1">
                                        <h4 class="text-sm font-semibold text-gray-800 flex items-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" 
                                                class="h-3.5 w-3.5 mr-1.5 text-gray-400" 
                                                viewBox="0 0 20 20" 
                                                fill="currentColor">
                                                <path fill-rule="evenodd" 
                                                    d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" 
                                                    clip-rule="evenodd" />
                                            </svg>
                                            Guest Composition
                                        </h4>

                                        <!-- Add Guest Button -->
                                        ${!isStatusDisabled ? `
                                        <button id="add-guest-btn"
                                            class="flex items-center px-3 py-1.5 bg-indigo-600 cursor-pointer hover:bg-indigo-700 text-white text-xs font-medium rounded-lg shadow-sm transition">
                                            <svg xmlns="http://www.w3.org/2000/svg" 
                                                class="h-4 w-4 mr-1" 
                                                fill="none" 
                                                viewBox="0 0 24 24" 
                                                stroke="currentColor" 
                                                stroke-width="2">
                                                <path stroke-linecap="round" 
                                                    stroke-linejoin="round" 
                                                    d="M12 4v16m8-8H4" />
                                            </svg>
                                            Add Guest
                                        </button>
                                        ` : ''}
                                    </div>

                                    <div class="mt-1 pl-5">
                                        ${guestCompositionHtml}
                                    </div>
                                </div>

                                ${guestAddonsHtml}


                                <!-- Rooms Booked -->
                                <div class="px-3 py-3 bg-white">
                                    <h4 class="text-sm font-semibold text-gray-800 flex items-center mb-1">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 mr-1.5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                            <path d="M10 2L2 9h3v7h4v-4h2v4h4V9h3L10 2z" />
                                        </svg>

                                        Rooms Booked
                                    </h4>
                                    <ul class="mt-1 pl-5">
                                        ${roomListHtml}
                                    </ul>
                                </div>

                                <!-- Breakfast Information (if available) -->
                                ${breakfastHtml}
                                <!-- Payment Summary -->
                                <div class="bg-white border border-gray-100 overflow-hidden">

                                    <div class="px-4 py-3 bg-gradient-to-r from-red-600 to-red-800 border-b border-gray-200">
                                        <h4 class="text-lg font-semibold text-white flex items-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2 text-white" viewBox="0 0 20 20" fill="currentColor">
                                                <path d="M8.433 7.418c.155-.103.346-.196.567-.267v1.698a2.305 2.305 0 01-.567-.267C8.07 8.34 8 8.114 8 8c0-.114.07-.34.433-.582zM11 12.849v-1.698c.22.071.412.164.567.267.364.243.433.468.433.582 0 .114-.07.34-.433.582a2.305 2.305 0 01-.567.267z" />
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-13a1 1 0 10-2 0v.092a4.535 4.535 0 00-1.676.662C6.602 6.234 6 7.009 6 8c0 .99.602 1.765 1.324 2.246.48.32 1.054.545 1.676.662v1.941c-.391-.127-.68-.317-.843-.504a1 1 0 10-1.51 1.31c.562.649 1.413 1.076 2.353 1.253V15a1 1 0 102 0v-.092a4.535 4.535 0 001.676-.662C13.398 13.766 14 12.991 14 12c0-.99-.602-1.765-1.324-2.246A4.535 4.535 0 0011 9.092V7.151c.391.127.68.317.843.504a1 1 0 101.511-1.31c-.563-.649-1.413-1.076-2.354-1.253V5z" clip-rule="evenodd" />
                                            </svg>
                                            PAYMENT SUMMARY
                                        </h4>
                                    </div>

                                    <div class="px-4 py-3 space-y-3">
                                        <div class="flex justify-between items-center">
                                            <span class="text-lg text-gray-600">Payment Scheme:</span>
                                            <span class="text-lg font-medium text-blue-600 bg-blue-50 px-2 py-1 rounded">
                                                ${paymentScheme}
                                            </span>
                                        </div>

                                        <div class="flex justify-between items-center">
                                            <span class="text-lg text-gray-600">Reference:</span>
                                            <span class="text-lg font-medium text-gray-600 bg-gray-50 px-2 py-1 rounded">
                                                ${reference}
                                            </span>
                                        </div>

                                        <div class="grid grid-cols-2 gap-2">
                                            <div class="bg-gray-50 p-2.5 rounded-lg">
                                                <p class="text-sm text-gray-500 mb-1">Total Amount</p>
                                                <p class="text-lg font-semibold text-gray-800">${formatCurrency(totalAmount)}</p>
                                            </div>

                                            <div class="bg-green-50 p-2.5 rounded-lg">
                                                <p class="text-sm text-green-600 mb-1">Advance Paid</p>
                                                <p class="text-lg font-semibold text-green-700">${formatCurrency(advancePaid)}</p>
                                            </div>

                                            <div class="bg-green-50 p-2.5 rounded-lg">
                                                <p class="text-sm text-green-600 mb-1">Check-in Paid</p>
                                                <p class="text-lg font-semibold text-green-700">${formatCurrency(checkinPaid)}</p>
                                            </div>

                                            <div class="bg-blue-50 p-2.5 rounded-lg">
                                                <p class="text-sm text-blue-600 mb-1">Total Paid</p>
                                                <p class="text-lg font-semibold text-blue-700">${formatCurrency(totalPayment)}</p>
                                            </div>
                                        </div>

                                        <div class="border-t border-gray-200 pt-3 mt-1">
                                            <div class="flex justify-between items-center py-2 ${balance > 0 ? 'bg-red-50 -mx-2 px-2 rounded' : 'bg-green-50 -mx-2 px-2 rounded'}">
                                                <span class="text-base font-semibold ${balance > 0 ? 'text-red-700' : 'text-green-700'}">Balance:</span>
                                                <span class="text-base font-bold ${balance > 0 ? 'text-red-700' : 'text-green-700'}">
                                                    ${formatCurrency(Math.abs(balance))}
                                                    <span class="text-sm font-normal ml-1">${balance > 0 ? '(Due)' : '(FULLY PAID)'}</span>
                                                </span>
                                            </div>
                                        </div>

                                        ${balance > 0 ? `
                                        <div class="mt-3 p-2.5 bg-yellow-50 border border-yellow-200 rounded-lg">
                                            <p class="text-sm text-yellow-800 flex items-start">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 mt-0.5 mr-1.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M4.93 19h14.14a1 1 0 00.86-1.5L13.86 4.5a1 1 0 00-1.72 0L4.07 17.5a1 1 0 00.86 1.5z" />
                                                </svg>
                                                <span>Customer must pay the outstanding balance of ${formatCurrency(Math.abs(balance))} upon check-in</span>
                                            </p>
                                        </div>
                                        ` : ''}
                                    </div>
                                </div>


                                <!-- Action Buttons -->
                                ${actionButtonsHtml ? `
                                <div class="px-3 py-3 bg-white sidebar-actions">
                                    ${actionButtonsHtml}

                                    <!-- Add date information below the buttons -->
                                    <div class="text-sm text-gray-500 mt-2 space-y-1">
                                        ${!buttonStates.confirm.disabled ? `
                                            <div class="button-hint">Booking confirmation is available now</div>
                                        ` : ''}

                                        ${buttonStates.checkin.disabled ? `
                                            <div class="button-hint">Check-in will be enabled on ${formatDate(checkinDate)}</div>
                                        ` : `
                                            <div class="button-hint">Check-in is available now</div>
                                        `}
                                    </div>
                                </div>
                                ` : ''}

                            </div>
                            `;

                    // Insert HTML into DOM
                    document.getElementById('booking-summary').innerHTML = html;

                } catch (error) {
                    console.error('Error:', error);
                    document.getElementById('booking-summary').innerHTML = `
                            <div class="p-3">
                                <div class="bg-red-50 border-l-4 border-red-400 p-2">
                                    <div class="flex">
                                        <div class="flex-shrink-0">
                                        <svg xmlns="http://www.w3.org/2000/svg" 
                                                viewBox="0 0 20 20" 
                                                fill="currentColor" 
                                                class="h-4 w-4 text-red-400">
                                            <path fill-rule="evenodd" 
                                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" 
                                                    clip-rule="evenodd" />
                                        </svg>

                                        </div>
                                        <div class="ml-2">
                                            <p class="text-sm text-red-700">
                                                Failed to load booking details. Please try again later.
                                            </p>
                                        </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        `;
                }
            }

            function generateGuestAddonsHtml(guestAddons) {
                if (!guestAddons || guestAddons.length === 0) {
                    return `
                            <div class="guest-addons-section">
                                <div class="guest-addons-header">
                                    <h4 class="text-sm font-semibold text-gray-800 flex items-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 mr-1.5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                        </svg>
                                        Guest Addons
                                    </h4>
                                </div>
                                <div class="guest-addons-list">
                                    <p class="no-addons">No addons added yet</p>
                                </div>
                            </div>
                        `;
                }

                const addonsTotal = guestAddons.reduce((sum, addon) => sum + (parseFloat(addon.total_cost) || 0), 0);

                return `
                        <div class="w-full px-3 py-3 bg-white">
                            <!-- Header -->
                            <div class="guest-addons-header mb-2">
                                <h4 class="guest-addons-title flex items-center text-sm font-semibold text-gray-800">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                    </svg>
                                    Guest Addons
                                </h4>
                            </div>

                            <!-- Addons List -->
                            <div class="guest-addons-list space-y-2 w-full">
                                ${guestAddons.map(addon => `
                                    <div class="guest-addon-item w-full" data-addon-id="${addon.id}">
                                        <div class="flex items-center justify-between w-full">
                                            <span class="guest-addon-name text-sm text-gray-700">${addon.quantity} x ${addon.type}</span>
                                            <div class="flex items-center">
                                                <span class="guest-addon-price mr-2 text-sm font-medium text-gray-800">${formatCurrency(addon.total_cost)}</span>
                                                <button class="delete-addon-btn text-red-600 hover:text-red-800 cursor-pointer" data-addon-id="${addon.id}" data-booking-id="${currentBookingId}">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                `).join('')}

                                <!-- Total Row -->
                                <div class="guest-addon-item flex items-center justify-between border-t border-gray-300 pt-2 mt-2 w-full">
                                    <span class="guest-addon-name font-semibold text-gray-900">Total Addons:</span>
                                    <span class="guest-addon-price font-semibold text-gray-900">${formatCurrency(addonsTotal)}</span>
                                </div>
                            </div>
                        </div>


                    `;
            }

            // Function to handle addon deletion
            async function deleteGuestAddon(addonId, bookingId) {
                if (!confirm('Are you sure you want to delete this addon?')) {
                    return;
                }

                try {
                    const response = await fetch(`/delete/guest-addons/${addonId}`, {
                        method: 'DELETE',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify({
                            booking_id: bookingId // Pass the current booking ID
                        })
                    });

                    const result = await response.json();
                    
                    if (!response.ok || !result.success) {
                        throw new Error(result.message || 'Failed to delete addon');
                    }

                    showToast('success', 'Addon deleted successfully');
                    
                    // Remove the addon item from the UI
                    const addonItem = document.querySelector(`.guest-addon-item[data-addon-id="${addonId}"]`);
                    if (addonItem) {
                        addonItem.remove();
                    }
                    
                    // Reload booking summary to update totals
                    if (bookingId) {
                        loadBookingSummary(bookingId);
                    }
                } catch (error) {
                    console.error('Error deleting addon:', error);
                    showToast('error', error.message || 'Failed to delete addon');
                }
            }

            // Add event delegation for delete buttons
            document.addEventListener('click', function(e) {
                if (e.target.closest('.delete-addon-btn')) {
                    const button = e.target.closest('.delete-addon-btn');
                    const addonId = button.dataset.addonId;
                    const bookingId = button.dataset.bookingId || currentBookingId;
                    deleteGuestAddon(addonId, bookingId);
                }
            });

            // Helper function to determine button states based on booking status

            function getButtonStates(status, checkinDate) {
                const states = {
                    confirm: { disabled: false, loading: false },
                    checkin: { disabled: false, loading: false },
                    checkout: { disabled: false, loading: false }
                };

                // If dates are not provided, disable check-in/check-out buttons
                if (!checkinDate) {
                    states.checkin.disabled = true;
                    return states;
                }

                const today = new Date();
                today.setHours(0, 0, 0, 0); // Normalize to compare dates only

                const checkin = new Date(checkinDate);
                checkin.setHours(0, 0, 0, 0);

                // Optional: Add grace periods
                const CHECKIN_GRACE_PERIOD = 0; // No grace period by default

                // Check if today is within the legitimate period for check-in
                const isCheckinPeriod = today >= new Date(checkin.getTime() - (CHECKIN_GRACE_PERIOD * 24 * 60 * 60 * 1000))

                switch (status) {
                    case 'pending_confirmation':
                        states.confirm.disabled = false;
                        states.checkin.disabled = true; // Can't check in until confirmed
                        states.checkout.disabled = true;
                        break;
                    case 'confirmed':
                        states.confirm.disabled = true;
                        states.checkin.disabled = !isCheckinPeriod; // Only enable during legitimate period
                        states.checkout.disabled = true;
                        break;
                    case 'checked_in':
                        states.confirm.disabled = true;
                        states.checkin.disabled = true;
                        states.checkout.disabled = false;
                        break;
                    case 'checked_out':
                        states.confirm.disabled = true;
                        states.checkin.disabled = true;
                        states.checkout.disabled = true;
                        break;
                    default:
                        states.confirm.disabled = true;
                        states.checkin.disabled = true;
                        states.checkout.disabled = true;
                }

                return states;
            }

            // Helper functions (assumed to exist)
            function formatCurrency(amount) {
                return new Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD' }).format(amount);
            }

            function formatDate(dateString) {
                return new Date(dateString).toLocaleDateString();
            }

            function getNights(checkin, checkout) {
                const diffTime = Math.abs(new Date(checkout) - new Date(checkin));
                return Math.ceil(diffTime / (1000 * 60 * 60 * 24));
            }

            document.addEventListener('click', function (e) {
                if (e.target.closest('[data-action="confirm"]')) {
                    const button = e.target.closest('[data-action="confirm"]');
                    const bookingId = button.dataset.bookingId;
                    handleConfirmBooking(bookingId, button);
                }

                if (e.target.closest('[data-action="checkin"]')) {
                    const button = e.target.closest('[data-action="checkin"]');
                    const bookingId = button.dataset.bookingId;
                    checkinBooking(bookingId, button);
                }

                if (e.target.closest('[data-action="checkout"]')) {
                    const button = e.target.closest('[data-action="checkout"]');
                    const bookingId = button.dataset.bookingId;
                    checkoutBooking(bookingId, button);
                }
            });

            async function handleConfirmBooking(bookingId, button) {
                if (!bookingId) {
                    alert('Invalid booking reference');
                    return;
                }

                if (!confirm('Are you sure you want to confirm this booking?')) {
                    return;
                }

                try {
                    // Show loading state on button
                    button.disabled = true;
                    button.innerHTML = '<div class="btn-preloader"></div> Processing...';

                    // Make AJAX request to confirm booking
                    const response = await fetch(`/bookings/${bookingId}/verify-with-receipt`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                        },
                        body: JSON.stringify({
                            send_notifier: true,
                        })
                    });

                    const data = await response.json();

                    if (!response.ok) {
                        throw new Error(data.message || 'Failed to confirm booking');
                    }

                    // Reset button state
                    button.disabled = false;
                    button.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg> Confirm Booking';

                    showResultModal(
                        "Booking Confirmed!",
                        data.message || 'The booking has been confirmed and the guest has been notified.',
                        true
                    );

                    // Reload the booking summary to update the status
                    loadBookingSummary(bookingId);
                    loadBookings(currentStatus, currentPage);
                } catch (error) {
                    console.error('Error:', error);
                    // Reset button state
                    button.disabled = false;
                    button.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg> Confirm Booking';

                    // Close confirmation modal and show error modal
                    showResultModal(
                        "Error",
                        error.message || 'Failed to confirm booking. Please try again.',
                        false
                    );
                }
            }

            async function checkinBooking(bookingId, button) {
                try {
                    // Show loading state on button
                    button.disabled = true;
                    button.innerHTML = '<div class="btn-preloader"></div> Processing...';

                    // First, check if there's an outstanding balance
                    const bookingResponse = await fetch(`/get/show/bookings/checkin/${bookingId}`, {
                        method: 'GET',
                        headers: headers
                    });

                    if (!bookingResponse.ok) {
                        throw new Error('Failed to fetch booking details');
                    }

                    const bookingData = await bookingResponse.json();
                    const booking = bookingData.data;

                    // Calculate balance (using the same logic as in loadBookingSummary)
                    const advancePaid = parseFloat(booking.payments?.[0]?.amount) || 0;
                    const totalAmount = booking.details?.reduce((sum, detail) => {
                        return sum + parseFloat(detail.total_price || 0);
                    }, 0) || 0;
                    const checkinPaid = parseFloat(booking.payments?.[0]?.checkin_paid) || 0;
                    const totalPayment = advancePaid + checkinPaid;
                    const balance = totalAmount - totalPayment;

                    // If there's a balance due, process full balance automatically
                    if (balance > 0) {
                        const paymentConfirmed = confirm(`The customer has settled the balance of ${formatCurrency(balance)}. Do you want to confirm this payment?`);

                        if (!paymentConfirmed) {
                            // Reset button state
                            button.disabled = false;
                            button.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg> Check-in Guest';
                            return;
                        }

                        // Process the payment with the exact balance
                        const paymentResponse = await fetch(`/bookings/${bookingId}/process-payment`, {
                            method: 'POST',
                            headers: headers,
                            body: JSON.stringify({
                                amount: balance,
                                payment_type: 'checkin',
                                notes: 'Payment upon check-in'
                            })
                        });

                        if (!paymentResponse.ok) {
                            const errorData = await paymentResponse.json();
                            throw new Error(errorData.message || 'Payment processing failed');
                        }

                        const paymentResult = await paymentResponse.json();
                        showToast('success', `Payment of ${formatCurrency(balance)} processed successfully`);
                    }

                    // Now proceed with check-in
                    const checkinResponse = await fetch(`/bookings/${bookingId}/checkin`, {
                        method: 'POST',
                        headers: headers
                    });

                    if (!checkinResponse.ok) {
                        const errorData = await checkinResponse.json();
                        throw new Error(errorData.message || 'Check-in failed');
                    }

                    const checkinResult = await checkinResponse.json();

                    // Reset button state
                    button.disabled = false;
                    button.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg> Check-in Guest';

                    showToast('success', `Booking ${bookingId} checked in successfully!`);

                    // Reload the booking summary to update the status
                    loadBookingSummary(bookingId);
                    loadBookings(currentStatus, currentPage);

                } catch (error) {
                    console.error('Check-in error:', error);
                    // Reset button state
                    button.disabled = false;
                    button.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg> Check-in Guest';

                    showToast('error', error.message || 'Failed to check in booking');
                }
            }


            async function checkoutBooking(bookingId, button) {

                window.location.href = `/check-out/receipt/${bookingId}`;
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
                toast.className = `fixed top-4 right-4 z-50 px-4 py-2 rounded-lg shadow-lg text-white ${type === 'success' ? 'bg-green-600' :
                    type === 'error' ? 'bg-red-600' :
                        type === 'info' ? 'bg-blue-600' : 'bg-gray-600'
                    }`;
                toast.innerHTML = `
                                                                <div class="flex items-center">
                                                                    <svg class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="${type === 'success' ? 'M5 13l4 4L19 7' :
                        type === 'error' ? 'M6 18L18 6M6 6l12 12' :
                            'M13 16h-1v-4h-1m1-4h.01M21 a9 9 0 11-18 0 9 9 0 0118 0z'
                    }" />
                                                                    </svg>
                                                                    <span class="text-lg">${message}</span>
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

            window.Echo.channel('bookings')
                .listen('.booking.created', (e) => {
                    console.log('New booking received:', e);
                    console.log('Booking status:', e.booking.status);
                    addNewBooking(e.booking);
                });

            function addNewBooking(booking) {
                const tableBody = document.getElementById('bookings-table-body');

                // Check if we're showing the "no bookings" message
                if (tableBody.innerHTML.includes('No bookings found')) {
                    tableBody.innerHTML = ''; // Clear the no bookings message
                }

                // Check if booking already exists in the table
                const existingRow = document.querySelector(`.booking-row[data-booking-id="${booking.id}"]`);
                if (existingRow) {
                    return; // Don't add duplicate
                }

                // Create the new booking row
                const statusInfo = STATUS_CONFIG[booking.status] || { class: 'bg-yellow-600', text: booking.status.toUpperCase() };

                const displayStatus = booking.status;
                const isPending = displayStatus === 'pending_confirmation';

                const newRow = document.createElement('tr');
                newRow.className = `booking-row ${isPending ? 'pending' : ''} fade-in cursor-pointer`;
                newRow.dataset.bookingId = booking.id;
                newRow.innerHTML = `
                    <td class="px-3 py-2">
                        <div class="text-sm text-gray-900 font-medium">${booking.id}</div>
                    </td>
                    <td class="px-3 py-2">
                        <span class="px-2 inline-flex text-sm leading-5 font-semibold rounded-full ${statusInfo.class} text-white status-badge">
                            ${statusInfo.text}
                        </span>
                    </td>
                    <td class="px-3 py-2">
                        <div class="text-lg text-gray-900">${booking.user?.firstname || 'Guest'} ${booking.user?.lastname || ''}</div>
                        <div class="text-sm text-gray-500">${booking.user?.phone || 'No phone'}</div>
                    </td>
                    <td class="px-3 py-2">
                        <div class="text-sm text-gray-900">${formatDate(booking.created_at)}</div>
                        <div class="text-sm text-gray-500">Just now</div>
                    </td>
                    <td class="px-3 py-2">
                        <div class="flex flex-wrap gap-1 justify-start">
                            <button class="action-btn-details btn-details" data-booking-id="${booking.id}" data-action="details">
                                Details
                            </button>
                        </div>
                    </td>
                `;

                // Add to the top of the table
                tableBody.insertBefore(newRow, tableBody.firstChild);

                // Add event listeners to the new row
                newRow.addEventListener('click', function (e) {
                    if (e.target.tagName === 'BUTTON') return;
                    loadBookingSummary(booking.id);
                    highlightBookingRow(booking.id);
                });

                // Add event listener to the details button
                newRow.querySelector('.action-btn-details').addEventListener('click', function () {
                    loadBookingSummary(booking.id);
                    highlightBookingRow(booking.id);
                });

                // Show a notification
                showToast('info', `New booking received: ${booking.user?.firstname} ${booking.user?.lastname}`);

                // Update pagination info if needed
                updatePaginationAfterNewBooking();
            }

            function updatePaginationAfterNewBooking() {
                // If we're on the first page, we might need to adjust pagination
                if (currentPage === 1) {
                    // We might need to remove the last row if we've exceeded perPage
                    const rows = document.querySelectorAll('.booking-row');
                    if (rows.length > perPage) {
                        rows[rows.length - 1].remove();
                    }

                    // Update the pagination info text
                    const paginationInfo = document.getElementById('pagination-info');
                    if (paginationInfo) {
                        const total = parseInt(paginationInfo.textContent.match(/of (\d+) entries/)[1]) + 1;
                        paginationInfo.textContent = paginationInfo.textContent.replace(
                            /of \d+ entries/,
                            `of ${total} entries`
                        );
                    }
                }
            }
        });
    </script>
@endsection