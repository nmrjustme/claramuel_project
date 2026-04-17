How to make the input in balance in notification not editable? Provide the full code, like my code:

@extends('layouts.admin')

@section('title', 'Bookings')

@php
$active = 'bookings';
@endphp

@section('content_css')
<style>
      /* Existing styles remain unchanged */
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

      .sticky-container {
            position: sticky;
            top: 1.5rem;
            max-height: calc(100vh - 2rem);
            overflow-y: auto;
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
            background-color: #fef2f2 !important;
      }

      .booking-row.selected {
            background-color: #fee2e2 !important;
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
            align-items: flex-start;
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

            .lg\:w-3\/4,
            .lg\:w-1\/4 {
                  width: 100%;
            }

            .flex-col.lg\:flex-row {
                  flex-direction: column;
            }

            .sticky-container {
                  position: relative;
                  top: 0;
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
</style>
@endsection

@section('content')



<!-- QR Scanner Modal -->
<div id="qr-scanner-modal" class="qr-modal">
      <div class="qr-modal-content">
            <button id="qr-modal-close" class="qr-modal-close">&times;</button>
            <h1 class="text-white text-2xl font-bold mb-4">Scan Guest QR Code</h1>

            <div class="flex-1 flex flex-col items-center justify-center">
                  <video id="qrVideo" width="100%" class="max-w-md mb-4 border-4 border-white rounded-lg"></video>
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

<div class="container mx-auto px-6 py-8">
      <div class="flex flex-col lg:flex-row gap-4">
            <!-- Main Content - Fixed width and overflow -->
            <div class="lg:w-2/3 w-full">

                  <div class="glass-card bg-white p-4 hover-scale rounded-lg shadow-sm">
                        <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-4 gap-3">
                              <div>
                                    <h1 class="text-xl font-bold text-gray-800">Bookings Management</h1>
                                    <p class="text-gray-600 text-sm mt-1">Manage all guest reservations</p>
                              </div>

                              <div class="flex items-center gap-2">
                                    <!-- QR Scanner Button (before search) -->
                                    <button id="qr-scanner-btn"
                                          class="flex items-center px-3 py-2 text-sm font-medium text-white bg-red-500 hover:bg-red-600 rounded-lg shadow">
                                          <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" stroke-width="2"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                      d="M4 4h4v4H4V4zm0 12h4v4H4v-4zm12-12h4v4h-4V4zm0 12h4v4h-4v-4zM9 7h6v2H9V7zm0 4h6v2H9v-2z" />
                                          </svg>
                                          Scan QR
                                    </button>

                                    <!-- Search Bar -->
                                    <div class="relative w-full md:w-56">
                                          <div
                                                class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                <svg class="h-4 w-4 text-gray-400" fill="currentColor"
                                                      viewBox="0 0 20 20">
                                                      <path fill-rule="evenodd"
                                                            d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z"
                                                            clip-rule="evenodd"></path>
                                                </svg>
                                          </div>
                                          <input id="search-input" type="text"
                                                class="block w-full pl-9 pr-10 py-2 text-sm border border-darkgray rounded-lg leading-5 bg-white/50 placeholder-gray-500 focus:outline-none focus:ring-1 focus:ring-red-500 focus:border-red-500"
                                                placeholder="Search by name...">
                                    </div>
                              </div>
                        </div>


                        <hr class="border-gray-300 my-3">

                        <!-- Booking Table - Fixed container -->
                        <div class="table-container">
                              <div class="overflow-x-auto custom-scroll">
                                    <table class="min-w-full divide-y divide-gray-200 compact-table">

                                          <thead class="bg-gray-50">
                                                <!-- In the table header -->
                                                <tr>
                                                      <th
                                                            class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                            ID
                                                      </th>
                                                      <th
                                                            class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                            Status
                                                      </th>
                                                      <th
                                                            class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                            Guest
                                                      </th>
                                                      <th
                                                            class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                            Check-in
                                                      </th>
                                                      <th
                                                            class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                            Actions
                                                      </th>
                                                </tr>
                                          </thead>
                                          <tbody class="bg-white divide-y divide-gray-200" id="bookings-table-body">
                                                <!-- Loading state -->
                                                <tr>
                                                      <td colspan="5" class="px-6 py-6 text-center">
                                                            <div class="flex justify-center">
                                                                  <div
                                                                        class="animate-spin rounded-full h-6 w-6 border-b-2 border-red-600">
                                                                  </div>
                                                            </div>
                                                            <p class="mt-2 text-sm text-gray-500">Loading bookings...
                                                            </p>
                                                      </td>
                                                </tr>
                                          </tbody>
                                    </table>
                              </div>
                        </div>

                        <!-- Pagination -->
                        <div class="flex flex-col sm:flex-row justify-between items-center mt-4 gap-3">
                              <div id="pagination-info" class="text-xs text-gray-600"></div>
                              <div class="flex gap-1">
                                    <button id="prev-page"
                                          class="px-3 py-1.5 bg-gray-100 text-gray-700 rounded-md text-sm font-medium hover:bg-gray-200 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                                          disabled>
                                          <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20"
                                                fill="currentColor">
                                                <path fill-rule="evenodd"
                                                      d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z"
                                                      clip-rule="evenodd" />
                                          </svg>
                                    </button>
                                    <button id="next-page"
                                          class="px-3 py-1.5 bg-gray-100 text-gray-700 rounded-md text-sm font-medium hover:bg-gray-200 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
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

            <!-- Booking Summary Sidebar -->
            <div class="lg:w-1/3 w-full">
                  <!-- Sticky Wrapper -->
                  <div class="sticky-container space-y-4">
                        <!-- Next Check-in Section -->
                        <div class="glass-card bg-white p-4 hover-scale rounded-lg shadow-sm w-full">
                              <div class="flex items-center justify-between mb-4">
                                    <h3 class="text-lg font-semibold text-gray-800">Next Check-in</h3>
                                    <a href="{{ route('incoming.list') }}">
                                          <div
                                                class="bg-red-100 text-red-800 text-xs font-medium px-2 py-0.5 rounded-full animate-pulse flex items-center cursor-pointer">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1" fill="none"
                                                      viewBox="0 0 24 24" stroke="currentColor">
                                                      <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18" />
                                                </svg>
                                                Upcoming
                                          </div>
                                    </a>
                              </div>
                              <p class="text-gray-600 text-sm mb-3" id="next-checkin-time">Loading...</p>
                              <div class="summary-card p-3 rounded-lg border border-red-100">
                                    <div class="flex items-center mb-1.5">
                                          <svg xmlns="http://www.w3.org/2000/svg" class="summary-icon text-red-500"
                                                viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd"
                                                      d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z"
                                                      clip-rule="evenodd" />
                                          </svg>
                                          <p class="text-sm font-medium text-gray-800" id="next-checkin-date">-</p>
                                    </div>
                                    <div class="flex items-center mb-1.5">
                                          <svg xmlns="http://www.w3.org/2000/svg" class="summary-icon text-red-500"
                                                viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd"
                                                      d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293 707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z"
                                                      clip-rule="evenodd" />
                                          </svg>
                                          <p class="text-xs text-gray-600" id="next-checkin-nights">-</p>
                                    </div>
                                    <div class="flex items-center mb-1.5">
                                          <svg xmlns="http://www.w3.org/2000/svg" class="summary-icon text-red-500"
                                                viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd"
                                                      d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z"
                                                      clip-rule="evenodd" />
                                          </svg>
                                          <p class="text-sm font-medium text-gray-800" id="next-checkin-guest">-</p>
                                    </div>
                                    <div class="flex items-center">
                                          <svg xmlns="http://www.w3.org/2000/svg" class="summary-icon text-red-500"
                                                viewBox="0 0 20 20" fill="currentColor">
                                                <path
                                                      d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z" />
                                          </svg>
                                          <p class="text-xs text-gray-600" id="next-checkin-phone">-</p>
                                    </div>
                              </div>
                        </div>

                        <!-- Booking Summary -->
                        <div class="glass-card bg-white overflow-hidden hover-scale rounded-lg shadow-sm w-full">
                              <div class="bg-gradient-to-r from-red-600 to-red-800 p-3 text-white">
                                    <h2 class="text-lg font-bold flex items-center">
                                          <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2"
                                                viewBox="0 0 20 20" fill="currentColor">
                                                <path
                                                      d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z" />
                                          </svg>
                                          Booking Details
                                    </h2>
                              </div>
                              <div class="p-0 fade-in w-full" id="booking-summary">
                                    <div class="text-center py-6 px-3 text-gray-400">
                                          <svg xmlns="http://www.w3.org/2000/svg"
                                                class="h-10 w-10 mx-auto mb-3 text-gray-300" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                                                      d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                          </svg>
                                          <p class="text-sm">Select a booking to view details</p>
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

@endsection

@section('content_js')
<!-- CSRF token -->
<meta name="csrf-token" content="{{ csrf_token() }}">

<script src="https://cdn.jsdelivr.net/npm/jsqr@1.4.0/dist/jsQR.min.js"></script>
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
        let qrStream = null;
        let isProcessing = false;
        let qrScanning = false;

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
        document.getElementById('resultModal').addEventListener('click', function(e) {
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
                        video.play();
                        requestAnimationFrame(scanQR);
                    })
                    .catch(function (err) {
                        console.error("Camera access error:", err);
                        resultContainer.innerHTML = "Could not access camera. Please grant permission.";
                        qrScanning = false;
                    });
            } else {
                resultContainer.innerHTML = "Camera not supported in this browser.";
                qrScanning = false;
            }
        }

        function stopQRScanner() {
            qrScanning = false;
            if (qrStream) {
                qrStream.getTracks().forEach(track => track.stop());
                qrStream = null;
            }
            video.srcObject = null;
            resultContainer.innerHTML = "";
            welcomeMessage.classList.add('hidden');
        }

        function scanQR() {
            if (!qrScanning || isProcessing) return;

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
                    requestAnimationFrame(scanQR);
                }
            } else {
                requestAnimationFrame(scanQR);
            }
        }
        
        async function processQRCode(qrData) {
            try {
                console.log("📦 QR Data:", qrData);
                
                // Validate input
                if (!qrData || typeof qrData !== 'string') {
                    throw new Error("Invalid QR code data");
                }
        
                resultContainer.innerHTML = "<div class='spinner'></div> Verifying...";
                
                // Create the request payload
                const payload = {
                    qr_data: qrData
                };
        
                const response = await fetch('/verify-qr-codes/checkin', {
                    method: 'POST',
                    headers: headers,
                    body: JSON.stringify(payload)
                });
                
                if (response.status === 409) {
                    const conflictData = await response.json();
                    // Optional: Show a message first before redirecting
                    resultContainer.innerHTML = `⚠️ ${conflictData.message || "QR Code already used."}`;
                    
                    // Redirect to a "conflict" page or show QR image if needed
                    setTimeout(() => {
                        window.location.href = `/check-in/used?path=${encodeURIComponent(conflictData.qr_path)}`;
                    }, 2000);
                    return;
                }

                // Handle response
                if (!response.ok) {
                    const errorData = await response.json().catch(() => null);
                    throw new Error(errorData?.message || `Server error: ${response.status}`);
                }
        
                const result = await response.json();
                
                if (!result.success) {
                    throw new Error(result.message || "Verification failed");
                }
                
                // Success case
                showWelcomeMessage(result.payment_id);
                video.srcObject.getTracks().forEach(track => track.stop());
                
                setTimeout(() => {
                    window.location.href = `/check-in/success/${result.payment_id}`;
                }, 3000);
        
            } catch (error) {
                console.error("Verification error:", error);
                resultContainer.innerHTML = `❌ ${error.message || "An error occurred"}`;
                isProcessing = false;
                setTimeout(() => {
                    if (qrScanning) {
                        requestAnimationFrame(scanQR);
                    }
                }, 2000);
            }
        }

        function showWelcomeMessage(paymentId) {
            fetch(`/qrScanner/customer-details/${paymentId}`, {
                method: 'GET',
                headers: headers
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const customer = data.customer;
                    welcomeTitle.textContent = `Welcome, ${customer.name}!`;                
                    resultContainer.classList.add('hidden');
                    welcomeMessage.classList.remove('hidden');
                } else {
                    welcomeTitle.textContent = "Welcome!";
                    customerDetails.textContent = "Successfully checked in!";
                    resultContainer.classList.add('hidden');
                    welcomeMessage.classList.remove('hidden');
                }
            })
            .catch(error => {
                console.error("Error fetching customer details:", error);
                welcomeTitle.textContent = "Welcome!";
                customerDetails.textContent = "Successfully checked in!";
                resultContainer.classList.add('hidden');
                welcomeMessage.classList.remove('hidden');
            });
        }

        // QR Modal Event Listeners
        qrOpenBtn.addEventListener('click', openQRScanner);
        qrCloseBtn.addEventListener('click', closeQRScanner);
        qrCancelBtn.addEventListener('click', closeQRScanner);

        // Current status filter and pagination
        let currentStatus = 'fully_paid';
        let currentPage = 1;
        let totalPages = 1;
        const perPage = 18; // Increased for more compact view
        let searchQuery = '';
        let currentBookingId = null;
        
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
        function loadBookings(status, page = 1) {
            const url = new URL(`/get/mybooking`, window.location.origin);
            url.searchParams.append('status', status);
            url.searchParams.append('page', page);
            url.searchParams.append('per_page', perPage);
            if (searchQuery) {
                url.searchParams.append('search', searchQuery);
            }
            
            // Show loading state
            document.getElementById('bookings-table-body').innerHTML = `
                <tr>
                    <td colspan="5" class="px-6 py-6 text-center">
                        <div class="flex justify-center">
                            <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-red-600"></div>
                        </div>
                        <p class="mt-2 text-sm text-gray-500">Loading bookings...</p>
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
                                    <path stroke-linecap="round" stroke-linejoin-round" stroke-width="1" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <p class="mt-2 text-sm text-gray-500">No bookings found</p>
                            </td>
                        </tr>
                    `;
                } else {
                    bookings.forEach(booking => {
                        const detail = booking.details?.[0];
                        // Use the status directly from the API response
                        const displayStatus = booking.status;
                        const statusInfo = STATUS_CONFIG[displayStatus] || {class: 'bg-yellow-600', text: displayStatus.toUpperCase()};
                        
                        html += `
                            <tr class="booking-row" data-booking-id="${booking.id}">
                                <td class="px-3 py-2">
                                    <div class="text-xs text-gray-900 font-medium">${booking.id}</div>
                                </td>
                                <td class="px-3 py-2">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full ${statusInfo.class} text-white status-badge">
                                        ${statusInfo.text}
                                    </span>
                                </td>
                                <td class="px-3 py-2">
                                    <div class="text-sm text-gray-900">${booking.user?.firstname || 'Guest'} ${booking.user?.lastname || ''}</div>
                                    <div class="text-xs text-gray-500">${booking.user?.phone || 'No phone'}</div>
                                </td>
                                <td class="px-3 py-2">
                                    <div class="text-xs text-gray-900">${detail ? formatDate(detail.checkin_date) : 'N/A'}</div>
                                    <div class="text-xs text-gray-500">${detail ? getNights(detail.checkin_date, detail.checkout_date) + ' nights' : 'N/A'}</div>
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
                    btn.addEventListener('click', function(e) {
                        const bookingId = this.dataset.bookingId;
                        const action = this.dataset.action;
                        
                        // Handle different actions
                        switch(action) {
                            case 'details':
                                loadBookingSummary(bookingId);
                                highlightBookingRow(bookingId);
                                break;
                        }
                    });
                });

                // Add click handlers for table rows
                document.querySelectorAll('.booking-row').forEach(row => {
                    row.addEventListener('click', function(e) {
                        // Don't trigger if clicking on a button
                        if (e.target.tagName === 'BUTTON') return;
                        
                        const bookingId = this.dataset.bookingId;
                        loadBookingSummary(bookingId);
                        highlightBookingRow(bookingId);
                    });
                });
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
                                        <p class="text-sm text-red-700">
                                            Failed to load bookings. Please try again later.
                                        </p>
                                    </div>
                                </div>
                                <button onclick="loadBookings(currentStatus, currentPage)" class="mt-2 px-3 py-1 bg-red-600 text-white rounded text-xs hover:bg-red-700">
                                    Retry
                                </button>
                            </div>
                        </td>
                    </tr>
                `;
            });
        }
        
        // Function to highlight the selected booking row
        function highlightBookingRow(bookingId) {
            document.querySelectorAll('.booking-row').forEach(row => {
                if (row.dataset.bookingId === bookingId) {
                    row.classList.add('selected');
                } else {
                    row.classList.remove('selected');
                }
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
                    // Now displays "1.7 days from now" instead of the long decimal
                    const daysUntil = data.days_until;
                    let displayText;
                    
                    if (daysUntil < 1) {
                        const hours = Math.round(daysUntil * 24);
                        displayText = `${hours} hour${hours !== 1 ? 's' : ''} from now`;
                    } else {
                        displayText = `${daysUntil} day${daysUntil !== 1 ? 's' : ''} from now`;
                    }
                    
                    document.getElementById('next-checkin-time').textContent = displayText;
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
            console.log(`Loading booking summary for ID: ${bookingId}`);
            currentBookingId = bookingId;
            
            try {
                // Show loading state
                document.getElementById('booking-summary').innerHTML = `
                    <div class="text-center py-6 px-3">
                        <div class="flex justify-center">
                            <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-red-600"></div>
                        </div>
                        <p class="mt-2 text-sm text-gray-500">Loading booking details...</p>
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
                
                // Use booking status directly (removed payment-based status logic)
                const bookingStatus = booking.status;
                const statusInfo = STATUS_CONFIG[bookingStatus] || {class: 'bg-yellow-600', text: bookingStatus.toUpperCase()};
                        
                console.log('Booking data:', booking);
                
                // PAYMENT COMPARISON LOGIC STARTS HERE
                const advancePaid = parseFloat(booking.payments?.[0]?.amount) || 0;

                const totalAmount = booking.details?.reduce((sum, detail) => {
                    return sum + parseFloat(detail.total_price || 0);
                }, 0) || 0;
                
                const checkinPaid = parseFloat(booking.payments?.[0]?.checkin_paid) || 0;
                
                // Total amount customer has paid so far
                const totalPayment = advancePaid + checkinPaid;
                
                // Calculate remaining balance (positive = owed, negative = overpaid)
                const balance = totalAmount - totalPayment;
                
                // Calculate payment completion percentage
                const paidPercentage = totalAmount > 0 ? (totalPayment / totalAmount) * 100 : 0;
                // PAYMENT COMPARISON LOGIC ENDS HERE
                
                // Generate room list HTML
                  const roomListHtml = booking.summaries?.length 
                        ? booking.summaries.map(summary => {
                              const room = summary.facility;
                              return room ? `
                              <li class="flex justify-between py-1.5 border-b border-gray-100 last:border-0">
                                    <span class="text-xs text-gray-700">${room.name}</span>
                                    <span class="text-xs font-medium text-gray-800">${formatCurrency(room.price)}</span>
                              </li>` : '';
                        }).join('')
                        : '<li class="text-xs text-gray-600 py-1.5">No room info available</li>';
                
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
                                                      <td class="p-0.5 border-b border-gray-200 text-xs">${type}</td>
                                                      <td class="p-0.5 border-b border-gray-200 text-xs text-right">${quantity} guest${quantity !== 1 ? 's' : ''}</td>
                                                      </tr>
                                                `).join('')}
                                                <tr class="font-semibold">
                                                      <td class="p-0.5 text-xs">Total Guests</td>
                                                      <td class="p-0.5 text-xs text-right">${Object.values(guestTypeCounts).reduce((a, b) => a + b, 0)}</td>
                                                </tr>
                                          </tbody>
                                          </table>
                                    `
                                    : '<p class="text-xs text-gray-500 italic mt-1">No guest details recorded</p>';
                              
                              return `
                                    <div class="mb-3 p-2 bg-gray-50 rounded">
                                          <h4 class="text-xs font-semibold text-gray-800">${room.name}</h4>
                                          ${guestItems}
                                    </div>
                              `;
                              }).join('')
                              : '<p class="text-xs text-gray-500 italic">No room information available</p>';
                    
                    // Generate action buttons HTML based on booking status
                    let actionButtonsHtml = '';
                    
                    // Get button states based on booking status
                    const buttonStates = getButtonStates(bookingStatus);
                    
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
                                '<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>'}
                            Check-in Guest
                        </button>
                        <button class="sidebar-btn bg-purple-600 text-white hover:bg-purple-700 ${buttonStates.checkout.disabled ? 'opacity-60 cursor-not-allowed' : ''}" 
                            data-action="checkout" data-booking-id="${bookingId}" ${buttonStates.checkout.disabled ? 'disabled' : ''}>
                            ${buttonStates.checkout.loading ? '<div class="btn-preloader"></div>' : 
                                '<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7" /></svg>'}
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
                                    <h3 class="text-sm font-bold text-gray-900">${booking.code || 'N/A'}</h3>
                                    <p class="text-xs text-gray-500 mt-0.5">Record ID: ${booking.id || 'N/A'}</p>
                                </div>
                                <span class="px-2 py-0.5 rounded-full text-xs font-semibold ${statusInfo.class} text-white status-badge">
                                    ${statusInfo.text}
                                </span>
                            </div>
                            
                            <!-- Payment Progress Bar -->
                            <div class="mb-3">
                                <div class="flex justify-between text-2xl sm:text-xs mb-1">
                                    <span class="text-gray-600">Payment Progress</span>
                                    <span class="font-medium">${Math.round(paidPercentage)}%</span>
                                </div>
                                <div class="progress-bar">
                                    <div class="progress-fill" style="width: ${paidPercentage}%"></div>
                                </div>
                            </div>
                            
                            <div class="mb-2">
                                <h4 class="text-xs font-semibold text-gray-800 flex items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 mr-1.5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                                    </svg>
                                    Guest Information
                                </h4>
                                <div class="mt-1 pl-5">
                                    <p class="text-xs font-medium text-gray-800">${booking.user?.firstname || 'Guest'} ${booking.user?.lastname || ''}</p>
                                    <p class="text-xs text-gray-600 mt-0.5">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 inline mr-1" viewBox="0 0 20 20" fill="currentColor">
                                            <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z" />
                                            <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z" />
                                        </svg>
                                        ${booking.user?.email || 'N/A'}
                                    </p>
                                    <p class="text-xs text-gray-600">
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
                            <h4 class="text-xs font-semibold text-gray-800 flex items-center mb-1">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 mr-1.5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd" />
                                </svg>
                                Stay Details
                            </h4>
                            <div class="mt-1 pl-5 space-y-1">
                                <div class="flex justify-between">
                                    <span class="text-xs text-gray-600">Check-in:</span>
                                    <span class="text-xs font-medium text-gray-800">
                                        ${detail ? formatDate(detail.checkin_date) : 'N/A'}
                                    </span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-xs text-gray-600">Check-out:</span>
                                    <span class="text-xs font-medium text-gray-800">
                                        ${detail ? formatDate(detail.checkout_date) : 'N/A'}
                                    </span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-xs text-gray-600">Nights:</span>
                                    <span class="text-xs font-medium text-gray-800">
                                        ${detail ? getNights(detail.checkin_date, detail.checkout_date) : 'N/A'}
                                    </span>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Guest Composition -->
                        <div class="px-3 py-3 bg-white">
                            <h4 class="text-xs font-semibold text-gray-800 flex items-center mb-1">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 mr-1.5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                                </svg>
                                Guest Composition
                            </h4>
                            <div class="mt-1 pl-5">
                                ${guestCompositionHtml}
                            </div>
                        </div>
                        
                        <!-- Rooms Booked -->
                        <div class="px-3 py-3 bg-white">
                            <h4 class="text-xs font-semibold text-gray-800 flex items-center mb-1">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 mr-1.5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z" />
                                </svg>
                                Rooms Booked
                            </h4>
                            <ul class="mt-1 pl-5">
                                ${roomListHtml}
                            </ul>
                        </div>
                    
                        <!-- Payment Summary -->
                        <div class="px-3 py-3 bg-white">
                            <h4 class="text-xs font-semibold text-gray-800 flex items-center mb-1">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 mr-1.5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path d="M8.433 7.418c.155-.103.346-.196.567-.267v1.698a2.305 2.305 0 01-.567-.267C8.07 8.34 8 8.114 8 8c0-.114.07-.34.433-.582zM11 12.849v-1.698c.22.071.412.164.567.267.364.243.433.468.433.582 0 .114-.07.34-.433.582a2.305 2.305 0 01-.567.267z" />
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-13a1 1 0 10-2 0v.092a4.535 4.535 0 00-1.676.662C6.602 6.234 6 7.009 6 8c0 .99.602 1.765 1.324 2.246.48.32 1.054.545 1.676.662v1.941c-.391-.127-.68-.317-.843-.504a1 1 0 10-1.51 1.31c.562.649 1.413 1.076 2.353 1.253V15a1 1 0 102 0v-.092a4.535 4.535 0 001.676-.662C13.398 13.766 14 12.991 14 12c0-.99-.602-1.765-1.324-2.246A4.535 4.535 0 0011 9.092V7.151c.391.127.68.317.843.504a1 1 0 101.511-1.31c-.563-.649-1.413-1.076-2.354-1.253V5z" clip-rule="evenodd" />
                                </svg>
                                Payment Summary
                            </h4>
                            <div class="mt-1 pl-5 space-y-1">
                                <div class="flex justify-between">
                                    <span class="text-xs text-gray-600">Total Amount:</span>
                                    <span class="text-xs font-medium text-gray-800">
                                        ${formatCurrency(totalAmount)}
                                    </span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-xs text-gray-600">Advance Paid:</span>
                                    <span class="text-xs font-medium text-green-600">
                                        ${formatCurrency(advancePaid)}
                                    </span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-xs text-gray-600">Check-in Paid:</span>
                                    <span class="text-xs font-medium text-green-600">
                                        ${formatCurrency(checkinPaid)}
                                    </span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-xs text-gray-600">Total Paid:</span>
                                    <span class="text-xs font-medium text-green-600">
                                        ${formatCurrency(totalPayment)}
                                    </span>
                                </div>
                                <div class="flex justify-between pt-1 border-t border-gray-100">
                                    <span class="text-6xl sm:text-sm font-semibold ${balance > 0 ? 'text-red-600' : 'text-green-600'}">Balance:</span>
                                    <span class="text-6xl sm:text-sm font-semibold ${balance > 0 ? 'text-red-600' : 'text-green-600'}">
                                        ${formatCurrency(Math.abs(balance))}
                                        ${balance > 0 ? '(Due upon check-in)' : '(PAID)'}
                                    </span>
                                </div>
                                ${balance > 0 ? `
                                <div class="mt-2 p-2 bg-yellow-50 border border-yellow-200 rounded">
                                    <p class="text-xs text-yellow-800">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 inline mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                        </svg>
                                        Customer must pay balance upon check-in
                                    </p>
                                </div>
                                ` : ''}
                            </div>
                        </div>

                        
                        <!-- Action Buttons -->
                        ${actionButtonsHtml ? `
                        <div class="px-3 py-3 bg-white sidebar-actions">
                            ${actionButtonsHtml}
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
                                        <svg class="h-4 w-4 text-red-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 极 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                    <div class="ml-2">
                                        <p class="text-xs text-red-700">
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

        // Helper function to determine button states based on booking status
        
        function getButtonStates(status) {
            const states = {
                confirm: { disabled: false, loading: false },
                checkin: { disabled: false, loading: false },
                checkout: { disabled: false, loading: false }
            };
            
            switch(status) {
                case 'pending_confirmation':
                    states.confirm.disabled = false;
                    states.checkin.disabled = true;
                    states.checkout.disabled = true;
                    break;
                case 'confirmed':
                    states.confirm.disabled = true;
                    states.checkin.disabled = false;
                    states.checkout.disabled = false;
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

        document.addEventListener('click', function(e) {
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
                closeConfirmationModal();
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
                const bookingResponse = await fetch(`/get/show/bookings/${bookingId}`, {
                    method: 'GET',
                    headers: headers
                });
                
                if (!bookingResponse.ok) {
                    throw new Error('Failed to fetch booking details');
                }
                
                const bookingData = await bookingResponse.json();
                const booking = bookingData.data;
                
                // Calculate balance (using the same logic as in loadBookingSummary)
                const advancePaid = parseFloat(booking.payments?.[0]?.amount_paid) || 0;
                const totalAmount = booking.details?.reduce((sum, detail) => {
                    return sum + parseFloat(detail.total_price || 0);
                }, 0) || 0;
                const checkinPaid = parseFloat(booking.payments?.[0]?.checkin_paid) || 0;
                const totalPayment = advancePaid + checkinPaid;
                const balance = totalAmount - totalPayment;
                
                // If there's a balance due, prompt for payment
                if (balance > 0) {
                    const paymentConfirmed = confirm(`This booking has an outstanding balance of ${formatCurrency(balance)}. Do you want to process payment now?`);
                    
                    if (!paymentConfirmed) {
                        // Reset button state
                        button.disabled = false;
                        button.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg> Check-in Guest';
                        return;
                    }
                    
                    // Process payment
                    const paymentAmount = parseFloat(prompt(`Enter payment amount (Balance due: ${formatCurrency(balance)}):`, balance));
                    
                    if (isNaN(paymentAmount) || paymentAmount <= 0) {
                        throw new Error('Invalid payment amount');
                    }
                    
                    // Process the payment
                    const paymentResponse = await fetch(`/bookings/${bookingId}/process-payment`, {
                        method: 'POST',
                        headers: headers,
                        body: JSON.stringify({
                            amount: paymentAmount,
                            payment_type: 'checkin',
                            notes: 'Payment upon check-in'
                        })
                    });
                    
                    if (!paymentResponse.ok) {
                        const errorData = await paymentResponse.json();
                        throw new Error(errorData.message || 'Payment processing failed');
                    }
                    
                    const paymentResult = await paymentResponse.json();
                    showToast('success', `Payment of ${formatCurrency(paymentAmount)} processed successfully`);
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
            try {
                // Show loading state on button
                button.disabled = true;
                button.innerHTML = '<div class="btn-preloader"></div> Processing...';
                
                // Implement your checkout booking logic here
                // Example API call:
                // const response = await fetch(`/bookings/${bookingId}/checkout`, {
                //     method: 'POST',
                //     headers: headers
                // });
                // const result = await response.json();
                
                // Simulate API call delay
                await new Promise(resolve => setTimeout(resolve, 1500));
                
                // Reset button state
                button.disabled = false;
                button.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7" /></svg> Check-out Guest';
                
                showToast('success', `Booking ${bookingId} checked out successfully!`);
                
                // Reload the booking summary to update the status
                loadBookingSummary(bookingId);
                loadBookings(currentStatus, currentPage);
            } catch (error) {
                console.error('Check-out error:', error);
                // Reset button state
                button.disabled = false;
                button.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7" /></svg> Check-out Guest';
                
                showToast('error', error.message || 'Failed to check out booking');
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
            toast.className = `fixed top-4 right-4 z-50 px-4 py-2 rounded-lg shadow-lg text-white ${
            type === 'success' ? 'bg-green-600' : 
            type === 'error' ? 'bg-red-600' : 
            type === 'info' ? 'bg-blue-600' : 'bg-gray-600' 
            }`;
            toast.innerHTML = `
                <div class="flex items-center">
                    <svg class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="${
                            type === 'success' ? 'M5 13l4 4L19 7' : 
                            type === 'error' ? 'M6 18L18 6M6 6l12 12' : 
                            'M13 16h-1v-4h-1m1-4h.01M21 极 a9 9 0 11-18 0 9 9 0 0118 0z'
                        }" />
                    </svg>
                    <span class="text-sm">${message}</span>
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