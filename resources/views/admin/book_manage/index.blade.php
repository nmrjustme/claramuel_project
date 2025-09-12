<!DOCTYPE html>
<html lang="en">

<head>
      <meta charset="UTF-8">
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <title>Booking Management System</title>
      <script src="https://cdn.tailwindcss.com"></script>
      <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
      <style>
            .scanner-overlay {
                  position: relative;
                  width: 300px;
                  height: 300px;
                  margin: 0 auto;
                  overflow: hidden;
                  border-radius: 12px;
                  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            }

            .scanner-overlay:before {
                  content: '';
                  position: absolute;
                  width: 100%;
                  height: 4px;
                  background: linear-gradient(to right, transparent, #dc2626, transparent);
                  animation: scan 2s linear infinite;
                  z-index: 10;
            }

            @keyframes scan {
                  0% {
                        top: 0;
                  }

                  100% {
                        top: 100%;
                  }
            }

            .loader {
                  border-top-color: #dc2626;
                  animation: spin 1s ease-in-out infinite;
            }

            @keyframes spin {
                  0% {
                        transform: rotate(0deg);
                  }

                  100% {
                        transform: rotate(360deg);
                  }
            }

            .pulse {
                  animation: pulse 2s infinite;
            }

            @keyframes pulse {
                  0% {
                        box-shadow: 0 0 0 0 rgba(220, 38, 38, 0.4);
                  }

                  70% {
                        box-shadow: 0 0 0 10px rgba(220, 38, 38, 0);
                  }

                  100% {
                        box-shadow: 0 0 0 0 rgba(220, 38, 38, 0);
                  }
            }

            .status-badge {
                  transition: all 0.3s ease;
            }

            .status-badge:hover {
                  transform: translateY(-2px);
                  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            }

            .booking-card {
                  transition: all 0.3s ease;
                  border-left: 4px solid #dc2626;
            }

            .booking-card:hover {
                  transform: translateY(-2px);
                  box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);
            }
      </style>
</head>

