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
          height: 400px;
          position: relative;
          background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
          border-radius: 16px;
          padding: 20px;
          box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
          overflow: hidden;
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

     /* Status Cards - High Contrast Version */
     .status-card {
          background: white;
          border-radius: 12px;
          padding: 1.5rem;
          position: relative;
          overflow: hidden;
          transition: all 0.2s ease;
     }

     .status-card::before {
          content: '';
          position: absolute;
          top: 0;
          left: 0;
          width: 5px;
          height: 100%;
     }

     .status-card.total-bookings::before {
          background: #2563EB;
          /* Vibrant blue */
     }

     .status-card.checked-out::before {
          background: #d91406;
          /* Vibrant amber */
     }

     .status-card.status-card.pending-confirmations::before {
          background: #d98c06;
          /* Vibrant amber */
     }

     .status-card.awaiting-payments::before {
          background: #7C3AED;
          /* Vibrant violet */
     }

     .status-card.verified-bookings::before {
          background: #059669;
          /* Vibrant emerald */
     }

     .status-card .card-content {
          display: flex;
          justify-content: space-between;
          align-items: flex-start;
     }

     .status-card .text-content {
          flex: 1;
     }

     .status-card .icon-wrapper {
          width: 2.75rem;
          height: 2.75rem;
          border-radius: 12px;
          display: flex;
          align-items: center;
          justify-content: center;
          margin-left: 1rem;
     }

     .status-card.total-bookings .icon-wrapper {
          background: #EFF6FF;
     }

     .status-card.pending-confirmations .icon-wrapper {
          background: #FFFBEB;
     }

     .status-card.awaiting-payments .icon-wrapper {
          background: #F5F3FF;
     }

     .status-card.verified-bookings .icon-wrapper {
          background: #ECFDF5;
     }

     .status-card .stat-value {
          font-size: 1.875rem;
          font-weight: 700;
          line-height: 1;
          margin: 0.5rem 0 0.25rem;
          font-family: 'Inter', sans-serif;
     }

     .status-card.total-bookings .stat-value {
          color: #1E40AF;
     }

     .status-card.pending-confirmations .stat-value {
          color: #92400E;
     }

     .status-card.awaiting-payments .stat-value {
          color: #5B21B6;
     }

     .status-card.verified-bookings .stat-value {
          color: #065F46;
     }

     .status-card .stat-label {
          font-size: 0.875rem;
          font-weight: 500;
          color: #4B5563;
          letter-spacing: 0.025em;
     }

     .status-card .stat-change {
          font-size: 0.75rem;
          font-weight: 500;
          display: flex;
          align-items: center;
          margin-top: 0.25rem;
     }

     .status-card.total-bookings .stat-change {
          color: #1E40AF;
     }

     .status-card.checked-out .stat-change {
          color: #b40909;
     }

     .status-card.pending-confirmations .stat-change {
          color: #b46a09;
     }

     .status-card.awaiting-payments .stat-change {
          color: #6D28D9;
     }

     .status-card.verified-bookings .stat-change {
          color: #047857;
     }

     .status-card .stat-change svg {
          width: 0.875rem;
          height: 0.875rem;
          margin-right: 0.25rem;
     }

     .stats-container {
          display: flex;
          justify-content: space-around;
          flex-wrap: wrap;
          padding: 20px;
          background: #f8f9fa;
          border-bottom: 1px solid #eaeaea;
     }

     .stat-box {
          background: white;
          border-radius: 12px;
          padding: 20px;
          margin: 10px;
          min-width: 200px;
          text-align: center;
          box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
          flex: 1;
     }

     .stat-box h3 {
          font-size: 16px;
          color: #6c757d;
          margin-bottom: 10px;
     }

     .stat-value {
          font-size: 24px;
          font-weight: 700;
          color: #4b6cb7;
     }

     .stat-box.best-month {
          background: linear-gradient(90deg, #4b6cb7 0%, #182848 100%);
          color: white;
     }

     .stat-box.best-month h3,
     .stat-box.best-month .stat-value {
          color: white;
     }

     .loading {
          display: flex;
          justify-content: center;
          align-items: center;
          height: 300px;
          flex-direction: column;
     }

     .spinner {
          width: 50px;
          height: 50px;
          border: 5px solid rgba(75, 108, 183, 0.2);
          border-radius: 50%;
          border-top-color: #4b6cb7;
          animation: spin 1s linear infinite;
          margin-bottom: 20px;
     }

     @keyframes spin {
          0% {
               transform: rotate(0deg);
          }

          100% {
               transform: rotate(360deg);
          }
     }

     .error-message {
          text-align: center;
          padding: 30px;
          color: #e74c3c;
          display: none;
     }
</style>
@endsection

@section('content')
<div class="min-h-screen px-6 py-6">
     <!-- Header with animated gradient -->
     <div class="rounded-lg mb-4 overflow-hidden">
          <div class="bg-gradient-to-r from-red-800 to-red-700 p-8 text-white rounded-lg relative overflow-hidden">
               <div class="absolute inset-0 bg-gradient-to-br from-white/10 to-white/0"></div>
               <div class="relative z-10">
                    <div class="flex items-center justify-between">
                         <div>
                              <h1 class="text-3xl font-bold">Dashboard Overview</h1>
                              @auth
                              <p class="opacity-90 mt-2">
                                   Welcome back, {{ auth()->user()->firstname }}! Here's what's happening today.
                              </p>
                              @endauth
                         </div>
                         <div class="flex items-center space-x-4">
                              <!-- Active Host Toggle with Indicator -->
                              <div class="flex flex-col items-end">
                                   <div class="flex items-center mb-1">
                                        <label for="activeHost" class="mr-2 text-sm font-medium">Active Admin</label>
                                        <label class="relative inline-flex items-center cursor-pointer">
                                             <input type="checkbox" id="activeHost" class="sr-only peer"
                                                  onchange="toggleActiveHost(this)" {{ auth()->user()->is_active ?
                                             'checked' : '' }}>
                                             <div class="w-11 h-6 bg-gray-300 peer-focus:outline-none rounded-full peer 
                                                  peer-checked:bg-green-500 after:content-[''] after:absolute 
                                                  after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 
                                                  after:border after:rounded-full after:h-5 after:w-5 
                                                  after:transition-all peer-checked:after:translate-x-full"></div>
                                        </label>
                                   </div>
                                   <div id="hostStatusIndicator"
                                        class="text-xs font-medium px-2 py-1 rounded-full 
                                   {{ auth()->user()->is_active ? 'bg-green-100/20 text-green-100' : 'bg-gray-100/20 text-gray-200' }}">
                                        {{ auth()->user()->is_active ?
                                        '✓ Booking email notifications are enabled.' :
                                        '✗ Booking email notifications are turned off.' }}
                                   </div>
                              </div>

                              <!-- User Icon -->
                              <div class="bg-white/20 p-3 rounded-lg backdrop-blur-sm">
                                   <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                             d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                   </svg>
                              </div>
                         </div>
                    </div>

                    <!-- Date and Time -->
                    <div class="mt-6 flex items-center space-x-2">
                         <span class="px-3 py-1 bg-white/20 rounded-full text-sm font-medium">
                              @php echo \Carbon\Carbon::now('Asia/Manila')->format('l, F j, Y'); @endphp
                         </span>
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

     <!-- Status Cards -->
     <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">

          <!-- Total Bookings -->
          <div class="status-card total-bookings border border-lightgray">
               <div class="card-content">
                    <div class="text-content">
                         <p class="stat-label">Total Bookings</p>
                         <h3 class="stat-value" id="total-bookings">0</h3>
                         <p class="stat-change">
                              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                   stroke="currentColor">
                                   <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                              </svg>
                              +5 This Week
                         </p>
                    </div>
                    <div class="icon-wrapper">
                         <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-600" fill="none"
                              viewBox="0 0 24 24" stroke="currentColor">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                   d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                         </svg>
                    </div>
               </div>
          </div>

          <!-- Pending Confirmations -->
          <div class="status-card pending-confirmations border border-lightgray">
               <div class="card-content">
                    <div class="text-content">
                         <p class="stat-label">Pending Confirmations</p>
                         <h3 class="stat-value" id="total-pending">8</h3>
                         <p class="stat-change">
                              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                   stroke="currentColor">
                                   <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                              </svg>
                              Awaiting response
                         </p>
                    </div>
                    <div class="icon-wrapper">
                         <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-amber-600" fill="none"
                              viewBox="0 0 24 24" stroke="currentColor">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                   d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                         </svg>
                    </div>
               </div>
          </div>

          <div class="status-card awaiting-payments border border-lightgray">
               <div class="card-content">
                    <div class="text-content">
                         <p class="stat-label">Checked In</p>
                         <h3 class="stat-value" id="checked-in-total">0</h3>
                         <p class="stat-change">
                              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                   stroke="currentColor">
                                   <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 9.75L12 4l9 5.75V20a1 1 0 01-1 1h-5a1 1 0 01-1-1v-5H9v5a1 1 0 01-1 1H3a1 1 0 01-1-1V9.75z" />
                              </svg>
                              Customer In-House
                         </p>
                    </div>
                    <div class="icon-wrapper">
                         <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-violet-600" fill="none"
                              viewBox="0 0 24 24" stroke="currentColor">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                   d="M3 12l9-9 9 9M4 10v10h16V10" />
                         </svg>
                    </div>

               </div>
          </div>

          <div class="status-card checked-out border border-lightgray">
               <div class="card-content">
                    <div class="text-content">
                         <p class="stat-label">Checked Out</p>
                         <h3 class="stat-value" id="total-checked-out">8</h3>
                         <p class="stat-change">
                              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline mr-1" fill="none"
                                   viewBox="0 0 24 24" stroke="currentColor">
                                   <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h6a2 2 0 012 2v1" />
                              </svg>
                              Customer Checked-Out
                         </p>
                    </div>
                    <div class="icon-wrapper bg-gray-50">
                         <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-red-600" fill="none"
                              viewBox="0 0 24 24" stroke="currentColor">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                   d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h6a2 2 0 012 2v1" />
                         </svg>
                    </div>
               </div>
          </div>
     </div>

     <!-- Main Content Grid -->
     <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">

          <!-- Middle Column (Revenue & Chart) -->
          <div class="p-6 bg-white rounded-lg border border-gray-200">
               <div class="error-message" id="error-message" style="display:none;">
                    <h3>Unable to load data</h3>
                    <p>Please check your connection and try again</p>
                    <button onclick="loadData()" class="mt-4 px-4 py-2 bg-blue-600 text-white rounded-lg">Retry</button>
               </div>
               
               <div class="chart-container">
                    <div class="loading" id="loading">
                         <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-red-600 mx-auto"></div>
                         <p>Loading income data...</p>
                    </div>
                    <canvas id="incomeChart"></canvas>
               </div>
          </div>

          <!--Facilities Occupied Today section  -->
          <div class="lg:col-span-2 p-6 bg-white rounded-lg border border-gray-200">
               <div class="flex justify-between items-center mb-6">
                    <h2 class="text-xl font-semibold text-gray-800">Facilities Occupied Today</h2>
                    <div class="flex items-center">
                         <span class="text-sm text-gray-500 mr-3">
                              @php
                              $today = \Carbon\Carbon::now('Asia/Manila')->toDateString();
                              echo \Carbon\Carbon::now('Asia/Manila')->format('F j, Y');
                              @endphp
                         </span>
                         <button onclick="loadRoomMonitoring()" class="text-gray-500 hover:text-red-600">
                              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                                   stroke="currentColor">
                                   <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                              </svg>
                         </button>
                    </div>
               </div>

               <!-- Room monitoring grid -->
               <div id="room-grid" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4">
                    <!-- JS will render here -->
                    <div class="col-span-full text-center py-8">
                         <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-red-600 mx-auto"></div>
                         <p class="mt-2 text-gray-500">Loading...</p>
                    </div>
               </div>
          </div>
     </div>

     <!-- Bottom Grid -->
     <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
          <!-- Left Column (Next Check-in) -->
          <div class="p-6 bg-white rounded-lg border border-gray-200">
               <!-- Next Check-in Section -->
               <div class="glass-card">
                    <div class="flex items-center justify-between mb-4">
                         <h3 class="text-lg font-semibold text-gray-800">Next Check-in</h3>
                         <a href="{{ route('incoming.list') }}">
                              <div
                                   class="bg-red-100 text-red-800 text-xs font-medium px-2.5 py-1 rounded-full animate-pulse flex items-center cursor-pointer">
                                   <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                             d="M5 10l7-7m0 0l7 7m-7-7v18" />
                                   </svg>
                                   Upcoming
                              </div>
                         </a>
                    </div>
                    <p class="text-gray-600 mb-4" id="next-checkin-time">Loading...</p>

                    <div class="bg-gradient-to-r from-red-50 to-red-100 p-4 rounded-lg border border-red-100">
                         <div class="flex items-center mb-2">
                              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-red-500 mr-2"
                                   viewBox="0 0 20 20" fill="currentColor">
                                   <path fill-rule="evenodd"
                                        d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z"
                                        clip-rule="evenodd" />
                              </svg>
                              <p class="font-medium text-gray-800" id="next-checkin-date">-</p>
                         </div>
                         <div class="flex items-center mb-2">
                              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-red-500 mr-2"
                                   viewBox="0 0 20 20" fill="currentColor">
                                   <path fill-rule="evenodd"
                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z"
                                        clip-rule="evenodd" />
                              </svg>
                              <p class="text-gray-600" id="next-checkin-nights">-</p>
                         </div>
                         <div class="flex items-center mb-2">
                              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-red-500 mr-2"
                                   viewBox="0 0 20 20" fill="currentColor">
                                   <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z"
                                        clip-rule="evenodd" />
                              </svg>
                              <p class="font-medium text-gray-800" id="next-checkin-guest">-</p>
                         </div>
                         <div class="flex items-center">
                              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-red-500 mr-2"
                                   viewBox="0 0 20 20" fill="currentColor">
                                   <path
                                        d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z" />
                              </svg>
                              <p class="text-gray-600" id="next-checkin-phone">-</p>
                         </div>
                         <div class="flex items-center mt-2">
                              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-red-500 mr-2"
                                   viewBox="0 0 20 20" fill="currentColor">
                                   <path fill-rule="evenodd"
                                        d="M9 2a1 1 0 00-1 1v1H5a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"
                                        clip-rule="evenodd" />
                              </svg>
                              <p class="font-medium text-gray-800" id="next-checkin-booking-code">-</p>
                         </div>
                    </div>
               </div>
          </div>

          <!-- Admins -->
          <div class="p-6 bg-white rounded-lg border border-gray-200" id="active-admins-container">
               <div class="flex justify-between items-center mb-4">
                    <h2 class="text-xl font-semibold text-gray-800">Admin</h2>
                    <button onclick="fetchActiveAdmins()" class="text-gray-500 hover:text-red-600">
                         <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                              stroke="currentColor">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                   d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                         </svg>
                    </button>
               </div>
               <div id="active-admins-list" class="overflow-y-auto max-h-64 space-y-4 pr-2">
                    <!-- Loading state -->
                    <div class="text-center py-8">
                         <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-red-600 mx-auto"></div>
                         <p class="mt-2 text-gray-500">Loading active admins...</p>
                    </div>
               </div>
          </div>


          <!-- Recent Enquiries -->
          <div class="h-card h-card--no-header h-py-8 h-mb-24 h-mr-8 p-6 bg-white rounded-lg border border-gray-200">

               <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center mb-6 gap-3 sm:gap-0">
                    <!-- Title + Badge -->
                    <div class="flex items-center">
                         <h2 class="text-lg sm:text-xl font-semibold text-gray-800">Recent Inquiries</h2>
                         <span class="ml-2 bg-red-500 text-white 
                                   text-[10px] sm:text-xs md:text-sm 
                                   font-bold 
                                   px-1.5 sm:px-2 md:px-3 
                                   py-0.5 sm:py-1 md:py-1.5 
                                   rounded-full 
                                   new-inquiries-count hidden">
                              0 new
                         </span>
                    </div>

                    <!-- Actions -->
                    <div class="flex flex-wrap gap-2 sm:gap-3">
                         <a href="{{ route('admin.inquiries') }}" class="text-sm text-red-600 hover:text-red-800">View
                              All</a>
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
                         placeholder="Search by id, reference, or name..." onkeyup="filterInquiries()">
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

     </div>




     <meta name="csrf-token" content="{{ csrf_token() }}">
     @endsection

     @section('content_js')
     <script src="https://unpkg.com/html5-qrcode@2.3.4/html5-qrcode.min.js"></script>
     <script src="https://cdn.jsdelivr.net/npm/jsqr@1.4.0/dist/jsQR.min.js"></script>
     <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
     <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.0.0"></script>

     <script>
     function toggleActiveHost(checkbox) {
          const isActive = checkbox.checked;
          const indicator = document.getElementById('hostStatusIndicator');
          
          // Send AJAX request to update host status
          fetch('/host/status', {
               method: 'POST',
               headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
               },
               body: JSON.stringify({ is_active: isActive })
          })
          .then(response => {
               if (!response.ok) {
                    throw new Error('Network response was not ok');
               }
               return response.json();
          })
          .then(data => {
               // Update indicator
               if(data.is_active) {
                    indicator.textContent = '✓ Receiving booking notifications';
                    indicator.classList.remove('bg-gray-100/20', 'text-gray-200');
                    indicator.classList.add('bg-green-100/20', 'text-green-100');
               } else {
                    indicator.textContent = '✗ Not accepting bookings notification';
                    indicator.classList.remove('bg-green-100/20', 'text-green-100');
                    indicator.classList.add('bg-gray-100/20', 'text-gray-200');
               }
          })
          .catch(error => {
               console.error('Error:', error);
               // Revert checkbox if update failed
               checkbox.checked = !isActive;
          });
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

     function fetchActiveAdmins() {
          // Show loading state
          document.getElementById('active-admins-list').innerHTML = `
               <div class="text-center py-4">
                    <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-red-600 mx-auto"></div>
                    <p class="mt-2 text-gray-500">Loading active admins...</p>
               </div>
          `;
          
          // Make AJAX request
          fetch(`/admin/active-admins`, {
               headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
               }
          })
          .then(response => response.json())
          .then(data => {
               if(data.length > 0) {
                    let html = '';
                    data.forEach(admin => {
                         html += `
                         <div class="flex items-center p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors duration-200">
                              <div class="relative flex-shrink-0">
                                   <img src="{{ url('imgs/profiles') }}/${admin.profile_img}" 
                                        alt="${admin.fullname}" 
                                        class="w-10 h-10 rounded-full object-cover border-2 border-white shadow-sm">
                                   ${admin.is_active ? `
                                        <span class="absolute bottom-0 right-0 w-3 h-3 bg-green-500 rounded-full border-2 border-white"></span>
                                   `: ''}
                              </div>
                              <div class="ml-3 overflow-hidden">
                                   <h3 class="font-medium text-gray-800 truncate">${admin.fullname}</h3>
                                   <p class="text-sm text-gray-600 flex items-center mt-1">
                                        ${admin.phone ? `
                                             <svg class="w-3.5 h-3.5 flex-shrink-0 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                             </svg>
                                             <span class="truncate">${admin.phone}</span>
                                        ` : `
                                             <svg class="w-3.5 h-3.5 flex-shrink-0 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                             </svg>
                                             <span class="truncate">${admin.email}</span>
                                        `}
                                   </p>
                              </div>
                         </div>
                         `;
                    });
                    document.getElementById('active-admins-list').innerHTML = html;
               } else {
                    document.getElementById('active-admins-list').innerHTML = `
                         <div class="text-center py-4 text-gray-500">
                              <svg class="w-8 h-8 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"></path>
                              </svg>
                              No active admins at the moment
                         </div>
                    `;
               }
          })
          .catch(error => {
               console.error('Error fetching active admins:', error);
               document.getElementById('active-admins-list').innerHTML = `
                    <div class="text-center py-4 text-red-500">
                         <svg class="w-8 h-8 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                         </svg>
                         Failed to load active admins
                    </div>
               `;
          });
     }

     async function fetchRooms() {
          try {
               const response = await fetch("{{ route('monitor.room.data') }}", {
                    headers: {
                         "X-Requested-With": "XMLHttpRequest",
                         "Content-Type": "application/json",
                         "Accept": "application/json",
                         "X-CSRF-TOKEN": "{{ csrf_token() }}"
                    }
               });

               if (!response.ok) throw new Error("Network error");

               const data = await response.json();
               renderRooms(data);
          } catch (err) {
               console.error("Fetch error:", err);
               document.getElementById('room-grid').innerHTML = `
                    <div class="col-span-full text-center py-4 text-red-500">
                         <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                         </svg>
                         <p>Failed to load room status</p>
                         <button onclick="fetchRooms()" class="mt-2 text-sm text-red-600 hover:text-red-800">Retry</button>
                    </div>
               `;
          }
     }

     function renderRooms(data) {
          const grid = document.getElementById("room-grid");
          grid.innerHTML = ""; // clear old content
          
          if (!data.facilities || data.facilities.length === 0) {
               grid.innerHTML = `
                    <div class="col-span-full text-center py-8">
                         <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-gray-400 mx-auto" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                   d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 
                                   9 9 0 0118 0z" />
                         </svg>
                         <p class="mt-2 text-gray-600">No facilities found</p>
                    </div>
               `;
               return;
          }
          
          data.facilities.forEach(facility => {
               const unavailable = data.unavailableDates[facility.id] || [];
               const today = data.today;

               const isOccupied = unavailable.some(date =>
                    today >= date.checkin_date && today < date.checkout_date
               );

               const div = document.createElement("div");
               div.className = `
                    p-3 rounded-lg shadow text-center text-sm
                    ${isOccupied ? 'bg-red-500 text-white' : 'bg-blue-50 text-gray-700'}
               `;
               div.innerHTML = `
                    <h3 class="font-semibold ${isOccupied ? 'text-white' : 'text-gray-700'}">
                         ${facility.name}
                    </h3>
                    <p class="text-xs italic ${isOccupied ? 'text-gray-100' : 'text-gray-500'}">
                         ${facility.category ?? 'No Category'}
                    </p>
                    <p class="mt-1 font-medium ${isOccupied ? 'text-white' : 'text-gray-600'}">
                         ${isOccupied ? 'Occupied' : 'Available'}
                    </p>
               `;
               
               // Optional click event
               div.addEventListener('click', () => {
                    console.log('Facility clicked:', facility);
               });
               
               grid.appendChild(div);
          });
     }
     

     function loadRoomMonitoring() {
          // Show loading state
          document.getElementById('room-grid').innerHTML = `
               <div class="col-span-full text-center py-8">
                    <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-red-600 mx-auto"></div>
                    <p class="mt-2 text-gray-500">Refreshing room status...</p>
               </div>
          `;
          fetchRooms();
     }
     

     // Document Ready Function
     document.addEventListener('DOMContentLoaded', function() {
          fetchRooms();
          loadData();
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
               document.getElementById('total-bookings').textContent = data.total_booking;
               document.getElementById('total-pending').textContent = data.pending;
               document.getElementById('checked-in-total').textContent = data.checked_in_total;
               document.getElementById('total-checked-out').textContent = data.total_checked_out;
          })
          .catch(error => {
               console.error(`Fetching error:`, error); 
          });
          
          // Load initial data
          loadNextCheckin();
          fetchActiveAdmins();
          

          // Set up periodic refreshes
          setInterval(loadNextCheckin, 300000);
          setInterval(fetchActiveAdmins, 300000);
          
          // Set up event listeners for all View buttons
          document.querySelectorAll('[data-id]').forEach(button => {
               button.addEventListener('click', function() {
                    const inquirerId = this.getAttribute('data-id');
                    openModal_accept_inquirer(this);
               });
          });
          // QR Upload and Processing for Check-in
          const qrUploadInput = document.getElementById('qr-upload-input');
          const qrUploadPreview = document.getElementById('qr-upload-preview');
          const qrImagePreview = document.getElementById('qr-image-preview');
          const fileNameSpan = document.getElementById('file-name');
          const processQrBtn = document.getElementById('process-qr-btn');

          let incomeChart;

          function formatCurrency(amount) {
               return '₱' + parseFloat(amount || 0).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
          }

          function loadData() {
               // Show loading, hide error
               document.getElementById('loading').style.display = 'flex';
               document.getElementById('error-message').style.display = 'none';
               
               // Make AJAX request to Laravel API endpoint
               fetch('/api/monthly-income')
                    .then(response => {
                         if (!response.ok) {
                              throw new Error('Network response was not ok');
                         }
                         return response.json();
                    })
                    .then(data => {
                         // Hide loading
                         document.getElementById('loading').style.display = 'none';
 
                         // Create or update chart - ensure data has the correct structure
                         if (data.chartData && data.chartData.labels && data.chartData.datasets) {
                              createOrUpdateChart(data.chartData);
                         } else {
                              console.error('Invalid chart data structure:', data);
                              document.getElementById('error-message').style.display = 'block';
                              document.getElementById('error-message').innerHTML = `
                                   <h3>Invalid chart data</h3>
                                   <p>Please check the API response format</p>
                                   <button onclick="loadData()" style="margin-top: 15px; padding: 8px 15px; background: #4b6cb7; color: white; border: none; border-radius: 5px; cursor: pointer;">Retry</button>
                              `;
                         }
                    })
                    .catch(error => {
                         console.error('Error fetching data:', error);
                         document.getElementById('loading').style.display = 'none';
                         document.getElementById('error-message').style.display = 'block';
                    });
               }
          // Create or update the chart
          function createOrUpdateChart(chartData) {
               const ctx = document.getElementById('incomeChart').getContext('2d');
               
               // If chart already exists, destroy it
               if (incomeChart) {
                    incomeChart.destroy();
               }
               
               // Create new chart
               incomeChart = new Chart(ctx, {
                    type: 'line',
                    data: chartData,
                    options: {
                         responsive: true,
                         maintainAspectRatio: false,
                         plugins: {
                         legend: {
                              position: 'top',
                         },
                         tooltip: {
                              mode: 'index',
                              intersect: false,
                              callbacks: {
                                   label: function(context) {
                                        return `Income: ${formatCurrency(context.raw)}`;
                                   }
                              }
                         }
                         },
                         scales: {
                         y: {
                              beginAtZero: true,
                              grid: {
                                   color: 'rgba(0, 0, 0, 0.05)'
                              },
                              ticks: {
                                   callback: function(value) {
                                        return formatCurrency(value);
                                   }
                              }
                         },
                         x: {
                              grid: {
                                   display: false
                              }
                         }
                         }
                    }
               });
          }

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
                         window.location.href = `/check-out/receipt/${data.payment_id}`;
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
          const dayTourForm = document.getElementById('dayTourForm');
          
          if (dayTourForm) {
               dayTourForm.addEventListener('submit', async function(e) {
                    e.preventDefault();
                    
                    const submitButton = e.target.querySelector('button[type="submit"]');
                    const originalButtonText = submitButton.innerHTML;
                    
                    try {
                         // Disable button and show loading state
                         submitButton.disabled = true;
                         submitButton.innerHTML = `
                              <div class="flex items-center">
                              <svg class="animate-spin -ml-1 mr-2 h-5 w-5 text-white"
                                   xmlns="http://www.w3.org/2000/svg"
                                   fill="none"
                                   viewBox="0 0 24 24">
                                   <circle class="opacity-25" cx="12" cy="12" r="10"
                                        stroke="currentColor" stroke-width="4"></circle>
                                   <path class="opacity-75" fill="currentColor"
                                        d="M4 12a8 8 0 018-8V0C5.373 0 
                                        0 5.373 0 12h4zm2 5.291A7.962 7.962 
                                        0 014 12H0c0 3.042 1.135 5.824 3 
                                        7.938l3-2.647z"></path>
                              </svg>
                              Processing...
                              </div>
                         
                         `;

                         const tourOption = document.querySelector('input[name="tourOption"]:checked').value;
                         
                         // Collect cottage data
                         const cottages = [];
                         document.querySelectorAll('input[id^="cottage_qty_"]').forEach(input => {
                              const cottageId = input.id.replace('cottage_qty_', '');
                              const quantity = parseInt(input.value) || 0;
                              if (quantity > 0) {
                                   cottages.push({
                                        id: cottageId,
                                        quantity: quantity
                                   });
                              }
                         });

                         const formData = {
                              first_name: document.getElementById('firstName').value,
                              last_name: document.getElementById('lastName').value,
                              phone: document.getElementById('phoneNumber').value,
                              email: document.getElementById('email').value || null,
                              tour_type: tourOption,
                              pool_adults: tourOption !== 'park' ? document.getElementById('poolAdultCount').value : 0,
                              pool_kids: tourOption !== 'park' ? document.getElementById('poolKidCount').value : 0,
                              pool_seniors: tourOption !== 'park' ? document.getElementById('poolSeniorCount').value : 0,
                              park_adults: tourOption !== 'pool' ? document.getElementById('parkAdultCount').value : 0,
                              park_kids: tourOption !== 'pool' ? document.getElementById('parkKidCount').value : 0,
                              park_seniors: tourOption !== 'pool' ? document.getElementById('parkSeniorCount').value : 0,
                              cottages: cottages,
                              total_amount: document.getElementById('finalTotal').textContent
                         };

                         // Send data to server
                         const response = await fetch('/day-tour/register', {
                              method: 'POST',
                              headers: {
                                   'Content-Type': 'application/json',
                                   'Accept': 'application/json',
                                   'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                              },
                              body: JSON.stringify(formData)
                         });
                         
                         const data = await response.json();
                         
                         if (!response.ok) {
                              throw new Error(data.message || 'Registration failed');
                         }

                         // Show success message
                         alert('Day tour registration successful!');
                         
                         // Close modal and reset form
                         closeDayTourModal();
                         
                         // Optionally redirect to receipt
                         if (data.booking_id) {
                              window.location.href = `/day-tour/receipt/${data.booking_id}`;
                         }

                    } catch (error) {
                         console.error('Registration error:', error);
                         alert(error.message || 'An error occurred during registration');
                    } finally {
                         // Reset button state
                         submitButton.disabled = false;
                         submitButton.innerHTML = originalButtonText;
                    }
               });
          }

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