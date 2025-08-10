@extends('layouts.admin')
@section('title', 'Dashboard')

@php
$active = 'dashboard';
@endphp

@section('content_css')
<style>
     #qr-reader {
          width: 100%;
          margin: 0 auto;
          position: relative;
     }
     
     .main-modal {
          position: fixed;
          top: 0;
          left: 0;
          right: 0;
          bottom: 0;
          overflow-y: auto;
     }
     
     #qr-reader__dashboard_section_csr {
          margin-top: 15px;
          text-align: center;
     }

     #qr-reader__scan_region {
          background: white;
     }
     
     #qr-reader__dashboard_section {
          padding: 10px;
     }

     /* New design elements */
     .glassy-card {
          background: rgba(255, 255, 255, 0.7);
          backdrop-filter: blur(10px);
          border: 1px solid rgba(255, 255, 255, 0.128);
          box-shadow: 0 4px 12px 0 rgba(108, 29, 32, 0.15);
     }
     
     .animated-gradient {
          animation: gradientBG 15s ease infinite;
          background-size: 400% 400%;
     }

     @keyframes gradientBG {
          0% {
               background-position: 0% 50%;
          }

          50% {
               background-position: 100% 50%;
          }
          
          100% {
               background-position: 0% 50%;
          }
     }

     .hover-scale {
          transition: transform 0.3s ease, box-shadow 0.3s ease;
     }

     .status-pulse {
          position: relative;
     }

     .status-pulse::after {
          content: '';
          position: absolute;
          top: 0;
          right: 0;
          width: 10px;
          height: 10px;
          background-color: #10B981;
          border-radius: 50%;
          animation: pulse 2s infinite;
     }

     @keyframes pulse {
          0% {
               transform: scale(0.95);
               opacity: 1;
          }

          70% {
               transform: scale(1.3);
               opacity: 0.7;
          }

          100% {
               transform: scale(0.95);
               opacity: 1;
          }
     }

     .scrollbar-hide::-webkit-scrollbar {
          display: none;
     }

     .scrollbar-hide {
          -ms-overflow-style: none;
          scrollbar-width: none;
     }

     .chart-container {
          height: 300px;
          position: relative;
     }

     .floating-action-btn {
          position: fixed;
          bottom: 2rem;
          right: 2rem;
          width: 56px;
          height: 56px;
          border-radius: 50%;
          background: linear-gradient(135deg, #EF4444 0%, #F59E0B 100%);
          box-shadow: 0 10px 15px -3px rgba(239, 68, 68, 0.3), 0 4px 6px -2px rgba(239, 68, 68, 0.2);
          display: flex;
          align-items: center;
          justify-content: center;
          z-index: 50;
          transition: all 0.3s ease;
     }

     .floating-action-btn:hover {
          transform: scale(1.1) rotate(90deg);
          box-shadow: 0 20px 25px -5px rgba(239, 68, 68, 0.3), 0 10px 10px -5px rgba(239, 68, 68, 0.2);
     }

     .timeline-item::before {
          content: '';
          position: absolute;
          left: -20px;
          top: 0;
          width: 12px;
          height: 12px;
          border-radius: 50%;
          background-color: #EF4444;
          border: 2px solid white;
     }

     .timeline-connector {
          position: absolute;
          left: -15px;
          top: 12px;
          bottom: -12px;
          width: 2px;
          background-color: #E5E7EB;
     }
     body.modal-open {
          overflow: hidden;
          position: fixed;
          width: 100%;
          height: 100%
     }
</style>
@endsection

@section('content')
<div class="min-h-screen px-6 py-6">
     <!-- Header with animated gradient -->
     <div class="rounded-lg mb-4 overflow-hidden">
          <div class="bg-gradient-to-r from-red-600 to-red-700 p-8 text-white rounded-lg relative overflow-hidden">
               <div class="absolute inset-0 bg-gradient-to-br from-white/10 to-white/0"></div>
               <div class="relative z-10">
                    <div class="flex items-center justify-between">
                         <div>
                              <h1 class="text-3xl font-bold">Dashboard Overview</h1>
                              @auth
                              <p class="opacity-90 mt-2">Welcome back,
                                   {{ auth()->user()->firstname }}! Here's what's happening today.
                              </p>
                              @endauth
                         </div>
                         <div class="bg-white/20 p-3 rounded-lg backdrop-blur-sm">
                              <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24"
                                   stroke="currentColor">
                                   <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                              </svg>
                         </div>
                    </div>
                    <div class="mt-6 flex items-center space-x-2">
                         <span class="px-3 py-1 bg-white/20 rounded-full text-sm font-medium">@php echo
                              \Carbon\Carbon::now('Asia/Manila')->format('l, F j, Y'); @endphp</span>
                         <span class="px-3 py-1 bg-white/20 rounded-full text-sm font-medium flex items-center">
                              <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none"
                                   viewBox="0 0 24 24" stroke="currentColor">
                                   <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                              </svg>
                              @php echo \Carbon\Carbon::now('Asia/Manila')->format('g:i A'); @endphp
                         </span>
                    </div>
               </div>
          </div>
     </div>

     <!-- Status Cards with hover effects -->
     <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-4">
               <!-- Total Bookings -->
               <div
                    class="p-5 rounded-lg border relative overflow-hidden glassy-card">
                    <div class="absolute -right-4 -top-4 h-12 w-12 rounded-full bg-blue-200 opacity-30"></div>
                    <div class="relative z-10 flex justify-between items-start">
                         <div>
                              <p class="text-sm font-medium text-blue-700">Total Bookings</p>
                              <h3 class="text-2xl font-bold text-blue-900 mt-1" id="total-bookings">24</h3>
                              <p class="text-xs text-blue-600 mt-1">+5 this week</p>
                         </div>
                         <div class="p-2 bg-white rounded-lg shadow-inner">
                              <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-blue-600" fill="none"
                                   viewBox="0 0 24 24" stroke="currentColor">
                                   <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                              </svg>
                         </div>
                    </div>
               </div>

               <!-- Pending Confirmations -->
               <div
                    class="bg-white p-5 rounded-lg border border-lightGray hover-scale relative overflow-hidden glassy-card">
                    <div class="absolute -right-6 -top-6 h-16 w-16 rounded-full bg-yellow-100 opacity-20"></div>
                    <div class="absolute -right-4 -top-4 h-12 w-12 rounded-full bg-yellow-200 opacity-30"></div>
                    <div class="relative z-10 flex justify-between items-start">
                         <div>
                              <p class="text-sm font-medium text-yellow-700">Pending Confirmations</p>
                              <h3 class="text-2xl font-bold text-yellow-900 mt-1" id="total-pending">8</h3>
                              <p class="text-xs text-yellow-600 mt-1">3 awaiting response</p>
                         </div>
                         <div class="p-2 bg-white rounded-lg shadow-inner">
                              <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-yellow-600" fill="none"
                                   viewBox="0 0 24 24" stroke="currentColor">
                                   <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                              </svg>
                         </div>
                    </div>
               </div>

               <!-- Awaiting Payments -->
               <div
                    class="bg-white p-5 rounded-lg border border-lightGray hover-scale relative overflow-hidden glassy-card">
                    <div class="absolute -right-6 -top-6 h-16 w-16 rounded-full bg-purple-100 opacity-20"></div>
                    <div class="absolute -right-4 -top-4 h-12 w-12 rounded-full bg-purple-200 opacity-30"></div>
                    <div class="relative z-10 flex justify-between items-start">
                         <div>
                              <p class="text-sm font-medium text-purple-700">Awaiting Payments</p>
                              <h3 class="text-2xl font-bold text-purple-900 mt-1" id="pending-payments">5</h3>
                              <p class="text-xs text-purple-600 mt-1">Under Verification</p>
                         </div>
                         <div class="p-2 bg-white rounded-lg shadow-inner flex items-center justify-center">
                              <span class="text-purple-600 text-2xl font-bold">₱</span>
                         </div>
                    </div>
               </div>

               <!-- Verified Bookings -->
               <div
                    class="bg-white p-5 rounded-lg border border-lightGray hover-scale relative overflow-hidden glassy-card">
                    <div class="absolute -right-6 -top-6 h-16 w-16 rounded-full bg-green-100 opacity-20"></div>
                    <div class="absolute -right-4 -top-4 h-12 w-12 rounded-full bg-green-200 opacity-30"></div>
                    <div class="relative z-10 flex justify-between items-start">
                         <div>
                              <p class="text-sm font-medium text-green-700">Verified Bookings</p>
                              <h3 class="text-2xl font-bold text-green-900 mt-1">11</h3>
                              <p class="text-xs text-green-600 mt-1">+3 confirmed today</p>
                         </div>
                         <div class="p-2 bg-white rounded-lg shadow-inner">
                              <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-green-600" fill="none"
                                   viewBox="0 0 24 24" stroke="currentColor">
                                   <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                              </svg>
                         </div>
                    </div>
               </div>

     </div>
     
     <!-- Main Content Grid -->
     <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-4">
          <!-- Recent Enquiries -->
          <div
               class="h-card h-card--no-header h-py-8 h-mb-24 h-mr-8 shadow-sm bg-white p-6 rounded-lg border border-lightgray hover-scale">
               <div class="flex justify-between items-center mb-6">
                    <div class="flex items-center">
                         <h2 class="text-xl font-semibold text-gray-800">Recent Inquiries</h2>
                         <span
                              class="ml-2 bg-red-500 text-white text-xs font-bold px-2 py-1 rounded-full new-inquiries-count hidden">
                              0 new
                         </span>
                    </div>
                    <div>
                         <button class="text-sm text-red-600 hover:text-red-800 mr-2" onclick="markAllAsRead()">Mark All
                              as Read</button>
                         <a href="{{ route('admin.inquiries') }}" type="button"
                              class="text-sm text-red-600 hover:text-red-800">View All</a>
                    </div>
               </div>

               <!-- Search Bar -->
               <div class="relative mb-6">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                         <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                   d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                         </svg>
                    </div>
                    <input type="text" id="inquiry-search"
                         class="block w-full pl-10 pr-3 py-2 border border-darkgray rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 sm:text-sm"
                         placeholder="Search inquiries..." onkeyup="filterInquiries()">
               </div>
               
               <div class="space-y-4 max-h-96 overflow-y-auto pr-2 custom-scroll" id="inquiries-container">
                    <!-- Inquiries will be loaded here via AJAX -->
                    <div class="text-center py-8">
                         <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-red-600 mx-auto">
                         </div>
                         <p class="mt-2 text-gray-500">Loading inquiries...</p>
                    </div>
               </div>
          </div>
          
          @include('admin.inquirers.recent_inquirers')
          
          <!-- Facilities Occupied Today -->
          <div class="lg:col-span-2 shadow-sm bg-white p-6 rounded-lg border border-lightgray hover-scale">
               <div class="flex justify-between items-center mb-6">
                    <h2 class="text-xl font-semibold text-gray-800">Facilities Occupied Today</h2>
                    <div class="flex items-center">
                         <span class="text-sm text-gray-500 mr-3">
                              @php
                              $today = \Carbon\Carbon::now(
                              'Asia/Manila',
                              )->toDateString();
                              echo \Carbon\Carbon::now('Asia/Manila')->format(
                              'F j, Y',
                              );
                              @endphp
                         </span>
                         <button onclick="loadOccupiedFacilities()" class="text-gray-500 hover:text-red-600">
                              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                                   stroke="currentColor">
                                   <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                              </svg>
                         </button>
                    </div>
               </div>
               
               <div class="relative">
                    <!-- Horizontal scroll container -->
                    <div class="flex space-x-4 pb-4 overflow-x-auto scrollbar-hide" id="occupied-facilities-container">
                         <!-- Loading state -->
                         <div class="flex-shrink-0 w-full h-48 flex flex-col items-center justify-center">
                              <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-red-600"></div>
                              <p class="mt-2 text-gray-500">Loading occupied facilities...</p>
                         </div>
                    </div>
                    
                    <!-- Scroll indicators -->
                    <div
                         class="absolute top-0 left-0 h-full w-8 bg-gradient-to-r from-white to-transparent pointer-events-none">
                    </div>
                    <div
                         class="absolute top-0 right-0 h-full w-8 bg-gradient-to-l from-white to-transparent pointer-events-none">
                    </div>
               </div>
          </div>
     </div>
     
     <!-- Bottom Grid -->
     <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
          <!-- Left Column (Next Check-in) -->
          <div class="bg-white p-6 rounded-lg border shadow-sm border-lightgray hover-scale">
               <!-- Next Check-in Section -->
               <div class="glass-card">
                    <div class="flex items-center justify-between mb-4">
                         <h3 class="text-lg font-semibold text-gray-800">Next Check-in</h3>
                         <div class="bg-red-100 text-red-800 text-xs font-medium px-2.5 py-1 rounded-full animate-pulse flex items-center">
                              <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                   <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18" />
                              </svg>
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
                         <div class="flex items-center mt-2">
                              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-red-500 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                   <path fill-rule="evenodd" d="M9 2a1 1 0 00-1 1v1H5a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" clip-rule="evenodd" />
                              </svg>
                              <p class="font-medium text-gray-800" id="next-checkin-booking-code">-</p>
                         </div>
                    </div>
               </div>
          </div>
          
          <!-- Quick Actions -->
          <div class="lg:col-span-2 bg-white p-6 rounded-lg border shadow-sm border-lightgray">
               <h2 class="text-xl font-semibold text-gray-800 mb-6">Quick Actions</h2>
               <div class="grid grid-cols-2 gap-4">
               
                    <!-- Full-width Day Tour button -->
                    <a href="#" onclick="openDayTourModal()"
                         class="p-4 bg-yellow-100 rounded-lg text-center hover:bg-yellow-200 transition-all group col-span-2 hover:-translate-y-0.5">
                         <div class="mx-auto h-10 w-10 bg-yellow-100 rounded-full flex items-center justify-center mb-2 group-hover:bg-white transition-colors">
                         <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-yellow-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                         </svg>
                         </div>
                         <span class="text-sm font-medium text-gray-700 group-hover:text-gray-900">Day Tour</span>
                    </a>
                    
                    <!-- Action Buttons -->
                    <a href="#" id="openBookingModal"
                         class="p-4 bg-red-100 rounded-lg text-center hover:bg-red-200 transition-all group hover:-translate-y-0.5">
                         <div class="mx-auto h-10 w-10 bg-red-100 rounded-full flex items-center justify-center mb-2 group-hover:bg-white transition-colors">
                         <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                         </svg>
                         </div>
                         <span class="text-sm font-medium text-gray-700 group-hover:text-gray-900">New Booking</span>
                    </a>
                    
                    <a href="#" onclick="openCheckInModal()"
                         class="p-4 bg-blue-100 rounded-lg text-center hover:bg-blue-200 transition-all group hover:-translate-y-0.5">
                         <div class="mx-auto h-10 w-10 bg-blue-100 rounded-full flex items-center justify-center mb-2 group-hover:bg-white transition-colors">
                         <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                         </svg>
                         </div>
                         <span class="text-sm font-medium text-gray-700 group-hover:text-gray-900">Check-in</span>
                    </a>
                    
                    <a href="#" onclick="openCheckOutModal()"
                         class="p-4 bg-green-100 rounded-lg text-center hover:bg-green-200 transition-all group hover:-translate-y-0.5">
                         <div class="mx-auto h-10 w-10 bg-green-100 rounded-full flex items-center justify-center mb-2 group-hover:bg-white transition-colors">
                         <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                         </svg>
                         </div>
                         <span class="text-sm font-medium text-gray-700 group-hover:text-gray-900">Check-out</span>
                    </a>
                    
                    <a href="#"
                         class="p-4 bg-purple-100 rounded-lg text-center hover:bg-purple-200 transition-all group hover:-translate-y-0.5">
                         <div class="mx-auto h-10 w-10 bg-purple-100 rounded-full flex items-center justify-center mb-2 group-hover:bg-white transition-colors">
                         <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                         </svg>
                         </div>
                         <span class="text-sm font-medium text-gray-700 group-hover:text-gray-900">Payments</span>
                    </a>
               </div>
          </div>
     </div>
</div>

<!-- Modal for check-in options -->
<div id="checkInModal"
     class="main-modal fixed inset-0 bg-black/50 z-[999] backdrop-blur-sm flex items-center justify-center hidden z-50">
     <div
          class="bg-white rounded-xl shadow-xl overflow-hidden w-full max-w-md mx-4">
          <!-- Modal Header - Updated to blue color scheme -->
          <div class="bg-gradient-to-r from-blue-600 to-blue-700 p-6">
               <h3 class="text-xl font-semibold text-white">Check-in Method</h3>
               <p class="text-blue-100 text-sm mt-1">Choose how you'd like to check in guests</p>
          </div>

          <div class="p-6 space-y-4">
               <!-- QR Code Scan Option - Updated to blue color scheme -->
               <a href="{{ route('checkin.scanner') }}" class="block group">
                    <div
                         class="p-4 bg-blue-50 rounded-lg border border-blue-100 hover:border-blue-300 transition-all duration-200 flex items-start hover-scale">
                         <div class="bg-blue-100 p-3 rounded-lg mr-4 group-hover:bg-blue-200 transition-colors">
                              <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-700" fill="none"
                                   viewBox="0 0 24 24" stroke="currentColor">
                                   <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
                              </svg>
                         </div>
                         <div>
                              <h4 class="font-medium text-gray-900">Scan QR Code</h4>
                              <p class="text-sm text-gray-600 mt-1">Use your device camera to scan
                                   guest's QR code</p>
                         </div>
                         <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400 ml-auto mt-1" fill="none"
                              viewBox="0 0 24 24" stroke="currentColor">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                         </svg>
                    </div>
               </a>

               <!-- Upload QR Code Option - Updated to blue color scheme -->
               <div
                    class="border border-blue-100 rounded-lg overflow-hidden hover:border-blue-300 transition-colors duration-200 hover-scale">
                    <div class="p-4 bg-blue-50 hover:bg-blue-100 transition-colors duration-200">
                         <div class="flex items-start">
                              <div class="bg-blue-100 p-3 rounded-lg mr-4 hover:bg-blue-200 transition-colors">
                                   <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-700" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                             d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                   </svg>
                              </div>
                              <div class="flex-1">
                                   <h4 class="font-medium text-gray-900">Upload QR Code</h4>
                                   <p class="text-sm text-gray-600 mt-1">Upload an image
                                        containing the QR code</p>

                                   <div class="mt-4">
                                        <input type="file" id="qr-upload-input" accept="image/*" class="hidden" />
                                        <label for="qr-upload-input"
                                             class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors cursor-pointer text-sm font-medium">
                                             <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none"
                                                  viewBox="0 0 24 24" stroke="currentColor">
                                                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                       d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                             </svg>
                                             Choose File
                                        </label>
                                        <span id="file-name" class="ml-3 text-sm text-gray-600"></span>
                                   </div>
                              </div>
                         </div>

                         <div id="qr-upload-preview" class="mt-4 hidden">
                              <div
                                   class="border-2 border-dashed border-gray-300 rounded-lg p-4 flex flex-col items-center">
                                   <img id="qr-image-preview" src="#" alt="QR Code Preview"
                                        class="mx-auto max-h-40 mb-3 hidden">
                                   <div id="upload-instructions" class="text-center text-sm text-gray-500">
                                        <p>QR code will appear here after selection</p>
                                   </div>
                              </div>
                              <button id="process-qr-btn"
                                   class="w-full mt-3 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium flex items-center justify-center">
                                   <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                             d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10" />
                                   </svg>
                                   Process QR Code
                              </button>
                         </div>
                    </div>
               </div>

               <!-- Manual Search Option -->
               <button onclick="showManualSearch()"
                    class="w-full p-4 bg-gray-50 rounded-lg border border-gray-200 hover:border-gray-300 transition-all duration-200 flex items-start hover-scale">
                    <div class="bg-gray-200 p-3 rounded-lg mr-4 hover:bg-gray-300 transition-colors">
                         <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-700" fill="none"
                              viewBox="0 0 24 24" stroke="currentColor">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                   d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                         </svg>
                    </div>
                    <div class="text-left">
                         <h4 class="font-medium text-gray-900">Manual Search</h4>
                         <p class="text-sm text-gray-600 mt-1">Search for guest by name or reference
                              number</p>
                    </div>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400 ml-auto mt-1" fill="none"
                         viewBox="0 0 24 24" stroke="currentColor">
                         <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
               </button>
          </div>

          <!-- Modal Footer -->
          <div class="bg-gray-50 px-6 py-4 border-t border-gray-200">
               <button onclick="closeCheckInModal()"
                    class="w-full py-2.5 text-gray-700 font-medium rounded-lg hover:bg-gray-300 transition-colors">
                    Cancel
               </button>
          </div>
     </div>
</div>

<!-- Manual Search Container (hidden by default) -->
<div id="manualSearchContainer" class="fixed inset-0 bg-white p-4 hidden z-50">
     <div class="max-w-md mx-auto">
          <div class="flex justify-between items-center mb-4">
               <h3 class="text-lg font-medium">Search Guest</h3>
               <button onclick="closeManualSearch()" class="text-gray-500 hover:text-gray-700">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                         stroke="currentColor">
                         <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M6 18L18 6M6 6l12 12" />
                    </svg>
               </button>
          </div>

          <div class="mb-4">
               <input type="text" id="searchInput" placeholder="Enter name or reference number"
                    class="w-full p-2 border rounded-lg">
          </div>

          <button onclick="performSearch()"
               class="w-full py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-colors">
               Search
          </button>

          <div id="searchResults" class="mt-4">
               <!-- Search results will appear here -->
          </div>
     </div>
</div>


<!-- Add this modal near your checkInModal in the HTML section -->
<div id="checkOutModal" class="main-modal fixed inset-0 bg-black/50 z-[999] backdrop-blur-sm flex items-center justify-center hidden z-50">
     <div class="bg-white rounded-xl shadow-xl overflow-hidden w-full max-w-md mx-4">
          <!-- Modal Header -->
          <div class="bg-gradient-to-r from-green-600 to-green-700 p-6">
               <h3 class="text-xl font-semibold text-white">Check-out Method</h3>
               <p class="text-green-100 text-sm mt-1">Choose how you'd like to check out guests</p>
          </div>

          <div class="p-6 space-y-4">
               <!-- QR Code Scan Option -->
               <a href="" class="block group">
                    <div class="p-4 bg-green-50 rounded-lg border border-green-100 hover:border-green-300 transition-all duration-200 flex items-start hover-scale">
                         <div class="bg-green-100 p-3 rounded-lg mr-4 group-hover:bg-green-200 transition-colors">
                              <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-green-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                   <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
                              </svg>
                         </div>
                         <div>
                              <h4 class="font-medium text-gray-900">Scan QR Code</h4>
                              <p class="text-sm text-gray-600 mt-1">Use your device camera to scan guest's QR code</p>
                         </div>
                         <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400 ml-auto mt-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                         </svg>
                    </div>
               </a>

               <!-- Upload QR Code Option -->
               <div class="border border-green-100 rounded-lg overflow-hidden hover:border-green-300 transition-colors duration-200 hover-scale">
                    <div class="p-4 bg-green-50 hover:bg-green-100 transition-colors duration-200">
                         <div class="flex items-start">
                              <div class="bg-green-100 p-3 rounded-lg mr-4 hover:bg-green-200 transition-colors">
                                   <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-green-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                   </svg>
                              </div>
                              <div class="flex-1">
                                   <h4 class="font-medium text-gray-900">Upload QR Code</h4>
                                   <p class="text-sm text-gray-600 mt-1">Upload an image containing the QR code</p>

                                   <div class="mt-4">
                                        <input type="file" id="checkout-qr-upload-input" accept="image/*" class="hidden" />
                                        <label for="checkout-qr-upload-input" class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors cursor-pointer text-sm font-medium">
                                             <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                             </svg>
                                             Choose File
                                        </label>
                                        <span id="checkout-file-name" class="ml-3 text-sm text-gray-600"></span>
                                   </div>
                              </div>
                         </div>

                         <div id="checkout-qr-upload-preview" class="mt-4 hidden">
                              <div class="border-2 border-dashed border-gray-300 rounded-lg p-4 flex flex-col items-center">
                                   <img id="checkout-qr-image-preview" src="#" alt="QR Code Preview" class="mx-auto max-h-40 mb-3 hidden">
                                   <div id="checkout-upload-instructions" class="text-center text-sm text-gray-500">
                                        <p>QR code will appear here after selection</p>
                                   </div>
                              </div>
                              <button id="checkout-process-qr-btn" class="w-full mt-3 py-2.5 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors font-medium flex items-center justify-center">
                                   <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10" />
                                   </svg>
                                   Process QR Code
                              </button>
                         </div>
                    </div>
               </div>

               <!-- Manual Search Option -->
               <button onclick="showCheckoutManualSearch()" class="w-full p-4 bg-gray-50 rounded-lg border border-gray-200 hover:border-gray-300 transition-all duration-200 flex items-start hover-scale">
                    <div class="bg-gray-200 p-3 rounded-lg mr-4 hover:bg-gray-300 transition-colors">
                         <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                         </svg>
                    </div>
                    <div class="text-left">
                         <h4 class="font-medium text-gray-900">Manual Search</h4>
                         <p class="text-sm text-gray-600 mt-1">Search for guest by name or reference number</p>
                    </div>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400 ml-auto mt-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                         <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
               </button>
          </div>

          <!-- Modal Footer -->
          <div class="bg-gray-50 px-6 py-4 border-t border-gray-200">
               <button onclick="closeCheckOutModal()" class="w-full py-2.5 text-gray-700 font-medium rounded-lg hover:bg-gray-300 transition-colors">
                    Cancel
               </button>
          </div>
     </div>
</div>

<!-- Manual Checkout Search Container -->
<div id="checkoutManualSearchContainer" class="main-modal fixed inset-0 bg-white p-4 hidden z-50">
     <div class="max-w-md mx-auto">
          <div class="flex justify-between items-center mb-4">
               <h3 class="text-lg font-medium">Search Guest for Check-out</h3>
               <button onclick="closeCheckoutManualSearch()" class="text-gray-500 hover:text-gray-700">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                         <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
               </button>
          </div>

          <div class="mb-4">
               <input type="text" id="checkoutSearchInput" placeholder="Enter name or reference number" class="w-full p-2 border rounded-lg">
          </div>

          <button onclick="performCheckoutSearch()" class="w-full py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 transition-colors">
               Search
          </button>

          <div id="checkoutSearchResults" class="mt-4">
               <!-- Checkout search results will appear here -->
          </div>
     </div>
</div>

<!-- Day Tour Modal -->
<div id="dayTourModal" class="modal fixed inset-0 bg-black/50 z-[999] backdrop-blur-sm flex items-center justify-center hidden">
     <div class="bg-white rounded-xl shadow-xl overflow-hidden w-full max-w-4xl mx-4 max-h-[90vh] overflow-y-auto">
          <!-- Modal Header -->
          <div class="bg-gradient-to-r from-yellow-600 to-yellow-700 p-6 sticky top-0 z-10">
               <h3 class="text-xl font-semibold text-white">Day Tour Registration</h3>
               <p class="text-yellow-100 text-sm mt-1">Register a new day tour guest</p>
          </div>

          <div class="p-6">
               <form id="dayTourForm">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                         <!-- Left Column - Customer Info -->
                         <div>
                              <!-- Customer Information -->
                              <div class="mb-6">
                                   <button type="button" class="flex items-center justify-between w-full" onclick="toggleSection('customerInfoSection')">
                                        <h4 class="text-lg font-medium text-gray-900">Customer Information</h4>
                                        <svg id="customerInfoChevron" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-500 transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                             <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                        </svg>
                                   </button>
                                   
                                   <div id="customerInfoSection" class="mt-4 space-y-4">
                                        <div class="grid grid-cols-2 gap-4">
                                             <div>
                                                  <label for="firstName" class="block text-sm font-mediumtext-gray-700 mb-1">First Name *</label>
                                                  <input type="text" id="firstName" name="firstName" required
                                                       class="w-full px-3 py-2 border border-darkgray rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500">
                                             </div>
                                             <div>
                                                  <label for="lastName" class="block text-sm font-medium text-gray-700 mb-1">Last Name *</label>
                                                  <input type="text" id="lastName" name="lastName" required
                                                       class="w-full px-3 py-2 border border-darkgray rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500">
                                             </div>
                                        </div>

                                        <div>
                                             <label for="phoneNumber" class="block text-sm font-medium text-gray-700 mb-1">Phone Number *</label>
                                             <input type="tel" id="phoneNumber" name="phoneNumber" required
                                                  class="w-full px-3 py-2 border border-darkgray rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500">
                                        </div>

                                        <div>
                                             <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email (Optional)</label>
                                             <input type="email" id="email" name="email"
                                                  class="w-full px-3 py-2 border border-darkgray rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500">
                                        </div>
                                   </div>
                              </div>

                              <!-- Tour Options -->
                              <div class="mb-6">
                                   <h4 class="text-lg font-medium text-gray-900 mb-3">Tour Options *</h4>
                                   <div class="grid grid-cols-3 gap-3">
                                        <div>
                                             <input id="poolOption" name="tourOption" type="radio" value="pool" class="hidden peer" checked>
                                             <label for="poolOption" class="block p-3 border border-lightgray rounded-lg text-center cursor-pointer peer-checked:border-yellow-500 peer-checked:bg-yellow-50">
                                                  <span class="block text-sm font-medium">Pool Only</span>
                                             </label>
                                        </div>
                                        <div>
                                             <input id="parkOption" name="tourOption" type="radio" value="park" class="hidden peer">
                                             <label for="parkOption" class="block p-3 border border-lightgray rounded-lg text-center cursor-pointer peer-checked:border-yellow-500 peer-checked:bg-yellow-50">
                                                  <span class="block text-sm font-medium">Park Only</span>
                                             </label>
                                        </div>
                                        <div>
                                             <input id="bothOption" name="tourOption" type="radio" value="both" class="hidden peer">
                                             <label for="bothOption" class="block p-3 border border-lightgray rounded-lg text-center cursor-pointer peer-checked:border-yellow-500 peer-checked:bg-yellow-50">
                                                  <span class="block text-sm font-medium">Both</span>
                                             </label>
                                        </div>
                                   </div>
                              </div>
                         </div>

                         <!-- Right Column - Pricing -->
                         <div>
                              <!-- Entrance Fees -->
                              <div class="mb-6">
                                   <button type="button" class="flex items-center justify-between w-full" onclick="toggleSection('entranceFeesSection')">
                                        <h4 class="text-lg font-medium text-gray-900">Entrance Fees</h4>
                                        <svg id="entranceFeesChevron" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-500 transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                             <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                        </svg>
                                   </button>
                                   
                                   <div id="entranceFeesSection" class="mt-4 space-y-4">
                                        <!-- Pool Fees (shown when Pool or Both selected) -->
                                        <div id="poolFees" class="space-y-3">
                                             <h5 class="text-sm font-medium text-gray-700">Pool Entrance</h5>
                                             <div class="grid grid-cols-3 gap-2 items-center">
                                                  <label class="block text-xs text-gray-600">Adults (₱150)</label>
                                                  <input type="number" id="poolAdultCount" name="poolAdultCount" min="0" value="0"
                                                       class="px-2 py-1 text-sm border border-darkgray rounded-md focus:outline-none focus:ring-1 focus:ring-yellow-500 focus:border-yellow-500">
                                                  <div class="text-sm font-medium text-right">₱<span id="poolAdultTotal">0</span></div>
                                             </div>
                                             
                                             <div class="grid grid-cols-3 gap-2 items-center">
                                                  <label class="block text-xs text-gray-600">Kids (₱100)</label>
                                                  <input type="number" id="poolKidCount" name="poolKidCount" min="0" value="0"
                                                       class="px-2 py-1 text-sm border border-darkgray rounded-md focus:outline-none focus:ring-1 focus:ring-yellow-500 focus:border-yellow-500">
                                                  <div class="text-sm font-medium text-right">₱<span id="poolKidTotal">0</span></div>
                                             </div>
                                             
                                             <div class="grid grid-cols-3 gap-2 items-center">
                                                  <label class="block text-xs text-gray-600">Seniors (₱100)</label>
                                                  <input type="number" id="poolSeniorCount" name="poolSeniorCount" min="0" value="0"
                                                       class="px-2 py-1 text-sm border border-darkgray rounded-md focus:outline-none focus:ring-1 focus:ring-yellow-500 focus:border-yellow-500">
                                                  <div class="text-sm font-medium text-right">₱<span id="poolSeniorTotal">0</span></div>
                                             </div>
                                        </div>
                                        
                                        <!-- Park Fees (shown when Park or Both selected) -->
                                        <div id="parkFees" class="space-y-3 mt-4 hidden">
                                             <h5 class="text-sm font-medium text-gray-700">Park Entrance</h5>
                                             <div class="grid grid-cols-3 gap-2 items-center">
                                                  <label class="block text-xs text-gray-600">Adults (₱80)</label>
                                                  <input type="number" id="parkAdultCount" name="parkAdultCount" min="0" value="0"
                                                       class="px-2 py-1 text-sm border border-darkgray rounded-md focus:outline-none focus:ring-1 focus:ring-yellow-500 focus:border-yellow-500">
                                                  <div class="text-sm font-medium text-right">₱<span id="parkAdultTotal">0</span></div>
                                             </div>
                                             
                                             <div class="grid grid-cols-3 gap-2 items-center">
                                                  <label class="block text-xs text-gray-600">Kids (₱50)</label>
                                                  <input type="number" id="parkKidCount" name="parkKidCount" min="0" value="0"
                                                       class="px-2 py-1 text-sm border border-darkgray rounded-md focus:outline-none focus:ring-1 focus:ring-yellow-500 focus:border-yellow-500">
                                                  <div class="text-sm font-medium text-right">₱<span id="parkKidTotal">0</span></div>
                                             </div>
                                             
                                             <div class="grid grid-cols-3 gap-2 items-center">
                                                  <label class="block text-xs text-gray-600">Seniors (₱50)</label>
                                                  <input type="number" id="parkSeniorCount" name="parkSeniorCount" min="0" value="0"
                                                       class="px-2 py-1 text-sm border border-darkgray rounded-md focus:outline-none focus:ring-1 focus:ring-yellow-500 focus:border-yellow-500">
                                                  <div class="text-sm font-medium text-right">₱<span id="parkSeniorTotal">0</span></div>
                                             </div>
                                        </div>
                                        
                                        <div class="grid grid-cols-3 gap-2 items-center pt-2 border-t border-gray-200 mt-4">
                                             <label class="block text-sm font-medium text-gray-700">Subtotal</label>
                                             <div></div>
                                             <div class="text-sm font-bold text-right">₱<span id="grandTotal">0</span></div>
                                        </div>
                                   </div>
                              </div>

                              <!-- Cottages Section -->
                              <div class="mb-6">
                                   <button type="button" class="flex items-center justify-between w-full" onclick="toggleSection('cottagesSection')">
                                        <h4 class="text-lg font-medium text-gray-900">Optional Cottages</h4>
                                        <svg id="cottagesChevron" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-500 transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                             <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                        </svg>
                                   </button>
                                   
                                   <div id="cottagesSection" class="mt-4">
                                        <div id="cottageOptions" class="space-y-3">
                                             <p class="text-sm text-gray-500">Loading cottage options...</p>
                                        </div>
                                        
                                        <div class="grid grid-cols-3 gap-2 items-center pt-2 border-t border-gray-200 mt-4">
                                             <label class="block text-sm font-medium text-gray-700">Cottage Total</label>
                                             <div></div>
                                             <div class="text-sm font-medium text-right">₱<span id="cottageTotal">0</span></div>
                                        </div>
                                   </div>
                              </div>
                         </div>
                    </div>

                    <!-- Final Total -->
                    <div class="bg-yellow-50 p-4 rounded-lg border border-yellow-100 mb-6">
                         <div class="flex justify-between items-center">
                              <h4 class="text-lg font-bold text-gray-900">Final Total</h4>
                              <div class="text-xl font-bold text-yellow-700">₱<span id="finalTotal">0</span></div>
                         </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="flex justify-end space-x-3">
                         <button type="button" onclick="closeDayTourModal()"
                              class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500">
                              Cancel
                         </button>
                         <button type="submit"
                              class="px-4 py-2 bg-yellow-600 border border-transparent rounded-md text-sm font-medium text-white hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500">
                              Register Guest
                         </button>
                    </div>
               </form>
          </div>
     </div>
</div>

<meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@section('content_js')
<script src="https://unpkg.com/html5-qrcode@2.3.4/html5-qrcode.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/jsqr@1.4.0/dist/jsQR.min.js"></script>
<script>
// Global functions for modal operations
function openCheckInModal() {
     document.body.classList.add('modal-open');
     document.getElementById('checkInModal').classList.remove('hidden');
}

function closeCheckInModal() {
     document.body.classList.remove('modal-open');
     document.getElementById('checkInModal').classList.add('hidden');
}

function openCheckOutModal() {
     document.body.classList.add('modal-open');
     document.getElementById('checkOutModal').classList.remove('hidden');
}

function closeCheckOutModal() {
     document.body.classList.remove('modal-open');
     document.getElementById('checkOutModal').classList.add('hidden');
}

function showManualSearch() {
     document.body.classList.add('modal-open');
     closeCheckInModal();
     document.getElementById('manualSearchContainer').classList.remove('hidden');
}

function closeManualSearch() {
     document.body.classList.remove('modal-open');
     document.getElementById('manualSearchContainer').classList.add('hidden');
}

function showCheckoutManualSearch() {
     document.body.classList.add('modal-open');
     closeCheckOutModal();
     document.getElementById('checkoutManualSearchContainer').classList.remove('hidden');
}

function closeCheckoutManualSearch() {
     document.body.classList.remove('modal-open');
     document.getElementById('checkoutManualSearchContainer').classList.add('hidden');
}

// Day Tour Modal Functions
function openDayTourModal() {
     document.body.classList.add('modal-open');
     document.getElementById('dayTourModal').classList.remove('hidden');
     loadCottageOptions();
}

function closeDayTourModal() {
     document.body.classList.remove('modal-open');
     document.getElementById('dayTourModal').classList.add('hidden');
}

function toggleSection(sectionId) {
     const section = document.getElementById(sectionId);
     const chevron = document.getElementById(`${sectionId}Chevron`);
     section.classList.toggle('hidden');
     chevron.classList.toggle('rotate-180');
}

// Data Loading Functions
function loadNextCheckin() {
     fetch('/get/bookings/next-checkin', {
          method: 'GET',
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
          if (!data.success) {
               throw new Error(data.message || 'Failed to load next check-in');
          }
          
          const container = document.getElementById('next-checkin-time');
          
          if (data.data) {
               const booking = data.data;
               const detail = booking.details[0];
               
               // Format the time display
               const daysUntil = data.days_until;
               let displayText;
               
               if (daysUntil < 1) {
                    const hours = Math.round(daysUntil * 24);
                    displayText = `${hours} hour${hours !== 1 ? 's' : ''} from now`;
               } else {
                    displayText = `${daysUntil.toFixed(1)} day${daysUntil !== 1 ? 's' : ''} from now`;
               }
               
               container.textContent = displayText;
               document.getElementById('next-checkin-date').textContent = formatDate(detail.checkin_date);
               document.getElementById('next-checkin-nights').textContent = 
                    `${getNights(detail.checkin_date, detail.checkout_date)} night${getNights(detail.checkin_date, detail.checkout_date) !== 1 ? 's' : ''}`;
               document.getElementById('next-checkin-guest').textContent = 
                    `${booking.user?.firstname || 'Guest'} ${booking.user?.lastname || ''}`;
               document.getElementById('next-checkin-phone').textContent = booking.user?.phone || 'N/A';
               document.getElementById('next-checkin-booking-code').textContent = booking.reference || 'N/A';
          } else {
               container.textContent = 'No upcoming check-ins';
               ['next-checkin-date', 'next-checkin-nights', 'next-checkin-guest', 'next-checkin-phone'].forEach(id => {
                    document.getElementById(id).textContent = '-';
               });
          }
     })
     .catch(error => {
          console.error('Error loading check-in data:', error);
          document.getElementById('next-checkin-time').textContent = 'Error loading check-in data';
          document.getElementById('next-checkin-time').classList.add('text-red-500');
     });
}

function loadOccupiedFacilities() {
     fetch('/admin/dashboard/occupied-facilities', {
          method: 'GET',
          headers: {
               'Content-Type': 'application/json',
               'Accept': 'application/json',
               'X-Requested-With': 'XMLHttpRequest',
               'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
          }
     })
     .then(response => response.json())
     .then(data => {
          const container = document.getElementById('occupied-facilities-container');
          
          if (data.length === 0) {
               container.innerHTML = `
                    <div class="flex-shrink-0 w-full h-48 flex flex-col items-center justify-center">
                         <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                         <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                         </svg>
                         <p class="mt-2 text-gray-600">No facilities occupied today</p>
                    </div>
               `;
               return;
          }
          
          let html = '';
          data.forEach(facility => {
               const hasBreakfast = facility.has_breakfast ? `
                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">
                         <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                         <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                         </svg>
                         Breakfast
                    </span>
               ` : '';
               
               const imageUrl = facility.image_url ? facility.image_url : '/images/default-facility.jpg';
               
               html += `
                    <div class="flex-shrink-0 w-64 bg-white rounded-lg border border-gray-200 overflow-hidden shadow-sm hover:shadow-md transition-shadow duration-200">
                         <div class="h-40 bg-gray-100 overflow-hidden">
                         <img src="${imageUrl}" alt="${facility.name}" class="w-full h-full object-cover">
                         </div>
                         <div class="p-4">
                         <div class="flex justify-between items-start">
                         <h3 class="font-medium text-gray-900 truncate">${facility.name}</h3>
                         ${hasBreakfast}
                         </div>
                         <div class="mt-2">
                         <p class="text-sm text-gray-600 truncate">
                              <span class="font-medium">Guest:</span> ${facility.user_name}
                         </p>
                         </div>
                         <div class="mt-3 flex justify-between items-center text-xs text-gray-500">
                         <span class="inline-flex items-center">
                              <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                   <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                              </svg>
                              Today
                         </span>
                         </div>
                         </div>
                    </div>
               `;
          });
          
          container.innerHTML = html;
     })
     .catch(error => {
          console.error('Error loading occupied facilities:', error);
          const container = document.getElementById('occupied-facilities-container');
          container.innerHTML = `
               <div class="flex-shrink-0 w-full h-48 flex flex-col items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                         <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    <p class="mt-2 text-gray-600">Failed to load facilities</p>
                    <button onclick="loadOccupiedFacilities()" class="mt-2 text-sm text-red-600 hover:text-red-800">Retry</button>
               </div>
          `;
     });
}

function loadCottageOptions() {
     fetch('/admin/cottages')
          .then(response => response.json())
          .then(data => {
               const container = document.getElementById('cottageOptions');
               if (data.length > 0) {
                    let html = '';
                    data.forEach(cottage => {
                         html += `
                         <div class="flex items-center justify-between">
                         <div>
                              <label class="block text-sm text-gray-700">${cottage.name} (₱${cottage.price})</label>
                              <p class="text-xs text-gray-500">${cottage.description || ''}</p>
                         </div>
                         <input type="checkbox" id="cottage_${cottage.id}" name="cottages[]" value="${cottage.id}" 
                              data-price="${cottage.price}" class="h-4 w-4 text-yellow-600 focus:ring-yellow-500 rounded"
                              onchange="calculateCottageTotal()">
                         </div>
                         `;
                    });
                    container.innerHTML = html;
               } else {
                    container.innerHTML = '<p class="text-sm text-gray-500">No cottages available</p>';
               }
          })
          .catch(error => {
               console.error('Error loading cottages:', error);
               document.getElementById('cottageOptions').innerHTML = '<p class="text-sm text-red-500">Error loading cottages</p>';
          });
}

// Utility Functions
function formatDate(dateString) {
     if (!dateString) return '-';
     const options = { year: 'numeric', month: 'long', day: 'numeric' };
     return new Date(dateString).toLocaleDateString(undefined, options);
     }

function getNights(checkin, checkout) {
     if (!checkin || !checkout) return 0;
     const diff = new Date(checkout) - new Date(checkin);
     return Math.floor(diff / (1000 * 60 * 60 * 24));
}

// Search Functions
function performSearch() {
     const searchTerm = document.getElementById('searchInput').value.trim();
     if (!searchTerm) return;
     
     const resultsContainer = document.getElementById('searchResults');
     resultsContainer.innerHTML = '<p class="text-center py-4">Searching...</p>';
     
     fetch(`/check-in/search-guests?q=${encodeURIComponent(searchTerm)}`)
          .then(response => response.json())
          .then(data => {
               if (data.length > 0) {
                    let html = '<div class="space-y-2">';
                    data.forEach(guest => {
                         if (guest.payment_id) {
                         html += `
                              <div class="p-3 border rounded-lg hover:bg-gray-50 cursor-pointer" 
                                   onclick="selectGuest('${guest.payment_id}')">
                                   <h4 class="font-medium">${guest.name}</h4>
                                   <p class="text-sm text-gray-600">Booking Ref: ${guest.reference_no}</p>
                                   <p class="text-sm text-green-600 font-medium">Payment ID: ${guest.payment_id}</p>
                              </div>
                         `;
                         }
                    });
                    html += '</div>';
                    resultsContainer.innerHTML = html;
               } else {
                    resultsContainer.innerHTML = '<p class="text-center py-4">No guests found</p>';
               }
          })
          .catch(error => {
               resultsContainer.innerHTML = '<p class="text-center py-4 text-red-500">Error searching</p>';
               console.error('Search error:', error);
          });
}

function performCheckoutSearch() {
     const searchTerm = document.getElementById('checkoutSearchInput').value.trim();
     if (!searchTerm) return;
     
     const resultsContainer = document.getElementById('checkoutSearchResults');
     resultsContainer.innerHTML = '<p class="text-center py-4">Searching...</p>';
     
     fetch(`/check-out/search-guests?q=${encodeURIComponent(searchTerm)}`)
          .then(response => response.json())
          .then(data => {
               if (data.length > 0) {
                    let html = '<div class="space-y-2">';
                    data.forEach(guest => {
                         if (guest.payment_id) {
                         html += `
                         <div class="p-3 border rounded-lg hover:bg-green-50 cursor-pointer" 
                              onclick="selectGuestForCheckout('${guest.payment_id}')">
                              <h4 class="font-medium">${guest.name}</h4>
                              <p class="text-sm text-gray-600">Booking Ref: ${guest.reference_no}</p>
                              <p class="text-sm text-green-600 font-medium">Payment ID: ${guest.payment_id}</p>
                              <div class="mt-2 flex items-center text-sm text-gray-500">
                                   <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                   </svg>
                                   ${guest.checkin_date} to ${guest.checkout_date}
                              </div>
                         </div>
                         `;
                         }
                    });
                    html += '</div>';
                    resultsContainer.innerHTML = html;
               } else {
                    resultsContainer.innerHTML = '<p class="text-center py-4">No guests found for checkout</p>';
               }
          })
          .catch(error => {
               resultsContainer.innerHTML = '<p class="text-center py-4 text-red-500">Error searching</p>';
               console.error('Checkout search error:', error);
          });
}

// Selection Functions
function selectGuest(paymentId) {
     window.location.href = `/check-in/success/${paymentId}`;
}

function selectGuestForCheckout(paymentId) {
     window.location.href = `/check-out/process/${paymentId}`;
}

// Calculation Functions
function calculateTotals() {
     const tourOption = document.querySelector('input[name="tourOption"]:checked').value;
     
     // Show/hide sections based on selection
     document.getElementById('poolFees').classList.toggle('hidden', tourOption === 'park');
     document.getElementById('parkFees').classList.toggle('hidden', tourOption === 'pool');
     document.getElementById('cottagesSection').classList.toggle('hidden', tourOption === 'park');
     
     // Calculate pool fees if selected
     if (tourOption === 'pool' || tourOption === 'both') {
          const poolAdultCount = parseInt(document.getElementById('poolAdultCount').value) || 0;
          const poolKidCount = parseInt(document.getElementById('poolKidCount').value) || 0;
          const poolSeniorCount = parseInt(document.getElementById('poolSeniorCount').value) || 0;
          
          const poolAdultTotal = poolAdultCount * 150;
          const poolKidTotal = poolKidCount * 100;
          const poolSeniorTotal = poolSeniorCount * 100;
          
          document.getElementById('poolAdultTotal').textContent = poolAdultTotal;
          document.getElementById('poolKidTotal').textContent = poolKidTotal;
          document.getElementById('poolSeniorTotal').textContent = poolSeniorTotal;
     }
     
    // Calculate park fees if selected
     if (tourOption === 'park' || tourOption === 'both') {
          const parkAdultCount = parseInt(document.getElementById('parkAdultCount').value) || 0;
          const parkKidCount = parseInt(document.getElementById('parkKidCount').value) || 0;
          const parkSeniorCount = parseInt(document.getElementById('parkSeniorCount').value) || 0;
          
          const parkAdultTotal = parkAdultCount * 80;
          const parkKidTotal = parkKidCount * 50;
          const parkSeniorTotal = parkSeniorCount * 50;
          
          document.getElementById('parkAdultTotal').textContent = parkAdultTotal;
          document.getElementById('parkKidTotal').textContent = parkKidTotal;
          document.getElementById('parkSeniorTotal').textContent = parkSeniorTotal;
     }
     
     calculateFinalTotal();
}

function calculateCottageTotal() {
     let total = 0;
     document.querySelectorAll('input[name="cottages[]"]:checked').forEach(checkbox => {
          total += parseFloat(checkbox.dataset.price);
     });
     document.getElementById('cottageTotal').textContent = total.toFixed(2);
     calculateFinalTotal();
}

function calculateFinalTotal() {
     const tourOption = document.querySelector('input[name="tourOption"]:checked').value;
     let entranceTotal = 0;
     
     if (tourOption === 'pool' || tourOption === 'both') {
          entranceTotal += parseInt(document.getElementById('poolAdultTotal').textContent) || 0;
          entranceTotal += parseInt(document.getElementById('poolKidTotal').textContent) || 0;
          entranceTotal += parseInt(document.getElementById('poolSeniorTotal').textContent) || 0;
     }
     
     if (tourOption === 'park' || tourOption === 'both') {
          entranceTotal += parseInt(document.getElementById('parkAdultTotal').textContent) || 0;
          entranceTotal += parseInt(document.getElementById('parkKidTotal').textContent) || 0;
          entranceTotal += parseInt(document.getElementById('parkSeniorTotal').textContent) || 0;
     }
     
     const cottageTotal = tourOption === 'park' ? 0 : (parseFloat(document.getElementById('cottageTotal').textContent) || 0);
     const finalTotal = entranceTotal + cottageTotal;
     
     document.getElementById('grandTotal').textContent = entranceTotal.toFixed(2);
     document.getElementById('finalTotal').textContent = finalTotal.toFixed(2);
}

// QR Code Processing Functions
async function readQRCodeFromImage(file) {
     return new Promise((resolve, reject) => {
          const reader = new FileReader();
          reader.onload = function(e) {
               const img = new Image();
               img.onload = function() {
                    try {
                         const canvas = document.createElement('canvas');
                         const ctx = canvas.getContext('2d');
                         canvas.width = img.width;
                         canvas.height = img.height;
                         ctx.drawImage(img, 0, 0);
                         
                         const imageData = ctx.getImageData(0, 0, canvas.width, canvas.height);
                         const code = jsQR(imageData.data, imageData.width, imageData.height);
                         
                         if (code) {
                         resolve(code.data);
                         } else {
                         reject(new Error("Could not read QR code from the image. Please ensure the image is clear and contains a valid QR code."));
                         }
                    } catch (error) {
                         reject(error);
                    }
               };
               img.onerror = () => reject(new Error("Failed to load image"));
               img.src = e.target.result;
          };
          reader.onerror = () => reject(new Error("Failed to read file"));
          reader.readAsDataURL(file);
     });
}

// Document Ready Function
document.addEventListener('DOMContentLoaded', function() {
     // Initialize dashboard stats
     fetch(`/admin/dashboard/stats`, {
          method: 'GET',
          headers: {
               'Accept': 'application/json',
               'X-Requested-With': 'XMLHttpRequest',
               'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
          }
     })
     .then(response => response.json()) 
     .then(data => {
          document.getElementById('total-pending').textContent = data.pending;
          document.getElementById('pending-payments').textContent = data.pending_payments;
     })
     .catch(error => {
          console.error(`Fetching error:`, error); 
     });
     
     // Load initial data
     loadNextCheckin();
     loadOccupiedFacilities();
     
     // Set up periodic refreshes
     setInterval(loadNextCheckin, 300000);
     setInterval(loadOccupiedFacilities, 300000);
     
     // Set up event listeners for all View buttons
     document.querySelectorAll('[data-id]').forEach(button => {
          button.addEventListener('click', function() {
               const inquirerId = this.getAttribute('data-id');
               openModal_accept_inquirer(this);
          });
     });
     
     // Set up event listener for New Booking button
     document.getElementById('openBookingModal').addEventListener('click', function(e) {
          e.preventDefault();
          openBookingModal();
     });
     
     // QR Upload and Processing for Check-in
     const qrUploadInput = document.getElementById('qr-upload-input');
     const qrUploadPreview = document.getElementById('qr-upload-preview');
     const qrImagePreview = document.getElementById('qr-image-preview');
     const fileNameSpan = document.getElementById('file-name');
     const processQrBtn = document.getElementById('process-qr-btn');
     
     qrUploadInput.addEventListener('change', function(e) {
          const file = e.target.files[0];
          if (!file) return;
          
          fileNameSpan.textContent = file.name;
          qrUploadPreview.classList.remove('hidden');
          document.getElementById('upload-instructions').classList.add('hidden');
          
          if (file.type.startsWith('image/')) {
               const reader = new FileReader();
               reader.onload = function(e) {
                    qrImagePreview.src = e.target.result;
                    qrImagePreview.classList.remove('hidden');
               };
               reader.readAsDataURL(file);
          }
     });

     processQrBtn.addEventListener('click', async function() {
          const file = qrUploadInput.files[0];
          if (!file) {
               alert('Please select a QR code image first');
               return;
          }
          
          processQrBtn.disabled = true;
          processQrBtn.innerHTML = `
               <svg class="animate-spin -ml-1 mr-2 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
               </svg>
               Processing...
          `;

          try {
               const qrData = await readQRCodeFromImage(file);
               
               const response = await fetch('/check-in/process-qr-upload', {
                    method: 'POST',
                    headers: {
                         'Content-Type': 'application/json',
                         'Accept': 'application/json',
                         'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ qr_data: qrData })
               });

               const data = await response.json();
               
               if (response.status === 409) {
                    const shouldRedirect = confirm(
                         data.message || "This QR code has already been used. Do you want to view details?"
                    );
                    
                    if (shouldRedirect && data.qr_path) {
                         window.location.href = `/check-in/used?path=${encodeURIComponent(data.qr_path)}`;
                    } else {
                         resetQRUploadForm();
                    }
                    return;
               }
               
               if (!response.ok) {
                    throw new Error(data.message || 'Server verification failed');
               }
               
               if (data.success) {
                    window.location.href = `/check-in/success/${data.payment_id}`;
               } else {
                    throw new Error(data.message || 'QR verification failed');
               }
          } catch (error) {
               console.error('Error:', error);
               alert(error.message || 'An error occurred while processing the QR code');
          } finally {
               resetQRUploadForm();
          }
     });

     function resetQRUploadForm() {
          processQrBtn.disabled = false;
          processQrBtn.innerHTML = `
               <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10" />
               </svg>
               Process QR Code
          `;
          qrUploadInput.value = '';
          fileNameSpan.textContent = '';
          qrImagePreview.src = '';
          qrImagePreview.classList.add('hidden');
          document.getElementById('upload-instructions').classList.remove('hidden');
     }

     // Checkout QR Upload and Processing
     const checkoutQrUploadInput = document.getElementById('checkout-qr-upload-input');
     const checkoutQrUploadPreview = document.getElementById('checkout-qr-upload-preview');
     const checkoutQrImagePreview = document.getElementById('checkout-qr-image-preview');
     const checkoutFileNameSpan = document.getElementById('checkout-file-name');
     const checkoutProcessQrBtn = document.getElementById('checkout-process-qr-btn');
     
     checkoutQrUploadInput.addEventListener('change', function(e) {
          const file = e.target.files[0];
          if (!file) return;
          
          checkoutFileNameSpan.textContent = file.name;
          checkoutQrUploadPreview.classList.remove('hidden');
          document.getElementById('checkout-upload-instructions').classList.add('hidden');
          
          if (file.type.startsWith('image/')) {
               const reader = new FileReader();
               reader.onload = function(e) {
                    checkoutQrImagePreview.src = e.target.result;
                    checkoutQrImagePreview.classList.remove('hidden');
               };
               reader.readAsDataURL(file);
          }
     });

     checkoutProcessQrBtn.addEventListener('click', async function() {
          const file = checkoutQrUploadInput.files[0];
          if (!file) {
               alert('Please select a QR code image first');
               return;
          }
          
          checkoutProcessQrBtn.disabled = true;
          checkoutProcessQrBtn.innerHTML = `
               <svg class="animate-spin -ml-1 mr-2 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
               </svg>
               Processing...
          `;

          try {
               const qrData = await readQRCodeFromImage(file);
               
               const response = await fetch('/check-out/process-qr-upload', {
                    method: 'POST',
                    headers: {
                         'Content-Type': 'application/json',
                         'Accept': 'application/json',
                         'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ qr_data: qrData })
               });
               
               const data = await response.json();
               
               if (!response.ok) {
                    throw new Error(data.message || 'Server verification failed');
               }
               
               if (data.success) {
                    window.location.href = `/check-out/process/${data.payment_id}`;
               } else {
                    throw new Error(data.message || 'QR verification failed');
               }
          } catch (error) {
               console.error('Error:', error);
               alert(error.message || 'An error occurred while processing the QR code');
          } finally {
               resetCheckoutQRUploadForm();
          }
     });
     
     function resetCheckoutQRUploadForm() {
          checkoutProcessQrBtn.disabled = false;
          checkoutProcessQrBtn.innerHTML = `
               <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10" />
               </svg>
               Process QR Code
          `;
          checkoutQrUploadInput.value = '';
          checkoutFileNameSpan.textContent = '';
          checkoutQrImagePreview.src = '';
          checkoutQrImagePreview.classList.add('hidden');
          document.getElementById('checkout-upload-instructions').classList.remove('hidden');
     }

     // Day Tour Form Submission
     document.getElementById('dayTourForm').addEventListener('submit', function(e) {
          e.preventDefault();
          
          const tourOption = document.querySelector('input[name="tourOption"]:checked').value;
          const formData = {
               firstName: document.getElementById('firstName').value,
               lastName: document.getElementById('lastName').value,
               phoneNumber: document.getElementById('phoneNumber').value,
               email: document.getElementById('email').value,
               tourOption: tourOption,
               ...(tourOption !== 'park' && {
                    poolAdults: document.getElementById('poolAdultCount').value,
                    poolKids: document.getElementById('poolKidCount').value,
                    poolSeniors: document.getElementById('poolSeniorCount').value
               }),
               ...(tourOption !== 'pool' && {
                    parkAdults: document.getElementById('parkAdultCount').value,
                    parkKids: document.getElementById('parkKidCount').value,
                    parkSeniors: document.getElementById('parkSeniorCount').value
               }),
               ...(tourOption !== 'park' && {
                    selectedCottages: Array.from(document.querySelectorAll('input[name="cottages[]"]:checked')).map(el => el.value)
               }),
               totalAmount: document.getElementById('finalTotal').textContent
          };
          
          console.log('Form submitted:', formData);
          alert('Day tour registration submitted successfully!');
          closeDayTourModal();
     });

     // Set up event listeners for tour option changes
     document.querySelectorAll('input[name="tourOption"]').forEach(radio => {
          radio.addEventListener('change', calculateTotals);
     });

     // Set up event listeners for input changes in day tour form
     document.getElementById('poolAdultCount').addEventListener('change', calculateTotals);
     document.getElementById('poolKidCount').addEventListener('change', calculateTotals);
     document.getElementById('poolSeniorCount').addEventListener('change', calculateTotals);
     document.getElementById('parkAdultCount').addEventListener('change', calculateTotals);
     document.getElementById('parkKidCount').addEventListener('change', calculateTotals);
     document.getElementById('parkSeniorCount').addEventListener('change', calculateTotals);
});
</script>
@endsection