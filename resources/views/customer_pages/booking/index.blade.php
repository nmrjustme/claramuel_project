@extends('layouts.bookings')
@section('title', 'Accommodations')
@section('bookings')
     <style>
          @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');

          /* Netflix-inspired category styling */
          .category-container {
               margin-bottom: 3rem;
               position: relative;
          }

          .category-title {
               margin-bottom: 1.5rem;
               color: #1F2937;
               padding-left: 0;
               position: relative;
               display: inline-block;
               font-size: clamp(1.25rem, 4vw, 1.75rem);
          }

          .category-title:after {
               content: '';
               position: absolute;
               bottom: -0.5rem;
               left: 0;
               width: 100%;
               height: 4px;
               background: linear-gradient(90deg, #DC2626 0%, rgba(220, 38, 38, 0.2) 100%);
               border-radius: 4px;
          }

          /* Netflix-style scrolling container */
          .rooms-scroll-container {
               display: flex;
               overflow-x: auto;
               gap: 1rem;
               padding: 1rem 0.5rem 2rem;
               scrollbar-width: none;
               -webkit-overflow-scrolling: touch;
               scroll-snap-type: x proximity;
               scroll-padding: 0 1rem;
               position: relative;
               margin: 0 -1.5rem;
          }

          @media (min-width: 640px) {
               .rooms-scroll-container {
                    gap: 1.5rem;
                    padding: 1.5rem 1rem 2rem;
                    margin: 0 -1rem;
               }
          }

          .rooms-scroll-container::-webkit-scrollbar {
               display: none;
          }

          /* Netflix-style room cards */
          .room-card {
               transition: all 0.3s ease;
               min-width: min(300px, 85vw);
               /* Responsive min-width */
               width: min(300px, 85vw);
               /* Responsive width */
               flex: 0 0 auto;
               border-radius: 1rem;
               overflow: visible;
               background: white;
               scroll-snap-align: start;
               position: relative;
               margin-bottom: 1rem;
          }

          @media (min-width: 480px) {
               .room-card {
                    min-width: min(340px, 80vw);
                    width: min(340px, 80vw);
               }
          }

          @media (min-width: 768px) {
               .room-card {
                    min-width: min(380px, 40vw);
                    width: min(380px, 40vw);
               }
          }

          @media (min-width: 1024px) {
               .room-card {
                    min-width: 380px;
                    width: 380px;
               }
          }

          .room-card.selected {
               border-color: #DC2626;
               background-color: #FEF2F2;
               box-shadow: 0 10px 25px rgba(220, 38, 38, 0.15);
          }

          /* Enhanced image container with Netflix-style hover */
          .room-image-container {
               position: relative;
               overflow: hidden;
               border-radius: 0.75rem 0.75rem 0 0;
               aspect-ratio: 4/3;
               /* Better aspect ratio for responsiveness */
               width: 100%;
          }

          .room-image-container img {
               width: 100%;
               height: 100%;
               object-fit: cover;
               transition: transform 0.5s ease;
          }

          .room-card:hover .room-image-container img {
               transform: scale(1.05);
          }

          .scroll-arrows {
               position: absolute;
               top: 50%;
               left: 0;
               right: 0;
               transform: translateY(-50%);
               display: flex;
               justify-content: space-between;
               pointer-events: none;
               z-index: 5;
               height: 0;
               padding: 0 1rem;
          }

          .scroll-arrow {
               position: relative;
               background-color: rgba(255, 255, 255, 0.95);
               color: #DC2626;
               border: none;
               border-radius: 50%;
               width: 40px;
               height: 40px;
               display: flex;
               align-items: center;
               justify-content: center;
               cursor: pointer;
               transition: all 0.3s ease;
               opacity: 1;
               box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
               pointer-events: auto;
               z-index: 10;
               transform: translateY(-50%);
          }

          @media (min-width: 768px) {
               .scroll-arrow {
                    width: 40px;
                    height: 40px;
               }
          }

          .scroll-arrow.scroll-left {
               left: 0.25rem;
          }

          .scroll-arrow.scroll-right {
               right: 0.25rem;
          }

          @media (min-width: 640px) {
               .scroll-arrow.scroll-left {
                    left: 0.5rem;
               }

               .scroll-arrow.scroll-right {
                    right: 0.5rem;
               }
          }


          .scroll-arrow.scroll-left {
               left: 0.5rem;
          }

          .scroll-arrow.scroll-right {
               right: 0.5rem;
          }

          .scroll-arrow {
               transition: all 0.3s ease;
          }

          .scroll-arrow:hover {
               background-color: #ffffff;
               transform: scale(1.2) translateY(-50%) rotate(5deg);
               box-shadow: 0 6px 18px rgba(0, 0, 0, 0.35);
               cursor: pointer;
               border-radius: 50%;
               color: #d81d2dff;
               /* nice blue accent */
          }


          .scroll-arrow i {
               font-size: 1.25rem;
          }

          @media (min-width: 768px) {
               .scroll-arrow i {
                    font-size: 1.25rem;
               }
          }

          /* Enhanced room details with Netflix-style text */
          .room-details {
               padding: 1rem;
               /* Increased from 1.25rem */
          }

          @media (min-width: 480px) {
               .room-details {
                    padding: 1.25rem;
               }
          }

          @media (min-width: 768px) {
               .room-details {
                    padding: 1.5rem;
               }
          }

          .room-title {
               font-size: clamp(1.125rem, 4vw, 1.375rem);
               /* Responsive font size */
               font-weight: 700;
               color: #1f2937;
               margin-bottom: 0.5rem;
               line-height: 1.3;
               word-wrap: break-word;
               overflow-wrap: break-word;
          }

          .room-number {
               color: #DC2626;
               font-weight: 600;
               font-size: 0.875em;
               /* Relative to parent */
          }

          @media (min-width: 480px) {
               .feature-icon {
                    width: 18px;
                    margin-right: 0.5rem;
               }
          }

          .room-features {
               display: flex;
               gap: 0.75rem;
               margin-bottom: 0.75rem;
               font-size: clamp(0.75rem, 3vw, 0.875rem);
               flex-wrap: wrap;
          }

          @media (max-width: 380px) {
               .room-features {
                    flex-direction: column;
                    gap: 0.5rem;
               }

               .feature-item {
                    font-size: 0.8rem;
               }

               .room-details {
                    padding: 0.875rem;
               }
          }

          @media (max-width: 480px) {
               .scroll-arrow {
                    width: 32px;
                    height: 32px;
               }

               .scroll-arrow i {
                    font-size: 0.875rem;
               }
          }

          .feature-item {
               display: flex;
               align-items: center;
               color: #4b5563;
               white-space: nowrap;
          }

          .feature-icon {
               color: #DC2626;
               margin-right: 0.375rem;
               width: 16px;
               text-align: center;
               flex-shrink: 0;
          }

          .amenities-container {
               margin-top: 0.5rem;
          }

          .price-container {
               display: flex;
               flex-direction: column;
               gap: 0.75rem;
               margin-top: 1rem;
               padding-top: 1rem;
               border-top: 1px solid #e5e7eb;
          }

          @media (min-width: 480px) {
               .price-container {
                    flex-direction: row;
                    justify-content: space-between;
                    align-items: flex-end;
               }
          }

          .price-details {
               flex-grow: 1;
               min-height: auto;
          }

          @media (min-width: 480px) {
               .price-details {
                    min-height: 60px;
               }
          }

          .night-price {
               font-size: clamp(1.25rem, 4vw, 1.625rem);
               font-weight: 700;
               color: #DC2626;
               line-height: 1.2;
          }

          .night-text {
               font-size: clamp(0.7rem, 2.5vw, 0.75rem);
               color: #6b7280;
               margin-top: 0.25rem;
          }

          .book-button {
               padding: 0.75rem 1.25rem;
               /* Increased from 0.75rem 1.5rem */
               font-size: 1rem;
               /* Larger text */
               color: white;
               width: 100%;
          }

          @media (min-width: 480px) {
               .book-button {
                    width: auto;
                    min-width: 140px;
               }
          }

          .book-button:hover {
               background-color: #2c4a75;
               transform: translateY(-2px);
               box-shadow: 0 6px 12px rgba(220, 38, 38, 0.15);
          }

          /* Netflix-style "ribbon" for included items */
          .included-ribbon {
               position: absolute;
               top: 8px;
               left: -5px;
               background-color: #DC2626;
               color: white;
               padding: 0.25rem 0.75rem;
               font-size: clamp(0.65rem, 2.5vw, 0.75rem);
               font-weight: 600;
               border-radius: 0.25rem;
               box-shadow: 2px 2px 5px rgba(0, 0, 0, 0.2);
               z-index: 2;
               max-width: 80%;
               white-space: nowrap;
               overflow: hidden;
               text-overflow: ellipsis;
          }

          .view-amenities-btn {
               font-size: 0.9375rem;
               /* Increased from text-sm */
               padding: 0.5rem 0;
               /* Added vertical padding */
          }

          .included-ribbon:before {
               content: '';
               position: absolute;
               left: 0;
               bottom: -5px;
               width: 0;
               height: 0;
               border-left: 5px solid transparent;
               border-right: 0 solid transparent;
               border-top: 5px solid #B91C1C;
          }

          /* Responsive adjustments */
          @media (max-width: 768px) {
               .amenities-modal-container {
                    width: 95%;
                    margin: 1rem;
               }

               .amenities-grid {
                    grid-template-columns: 1fr;
               }

               .amenity-item {
                    padding: 0.75rem;
                    min-height: 70px;
               }

               .amenity-icon {
                    width: 28px;
                    height: 28px;
                    font-size: 1rem;
                    margin-right: 0.75rem;
               }

               .amenity-name {
                    font-size: 0.9375rem;
               }
          }

          /* Additional Netflix-inspired styles */
          .category-container {
               position: relative;
          }

          .rooms-scroll-container {
               scroll-behavior: smooth;
          }

          .rooms-scroll-container:before {
               left: 0;
               background: linear-gradient(to right, rgba(248, 250, 252, 1) 0%, rgba(248, 250, 252, 0) 100%);
          }

          .rooms-scroll-container:after {
               right: 0;
               background: linear-gradient(to left, rgba(248, 250, 252, 1) 0%, rgba(248, 250, 252, 0) 100%);
          }

          /* Validation styles */
          .error-message {
               color: #DC2626;
               font-size: 0.75rem;
               margin-top: 0.25rem;
               display: none;
          }

          .input-error {
               border-color: #DC2626 !important;
               background-color: #FEF2F2;
          }

          .input-success {
               border-color: #10B981 !important;
          }

          /* Verification Modal Styles */
          .verification-modal {
               animation: fadeIn 0.3s ease-out;
          }

          @keyframes fadeIn {
               from {
                    opacity: 0;
                    transform: translateY(-20px);
               }

               to {
                    opacity: 1;
                    transform: translateY(0);
               }
          }

          .resend-link {
               color: #3B82F6;
               cursor: pointer;
               text-decoration: underline;
          }

          .resend-link:hover {
               color: #2563EB;
          }

          /* New styles for enhanced UI */
          .progress-step {
               position: relative;
          }

          .uppercase-input {
               text-transform: uppercase;
          }

          .unavailable-badge {
               display: inline-flex;
               align-items: center;
          }

          .amenity-badge {
               transition: all 0.2s ease;
          }

          .amenity-badge:hover {
               background-color: #DC2626 !important;
               color: white !important;
          }

          .amenity-badge:hover i {
               color: white !important;
          }

          /* Floating notification */
          .notification {
               position: fixed;
               bottom: 20px;
               right: 20px;
               background-color: #10B981;
               color: white;
               padding: 1rem 1.5rem;
               border-radius: 0.5rem;
               box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
               transform: translateY(100px);
               opacity: 0;
               transition: all 0.3s ease;
               z-index: 1000;
               display: flex;
               align-items: center;
          }

          .notification.show {
               transform: translateY(0);
               opacity: 1;
          }

          .notification.error {
               background-color: #DC2626;
          }

          /* Datepicker custom styles */
          .flatpickr-day.unavailable {
               color: #DC2626 !important;
               text-decoration: line-through;
               background: rgba(220, 38, 38, 0.1);
          }

          .flatpickr-day.unavailable:hover {
               background: rgba(220, 38, 38, 0.2) !important;
          }

          /* Header styles */
          header {
               z-index: 1000;
               box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
          }

          /* Sticky sidebar */
          .sticky-sidebar {
               position: sticky;
               top: 2rem;
               height: calc(100vh - 7.5rem);
               overflow-y: auto;
               margin-left: 2rem;
               /* Added for more spacing */
          }

          /* Improved button styles */
          .btn-primary {
               background: linear-gradient(135deg, #DC2626 0%, #B91C1C 100%);
               transition: all 0.3s ease;
          }

          .btn-primary:hover {
               transform: translateY(-2px);
               box-shadow: 0 6px 12px rgba(220, 38, 38, 0.2);
          }

          /* Better form input focus states */
          .form-input:focus {
               box-shadow: 0 0 0 3px rgba(220, 38, 38, 0.2);
          }

          /* Responsive adjustments */
          @media (max-width: 1024px) {
               .sticky-sidebar {
                    position: static;
                    height: auto;
                    margin-left: 0;
               }
          }

          /* Confirmation checkbox styles */
          .confirmation-checkbox {
               margin-top: 1.5rem;
               padding-top: 1.5rem;
               border-top: 1px solid #e5e7eb;
          }

          .checkbox-container {
               display: flex;
               align-items: flex-start;
               margin-bottom: 1rem;
          }

          .checkbox-container input[type="checkbox"] {
               margin-right: 0.75rem;
               margin-top: 0.25rem;
          }

          .checkbox-container label {
               font-size: 0.875rem;
               color: #4b5563;
          }

          .checkbox-error {
               color: #DC2626;
               font-size: 0.75rem;
               margin-top: 0.25rem;
               display: none;
          }

          .unavailable-dates-container {
               border-top: 1px dashed #e5e7eb;
               padding-top: 0.75rem;
               margin-top: 0.75rem;
          }

          .toggle-unavailable-dates {
               transition: all 0.2s ease;
               font-size: clamp(0.8rem, 3vw, 0.875rem);
               padding: 0.5rem 0;
          }

          .room-actions {
               display: flex;
               flex-direction: column;
               gap: 0.5rem;
               align-items: stretch;
          }

          @media (min-width: 480px) {
               .room-actions {
                    flex-direction: row;
                    align-items: flex-end;
                    justify-content: space-between;
               }
          }

          .room-indicator {
               font-size: clamp(0.7rem, 2.5vw, 0.75rem);
               text-align: center;
               margin-top: 0.5rem;
          }

          @media (min-width: 480px) {
               .room-indicator {
                    text-align: left;
                    margin-top: 0.75rem;
               }
          }

          .discount-badge {
               font-size: clamp(0.7rem, 2.5vw, 0.75rem);
               padding: 0.25rem 0.5rem;
          }

          .toggle-unavailable-dates:hover {
               color: #B91C1C;
          }

          .toggle-unavailable-dates.active i:last-child {
               transform: rotate(180deg);
          }

          .unavailable-dates-content {
               max-height: 150px;
               overflow-y: auto;
               padding-right: 0.5rem;
          }

          /* Custom scrollbar */
          .unavailable-dates-content::-webkit-scrollbar {
               width: 4px;
          }

          .unavailable-dates-content::-webkit-scrollbar-thumb {
               background-color: #DC2626;
               border-radius: 4px;
          }

          /* Amenities Modal Styles */
          #amenities-modal {
               transition: opacity 0.3s ease, visibility 0.3s ease;
               display: flex;
               align-items: center;
               justify-content: center;
          }

          .amenities-grid {
               display: grid;
               grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
               gap: 1rem;
          }

          #amenities-modal.hidden {
               opacity: 0;
               visibility: hidden;
          }

          #amenities-modal:not(.hidden) {
               opacity: 1;
               visibility: visible;
          }

          .amenities-modal-container {
               background: white;
               border-radius: 1rem;
               width: 90%;
               max-width: 800px;
               max-height: 90vh;
               overflow-y: auto;
               box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
          }

          .amenities-modal-header {
               padding: 1.5rem 2rem;
               border-bottom: 1px solid #e5e7eb;
               display: flex;
               justify-content: space-between;
               align-items: center;
          }

          .amenities-modal-title {
               font-size: 1.5rem;
               font-weight: 600;
               color: #1F2937;
               margin: 0;
          }

          .amenities-modal-close {
               background: none;
               border: none;
               font-size: 1.5rem;
               cursor: pointer;
               color: #6B7280;
               padding: 0.25rem;
               border-radius: 0.25rem;
          }

          .amenities-modal-close:hover {
               color: #374151;
               background: #F3F4F6;
          }

          .amenities-modal-body {
               padding: 2rem;
          }

          .amenity-item {
               display: flex;
               align-items: center;
               padding: 1rem;
               border-radius: 0.75rem;
               background-color: #f8fafc;
               transition: all 0.2s ease;
               border: 1px solid #e5e7eb;
               min-height: 80px;
          }

          .amenity-name {
               font-size: 1.125rem;
               color: #1F2937;
               font-weight: 500;
               text-align: left;
          }

          @media (max-width: 768px) {
               #amenities-modal .modal-container {
                    width: 95%;
                    margin: 0 auto;
               }

               .amenity-item {
                    padding: 1rem;
                    min-height: 70px;
               }

               .amenity-icon {
                    width: 28px;
                    height: 28px;
                    font-size: 1rem;
                    margin-right: 1rem;
               }

               .amenity-name {
                    font-size: 1rem;
               }
          }

          .amenity-item:hover {
               transform: translateY(-2px);
               box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
               border-color: #DC2626;
          }

          .amenity-icon {
               width: 32px;
               height: 32px;
               display: flex;
               align-items: center;
               justify-content: center;
               margin-right: 1rem;
               color: #DC2626;
               font-size: 1.25rem;
               flex-shrink: 0;
          }

          .amenity-name {
               font-size: 1rem;
               color: #1F2937;
               font-weight: 500;
               text-align: left;
          }

          #checkout-btn {
               background: linear-gradient(135deg, #DC2626 0%, #B91C1C 100%);
               color: white;
               font-weight: 600;
               padding: 0.875rem 1.5rem;
               border-radius: 0.5rem;
               border: none;
               cursor: pointer;
               transition: all 0.3s ease;
               font-size: 1rem;
               width: 100%;
               display: flex;
               align-items: center;
               justify-content: center;
               gap: 0.75rem;
               box-shadow: 0 4px 6px rgba(220, 38, 38, 0.1);
               position: relative;
               overflow: hidden;
          }

          #checkout-btn:hover {
               transform: translateY(-2px);
               box-shadow: 0 6px 12px rgba(220, 38, 38, 0.2);
          }

          #checkout-btn:active {
               transform: translateY(0);
               box-shadow: 0 2px 4px rgba(220, 38, 38, 0.2);
          }

          #checkout-btn::after {
               content: '';
               position: absolute;
               top: -50%;
               left: -60%;
               width: 200%;
               height: 200%;
               background: rgba(255, 255, 255, 0.1);
               transform: rotate(30deg);
               transition: all 0.3s ease;
          }

          #checkout-btn:hover::after {
               left: 100%;
          }

          #checkout-btn:disabled {
               opacity: 0.7;
               cursor: not-allowed;
               transform: none !important;
               box-shadow: none !important;
          }

          /* Loading Spinner - Enhanced */
          .loading-spinner {
               display: none;
               width: 1.25rem;
               height: 1.25rem;
               border: 3px solid rgba(255, 255, 255, 0.3);
               border-radius: 50%;
               border-top-color: white;
               animation: spin 1s ease-in-out infinite;
          }

          .loading-spinner:not(.hidden) {
               display: inline-block;
               /* Show when not hidden */
          }

          @keyframes spin {
               0% {
                    transform: rotate(0deg);
               }

               100% {
                    transform: rotate(360deg);
               }
          }

          /* Add this CSS to fix the calendar display issue */
          .calendar-container {
               width: 100% !important;
               min-width: 300px;
               max-width: 100%;
               overflow: visible !important;
          }

          .calendar-container .flatpickr-calendar {
               position: relative !important;
               width: 100% !important;
               max-width: 100% !important;
               left: 0 !important;
               top: 0 !important;
               opacity: 1 !important;
               visibility: visible !important;
               transform: none !important;
               box-shadow: none !important;
          }

          .calendar-container .flatpickr-days {
               width: 100% !important;
          }

          .calendar-container .dayContainer {
               width: 100% !important;
               min-width: 100% !important;
               max-width: 100% !important;
          }

          .unavailable-dates-content {
               max-height: none !important;
               /* Remove height restriction */
               overflow: visible !important;
               /* Allow calendar to expand fully */
          }

          /* Ensure the calendar is properly visible when expanded */
          .unavailable-dates-content:not(.hidden) {
               display: block;
               padding: 1rem 0;
          }

          /* Make sure the calendar container doesn't get cut off */
          .room-card {
               overflow: visible !important;
               /* Allow calendar to expand outside card */
               z-index: auto !important;
               /* Reset z-index to prevent stacking issues */
          }

          /* When calendar is open, ensure it's above other elements */
          .unavailable-dates-content:not(.hidden) .calendar-container {
               z-index: 100;
               position: relative;
          }

          .legend-item {
               display: flex;
               align-items: center;
               font-size: 12px;
          }

          .legend-color {
               width: 15px;
               height: 15px;
               border-radius: 3px;
               margin-right: 5px;
          }

          /* By default: not clickable */
          .mobile-only-link {
               pointer-events: none;
               color: gray;
               /* Optional: show disabled look */
          }

          /* Enable click only on small screens (phone size) */
          @media (max-width: 640px) {
               .mobile-only-link {
                    pointer-events: auto;
                    color: rgba(139, 139, 238, 1);
                    /* Optional: restore normal link look */
               }
          }
     </style>

     <x-header />

     <div class="container mx-auto px-6 py-8 max-w-12xl">

          <!-- Progress Steps -->
          <x-progress-step :currentStep="1" :steps="[
               ['label' => 'Select Rooms'],
               ['label' => 'Your Details'],
               ['label' => 'Payment'],
               ['label' => 'Completed']
          ]" />

          <!-- Main Content -->
          <div class="flex flex-col lg:flex-row gap-12">
               <!-- Left Column -->
               <div class="lg:w-4/6">
                    <div class="space-y-4">
                         <h3 class="category-title font-bold text-dark mb-2 text-lg sm:text-xl md:text-2xl">Available
                              Accommodation</h3>

                         <div class="bg-red-50 border border-red-200 rounded-lg p-4 md:p-6 mb-6 w-full">
                              <div class="flex items-start">
                                   <div
                                        class="bg-red-100 p-2 md:p-3 rounded-full mr-3 md:mr-4 shadow-inner flex-shrink-0">
                                        <i class="fas fa-check-circle text-red-600 text-lg md:text-2xl"></i>
                                   </div>
                                   <div class="flex-1">
                                        <h4
                                             class="font-bold text-red-900 text-lg md:text-xl mb-2 md:mb-3 flex flex-col sm:flex-row sm:items-center gap-2">
                                             All Rooms Include:
                                             <span
                                                  class="text-red-600 text-xs md:text-sm font-normal bg-red-100 px-2 py-1 rounded-full w-fit">10
                                                  Premium Amenities</span>
                                        </h4>
                                        <ul
                                             class="grid grid-cols-2 sm:grid-cols-1 md:grid-cols-2 gap-2 md:gap-x-8 md:gap-y-3 text-red-800">
                                             <li class="flex items-center p-2 rounded-lg">
                                                  <div class="bg-red-200 p-2 rounded-full mr-3 flex-shrink-0">
                                                       <i
                                                            class="fas fa-swimming-pool text-red-700 w-4 md:w-5 text-center text-sm md:text-base"></i>
                                                  </div>
                                                  <span class="text-left hidden sm:inline">Complimentary pool access</span>
                                                  <span class="text-left sm:hidden">Pool</span>
                                             </li>
                                             <li class="flex items-center p-2 rounded-lg transition-colors duration-200">
                                                  <div class="bg-red-200 p-2 rounded-full mr-3 flex-shrink-0">
                                                       <i
                                                            class="fas fa-torii-gate text-red-700 w-4 md:w-5 text-center text-sm md:text-base"></i>
                                                  </div>
                                                  <span class="text-left hidden sm:inline">Unlimited access to park</span>
                                                  <span class="text-left sm:hidden">Park</span>
                                             </li>
                                             <li class="flex items-center p-2 rounded-lg transition-colors duration-200">
                                                  <div class="bg-red-200 p-2 rounded-full mr-3 flex-shrink-0">
                                                       <i
                                                            class="fas fa-wifi text-red-700 w-4 md:w-5 text-center text-sm md:text-base"></i>
                                                  </div>
                                                  <span class="text-left hidden sm:inline">Free high-speed WiFi</span>
                                                  <span class="text-left sm:hidden">WiFi</span>
                                             </li>
                                             <li class="flex items-center p-2 rounded-lg transition-colors duration-200">
                                                  <div class="bg-red-200 p-2 rounded-full mr-3 flex-shrink-0">
                                                       <i
                                                            class="fas fa-parking text-red-700 w-4 md:w-5 text-center text-sm md:text-base"></i>
                                                  </div>
                                                  <span class="text-left hidden sm:inline">Complimentary parking</span>
                                                  <span class="text-left sm:hidden">Parking</span>
                                             </li>
                                             <li class="flex items-center p-2 rounded-lg transition-colors duration-200">
                                                  <div class="bg-red-200 p-2 rounded-full mr-3 flex-shrink-0">
                                                       <i
                                                            class="fas fa-mug-hot text-red-700 w-4 md:w-5 text-center text-sm md:text-base"></i>
                                                  </div>
                                                  <span class="text-left hidden sm:inline">Free welcome drinks</span>
                                                  <span class="text-left sm:hidden">Drinks</span>
                                             </li>
                                             <li class="flex items-center p-2 rounded-lg transition-colors duration-200">
                                                  <div class="bg-red-200 p-2 rounded-full mr-3 flex-shrink-0">
                                                       <i
                                                            class="fas fa-bath text-red-700 w-4 md:w-5 text-center text-sm md:text-base"></i>
                                                  </div>
                                                  <span class="text-left hidden sm:inline">Hot & cold shower</span>
                                                  <span class="text-left sm:hidden">Shower</span>
                                             </li>
                                             <li class="flex items-center p-2 rounded-lg transition-colors duration-200">
                                                  <div class="bg-red-200 p-2 rounded-full mr-3 flex-shrink-0">
                                                       <i
                                                            class="fas fa-tv text-red-700 w-4 md:w-5 text-center text-sm md:text-base"></i>
                                                  </div>
                                                  <span class="text-left hidden sm:inline">Cable TV / Smart TV</span>
                                                  <span class="text-left sm:hidden">TV</span>
                                             </li>
                                             <li class="flex items-center p-2 rounded-lg transition-colors duration-200">
                                                  <div class="bg-red-200 p-2 rounded-full mr-3 flex-shrink-0">
                                                       <i
                                                            class="fas fa-snowflake text-red-700 w-4 md:w-5 text-center text-sm md:text-base"></i>
                                                  </div>
                                                  <span class="text-left hidden sm:inline">Air-conditioned rooms</span>
                                                  <span class="text-left sm:hidden">AC</span>
                                             </li>
                                             <li class="flex items-center p-2 rounded-lg transition-colors duration-200">
                                                  <div class="bg-red-200 p-2 rounded-full mr-3 flex-shrink-0">
                                                       <i
                                                            class="fas fa-utensils text-red-700 w-4 md:w-5 text-center text-sm md:text-base"></i>
                                                  </div>
                                                  <span class="text-left hidden sm:inline">Complimentary breakfast</span>
                                                  <span class="text-left sm:hidden">Breakfast</span>
                                             </li>
                                             <li class="flex items-center p-2 rounded-lg transition-colors duration-200">
                                                  <div class="bg-red-200 p-2 rounded-full mr-3 flex-shrink-0">
                                                       <i
                                                            class="fas fa-concierge-bell text-red-700 w-4 md:w-5 text-center text-sm md:text-base"></i>
                                                  </div>
                                                  <span class="text-left hidden sm:inline">24/7 front desk service</span>
                                                  <span class="text-left sm:hidden">Front Desk</span>
                                             </li>
                                        </ul>
                                   </div>
                              </div>
                         </div>


                         <!-- Group rooms by category with Netflix-style horizontal scrolling -->
                         <div id="facilities-container" class="space-y-8">
                              @php
                                   // Group facilities by category
                                   $groupedFacilities = $facilities->groupBy('category');
                                   echo "<script>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                          console.log('Unavailable Dates Data:', " . json_encode($unavailable_dates) . ");
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                     </script>";
                              @endphp

                              @foreach($groupedFacilities as $category => $facilitiesInCategory)
                                   <div class="category-container">
                                        <h3 class="category-title font-bold text-dark text-lg sm:text-xl md:text-2xl">
                                             {{ $category }}
                                        </h3>

                                        <div class="relative" style="overflow: visible;">
                                             <div class="rooms-scroll-container" id="scroll-{{ $loop->index }}">
                                                  @foreach($facilitiesInCategory as $facility)
                                                       @php
                                                            $facilityId = 'facility-' . $facility->id;
                                                            $allImages = [];
                                                            if ($facility->images->isNotEmpty()) {
                                                                 $allImages = $facility->images->map(function ($img) {
                                                                      return asset('imgs/facility_img/' . $img->path);
                                                                 })->toArray();
                                                            } else {
                                                                 $allImages = [$facility->main_image];
                                                            }

                                                            $bookedDates = [];
                                                            $today = \Carbon\Carbon::today()->format('Y-m-d');

                                                            if (isset($unavailable_dates[$facility->id])) {
                                                                 foreach ($unavailable_dates[$facility->id] as $period) {
                                                                      try {
                                                                           $start = \Carbon\Carbon::parse($period['checkin_date']);
                                                                           $end = \Carbon\Carbon::parse($period['checkout_date'])->subDay(); // Subtract 1
                                                                           // day to exclude checkout date

                                                                           // If the booking includes today, make sure to include it
                                                                           if ($start->format('Y-m-d') === $today) {
                                                                                $bookedDates[] = $today;
                                                                           }

                                                                           $current = clone $start;
                                                                           while ($current <= $end) {
                                                                                $bookedDates[] = $current->format('Y-m-d');
                                                                                $current->addDay();
                                                                           }
                                                                      } catch (\Exception $e) {
                                                                           continue;
                                                                      }
                                                                 }
                                                            }

                                                            $bookedDates = array_values(array_unique($bookedDates));
                                                       @endphp

                                                       <div class="room-card border border-lightgray flex flex-col h-full"
                                                            data-price="{{ $facility->price }}" data-room-id="{{ $facilityId }}"
                                                            data-images='@json($allImages)'
                                                            data-booked-dates='{{ json_encode($bookedDates) }}'>

                                                            @if ($facility->included != null)
                                                                 <div class="included-ribbon">
                                                                      {{ $facility->included }}
                                                                 </div>
                                                            @endif

                                                            <!-- Image Section -->
                                                            <div class="room-image-container">
                                                                 <a href="{{ route('my_modals', ['id' => $facility->id]) }}"
                                                                      class="block h-full">
                                                                      @if($facility->main_image)
                                                                           <img src="{{ $facility->main_image }}"
                                                                                alt="{{ $facility->name }}"
                                                                                class="w-full h-full object-cover transition-transform duration-300"
                                                                                onerror="this.onerror=null;this.src='https://placehold.co/500x300?text=No+Image+Available&font=roboto';this.classList.add('opacity-80')">
                                                                      @else
                                                                           <img src="https://placehold.co/500x300?text=No+Image+Available&font=roboto"
                                                                                alt="{{ $facility->name }}"
                                                                                class="w-full h-full object-cover opacity-80">
                                                                      @endif
                                                                 </a>
                                                            </div>

                                                            <!-- Room Details -->
                                                            <div class="room-details flex flex-col flex-grow p-4">
                                                                 <div>
                                                                      <h3 class="room-title">
                                                                           {{ $facility->name }}
                                                                           @if($facility->room_number)
                                                                                <span class="room-number">({{ $facility->room_number }}
                                                                                     Room
                                                                                     )</span>
                                                                           @endif
                                                                      </h3>

                                                                      <div class="room-features">
                                                                           @if($facility->bed_number)
                                                                                                                        <div class="feature-item">
                                                                                                                             <i class="fas fa-bed feature-icon"></i>
                                                                                                                             <span>{{ $facility->bed_number }} bed{{
                                                                                $facility->bed_number != 1 ? 's' : ''
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                           }}</span>
                                                                                                                        </div>
                                                                           @endif
                                                                           <div class="feature-item">
                                                                                <i class="fas fa-user-friends feature-icon"></i>
                                                                                <span>{{ $facility->pax }} Pax</span>
                                                                           </div>
                                                                      </div>
                                                                      <!-- 
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        <button
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                             class="view-amenities-btn mt-2 text-sm text-primary font-medium flex items-center hover:text-primary-dark transition-colors"
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                             data-room-id="{{ $facility->id }}"
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                             data-amenities='@json($facility->amenities->map(function($a) { return ['
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                             name'=> $a->name]; }))'>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                             <i class="fas fa-list mr-2"></i> View All Amenities
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        </button> -->

                                                                      <!-- Booked dates only -->

                                                                      <div class="unavailable-dates-container mt-3 mb-2">
                                                                           <button
                                                                                class="toggle-unavailable-dates text-sm text-primary font-medium flex items-center">
                                                                                <i class="fas fa-calendar mr-2"></i>
                                                                                View Availability
                                                                                <i
                                                                                     class="fas fa-chevron-down ml-2 text-xs transition-transform duration-200"></i>
                                                                           </button>

                                                                           <div class="unavailable-dates-content hidden mt-2">
                                                                                <div class="legend-item flex items-center gap-2 mb-2">
                                                                                     <div class="legend-color w-4 h-4 rounded-sm"
                                                                                          style="background-color: #f4bdc2; border: 1px solid #f1aeb5;">
                                                                                     </div>
                                                                                     <span class="text-sm text-gray-700">Booked</span>
                                                                                </div>

                                                                                <div class="legend-item flex items-center gap-2 mb-2">
                                                                                     <div class="legend-color w-4 h-4 rounded-sm"
                                                                                          style="background-color: #bdf4c8; border: 1px solid #aef1b5;">
                                                                                     </div>
                                                                                     <span
                                                                                          class="text-sm text-gray-700">Available</span>
                                                                                </div>

                                                                                @php
                                                                                     $sortedDates =
                                                                                          collect($bookedDates)->sort()->values()->all();
                                                                                @endphp
                                                                                <div class="calendar-container"
                                                                                     id="calendar-{{ $facility->id }}"
                                                                                     data-booked='@json($sortedDates)'>
                                                                                </div>
                                                                           </div>
                                                                      </div>

                                                                 </div>

                                                                 <!-- Price and Book Button - Fixed at bottom -->
                                                                 <div class="mt-auto pt-3">
                                                                      <div
                                                                           class="flex flex-col xs:flex-row justify-between items-start xs:items-end gap-2">
                                                                           <div class="price-details flex-grow min-h-[60px]">
                                                                                @php
                                                                                     $activeDiscount =
                                                                                          $facility->discounts->first(function ($discount) {
                                                                                               return \Carbon\Carbon::now()->between(
                                                                                                    \Carbon\Carbon::parse($discount->start_date),
                                                                                                    \Carbon\Carbon::parse($discount->end_date)
                                                                                               );
                                                                                          });
                                                                                @endphp
                                                                                @if ($activeDiscount)
                                                                                <div class="flex items-center flex-wrap gap-x-2">
                                                                                <!-- Old price -->
                                                                                <div class="night-price text-gray-400 line-through text-xs xs:text-sm sm:text-base">
                                                                                     ₱{{ number_format($facility->price) }}
                                                                                </div>

                                                                                <!-- New price -->
                                                                                <div class="night-price text-red-600 font-semibold text-sm xs:text-base">
                                                                                     ₱{{
                                                                                          number_format(
                                                                                               $facility->price - (
                                                                                                    $activeDiscount->discount_type === 'percent'
                                                                                                    ? ($facility->price * $activeDiscount->discount_value / 100)
                                                                                                    : $activeDiscount->discount_value
                                                                                               )
                                                                                          )
                                                                                     }}
                                                                                </div>

                                                                                <!-- Discount icon + text -->
                                                                                <div class="flex items-center text-xs xs:text-sm text-green-600 font-medium">
                                                                                     <i class="fas fa-tag mr-1"></i>
                                                                                     @if ($activeDiscount->discount_type === 'percent')
                                                                                          {{ $activeDiscount->discount_value }}% OFF
                                                                                     @else
                                                                                          ₱{{ number_format($activeDiscount->discount_value) }} OFF
                                                                                     @endif
                                                                                </div>
                                                                                </div>
                                                                                @else
                                                                                <div class="night-price font-semibold text-sm xs:text-base">
                                                                                ₱{{ number_format($facility->price) }}
                                                                                </div>
                                                                                @endif

                                                                                <div
                                                                                     class="night-text text-xs xs:text-sm text-gray-500 mt-0.5">
                                                                                     per night
                                                                                </div>
                                                                           </div>
                                                                           <div class="flex flex-col items-start">
                                                                                <button
                                                                                     class="book-button add-to-cart-btn btn-primary w-full xs:w-auto px-4 py-3 xs:px-5 xs:py-3 text-sm xs:text-base font-medium rounded-xl hover:scale-[1.02] transition-all duration-300 ease-out shadow-md hover:shadow-lg active:scale-[0.98] flex-shrink-0 bg-gradient-to-r from-red-600 to-red-700 text-white hover:from-red-700 hover:to-red-800 focus:ring-2 focus:ring-red-300 focus:outline-none cursor-pointer"
                                                                                     data-room="{{ $facilityId }}">
                                                                                     <span class="flex items-center justify-center">
                                                                                          Select Room
                                                                                          <svg xmlns="http://www.w3.org/2000/svg"
                                                                                               class="ml-2 h-4 w-4" fill="none"
                                                                                               viewBox="0 0 24 24"
                                                                                               stroke="currentColor">
                                                                                               <path stroke-linecap="round"
                                                                                                    stroke-linejoin="round"
                                                                                                    stroke-width="2"
                                                                                                    d="M13 7l5 5m0 0l-5 5m5-5H6" />
                                                                                          </svg>
                                                                                     </span>
                                                                                </button>

                                                                                <!-- Indicator text -->
                                                                                <span
                                                                                     class="mt-3 text-xs xs:text-sm text-gray-600 room-indicator transition-opacity duration-300 flex items-center">
                                                                                     <a href="#booking-summary"
                                                                                          class="mobile-only-link">Room will
                                                                                          appear in your Booking Summary</a>
                                                                                     <svg xmlns="http://www.w3.org/2000/svg"
                                                                                          class="ml-1 h-4 w-4 animate-pulse"
                                                                                          fill="none" viewBox="0 0 24 24"
                                                                                          stroke="currentColor">
                                                                                          <path stroke-linecap="round"
                                                                                               stroke-linejoin="round" stroke-width="2"
                                                                                               d="M9 5l7 7-7 7" />
                                                                                     </svg>
                                                                                </span>
                                                                           </div>

                                                                      </div>
                                                                 </div>
                                                            </div>
                                                       </div>
                                                  @endforeach
                                             </div>

                                             <div class="scroll-arrows">
                                                  <button class="scroll-arrow scroll-left"
                                                       data-target="scroll-{{ $loop->index }}">
                                                       <i class="fas fa-chevron-left"></i>
                                                  </button>
                                                  <button class="scroll-arrow scroll-right"
                                                       data-target="scroll-{{ $loop->index }}">
                                                       <i class="fas fa-chevron-right"></i>
                                                  </button>
                                             </div>
                                        </div>
                                   </div>
                              @endforeach
                         </div>
                    </div>
               </div>

               <!-- Right Column - Order Summary -->
               <div class="lg:w-2/5 space-y-4 sticky-sidebar">
                    <!-- Date Selection Card -->
                    <div class="bg-white rounded-xl p-8 border border-lightgray">
                         <h2 class="text-xl font-bold text-dark mb-4 flex items-center">
                              <i class="far fa-calendar-alt text-primary mr-3"></i>
                              Select Your Dates
                         </h2>

                         <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                              <div>
                                   <label for="checkin" class="block text-sm font-medium text-gray-700 mb-2">
                                        Check-in Date
                                   </label>
                                   <input type="text" id="checkin" placeholder="Select date"
                                        class="w-full px-4 py-3 border border-darkgray rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent datepicker form-input">
                              </div>
                              <div>
                                   <label for="checkout" class="block text-sm font-medium text-gray-700 mb-2">
                                        Check-out Date
                                   </label>
                                   <input type="text" id="checkout" placeholder="Select date"
                                        class="w-full px-4 py-3 border border-darkgray rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent datepicker form-input">
                              </div>
                         </div>

                         <!-- Info Section -->
                         <div class="mt-4 space-y-3">
                              <div class="flex items-center text-sm text-gray-600">
                                   <i class="fas fa-info-circle text-primary mr-2"></i>
                                   <span id="nights-display">Minimum stay: 1 night</span>
                              </div>

                              <div class="bg-red-50 border-l-4 border-red-400 p-4 rounded-lg shadow-sm">
                                   <div class="flex items-start">
                                        <div>
                                             <p class="text-sm font-semibold text-gray-800">Important Reminder</p>
                                             <p class="text-sm text-gray-600 mt-1">
                                                  Check-in time is <span class="font-medium text-gray-900">12:00 NN</span>
                                                  and Check-out time is <span class="font-medium text-gray-900">10:00
                                                       AM</span> only.
                                             </p>
                                        </div>
                                   </div>
                              </div>
                         </div>
                    </div>


                    <!-- Breakfast Option Card -->
                    <div class="bg-white rounded-xl p-8 border border-lightgray">
                         <h2 class="text-xl font-bold text-dark mb-4 flex items-center">
                              <i class="fas fa-utensils text-primary mr-3"></i>
                              Breakfast Option
                         </h2>
                         @if($breakfast_price->status == 'Active')
                                        <div class="flex items-center justify-between">
                                             <div>
                                                  <h3 class="font-medium text-gray-800">Add Breakfast for your stay</h3>
                                                  <p class="text-sm text-gray-600">Enjoy a delicious breakfast each morning for just ₱{{
                              number_format($breakfast_price->price) }} per room per night</p>
                                             </div>
                                             <label class="relative inline-flex items-center cursor-pointer">
                                                  <input type="checkbox" id="breakfast-toggle" class="sr-only peer" value="0">
                                                  <div
                                                       class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-primary/30 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary">
                                                  </div>
                                             </label>
                                        </div>
                         @else
                              <div class="text-center py-4">
                                   <p class="text-gray-500 italic">Breakfast service is currently not available</p>
                              </div>
                         @endif
                    </div>

                    <!-- Booking Summary Card -->
                    <div id='booking-summary' class="bg-white rounded-xl p-8 border border-lightgray">
                         <h2 class="text-2xl font-bold text-gray-800 mb-5 flex items-center">
                              <i class="fas fa-receipt text-primary mr-3 text-2xl"></i>
                              Booking Summary
                         </h2>

                         <div id="cart-items" class="space-y-4 min-h-[120px]">
                              <!-- Empty cart state -->
                              <div class="text-gray-400 text-center py-6">
                                   <i class="fas fa-shopping-cart text-3xl mb-3 opacity-50"></i>
                                   <p>Your list is empty</p>
                              </div>
                         </div>

                         @if($breakfast_price->status == 'Active')
                                        <div id="breakfast-summary" class="hidden border-t border-gray-200 pt-4">
                                             <div class="flex justify-between items-center">
                                                  <div>
                                                       <h4 class="font-medium text-dark">Breakfast Package</h4>
                                                       <div class="text-sm text-gray-600" id="breakfast-nights">1 night × 1 room</div>
                                                  </div>
                                                  <div class="text-right">
                                                       <div class="font-medium" id="breakfast-price">₱{{
                              number_format($breakfast_price->price, 2) }}</div>
                                                  </div>
                                             </div>
                                        </div>
                         @endif

                         <!-- Simplified total section -->
                         <div class="border-t border-gray-200 pt-4 mt-4">
                              <div class="flex justify-between items-center">
                                   <span class="text-lg font-bold text-gray-800">Total</span>
                                   <span class="text-3xl font-bold text-primary" id="total-price">₱0.00</span>
                              </div>
                         </div>

                         <button id="checkout-btn"
                              class="w-full mt-6 bg-gradient-to-r from-primary to-secondary hover:from-primary/90 hover:to-secondary/90 text-white font-bold py-4 px-6 rounded-xl transition-all duration-300 shadow-md hover:shadow-lg flex items-center justify-center disabled:opacity-70 disabled:transform-none hover:-translate-y-0.5 active:translate-y-0 btn-primary"
                              disabled>
                              <span id="button-text">Proceed to Your Details</span>
                              <div id="button-spinner" class="loading-spinner hidden"></div>
                         </button>
                    </div>
               </div>
          </div>
     </div>

     <!-- Amenities Modal -->
     <!-- <div id="amenities-modal" class="fixed inset-0 z-[999] hidden">
                                                                                                                                                                                         <div class="fixed inset-0 bg-black/50 backdrop-blur-sm transition-opacity" aria-hidden="true"></div>

                                                                                                                                                                                         <div class="fixed inset-0 flex items-center justify-center p-4">
                                                                                                                                                                                              <div class="amenities-modal-container">
                                                                                                                                                                                                   <div class="amenities-modal-header">
                                                                                                                                                                                                        <h3 class="amenities-modal-title">Room Amenities</h3>
                                                                                                                                                                                                        <button type="button" class="amenities-modal-close" id="close-amenities-modal">
                                                                                                                                                                                                             <i class="fas fa-times"></i>
                                                                                                                                                                                                        </button>
                                                                                                                                                                                                   </div>

                                                                                                                                                                                                   <div class="amenities-modal-body">
                                                                                                                                                                                                        <div id="amenities-list" class="amenities-grid">
                                                                                                                                                                                                             <!-- Amenities will be populated here -->
     </div>
     </div>
     </div>
     </div>
     </div> -->



     <!-- Notification element -->
     <div id="notification" class="notification hidden">
          <i class="fas fa-check-circle mr-2"></i>
          <span id="notification-message"></span>
     </div>

     <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
     <script>
          class BookingSystem {

               constructor() {
                    this.cart = [];
                    this.nights = 1;
                    this.roomsData = {};
                    this.bookedDates = {};
                    this.datePicker = null;
                    this.breakfastIncluded = false;
                    this.breakfastPrice = {{ $breakfast_price->status == 'Active' ? $breakfast_price->price : 0 }};
                    this.isSubmitting = false;
                    this.validDates = false;

                    // Load cart from storage
                    this.cart = [];

                    // Load breakfast state
                    const savedBreakfast = sessionStorage.getItem('breakfastIncluded');
                    this.breakfastIncluded = savedBreakfast === 'true';

                    this.initializeRoomsData();
                    this.init();
               }

               saveCartToStorage() {
                    sessionStorage.setItem('bookingCart', JSON.stringify(this.cart));
                    //localStorage.setItem('bookingCart', JSON.stringify(this.cart));
                    sessionStorage.setItem('breakfastIncluded', this.breakfastIncluded);
               }

               initializeRoomsData() {
                    document.querySelectorAll('.room-card').forEach(card => {
                         const roomId = card.dataset.roomId;
                         const images = JSON.parse(card.dataset.images);

                         let bookedDates = [];
                         try {
                              bookedDates = JSON.parse(card.dataset.bookedDates || '[]');
                              if (!Array.isArray(bookedDates)) {
                                   bookedDates = [];
                              }
                         } catch (e) {
                              console.error('Error parsing booked dates:', e);
                              bookedDates = [];
                         }

                         this.bookedDates[roomId] = bookedDates;
                         const discountedPriceElement = card.querySelector('.night-price.text-red-600');
                         const price = discountedPriceElement
                              ? parseFloat(discountedPriceElement.textContent.replace(/[^\d.]/g, ''))
                              : parseFloat(card.dataset.price);

                         this.roomsData[roomId] = {
                              name: card.querySelector('h3').textContent.trim(),
                              price: price,
                              originalPrice: parseFloat(card.dataset.price),
                              images: images,
                              mainImage: images[0] || 'https://via.placeholder.com/500x300?text=No+Image',
                              id: roomId,
                              facilityId: roomId.replace('facility-', '')
                         };

                         this.bookedDates[roomId] = bookedDates;
                    });
               }

               init() {
                    this.setupEventListeners();
                    this.initDatePickers();
                    this.setDefaultDates();
                    this.setupScrollArrows();
                    this.setupUnavailableDatesToggle();
                    // this.setupAmenitiesModal();
                    this.initializeCart();
               }

               setupAmenitiesModal() {
                    const modal = document.getElementById('amenities-modal');
                    const closeBtn = document.getElementById('close-amenities-modal');
                    const amenitiesList = document.getElementById('amenities-list');

                    // Close handlers
                    const closeModal = () => {
                         modal.classList.add('hidden');
                         document.body.style.overflow = 'auto'; // Re-enable scrolling
                    };

                    closeBtn.addEventListener('click', closeModal);

                    modal.addEventListener('click', (e) => {
                         if (e.target === modal) closeModal();
                    });

                    // Escape key to close
                    document.addEventListener('keydown', (e) => {
                         if (e.key === 'Escape' && !modal.classList.contains('hidden')) {
                              closeModal();
                         }
                    });

                    // View amenities button handler
                    document.addEventListener('click', async (e) => {
                         if (e.target.closest('.view-amenities-btn')) {
                              const button = e.target.closest('.view-amenities-btn');
                              const facilityId = button.dataset.roomId;

                              try {
                                   // Show loading state
                                   amenitiesList.innerHTML = `
                                                                                                                                                                                                                  <div class="col-span-3 py-8 flex flex-col items-center justify-center">
                                                                                                                                                                                                                       <i class="fas fa-spinner fa-spin text-primary text-3xl mb-4"></i>
                                                                                                                                                                                                                       <div class="text-lg">Loading amenities...</div>
                                                                                                                                                                                                                  </div>`;

                                   modal.classList.remove('hidden');
                                   document.body.style.overflow = 'hidden'; // Prevent background scrolling

                                   // Fetch amenities via API
                                   const response = await fetch(`/api/facilities/${facilityId}/amenities`);
                                   const data = await response.json();

                                   if (response.ok && data.success) {
                                        // Populate amenities list
                                        amenitiesList.innerHTML = data.amenities.map(amenity => `
                                                                                                                                                                                                                            <div class="amenity-item">
                                                                                                                                                                                                                                 <div class="amenity-icon">
                                                                                                                                                                                                                                      <i class="${amenity.icon || this.getAmenityIcon(amenity.name)}"></i>
                                                                                                                                                                                                                                 </div>
                                                                                                                                                                                                                                 <div class="amenity-name">${amenity.name}</div>
                                                                                                                                                                                                                            </div>
                                                                                                                                                                                                                       `).join('');
                                   } else {
                                        throw new Error(data.message || 'Failed to load amenities');
                                   }
                              } catch (error) {
                                   console.error('Error loading amenities:', error);
                                   amenitiesList.innerHTML = `
                                                                                                                                                                                                                  <div class="col-span-3 py-8 text-center">
                                                                                                                                                                                                                       <i class="fas fa-exclamation-circle text-red-500 text-3xl mb-4"></i>
                                                                                                                                                                                                                       <div class="text-lg text-red-500">
                                                                                                                                                                                                                            ${error.message || 'Failed to load amenities'}
                                                                                                                                                                                                                       </div>
                                                                                                                                                                                                                  </div>`;
                              }
                         }
                    });
               }

               getAmenityIcon(amenityName) {
                    const iconMap = {
                         'wifi': 'fas fa-wifi',
                         'tv': 'fas fa-tv',
                         'air conditioning': 'fas fa-snowflake',
                         'kitchen': 'fas fa-utensils',
                         'parking': 'fas fa-parking',
                         'pool': 'fas fa-swimming-pool',
                         'breakfast': 'fas fa-coffee',
                         'gym': 'fas fa-dumbbell',
                         'bathroom': 'fas fa-bath',
                         'shower': 'fas fa-shower',
                         'bed': 'fas fa-bed',
                         'hot tub': 'fas fa-hot-tub',
                         'fireplace': 'fas fa-fire',
                         'washing machine': 'fas fa-soap',
                         'dryer': 'fas fa-wind',
                         'heating': 'fas fa-temperature-high',
                         'balcony': 'fas fa-door-open',
                         'garden': 'fas fa-seedling',
                         'terrace': 'fas fa-umbrella-beach',
                         'view': 'fas fa-mountain',
                         'security': 'fas fa-shield-alt',
                         'accessible': 'fas fa-wheelchair',
                         'pet friendly': 'fas fa-paw',
                         'smoke free': 'fas fa-smoking-ban',
                         'family friendly': 'fas fa-child',
                         'workspace': 'fas fa-laptop',
                         'iron': 'fas fa-tshirt',
                         'hair dryer': 'fas fa-wind',
                         'essentials': 'fas fa-soap',
                         'hot water': 'fas fa-faucet',
                         // Add more mappings as needed
                    };

                    const lowerName = amenityName.toLowerCase();
                    return iconMap[lowerName] || 'fas fa-check-circle';
               }

               setupUnavailableDatesToggle() {
                    // Cache styles to avoid duplication
                    const STYLES_ADDED = Symbol('stylesAdded');

                    document.addEventListener('click', (e) => {
                         const toggleBtn = e.target.closest('.toggle-unavailable-dates');
                         if (!toggleBtn) return;

                         const content = toggleBtn.nextElementSibling;
                         const isOpening = content.classList.contains('hidden');

                         // Toggle visibility
                         toggleBtn.classList.toggle('active');
                         content.classList.toggle('hidden');

                         // Rotate chevron icon
                         this.rotateChevronIcon(toggleBtn, isOpening);

                         // Initialize calendar if opening
                         if (isOpening) {
                              this.initializeCalendarIfNeeded(content);
                         }
                    });
               }

               // Helper method for icon rotation
               rotateChevronIcon(toggleBtn, isOpening) {
                    const icon = toggleBtn.querySelector('i:last-child');
                    if (icon) {
                         icon.style.transform = isOpening ? 'rotate(180deg)' : 'rotate(0deg)';
                    }
               }

               // Helper method for calendar initialization
               initializeCalendarIfNeeded(content) {
                    const calendarEl = content.querySelector('.calendar-container');
                    if (!calendarEl || calendarEl.dataset.initialized) return;

                    this.initializeFlatpickr(calendarEl);
                    this.addCalendarStyles();

                    // Mark as initialized
                    calendarEl.dataset.initialized = "true";
               }

               // Flatpickr initialization
               initializeFlatpickr(calendarEl) {
                    let bookedDates = JSON.parse(calendarEl.dataset.booked || "[]");
                    const today = new Date().setHours(0, 0, 0, 0);

                    flatpickr(calendarEl, {
                         inline: true,
                         dateFormat: "Y-m-d",
                         disable: [
                              ...bookedDates,
                              (date) => date < today // disable past dates
                         ],
                         locale: { firstDayOfWeek: 0 },
                         clickOpens: false,
                         onChange: () => { }, // No-op function

                         onDayCreate: (dObj, dStr, fp, dayElem) => {
                              // Disable interactions
                              dayElem.style.pointerEvents = 'none';
                              dayElem.style.cursor = 'default';

                              const dateObj = dayElem.dateObj.setHours(0, 0, 0, 0);

                              if (dayElem.classList.contains('flatpickr-disabled')) {
                                   this.styleDisabledDay(dayElem, dateObj, today);
                              } else {
                                   // Style available dates
                                   dayElem.classList.add('available');
                              }
                         }
                    });
               }

               // Style disabled days based on type
               styleDisabledDay(dayElem, dateObj, today) {
                    if (dateObj < today) {
                         // Past dates
                         dayElem.classList.add('past-day');
                    } else {
                         // Booked/unavailable dates
                         dayElem.classList.add('unavailable');
                    }
               }

               // Add calendar styles (only once)
               addCalendarStyles() {
                    if (document.head.querySelector('[data-calendar-styles]')) return;

                    const style = document.createElement('style');
                    style.setAttribute('data-calendar-styles', 'true');
                    style.textContent = this.getCalendarStyles();
                    document.head.appendChild(style);
               }

               // CSS styles as template
               getCalendarStyles() {
                    return `
                                                                                                                                                                                                        /* Booked / unavailable days */
                                                                                                                                                                                                        .flatpickr-day.unavailable {
                                                                                                                                                                                                             background: #fee2e2 !important;
                                                                                                                                                                                                             color: #dc2626 !important;
                                                                                                                                                                                                             border-radius: 6px;
                                                                                                                                                                                                        }
                                                                                                                                                                                                        .flatpickr-day.unavailable:hover {
                                                                                                                                                                                                             background: #fecaca !important;
                                                                                                                                                                                                             color: #b91c1c !important;
                                                                                                                                                                                                        }

                                                                                                                                                                                                        /* Available days */
                                                                                                                                                                                                        .flatpickr-day.available {
                                                                                                                                                                                                             background: #dcfce7 !important;
                                                                                                                                                                                                             color: #16a34a !important;
                                                                                                                                                                                                             border-radius: 6px;
                                                                                                                                                                                                        }
                                                                                                                                                                                                        .flatpickr-day.available:hover {
                                                                                                                                                                                                             background: #bbf7d0 !important;
                                                                                                                                                                                                             color: #15803d !important;
                                                                                                                                                                                                        }

                                                                                                                                                                                                        /* Past days */
                                                                                                                                                                                                        .flatpickr-day.past-day {
                                                                                                                                                                                                             background: #f3f4f6 !important;
                                                                                                                                                                                                             color: #9ca3af !important;
                                                                                                                                                                                                             border-radius: 6px;
                                                                                                                                                                                                        }

                                                                                                                                                                                                        /* Disable interactions globally */
                                                                                                                                                                                                        .calendar-container .flatpickr-day {
                                                                                                                                                                                                             pointer-events: none !important;
                                                                                                                                                                                                        }
                                                                                                                                                                                                   `;
               }






               setupScrollArrows() {
                    const categoryContainers = document.querySelectorAll('.category-container');

                    categoryContainers.forEach(container => {
                         const scrollContainer = container.querySelector('.rooms-scroll-container');
                         const leftArrow = container.querySelector('.scroll-left');
                         const rightArrow = container.querySelector('.scroll-right');

                         const updateArrows = () => {
                              const scrollLeft = scrollContainer.scrollLeft;
                              const maxScroll = scrollContainer.scrollWidth - scrollContainer.clientWidth;

                              if (scrollLeft <= 10) {
                                   leftArrow.style.opacity = '0';
                                   leftArrow.style.pointerEvents = 'none';
                              } else {
                                   leftArrow.style.opacity = '1';
                                   leftArrow.style.pointerEvents = 'auto';
                              }

                              if (scrollLeft >= maxScroll - 10) {
                                   rightArrow.style.opacity = '0';
                                   rightArrow.style.pointerEvents = 'none';
                              } else {
                                   rightArrow.style.opacity = '1';
                                   rightArrow.style.pointerEvents = 'auto';
                              }
                         };

                         updateArrows();

                         leftArrow.addEventListener('click', () => {
                              const cardWidth = scrollContainer.querySelector('.room-card').offsetWidth;
                              scrollContainer.scrollBy({
                                   left: -cardWidth * 2,
                                   behavior: 'smooth'
                              });
                         });

                         rightArrow.addEventListener('click', () => {
                              const cardWidth = scrollContainer.querySelector('.room-card').offsetWidth;
                              scrollContainer.scrollBy({
                                   left: cardWidth * 2,
                                   behavior: 'smooth'
                              });
                         });

                         scrollContainer.addEventListener('scroll', updateArrows);
                         window.addEventListener('resize', updateArrows);
                    });
               }

               initDatePickers() {
                    const self = this;

                    this.checkinPicker = flatpickr("#checkin", {
                         minDate: "today",
                         dateFormat: "Y-m-d",
                         onChange: function (selectedDates, dateStr) {
                              self.handleDateChange();
                              if (selectedDates.length > 0) {
                                   const nextDay = new Date(selectedDates[0]);
                                   nextDay.setDate(nextDay.getDate() + 1);
                                   self.checkoutPicker.set('minDate', nextDay);

                                   if (self.cart.length > 0) {
                                        self.updateDatePickerDisabledDates(self.cart[0].id);
                                   }
                              }
                         },
                         onDayCreate: function (dObj, dStr, fp, dayElem) {
                              if (dayElem.classList.contains('flatpickr-disabled')) {
                                   dayElem.classList.add('unavailable');
                              }
                         }
                    });

                    this.checkoutPicker = flatpickr("#checkout", {
                         minDate: new Date(Date.now() + 86400000), // Tomorrow
                         dateFormat: "Y-m-d",
                         onChange: function (selectedDates, dateStr) {
                              self.handleDateChange();
                         },
                         onDayCreate: function (dObj, dStr, fp, dayElem) {
                              if (dayElem.classList.contains('flatpickr-disabled')) {
                                   dayElem.classList.add('unavailable');
                              }
                         }
                    });
               }

               updateDatePickerDisabledDates(roomId) {
                    if (!roomId) return;

                    const bookedDates = this.bookedDates[roomId] || [];

                    // Convert booked dates to Date objects
                    const disabledDates = bookedDates.map(dateStr => {
                         const [year, month, day] = dateStr.split('-').map(Number);
                         return new Date(year, month - 1, day);
                    });

                    // Flatpickr disable function
                    const disableFunction = (date) => {
                         // Check if date is in disabledDates array
                         return disabledDates.some(disabledDate =>
                              date.getFullYear() === disabledDate.getFullYear() &&
                              date.getMonth() === disabledDate.getMonth() &&
                              date.getDate() === disabledDate.getDate()
                         );
                    };

                    // Update both datepickers
                    this.checkinPicker.set('disable', [disableFunction]);
                    this.checkoutPicker.set('disable', [disableFunction]);

                    // Force redraw of the calendar
                    this.checkinPicker.redraw();
                    this.checkoutPicker.redraw();
               }

               setDefaultDates() {
                    const today = new Date();
                    const tomorrow = new Date(today);
                    tomorrow.setDate(tomorrow.getDate() + 1);

                    this.checkinPicker.setDate(today);
                    this.checkoutPicker.setDate(tomorrow);

                    this.calculateNightsAndPrices();
               }

               setupEventListeners() {
                    document.addEventListener('click', (e) => {
                         if (e.target.closest('.add-to-cart-btn')) {
                              const button = e.target.closest('.add-to-cart-btn');
                              this.addToCart(button.dataset.room);
                         }

                         if (e.target.closest('.remove-btn')) {
                              const button = e.target.closest('.remove-btn');
                              this.removeFromCart(button.dataset.room);
                         }
                    });

                    document.getElementById('breakfast-toggle')?.addEventListener('change', (e) => {
                         this.breakfastIncluded = e.target.checked;
                         this.updateCartDisplay();
                    });

                    document.getElementById('checkout-btn').addEventListener('click', () => this.handleCheckout());

               }

               validateCheckoutButton() {
                    const checkoutBtn = document.getElementById('checkout-btn');
                    checkoutBtn.disabled = this.cart.length === 0;
               }

               formatDate(date) {
                    if (!date) return '';

                    // Handle both string dates and Date objects
                    const d = new Date(date);
                    if (isNaN(d.getTime())) return ''; // Invalid date

                    // Use local date components (not UTC)
                    const year = d.getFullYear();
                    const month = String(d.getMonth() + 1).padStart(2, '0');
                    const day = String(d.getDate()).padStart(2, '0');

                    return `${year}-${month}-${day}`;
               }

               async handleCheckout() {
                    if (this.cart.length === 0 || this.isSubmitting) return;

                    const button = document.getElementById('checkout-btn');
                    const buttonText = document.getElementById('button-text');
                    const spinner = document.getElementById('button-spinner');

                    try {
                         // Show loading state
                         button.disabled = true;
                         this.isSubmitting = true;
                         spinner.classList.add('hidden');
                         buttonText.textContent = 'Processing...';

                         // Get pax information from each room card
                         const bookingData = {
                              checkin_date: this.formatForBackend(this.checkinPicker.selectedDates[0]),
                              checkout_date: this.formatForBackend(this.checkoutPicker.selectedDates[0]),
                              facilities: this.cart.map(item => {
                                   // Find the room card element to get pax info
                                   const roomCard = document.querySelector(`.room-card[data-room-id="${item.id}"]`);
                                   const pax = roomCard ? parseInt(roomCard.querySelector('.feature-item:last-child span').textContent.match(/\d+/)[0]) : 1;
                                   const categoryContainer = roomCard.closest('.category-container');
                                   const category = categoryContainer ? categoryContainer.querySelector('.category-title').textContent.trim() : 'Standard';

                                   return {
                                        facility_id: item.facilityId,
                                        name: item.name,
                                        price: item.price,
                                        nights: this.nights,
                                        total_price: item.price * this.nights,
                                        mainImage: item.mainImage,
                                        pax: pax, // Add pax information here
                                        category: category
                                   };
                              }),
                              breakfast_included: this.breakfastIncluded,
                              breakfast_price: this.breakfastIncluded ? this.breakfastPrice * this.nights * this.cart.length : 0,
                              total_price: this.calculateTotalPrice(),
                         };

                         // Store booking data in sessionStorage to pass to next page
                         sessionStorage.setItem('bookingData', JSON.stringify(bookingData));
                         //localStorage.setItem('bookingCart', JSON.stringify(this.cart));

                         // Redirect to customer information page
                         window.location.href = '/bookings/customer-info';

                    } catch (error) {
                         console.error('Checkout error:', error);
                         showNotification('Failed to proceed to checkout. Please try again.', true);

                         button.textContent = 'Proceed to Customer Information';
                         spinner.classList.remove('hidden');
                         button.disabled = this.cart.length === 0;
                    } finally {
                         this.isSubmitting = false;
                         spinner.classList.remove('hidden');
                    }
               }

               formatForBackend(date) {
                    if (!date) return null;

                    // Create a new date object to avoid modifying the original
                    const d = new Date(date);

                    // Use local date components
                    const year = d.getFullYear();
                    const month = String(d.getMonth() + 1).padStart(2, '0');
                    const day = String(d.getDate()).padStart(2, '0');

                    return `${year}-${month}-${day}`;
               }

               calculateTotalPrice() {
                    const roomsTotal = this.cart.reduce((sum, item) => sum + (item.price * this.nights), 0);
                    const breakfastTotal = this.breakfastIncluded ? this.breakfastPrice * this.nights * this.cart.length : 0;
                    return roomsTotal + breakfastTotal;
               }

               handleDateChange() {
                    const checkinDate = this.checkinPicker.selectedDates[0];
                    const checkoutDate = this.checkoutPicker.selectedDates[0];
                    this.validDates = !!checkinDate && !!checkoutDate && checkinDate < checkoutDate;
                    if (!checkinDate || !checkoutDate) return;

                    if (checkinDate >= checkoutDate) {
                         const newCheckout = new Date(checkinDate);
                         newCheckout.setDate(newCheckout.getDate() + 1);
                         this.checkoutPicker.setDate(newCheckout);
                         return;
                    }

                    if (this.cart.length > 0) {
                         const roomId = this.cart[0].id;
                         const checkin = this.formatDate(checkinDate);
                         const checkout = this.formatDate(checkoutDate);

                         if (!this.isDateRangeAvailable(roomId, checkin, checkout)) {
                              const roomName = this.roomsData[roomId]?.name || 'This room';
                              const formattedCheckin = this.formatDisplayDate(checkinDate);
                              const formattedCheckout = this.formatDisplayDate(checkoutDate);
                              const nextAvailable = this.findNextAvailableDates(roomId, checkinDate);

                              let message = `${roomName} is not available on the selected dates. Please try different dates.`;

                              showNotification(message, true);
                              this.checkoutPicker.setDate(null);
                              return;
                         }
                    }

                    this.calculateNightsAndPrices();
               }

               formatDisplayDate(date) {
                    if (typeof date === 'string') {
                         date = new Date(date);
                    }
                    return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
               }

               calculateNightsAndPrices() {
                    const checkinDate = this.checkinPicker.selectedDates[0];
                    const checkoutDate = this.checkoutPicker.selectedDates[0];

                    if (!checkinDate || !checkoutDate) return;

                    const timeDiff = checkoutDate.getTime() - checkinDate.getTime();
                    this.nights = Math.ceil(timeDiff / (1000 * 3600 * 24));

                    document.getElementById('nights-display').textContent =
                         `${this.nights} night${this.nights !== 1 ? 's' : ''} selected (${this.formatDisplayDate(checkinDate)} - ${this.formatDisplayDate(checkoutDate)})`;

                    if (this.cart.length > 0) {
                         this.updateCartDisplay();
                    }
               }

               addToCart(roomId) {
                    this.saveCartToStorage();
                    try {
                         if (!this.roomsData[roomId]) {
                              throw new Error('Room not found');
                         }

                         // Check if already in cart
                         if (this.cart.some(item => item.id === roomId)) {
                              showNotification('This room is already in your cart', true);
                              return;
                         }

                         // Validate dates
                         const checkinDate = this.checkinPicker.selectedDates[0];
                         const checkoutDate = this.checkoutPicker.selectedDates[0];

                         if (!checkinDate || !checkoutDate) {
                              showNotification('Please select both check-in and check-out dates', true);
                              return;
                         }

                         // Convert to YYYY-MM-DD format for consistency
                         const checkin = this.formatDate(checkinDate);
                         const checkout = this.formatDate(checkoutDate);

                         // Additional validation
                         if (new Date(checkin) >= new Date(checkout)) {
                              showNotification('Check-out date must be after check-in date', true);
                              return;
                         }

                         // Check availability
                         if (!this.isDateRangeAvailable(roomId, checkin, checkout)) {
                              const roomName = this.roomsData[roomId].name;
                              const formattedCheckin = this.formatDisplayDate(checkinDate);
                              const formattedCheckout = this.formatDisplayDate(checkoutDate);

                              // Find next available dates for better UX
                              const nextAvailable = this.findNextAvailableDates(roomId, checkinDate);
                              let message = `${roomName} is not available on the selected dates. Please try different dates.`;


                              showNotification(message, true);
                              return;
                         }

                         // Add to cart
                         const room = this.roomsData[roomId];
                         this.cart.push({
                              id: roomId,
                              name: room.name,
                              price: room.price,
                              images: room.images,
                              mainImage: room.mainImage,
                              nights: this.nights,
                              facilityId: room.facilityId,
                              checkin: checkin,
                              checkout: checkout
                         });

                         // Update UI
                         this.updateCartDisplay();
                         this.updateDatePickerDisabledDates(roomId);
                         this.validateCheckoutButton();

                         // Highlight selected room
                         document.querySelectorAll('.room-card').forEach(card => {
                              card.classList.toggle('selected', card.dataset.roomId === roomId);
                         });

                         // Show success feedback
                         const buttons = document.querySelectorAll(`.add-to-cart-btn[data-room="${roomId}"]`);
                         buttons.forEach(button => {
                              const originalHTML = button.innerHTML;
                              button.innerHTML = '<i class="fas fa-check mr-2"></i> Added!';
                              button.classList.replace('bg-primary', 'bg-green-500');

                              setTimeout(() => {
                                   button.innerHTML = originalHTML;
                                   button.classList.replace('bg-green-500', 'bg-primary');
                              }, 2000);
                         });

                         showNotification(`${room.name} added to your booking`);
                    } catch (error) {
                         console.error('Error adding to cart:', error);
                         showNotification(error.message || 'Failed to add room to cart', true);
                    }
               }

               isDateRangeAvailable(roomId, checkin, checkout) {
                    console.groupCollapsed(`Checking availability for room ${roomId} from ${checkin} to ${checkout}`);
                    console.log("Input Dates:", { checkin, checkout }); // Check if dates match datepicker
                    console.log("Booked Dates:", this.bookedDates[roomId]); // Verify stored booked dates

                    if (!roomId || !checkin || !checkout) {
                         return false;
                    }

                    // Parse dates as local dates (no timezone conversion)
                    const parseLocalDate = (dateStr) => {
                         if (dateStr instanceof Date) {
                              // Return a new date with just the date components (no time)
                              return new Date(dateStr.getFullYear(), dateStr.getMonth(), dateStr.getDate());
                         }
                         const [year, month, day] = dateStr.split('-').map(Number);
                         return new Date(year, month - 1, day);
                    };

                    const checkinDate = parseLocalDate(checkin);
                    const checkoutDate = parseLocalDate(checkout);

                    // Basic validation
                    if (checkinDate >= checkoutDate) {
                         return false;
                    }

                    const today = new Date();
                    today.setHours(0, 0, 0, 0);
                    if (checkinDate < today) {
                         return false;
                    }

                    // Ensure bookedDates is always an array
                    const bookedDates = Array.isArray(this.bookedDates[roomId])
                         ? this.bookedDates[roomId]
                         : [];

                    // Check against individual booked dates
                    for (const dateStr of bookedDates) {
                         try {
                              const bookedDate = parseLocalDate(dateStr);

                              if (bookedDate >= checkinDate && bookedDate < checkoutDate) {

                                   return false;
                              }
                         } catch (e) {
                              console.error('Error parsing booked date:', dateStr, e);
                              continue;
                         }
                    }

                    return true;
               }

               findNextAvailableDates(roomId, afterDate) {
                    try {
                         const bookedDates = this.bookedDates[roomId] || [];

                         // Convert booked dates to Date objects and sort
                         const unavailableDates = bookedDates
                              .map(dateStr => new Date(dateStr))
                              .sort((a, b) => a - b);

                         // Start checking from the day after the requested date
                         let currentDate = new Date(afterDate);
                         currentDate.setDate(currentDate.getDate() + 1);

                         // Find the first available date
                         for (const bookedDate of unavailableDates) {
                              // If current date is before this booked date, we found availability
                              if (currentDate < bookedDate) {
                                   return {
                                        checkin: this.formatDisplayDate(currentDate),
                                        checkout: this.formatDisplayDate(new Date(currentDate.getTime() + 86400000)) // Suggest 1 night
                                   };
                              }

                              // If current date is on or after this booked date, move to next day
                              if (currentDate <= bookedDate) {
                                   currentDate = new Date(bookedDate);
                                   currentDate.setDate(currentDate.getDate() + 1);
                              }
                         }

                         // If we get here, all dates after currentDate are available
                         return {
                              checkin: this.formatDisplayDate(currentDate),
                              checkout: this.formatDisplayDate(new Date(currentDate.getTime() + 86400000)) // Suggest 1 night
                         };
                    } catch (error) {
                         console.error('Error finding next available dates:', error);
                         return null;
                    }
               }

               updateCartDisplay() {
                    const container = document.getElementById('cart-items');
                    const checkoutBtn = document.getElementById('checkout-btn');
                    const totalElement = document.getElementById('total-price');
                    const breakfastSummary = document.getElementById('breakfast-summary');
                    const breakfastNights = document.getElementById('breakfast-nights');
                    const breakfastPrice = document.getElementById('breakfast-price');

                    if (this.cart.length === 0) {
                         container.innerHTML = '<div class="text-gray-400 text-center py-6"><i class="fas fa-shopping-cart text-3xl mb-3 opacity-50"></i><p>Your list is empty</p></div>';
                         totalElement.textContent = '₱0.00';
                         checkoutBtn.disabled = true;
                         breakfastSummary?.classList.add('hidden');
                         return;
                    }

                    checkoutBtn.disabled = false;

                    let subtotal = 0;
                    let html = '';

                    this.cart.forEach(item => {
                         const itemTotal = item.price * this.nights;
                         subtotal += itemTotal;

                         html += `
                                                                                                                                                                                                             <div class="flex justify-between items-start border-b border-gray-100 pb-4">
                                                                                                                                                                                                                  <div class="flex items-start">
                                                                                                                                                                                                                  <img src="${item.mainImage}" alt="${item.name}" class="w-16 h-16 object-cover rounded-lg mr-3" onerror="this.src='https://via.placeholder.com/500x300?text=Image+Not+Found'">
                                                                                                                                                                                                                  <div>
                                                                                                                                                                                                                       <h4 class="font-medium text-dark">${item.name}</h4>
                                                                                                                                                                                                                       <div class="text-sm text-gray-600">${this.nights} night${this.nights !== 1 ? 's' : ''} × ₱${item.price.toFixed(2)}</div>
                                                                                                                                                                                                                  </div>
                                                                                                                                                                                                                  </div>
                                                                                                                                                                                                                  <div class="text-right">
                                                                                                                                                                                                                  <div class="font-medium">₱${itemTotal.toFixed(2)}</div>
                                                                                                                                                                                                                  <button class="text-red-500 text-sm hover:text-red-700 transition remove-btn" data-room="${item.id}">
                                                                                                                                                                                                                       <i class="far fa-trash-alt mr-1"></i> Remove
                                                                                                                                                                                                                  </button>
                                                                                                                                                                                                                  </div>
                                                                                                                                                                                                             </div>
                                                                                                                                                                                                        `;
                    });

                    container.innerHTML = html;

                    if (this.breakfastIncluded && breakfastSummary) {
                         const breakfastTotal = this.breakfastPrice * this.nights * this.cart.length;
                         breakfastNights.textContent = `${this.nights} night${this.nights !== 1 ? 's' : ''} × ${this.cart.length} room${this.cart.length !== 1 ? 's' : ''}`;
                         breakfastPrice.textContent = `₱${breakfastTotal.toFixed(2)}`;
                         breakfastSummary.classList.remove('hidden');
                         subtotal += breakfastTotal;
                    } else if (breakfastSummary) {
                         breakfastSummary.classList.add('hidden');
                    }

                    totalElement.textContent = `₱${subtotal.toFixed(2)}`;
               }

               // Add this method to BookingSystem class
               setupBreakfastToggle() {
                    const breakfastToggle = document.getElementById('breakfast-toggle');
                    if (!breakfastToggle) return;

                    // Set initial state
                    breakfastToggle.checked = this.breakfastIncluded;

                    // Update event listener to handle state properly
                    breakfastToggle.addEventListener('change', (e) => {
                         this.breakfastIncluded = e.target.checked;
                         this.updateCartDisplay();
                         this.saveCartToStorage();
                    });
               }

               initializeCart() {
                    this.cart = [];
                    this.setupBreakfastToggle();
                    this.updateCartDisplay();
                    this.validateCheckoutButton();
                    if (this.cart.length > 0) {
                         // Restore selected state for rooms in cart
                         this.cart.forEach(item => {
                              document.querySelectorAll(`.room-card[data-room-id="${item.id}"]`)
                                   .forEach(card => card.classList.add('selected'));
                         });
                         this.updateCartDisplay();
                         this.validateCheckoutButton();

                         // Restore date pickers if we have checkin/checkout dates
                         if (this.cart[0].checkin && this.cart[0].checkout) {
                              this.checkinPicker.setDate(this.cart[0].checkin, true);
                              this.checkoutPicker.setDate(this.cart[0].checkout, true);

                              // Force date validation
                              setTimeout(() => {
                                   this.handleDateChange();
                                   this.updateDatePickerDisabledDates(this.cart[0].id);
                              }, 100);
                         }
                    }
               }

               removeFromCart(roomId) {
                    this.saveCartToStorage();
                    const room = this.roomsData[roomId];
                    this.cart = this.cart.filter(item => item.id !== roomId);
                    this.updateCartDisplay();
                    this.saveCartToStorage();

                    document.querySelectorAll('.room-card').forEach(card => {
                         if (card.dataset.roomId === roomId) {
                              card.classList.remove('selected');
                         }
                    });

                    const buttons = document.querySelectorAll(`.add-to-cart-btn[data-room="${roomId}"]`);
                    buttons.forEach(button => {
                         button.innerHTML = 'Select Room';
                         button.classList.remove('bg-green-500', 'bg-primary');
                         button.classList.add('bg-primary');
                         button.disabled = false;
                    });

                    if (this.cart.length === 0) {
                         this.checkinPicker.set('disable', []);
                         this.checkoutPicker.set('disable', []);
                    }

                    showNotification(`${room.name} removed from your booking`);
               }
          }

          document.addEventListener('DOMContentLoaded', () => {
               window.bookingSystem = new BookingSystem();

               window.addEventListener('beforeunload', () => {
                    if (window.bookingSystem?.cart?.length > 0) {
                         sessionStorage.setItem('bookingCart', JSON.stringify(window.bookingSystem.cart));
                         sessionStorage.setItem('breakfastIncluded', window.bookingSystem.breakfastIncluded);
                    }
               });
          });

          // Helper function to show notifications
          function showNotification(message, isError = false) {
               const notification = document.getElementById('notification');
               const notificationMessage = document.getElementById('notification-message');

               notificationMessage.textContent = message;
               notification.className = isError ? 'notification error show' : 'notification show';

               // Clear any existing timeout to prevent hiding a new notification prematurely
               if (notification.timeoutId) {
                    clearTimeout(notification.timeoutId);
               }

               notification.timeoutId = setTimeout(() => {
                    notification.className = notification.className.replace('show', '');
               }, 5000);
          }
     </script>
@endsection