<body class="bg-gray-100 min-h-screen">
      <div class="container mx-auto px-4 py-8">
            <!-- Header -->
            <header class="flex flex-col md:flex-row justify-between items-center mb-8">
                  <h1 class="text-3xl font-bold text-red-700 flex items-center">
                        <i class="fas fa-calendar-check mr-3"></i>Booking Management System
                  </h1>
                  <div class="flex items-center mt-4 md:mt-0">
                        <div class="bg-white rounded-full p-2 shadow-md mr-4">
                              <i class="fas fa-bell text-red-600 text-xl"></i>
                              <span
                                    class="absolute -mt-3 -mr-1 bg-red-600 text-white rounded-full text-xs px-1.5 py-0.5">3</span>
                        </div>
                        <div class="flex items-center bg-white rounded-full pl-1 pr-4 py-1 shadow-md">
                              <img src="https://i.pravatar.cc/40?img=12" alt="User" class="rounded-full h-10 w-10 mr-2">
                              <span class="text-gray-700 font-medium">Admin User</span>
                        </div>
                  </div>
            </header>

            <!-- Stats Section -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                  <div class="bg-white rounded-xl shadow-md p-6 flex items-center justify-between">
                        <div>
                              <p class="text-gray-500">Total Bookings</p>
                              <p class="text-3xl font-bold text-red-700">142</p>
                        </div>
                        <div class="bg-red-100 p-4 rounded-full">
                              <i class="fas fa-calendar text-red-600 text-2xl"></i>
                        </div>
                  </div>
                  <div class="bg-white rounded-xl shadow-md p-6 flex items-center justify-between">
                        <div>
                              <p class="text-gray-500">Pending</p>
                              <p class="text-3xl font-bold text-yellow-600">24</p>
                        </div>
                        <div class="bg-yellow-100 p-4 rounded-full">
                              <i class="fas fa-clock text-yellow-600 text-2xl"></i>
                        </div>
                  </div>
                  <div class="bg-white rounded-xl shadow-md p-6 flex items-center justify-between">
                        <div>
                              <p class="text-gray-500">Checked In</p>
                              <p class="text-3xl font-bold text-blue-600">68</p>
                        </div>
                        <div class="bg-blue-100 p-4 rounded-full">
                              <i class="fas fa-door-open text-blue-600 text-2xl"></i>
                        </div>
                  </div>
                  <div class="bg-white rounded-xl shadow-md p-6 flex items-center justify-between">
                        <div>
                              <p class="text-gray-500">Revenue</p>
                              <p class="text-3xl font-bold text-green-600">$9,842</p>
                        </div>
                        <div class="bg-green-100 p-4 rounded-full">
                              <i class="fas fa-dollar-sign text-green-600 text-2xl"></i>
                        </div>
                  </div>
            </div>

            <!-- Search Section -->
            <div class="bg-white rounded-xl shadow-md p-6 mb-8">
                  <div class="flex flex-col md:flex-row gap-4">
                        <!-- QR Scanner Button -->
                        <button id="qrScannerBtn"
                              class="bg-red-600 hover:bg-red-700 text-white py-3 px-6 rounded-xl flex items-center justify-center gap-2 transition-colors pulse">
                              <i class="fas fa-qrcode"></i> QR Scanner
                        </button>

                        <!-- Manual Search -->
                        <div class="flex-1 flex flex-col md:flex-row gap-2">
                              <input type="text" id="firstNameSearch" placeholder="First Name"
                                    class="flex-1 px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-transparent focus:outline-none">
                              <input type="text" id="lastNameSearch" placeholder="Last Name"
                                    class="flex-1 px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-transparent focus:outline-none">
                              <button id="searchBtn"
                                    class="bg-red-600 hover:bg-red-700 text-white py-3 px-6 rounded-xl transition-colors flex items-center gap-2">
                                    <i class="fas fa-search"></i> Search
                              </button>
                        </div>
                  </div>
            </div>

            <!-- Main Content -->
            <div class="flex flex-col lg:flex-row gap-8">
                  <!-- List Section -->
                  <div class="w-full lg:w-2/5 bg-white rounded-xl shadow-md p-6">
                        <div class="flex justify-between items-center mb-4">
                              <h2 class="text-xl font-semibold text-red-700">Bookings List</h2>
                              <div class="flex items-center gap-2">
                                    <span class="text-sm text-gray-500">Sort by:</span>
                                    <select
                                          class="border border-gray-300 rounded-lg px-2 py-1 text-sm focus:ring-2 focus:ring-red-500 focus:outline-none">
                                          <option>Newest</option>
                                          <option>Oldest</option>
                                          <option>Name</option>
                                          <option>Status</option>
                                    </select>
                              </div>
                        </div>
                        <div class="overflow-x-auto">
                              <table class="min-w-full">
                                    <thead>
                                          <tr class="bg-red-50">
                                                <th class="py-3 px-4 text-left text-red-700 font-medium">Record ID</th>
                                                <th class="py-3 px-4 text-left text-red-700 font-medium">Full Name</th>
                                                <th class="py-3 px-4 text-left text-red-700 font-medium">Status</th>
                                                <th class="py-3 px-4 text-left text-red-700 font-medium">Actions</th>
                                          </tr>
                                    </thead>
                                    <tbody id="bookingsList">
                                          <tr>
                                                <td colspan="4" class="py-8 text-center">
                                                      <div class="flex justify-center">
                                                            <div
                                                                  class="loader ease-linear rounded-full border-4 border-t-4 border-gray-200 h-12 w-12">
                                                            </div>
                                                      </div>
                                                      <p class="mt-2 text-gray-500">Loading bookings...</p>
                                                </td>
                                          </tr>
                                    </tbody>
                              </table>
                        </div>
                  </div>

                  <!-- Details Section -->
                  <div class="w-full lg:w-3/5 bg-white rounded-xl shadow-md p-6">
                        <h2 class="text-xl font-semibold mb-4 text-red-700">Booking Details</h2>
                        <div id="noSelectionMessage" class="text-center py-12 text-gray-500">
                              <div class="bg-red-50 rounded-full p-6 inline-flex items-center justify-center mb-4">
                                    <i class="fas fa-info-circle text-red-600 text-3xl"></i>
                              </div>
                              <p class="text-lg font-medium">Select a booking from the list to view details</p>
                              <p class="mt-2 text-sm">You can search by name or use the QR scanner</p>
                        </div>

                        <div id="bookingDetails" class="hidden">
                              <div class="mb-6">
                                    <h3 class="text-lg font-medium mb-3 text-red-700 border-b pb-2 flex items-center">
                                          <i class="fas fa-receipt mr-2"></i> Payment Summary
                                    </h3>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4" id="paymentSummary">
                                          <!-- Payment details will be populated via JavaScript -->
                                    </div>
                              </div>

                              <div class="mb-6">
                                    <h3 class="text-lg font-medium mb-3 text-red-700 border-b pb-2 flex items-center">
                                          <i class="fas fa-calendar-alt mr-2"></i> Booking Summary
                                    </h3>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4" id="bookingSummary">
                                          <!-- Booking details will be populated via JavaScript -->
                                    </div>
                              </div>

                              <div class="mt-8">
                                    <button id="actionButton"
                                          class="w-full bg-red-600 hover:bg-red-700 text-white py-3 px-6 rounded-xl transition-colors flex items-center justify-center gap-2 text-lg font-medium">
                                          <i class="fas fa-cog"></i> Perform Action
                                    </button>
                              </div>
                        </div>
                  </div>
            </div>
      </div>

      <!-- QR Scanner Modal -->
      <div id="qrScannerModal"
            class="fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center hidden z-50">
            <div class="bg-white rounded-xl p-8 w-11/12 md:w-1/2 lg:w-1/3">
                  <h3 class="text-xl font-semibold mb-4 text-red-700 flex items-center">
                        <i class="fas fa-qrcode mr-2"></i> QR Code Scanner
                  </h3>
                  <div class="scanner-overlay bg-gray-200 mb-4 flex items-center justify-center">
                        <div class="absolute inset-0 flex items-center justify-center">
                              <div class="border-2 border-red-500 border-dashed w-11/12 h-11/12"></div>
                        </div>
                        <p class="text-gray-500 z-10 bg-white px-4 py-2 rounded-lg shadow-md">Scanner preview</p>
                  </div>
                  <p class="text-center text-gray-600 mb-4">Position the QR code within the frame to scan</p>
                  <div class="flex justify-between">
                        <button id="closeScanner"
                              class="bg-gray-300 hover:bg-gray-400 text-gray-800 py-2 px-4 rounded-lg transition-colors flex items-center gap-2">
                              <i class="fas fa-times"></i> Cancel
                        </button>
                        <button id="simulateScan"
                              class="bg-red-600 hover:bg-red-700 text-white py-2 px-4 rounded-lg transition-colors flex items-center gap-2">
                              <i class="fas fa-camera"></i> Simulate Scan
                        </button>
                  </div>
            </div>
      </div>

      <!-- Toast Notification -->
      <div id="toast"
            class="fixed top-4 right-4 bg-red-600 text-white px-6 py-3 rounded-lg shadow-lg transform transition-transform duration-300 translate-y-[-100px]">
            <div class="flex items-center">
                  <i class="fas fa-check-circle mr-2"></i>
                  <span id="toastMessage">Operation completed successfully</span>
            </div>
      </div>

      <script>
            // Simulated database - in a real application, this would be your Laravel backend
        const simulatedDatabase = {
            bookings: [
                {
                    id: 1,
                    user_id: 101,
                    user: {
                        first_name: 'John',
                        last_name: 'Doe',
                        email: 'john.doe@example.com',
                        phone: '+1 (555) 123-4567'
                    },
                    confirmation_token: 'token_abc123',
                    verified_at: '2023-06-15T10:30:00',
                    reference: 'REF001',
                    status: 'confirmed',
                    code: 'CODE123',
                    confirmed_at: '2023-06-15T10:30:00',
                    payment_status: 'paid',
                    is_read: true,
                    checked_in_at: null,
                    checked_in_by: null,
                    checked_out_at: null,
                    checked_out_by: null,
                    qr_code_path: '/qrcodes/booking_001.png',
                    amount: 150.00,
                    facility: 'Conference Room A',
                    booking_date: '2023-06-15',
                    check_in_date: '2023-07-01',
                    check_out_date: '2023-07-05',
                    created_at: '2023-06-10T14:22:00',
                    updated_at: '2023-06-15T10:30:00'
                },
                {
                    id: 2,
                    user_id: 102,
                    user: {
                        first_name: 'Jane',
                        last_name: 'Smith',
                        email: 'jane.smith@example.com',
                        phone: '+1 (555) 987-6543'
                    },
                    confirmation_token: 'token_def456',
                    verified_at: null,
                    reference: 'REF002',
                    status: 'pending',
                    code: 'CODE456',
                    confirmed_at: null,
                    payment_status: 'pending',
                    is_read: false,
                    checked_in_at: null,
                    checked_in_by: null,
                    checked_out_at: null,
                    checked_out_by: null,
                    qr_code_path: '/qrcodes/booking_002.png',
                    amount: 200.00,
                    facility: 'Event Hall B',
                    booking_date: '2023-06-18',
                    check_in_date: '2023-07-10',
                    check_out_date: '2023-07-12',
                    created_at: '2023-06-12T09:15:00',
                    updated_at: '2023-06-12T09:15:00'
                },
                {
                    id: 3,
                    user_id: 103,
                    user: {
                        first_name: 'Robert',
                        last_name: 'Johnson',
                        email: 'robert.j@example.com',
                        phone: '+1 (555) 456-7890'
                    },
                    confirmation_token: 'token_ghi789',
                    verified_at: '2023-06-20T11:45:00',
                    reference: 'REF003',
                    status: 'checked_in',
                    code: 'CODE789',
                    confirmed_at: '2023-06-20T11:45:00',
                    payment_status: 'paid',
                    is_read: true,
                    checked_in_at: '2023-07-05T14:20:00',
                    checked_in_by: 'admin1',
                    checked_out_at: null,
                    checked_out_by: null,
                    qr_code_path: '/qrcodes/booking_003.png',
                    amount: 300.00,
                    facility: 'Meeting Room C',
                    booking_date: '2023-06-20',
                    check_in_date: '2023-07-05',
                    check_out_date: '2023-07-08',
                    created_at: '2023-06-15T16:40:00',
                    updated_at: '2023-07-05T14:20:00'
                },
                {
                    id: 4,
                    user_id: 104,
                    user: {
                        first_name: 'Sarah',
                        last_name: 'Williams',
                        email: 'sarah.w@example.com',
                        phone: '+1 (555) 234-5678'
                    },
                    confirmation_token: 'token_jkl012',
                    verified_at: '2023-06-22T08:20:00',
                    reference: 'REF004',
                    status: 'checked_out',
                    code: 'CODE012',
                    confirmed_at: '2023-06-22T08:20:00',
                    payment_status: 'paid',
                    is_read: true,
                    checked_in_at: '2023-07-03T10:15:00',
                    checked_in_by: 'admin2',
                    checked_out_at: '2023-07-06T12:30:00',
                    checked_out_by: 'admin1',
                    qr_code_path: '/qrcodes/booking_004.png',
                    amount: 450.00,
                    facility: 'Banquet Hall D',
                    booking_date: '2023-06-22',
                    check_in_date: '2023-07-03',
                    check_out_date: '2023-07-06',
                    created_at: '2023-06-18T13:25:00',
                    updated_at: '2023-07-06T12:30:00'
                }
            ]
        };

        // Simulate API delay
        const simulateDelay = () => new Promise(resolve => setTimeout(resolve, 800));

        // DOM elements
        const qrScannerBtn = document.getElementById('qrScannerBtn');
        const qrScannerModal = document.getElementById('qrScannerModal');
        const closeScanner = document.getElementById('closeScanner');
        const simulateScan = document.getElementById('simulateScan');
        const searchBtn = document.getElementById('searchBtn');
        const firstNameSearch = document.getElementById('firstNameSearch');
        const lastNameSearch = document.getElementById('lastNameSearch');
        const bookingsList = document.getElementById('bookingsList');
        const bookingDetails = document.getElementById('bookingDetails');
        const noSelectionMessage = document.getElementById('noSelectionMessage');
        const paymentSummary = document.getElementById('paymentSummary');
        const bookingSummary = document.getElementById('bookingSummary');
        const actionButton = document.getElementById('actionButton');
        const toast = document.getElementById('toast');
        const toastMessage = document.getElementById('toastMessage');

        // Current selected booking
        let selectedBooking = null;
        let allBookings = [];

        // Initialize the page
        document.addEventListener('DOMContentLoaded', function() {
            fetchBookings();
        });

        // Show toast notification
        function showToast(message, isError = false) {
            toastMessage.textContent = message;
            toast.className = `fixed top-4 right-4 ${isError ? 'bg-red-600' : 'bg-green-600'} text-white px-6 py-3 rounded-lg shadow-lg transform transition-transform duration-300 flex items-center`;
            toast.classList.remove('translate-y-[-100px]');
            toast.classList.add('translate-y-0');
            
            setTimeout(() => {
                toast.classList.remove('translate-y-0');
                toast.classList.add('translate-y-[-100px]');
            }, 3000);
        }

        // Fetch all bookings from the server
        async function fetchBookings() {
            showLoading();
            
            // Simulate API call
            await simulateDelay();
            
            allBookings = simulatedDatabase.bookings;
            renderBookingsList(allBookings);
        }

        // Search bookings by name
        async function searchBookings(firstName, lastName) {
            showLoading();
            
            // Simulate API call
            await simulateDelay();
            
            const filteredBookings = simulatedDatabase.bookings.filter(booking => {
                const matchesFirstName = booking.user.first_name.toLowerCase().includes(firstName.toLowerCase());
                const matchesLastName = booking.user.last_name.toLowerCase().includes(lastName.toLowerCase());
                
                if (firstName && lastName) {
                    return matchesFirstName && matchesLastName;
                } else if (firstName) {
                    return matchesFirstName;
                } else if (lastName) {
                    return matchesLastName;
                }
                
                return true;
            });
            
            renderBookingsList(filteredBookings);
        }

        // Show loading state
        function showLoading() {
            bookingsList.innerHTML = `
                <tr>
                    <td colspan="4" class="py-8 text-center">
                        <div class="flex justify-center">
                            <div class="loader ease-linear rounded-full border-4 border-t-4 border-gray-200 h-12 w-12"></div>
                        </div>
                        <p class="mt-2 text-gray-500">Loading bookings...</p>
                    </td>
                </tr>
            `;
        }

        // Render bookings list
        function renderBookingsList(bookingsData) {
            bookingsList.innerHTML = '';
            
            if (bookingsData.length === 0) {
                bookingsList.innerHTML = `
                    <tr>
                        <td colspan="4" class="py-4 px-4 text-center text-gray-500">
                            <i class="fas fa-search text-2xl mb-2"></i>
                            <p>No bookings found</p>
                        </td>
                    </tr>
                `;
                return;
            }
            
            bookingsData.forEach(booking => {
                const row = document.createElement('tr');
                row.className = 'border-b hover:bg-red-50 cursor-pointer booking-card';
                row.innerHTML = `
                    <td class="py-3 px-4 font-mono">#${booking.id}</td>
                    <td class="py-3 px-4">${booking.user.first_name} ${booking.user.last_name}</td>
                    <td class="py-3 px-4">
                        <span class="status-badge px-3 py-1 rounded-full text-xs ${getStatusColor(booking.status)}">
                            ${formatStatus(booking.status)}
                        </span>
                    </td>
                    <td class="py-3 px-4">
                        <button class="view-booking text-red-600 hover:text-red-800 transition-colors flex items-center gap-1" data-id="${booking.id}">
                            <i class="fas fa-eye"></i> View
                        </button>
                    </td>
                `;
                bookingsList.appendChild(row);
            });
            
            // Add event listeners to view buttons
            document.querySelectorAll('.view-booking').forEach(button => {
                button.addEventListener('click', function() {
                    const bookingId = parseInt(this.getAttribute('data-id'));
                    selectBooking(bookingId);
                });
            });
        }

        // Select a booking and show details
        function selectBooking(bookingId) {
            selectedBooking = allBookings.find(booking => booking.id === bookingId);
            
            if (selectedBooking) {
                noSelectionMessage.classList.add('hidden');
                bookingDetails.classList.remove('hidden');
                
                // Update payment summary
                paymentSummary.innerHTML = `
                    <div class="bg-red-50 p-4 rounded-xl">
                        <p class="font-medium text-gray-600">Amount</p>
                        <p class="text-lg font-semibold text-red-700">$${selectedBooking.amount.toFixed(2)}</p>
                    </div>
                    <div class="bg-red-50 p-4 rounded-xl">
                        <p class="font-medium text-gray-600">Payment Status</p>
                        <p class="text-lg font-semibold ${selectedBooking.payment_status === 'paid' ? 'text-green-600' : 'text-red-600'}">
                            ${formatStatus(selectedBooking.payment_status)}
                        </p>
                    </div>
                    <div class="bg-red-50 p-4 rounded-xl">
                        <p class="font-medium text-gray-600">Reference</p>
                        <p class="text-lg font-semibold text-gray-800">${selectedBooking.reference || 'N/A'}</p>
                    </div>
                    <div class="bg-red-50 p-4 rounded-xl">
                        <p class="font-medium text-gray-600">Confirmation Token</p>
                        <p class="text-lg font-semibold text-gray-800 font-mono">${selectedBooking.confirmation_token || 'N/A'}</p>
                    </div>
                `;
                
                // Update booking summary
                bookingSummary.innerHTML = `
                    <div class="bg-red-50 p-4 rounded-xl">
                        <p class="font-medium text-gray-600">Status</p>
                        <p class="text-lg font-semibold ${getStatusColor(selectedBooking.status)}">
                            ${formatStatus(selectedBooking.status)}
                        </p>
                    </div>
                    <div class="bg-red-50 p-4 rounded-xl">
                        <p class="font-medium text-gray-600">Facility</p>
                        <p class="text-lg font-semibold text-gray-800">${selectedBooking.facility}</p>
                    </div>
                    <div class="bg-red-50 p-4 rounded-xl">
                        <p class="font-medium text-gray-600">Check-in Date</p>
                        <p class="text-lg font-semibold text-gray-800">${formatDate(selectedBooking.check_in_date)}</p>
                    </div>
                    <div class="bg-red-50 p-4 rounded-xl">
                        <p class="font-medium text-gray-600">Check-out Date</p>
                        <p class="text-lg font-semibold text-gray-800">${formatDate(selectedBooking.check_out_date)}</p>
                    </div>
                `;
                
                // Update action button
                updateActionButton(selectedBooking.status);
            }
        }

        // Format status for display
        function formatStatus(status) {
            if (!status) return 'N/A';
            return status.split('_').map(word => word.charAt(0).toUpperCase() + word.slice(1)).join(' ');
        }

        // Format date for display
        function formatDate(dateString) {
            if (!dateString) return 'N/A';
            return new Date(dateString).toLocaleDateString('en-US', { 
                year: 'numeric', 
                month: 'short', 
                day: 'numeric' 
            });
        }

        // Get color based on status
        function getStatusColor(status) {
            if (!status) return 'bg-gray-100 text-gray-800';
            
            switch(status) {
                case 'confirmed': return 'bg-green-100 text-green-800';
                case 'pending': return 'bg-yellow-100 text-yellow-800';
                case 'checked_in': return 'bg-blue-100 text-blue-800';
                case 'checked_out': return 'bg-purple-100 text-purple-800';
                default: return 'bg-gray-100 text-gray-800';
            }
        }

        // Update action button based on status
        function updateActionButton(status) {
            switch(status) {
                case 'pending':
                    actionButton.innerHTML = '<i class="fas fa-check-circle"></i> Confirm Booking';
                    actionButton.className = 'w-full bg-red-600 hover:bg-red-700 text-white py-3 px-6 rounded-xl transition-colors flex items-center justify-center gap-2 text-lg font-medium';
                    actionButton.onclick = () => updateBookingStatus('confirmed');
                    break;
                case 'confirmed':
                    actionButton.innerHTML = '<i class="fas fa-door-open"></i> Check In';
                    actionButton.className = 'w-full bg-blue-600 hover:bg-blue-700 text-white py-3 px-6 rounded-xl transition-colors flex items-center justify-center gap-2 text-lg font-medium';
                    actionButton.onclick = () => updateBookingStatus('checked_in');
                    break;
                case 'checked_in':
                    actionButton.innerHTML = '<i class="fas fa-sign-out-alt"></i> Check Out';
                    actionButton.className = 'w-full bg-purple-600 hover:bg-purple-700 text-white py-3 px-6 rounded-xl transition-colors flex items-center justify-center gap-2 text-lg font-medium';
                    actionButton.onclick = () => updateBookingStatus('checked_out');
                    break;
                default:
                    actionButton.innerHTML = '<i class="fas fa-info-circle"></i> No Action Available';
                    actionButton.className = 'w-full bg-gray-600 hover:bg-gray-700 text-white py-3 px-6 rounded-xl transition-colors flex items-center justify-center gap-2 text-lg font-medium';
                    actionButton.onclick = null;
            }
        }

        // Update booking status
        async function updateBookingStatus(newStatus) {
            if (!selectedBooking) return;
            
            // Simulate API call
            await simulateDelay();
            
            // Update the local data
            selectedBooking.status = newStatus;
            
            // Update the UI
            updateActionButton(newStatus);
            
            // Show success message
            showToast(`Booking status updated to ${formatStatus(newStatus)}`);
            
            // Refresh the bookings list
            fetchBookings();
        }

        // Search functionality
        searchBtn.addEventListener('click', function() {
            const firstName = firstNameSearch.value.trim();
            const lastName = lastNameSearch.value.trim();
            
            if (firstName || lastName) {
                searchBookings(firstName, lastName);
            } else {
                fetchBookings();
            }
        });

        // QR Scanner functionality
        qrScannerBtn.addEventListener('click', function() {
            qrScannerModal.classList.remove('hidden');
        });

        closeScanner.addEventListener('click', function() {
            qrScannerModal.classList.add('hidden');
        });

        simulateScan.addEventListener('click', function() {
            // Simulate scanning a booking
            if (allBookings.length > 0) {
                selectBooking(allBookings[0].id);
                showToast('QR code scanned successfully');
            }
            qrScannerModal.classList.add('hidden');
        });

        // Allow pressing Enter to search
        firstNameSearch.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') searchBtn.click();
        });
        
        lastNameSearch.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') searchBtn.click();
        });

        // Simulate initial data loading
        setTimeout(() => {
            fetchBookings();
        }, 1000);
      </script>
</body>

</html>