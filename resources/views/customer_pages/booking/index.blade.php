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
               color: #1F2937;
               padding-left: 0;
               position: relative;
               display: inline-block;
               font-size: clamp(1.25rem, 4vw, 1.75rem);
          }

          @media (max-width: 480px) {
               .category-title {
                    font-size: 1.125rem;
                    /* Smaller category titles */
                    margin-bottom: 0.5rem;
               }
          }

          .category-title:after {
               content: '';
               position: absolute;
               bottom: -0.25rem;
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
               min-width: min(260px, 85vw);
               /* Responsive min-width */
               width: min(260px, 85vw);
               /* Responsive width */
               flex: 0 0 auto;
               border-radius: 0.875rem;
               overflow: visible;
               background: white;
               scroll-snap-align: start;
               position: relative;
               margin-bottom: 0.5rem;
          }

          @media (max-width: 360px) {
               .room-card {
                    min-width: min(180px, 72vw);
                    width: min(240px, 82vw);
                    border-radius: 0.75rem;
                    margin-bottom: 0.25rem;
                    /* reduced spacing between cards */
               }

               .room-details {
                    padding: 0.5rem !important;
                    /* tighter padding */
               }

               .room-title {
                    font-size: 0.95rem !important;
                    line-height: 1.1;
                    margin-bottom: 0.25rem;
               }

               .room-features {
                    gap: 0.25rem;
                    margin-bottom: 0.375rem;
                    flex-direction: column;
               }

               .feature-item {
                    font-size: 0.7rem !important;
               }

               .feature-icon {
                    width: 12px !important;
                    margin-right: 0.2rem !important;
               }

               .price-container {
                    margin-top: 0.5rem;
                    padding-top: 0.5rem;
                    gap: 0.375rem;
               }

               .night-price {
                    font-size: 1rem !important;
               }

               .book-button {
                    padding: 0.5rem 0.75rem !important;
                    font-size: 0.75rem !important;
                    min-width: 100px !important;
               }

               .room-image-container {
                    border-radius: 0.5rem 0.5rem 0 0;
                    aspect-ratio: 4/2.8;
                    /* reduced image height */
               }
          }


          @media (max-width: 380px) {
               .room-card {
                    min-width: min(240px, 82vw);
                    /* Even smaller */
                    width: min(240px, 82vw);
                    border-radius: 0.75rem;
                    margin-bottom: 0.375rem;
               }

               .room-details {
                    padding: 0.75rem !important;
                    /* Tighter padding */
               }

               .room-title {
                    font-size: 1rem !important;
                    /* Smaller title */
                    line-height: 1.2;
                    margin-bottom: 0.375rem;
               }

               .room-features {
                    gap: 0.375rem;
                    margin-bottom: 0.5rem;
                    flex-direction: column;
                    /* Stack features vertically */
               }

               .feature-item {
                    font-size: 0.75rem !important;
               }

               .feature-icon {
                    width: 14px !important;
                    margin-right: 0.25rem !important;
               }

               .price-container {
                    margin-top: 0.625rem;
                    padding-top: 0.625rem;
                    gap: 0.5rem;
               }

               .night-price {
                    font-size: 1.125rem !important;
               }

               .book-button {
                    padding: 0.625rem 0.875rem !important;
                    font-size: 0.8125rem !important;
                    min-width: 110px !important;
               }

               .room-image-container {
                    border-radius: 0.5rem 0.5rem 0 0;
                    /* Smaller radius */
                    aspect-ratio: 4/3;
                    /* Maintain aspect ratio */
               }
          }


          @media (min-width: 381px) and (max-width: 480px) {
               .room-card {
                    min-width: min(270px, 80vw);
                    width: min(270px, 80vw);
               }
          }

          @media (min-width: 481px) {
               .room-card {
                    min-width: min(320px, 75vw);
                    /* Adjusted breakpoint */
                    width: min(320px, 75vw);
               }
          }

          @media (min-width: 768px) {
               .room-card {
                    min-width: min(350px, 40vw);
                    /* Slightly smaller */
                    width: min(350px, 40vw);
               }
          }

          @media (min-width: 1024px) {
               .room-card {
                    min-width: 360px;
                    /* Slightly smaller than original 380px */
                    width: 360px;
               }
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

          @media (max-width: 480px) {
               .room-image-container {
                    border-radius: 0.625rem 0.625rem 0 0;
                    /* Smaller radius */
               }
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
               top: -10px;
               transform: translateY(-20%);
               left: 0;
               right: 0;
               display: flex;
               justify-content: space-between;
               pointer-events: none;
               z-index: 20;
               height: auto;
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
               z-index: 21;
               transform: none;
               /* Add this to center the arrow itself */
          }

          @media (min-width: 768px) {
               .scroll-arrow {
                    width: 40px;
                    height: 40px;
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
               box-shadow: 0 6px 18px rgba(0, 0, 0, 0.35);
               cursor: pointer;
               border-radius: 50%;
               color: #d81d2dff;
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
               .rooms-scroll-container {
                    gap: 0.75rem;
                    /* Reduced gap */
                    padding: 0.75rem 0.5rem 1.5rem;
                    /* Reduced padding */
               }

               .scroll-arrow {
                    width: 32px;
                    height: 32px;
                    top: -15px;
                    /* Adjusted position */
               }

               .scroll-arrow i {
                    font-size: 0.875rem;
               }
          }

          /* Optimize room details for mobile */
          @media (max-width: 480px) {
               .room-details {
                    padding: 0.875rem;
                    /* Reduced padding */
               }

               .room-title {
                    font-size: 1.0625rem;
                    /* Slightly smaller */
                    margin-bottom: 0.375rem;
               }

               .room-features {
                    font-size: 0.75rem;
                    gap: 0.625rem;
                    margin-bottom: 0.625rem;
               }

               .feature-icon {
                    width: 14px;
                    margin-right: 0.25rem;
               }

               .price-container {
                    gap: 0.5rem;
                    margin-top: 0.875rem;
                    padding-top: 0.875rem;
               }

               .price-details {
                    min-height: 50px;
                    /* Reduced height */
               }

               .night-price {
                    font-size: 1.25rem;
                    /* Smaller price */
               }

               .night-text {
                    font-size: 0.7rem;
               }

               .book-button {
                    padding: 0.75rem 1rem;
                    /* Smaller button */
                    font-size: 0.875rem;
                    min-width: 120px;
                    /* Smaller min-width */
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

          @media (max-width: 480px) {
               .included-ribbon {
                    font-size: 0.6rem;
                    padding: 0.2rem 0.5rem;
                    top: 6px;
                    left: -4px;
               }
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

          .category-container .relative {
               position: relative;
               min-height: 300px;
               /* Ensure minimum height for arrow visibility */
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

          /* Floating notification - Responsive */
          .notification {
               position: fixed;
               top: 20px;
               right: 20px;
               left: 20px;
               background-color: #10B981;
               color: white;
               padding: 1rem 1.5rem;
               border-radius: 0.5rem;
               box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
               transform: translateY(-100px);
               opacity: 0;
               transition: all 0.3s ease;
               z-index: 1000;
               display: flex;
               align-items: center;
               max-width: 400px;
               margin: 0 auto;
          }

          .notification.show {
               transform: translateY(0);
               opacity: 1;
          }

          .notification.error {
               background-color: #DC2626;
          }

          /* Mobile-first responsive styles */
          @media (max-width: 640px) {
               .notification {
                    top: 10px;
                    right: 10px;
                    left: 10px;
                    padding: 0.875rem 1.25rem;
                    max-width: none;
                    margin: 0 10px;
               }

               .notification i {
                    font-size: 1rem;
                    margin-right: 0.5rem;
               }

               #notification-message {
                    font-size: 0.875rem;
                    line-height: 1.4;
               }
          }

          /* Small mobile devices */
          @media (max-width: 380px) {
               .notification {
                    padding: 0.75rem 1rem;
                    top: 5px;
                    right: 5px;
                    left: 5px;
                    margin: 0 5px;
               }

               .notification i {
                    font-size: 0.875rem;
                    margin-right: 0.375rem;
               }

               #notification-message {
                    font-size: 0.8125rem;
               }
          }

          /* Tablet and larger screens */
          @media (min-width: 641px) {
               .notification {
                    right: 20px;
                    left: auto;
                    margin: 0;
               }
          }

          /* Large screens */
          @media (min-width: 1024px) {
               .notification {
                    right: 30px;
                    top: 25px;
               }
          }

          /* Extra large screens */
          @media (min-width: 1280px) {
               .notification {
                    right: 40px;
                    top: 30px;
               }
          }

          /* Animation for better mobile experience */
          @keyframes slideInDown {
               from {
                    transform: translateY(-100%);
                    opacity: 0;
               }

               to {
                    transform: translateY(0);
                    opacity: 1;
               }
          }

          @keyframes slideOutUp {
               from {
                    transform: translateY(0);
                    opacity: 1;
               }

               to {
                    transform: translateY(-100%);
                    opacity: 0;
               }
          }

          /* Enhanced mobile interactions */
          .notification {
               touch-action: pan-y;
          }

          /* Prevent notification from interfering with form inputs on mobile */
          @media (max-width: 640px) {
               .notification {
                    z-index: 9999;
                    /* Higher z-index to ensure it's above modals */
               }
          }

          /* Adjust position when keyboard is visible on mobile */
          @media (max-width: 640px) and (max-height: 500px) {
               .notification {
                    top: 5px;
                    padding: 0.5rem 1rem;
               }
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

          .unavailable-dates-calendar-container {
               border-top: 1px dashed #e5e7eb;
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

          .toggle-unavailable-dates.active i:last-child {
               transform: rotate(180deg);
          }

          .calendar-container {
               width: 100%;
               display: block;
               padding: 0;
               /* Constraint the container */
               max-width: 100%;
               overflow: hidden;
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
               width: 100%;
               display: block;
               overflow: hidden;
               /* Prevents overflow */
               padding: 10px 0;
          }

          .flatpickr-calendar.inline {
               width: 100% !important;
               max-width: 100% !important;
               min-width: 0 !important;
               /* Allow shrinking */
               box-shadow: none !important;
               background: transparent !important;
               margin: 0 auto !important;
               padding: 0 !important;
               top: 0 !important;
          }

          .flatpickr-innerContainer {
               width: 100% !important;
               display: block !important;
          }

          .flatpickr-rContainer {
               width: 100% !important;
               display: block !important;
          }

          .flatpickr-innerContainer,
          .flatpickr-rContainer {
               width: 100% !important;
          }

          .flatpickr-days {
               width: 100% !important;
               display: flex !important;
               border: none !important;
          }

          @media (max-width: 480px) {
               .calendar-container {
                    padding: 0.75rem;
                    border-radius: 0.5rem;
                    max-width: 95%;
                    position: relative;
               }
          }

          .calendar-container .flatpickr-calendar {
               width: 100% !important;
               max-width: 100% !important;
               box-shadow: none !important;
               margin: 0 !important;
               padding: 0 !important;
          }

          .calendar-container .flatpickr-days {
               width: 100% !important;
               border: none !important;
          }

          .calendar-container .dayContainer {
               width: 100% !important;
               min-width: 100% !important;
               max-width: 100% !important;
               padding: 5px !important;
          }

          .dayContainer {
               width: 100% !important;
               min-width: 100% !important;
               max-width: 100% !important;
               display: flex !important;
               justify-content: space-between !important;
               padding: 0 !important;
          }

          .calendar-container .flatpickr-day {
               min-width: 30px !important;
               height: 30px !important;
               line-height: 30px !important;
               margin: 2px !important;
               font-size: 12px !important;
          }

          /* Mobile-specific calendar styles */
          @media (max-width: 480px) {
               .calendar-container .flatpickr-calendar {
                    font-size: 0.85rem;
                    width: 95% !important;
                    left: 50% !important;
                    transform: translateX(-50%);
                    position: relative;
               }

               .calendar-container .flatpickr-day {
                    min-width: 28px !important;
                    height: 28px !important;
                    line-height: 28px !important;
                    margin: 1px !important;
                    font-size: 11px !important;
               }

               .calendar-container .flatpickr-weekdays {
                    height: 25px !important;
               }

               .calendar-container .flatpickr-weekday {
                    font-size: 10px !important;
               }

               .calendar-container .flatpickr-current-month {
                    font-size: 14px !important;
                    padding: 4px 0 !important;
               }

               .calendar-container .flatpickr-months .flatpickr-month,
               .calendar-container .flatpickr-months .flatpickr-next-month,
               .calendar-container .flatpickr-months .flatpickr-prev-month {
                    height: 35px !important;
               }
          }

          /* Very small screens */
          @media (max-width: 360px) {
               .calendar-container .flatpickr-day {
                    position: relative;
                    min-width: 24px !important;
                    height: 24px !important;
                    line-height: 24px !important;
                    margin: 1px !important;
                    font-size: 10px !important;
               }

               .calendar-container .flatpickr-weekdays {
                    height: 22px !important;
               }

               .calendar-container .flatpickr-weekday {
                    font-size: 9px !important;
               }
          }

          .unavailable-dates-content {
               max-height: none !important;
               overflow: visible !important;
          }

          .unavailable-dates-content:not(.hidden) {
               position: relative;
               z-index: 1000;
               /* Higher z-index */
               margin-bottom: 20px;
               overflow: visible !important;
               /* Allow calendar to extend */
          }

          /* Ensure room card doesn't interfere */
          .room-card {
               overflow: visible !important;
               position: relative;
          }

          /* When calendar is open, ensure proper stacking */
          .unavailable-dates-content:not(.hidden) .calendar-container {
               z-index: 100;
               position: relative;
          }

          .unavailable-dates-content {
               max-height: none !important;
               /* Remove height restriction */
               overflow: visible !important;
               /* Allow calendar to expand fully */
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

          /* In-button-area notification styles */
          .button-notification {
               position: relative;
               width: 100%;
          }

          .notification-box {
               position: absolute;
               bottom: 100%;
               left: 0;
               right: 0;
               background-color: #10B981;
               color: white;
               padding: 0.875rem 1rem;
               border-radius: 0.5rem;
               box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
               margin-bottom: 0.75rem;
               transform: translateY(10px);
               opacity: 0;
               visibility: hidden;
               transition: all 0.3s ease;
               z-index: 10;
          }

          .notification-box.show {
               transform: translateY(0);
               opacity: 1;
               visibility: visible;
          }

          .notification-box.error {
               background-color: #DC2626;
          }

          .notification-box.warning {
               background-color: #F59E0B;
          }

          .notification-box-content {
               display: flex;
               align-items: flex-start;
               gap: 0.5rem;
          }

          .notification-box-content i {
               margin-top: 0.125rem;
               flex-shrink: 0;
          }

          .notification-box-message {
               font-size: 0.875rem;
               line-height: 1.4;
               flex-grow: 1;
          }

          .notification-box-close {
               background: none;
               border: none;
               color: white;
               cursor: pointer;
               padding: 0.125rem;
               margin-left: 0.5rem;
               flex-shrink: 0;
          }

          /* Adjust button container to accommodate notification */
          #checkout-btn-container {
               position: relative;
               margin-top: 2rem;
          }

          #hold-status-message {
               transition: all 0.3s ease;
               max-height: 0;
               overflow: hidden;
               opacity: 0;
          }

          #hold-status-message.active {
               max-height: 100px;
               opacity: 1;
               margin-bottom: 1rem;
               padding: 0.75rem;
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

                         <div class="bg-red-50 border border-red-200 rounded-lg p-4 md:p-6 mt-6 mb-6 w-full">
                              <div class="flex items-start">
                                   <div class="bg-red-100 p-2 md:p-3 rounded-full mr-3 md:mr-4 shadow-inner flex-shrink-0">
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
                                             class="grid grid-cols-2 sm:grid-cols-1 md:grid-cols-2 gap-2 md:gap-x-8 md:gap-y-3 text-red-800 text-[9px] sm:text-xs md:text-sm">
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
                                   $groupedFacilities = $facilities->groupBy('category');
                              @endphp

                              <script>
                                   console.log('Unavailable Dates Data:', @json($unavailable_dates));
                              </script>
                              @foreach($groupedFacilities as $category => $facilitiesInCategory)
                                   <div class="category-container">
                                        <h3 class="category-title font-bold text-dark text-lg sm:text-xl md:text-2xl">
                                             {{ $category }}
                                        </h3>

                                        <div class="relative" style="overflow: visible; position: relative; margin-top: 40px;">


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
                                                                                $facility->bed_number != 1 ? 's' : ''}}</span>
                                                                                                                        </div>
                                                                           @endif
                                                                           <div class="feature-item">
                                                                                <i class="fas fa-user-friends feature-icon"></i>
                                                                                <span>{{ $facility->pax }} Pax</span>
                                                                           </div>
                                                                      </div>

                                                                      <div class="unavailable-dates-calendar-container mb-2">
                                                                           <button
                                                                                class="toggle-unavailable-dates text-sm text-red-600 font-medium flex items-center cursor-pointer">
                                                                                <i class="fas fa-calendar mr-2"></i>
                                                                                <a class="md:text-sm lg:text-md hover:underline">View Availability</a>
                                                                                <i class="fas fa-chevron-down ml-2 text-xs transition-transform duration-200 animate-bounce"></i>
                                                                           </button>

                                                                           <div class="unavailable-dates-content hidden mt-2">
                                                                                <div class="legend-item flex items-center gap-2 mb-2">
                                                                                     <div class="legend-color w-4 h-4 rounded-sm"
                                                                                          style="background-color: #f4bdc2; border: 1px solid #f1aeb5;">
                                                                                     </div>
                                                                                     <span class="text-sm text-gray-700">Booked</span>
                                                                                </div>

                                                                                <div class="legend-item flex items-center gap-2">
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
                                                                                <div class="calendar-container w-full"
                                                                                     id="calendar-{{ $facility->id }}"
                                                                                     data-booked='@json($sortedDates)'>
                                                                                </div>
                                                                           </div>
                                                                      </div>

                                                                 </div>

                                                                 <!-- Price and Book Button - Fixed at bottom -->
                                                                 <div class="mt-auto pt-1">
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
                                                                                                                                       <div
                                                                                                                                            class="night-price text-gray-400 line-through text-xs xs:text-sm sm:text-base">
                                                                                                                                            ₱{{ number_format($facility->price) }}
                                                                                                                                       </div>

                                                                                                                                       <!-- New price -->
                                                                                                                                       <div
                                                                                                                                            class="night-price text-red-600 font-semibold text-sm xs:text-base">
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
                                                                                                                                       <div
                                                                                                                                            class="flex items-center text-xs xs:text-sm text-green-600 font-medium">
                                                                                                                                            <i class="fas fa-tag mr-1"></i>
                                                                                                                                            @if ($activeDiscount->discount_type === 'percent')
                                                                                                                                                 {{ $activeDiscount->discount_value }}% OFF
                                                                                                                                            @else
                                                                                                                                                 ₱{{ number_format($activeDiscount->discount_value) }}
                                                                                                                                                 OFF
                                                                                                                                            @endif
                                                                                                                                       </div>
                                                                                                                                  </div>
                                                                                @else
                                                                                     <div
                                                                                          class="night-price font-semibold text-sm xs:text-base">
                                                                                          ₱{{ number_format($facility->price) }}
                                                                                     </div>
                                                                                @endif

                                                                                <div
                                                                                     class="night-text text-xs xs:text-sm text-gray-500">
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
                         <h2 class="text-md md:text-xl font-bold text-dark mb-4 flex items-center">
                              <i class="far fa-calendar-alt text-primary mr-3 text-xl"></i>
                              Select Your Dates
                         </h2>

                         <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-6">
                              <!-- Check-in -->
                              <div>
                                   <label for="checkin"
                                        class="block text-sm sm:text-base font-medium text-gray-700 mb-1 sm:mb-2">
                                        Check-in Date
                                   </label>
                                   <input type="text" id="checkin" placeholder="Select date"
                                        class="w-full px-3 sm:px-4 py-2 sm:py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent datepicker form-input">
                              </div>

                              <!-- Check-out -->
                              <div>
                                   <label for="checkout"
                                        class="block text-sm sm:text-base font-medium text-gray-700 mb-1 sm:mb-2">
                                        Check-out Date
                                   </label>
                                   <input type="text" id="checkout" placeholder="Select date"
                                        class="w-full px-3 sm:px-4 py-2 sm:py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent datepicker form-input">
                              </div>
                         </div>



                         <!-- Info Section -->
                         <div class="mt-4 space-y-3">
                              <div class="flex items-center text-xs md:text-sm text-gray-600">
                                   <i class="fas fa-info-circle text-primary mr-2"></i>
                                   <span id="nights-display">Minimum stay: 1 night</span>
                              </div>
                         </div>
                    </div>


                    <!-- Breakfast Option Card -->
                    <div class="bg-white rounded-xl p-8 border border-lightgray">
                         <h2 class="text-md md:text-xl font-bold text-dark mb-4 flex items-center">
                              <i class="fas fa-utensils text-primary mr-3 text-xl"></i>
                              Breakfast Option
                         </h2>
                         @if($breakfast_price->status == 'Active')
                                        <div class="flex items-center justify-between">
                                             <div>
                                                  <h3 class="text-sm md:text-lg font-medium text-gray-800">Add Breakfast for your stay</h3>
                                                  <p class="text-xs md:text-sm text-gray-600">Enjoy a delicious breakfast each morning for
                                                       just ₱{{
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
                         <h2 class="text-md md:text-xl font-bold text-gray-800 mb-5 flex items-center">
                              <i class="fas fa-receipt text-primary mr-3 text-xl"></i>
                              Booking Summary
                         </h2>

                         <div id="cart-hold-warning"
                              class="mb-4 p-4 bg-orange-50 border border-orange-400 rounded-lg flex items-start gap-3 hidden">
                              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                   stroke="currentColor" class="w-5 h-5 text-orange-600 mt-0.5 flex-shrink-0">
                                   <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
                              </svg>
                              <p class="text-xs md:text-sm text-orange-800">
                                   If a room is on hold, please <b>choose another date</b>, <b>select a different room</b>,
                                   <b>or wait 10–15 minutes</b>.
                              </p>
                         </div>

                         <div id="cart-items" class="space-y-4 min-h-[120px]">
                              <div class="text-gray-400 text-center py-6">
                                   <i class="fas fa-shopping-cart text-xs md:text-3xl mb-3 opacity-50"></i>
                                   <p>Your list is empty</p>
                              </div>
                         </div>

                         @if($breakfast_price->status == 'Active')
                              <div id="breakfast-summary"
                                   class="hidden border-b border-gray-100 pb-4 mt-4 transition-all duration-300 ease-in-out">
                                   <div class="flex justify-between items-start">
                                        <div class="flex items-start">
                                             <div class="bg-red-50 p-3 rounded-xl mr-3 flex-shrink-0">
                                                  <i class="fas fa-utensils text-red-600 text-lg"></i>
                                             </div>
                                             <div>
                                                  <h4 class="font-bold text-gray-800 text-sm md:text-base">Daily Breakfast</h4>
                                                  <div class="text-xs text-gray-500 mt-1" id="breakfast-nights">
                                                       1 night × 1 room
                                                  </div>
                                                  <span
                                                       class="inline-block mt-1 text-[10px] font-semibold text-red-600 bg-red-50 px-2 py-0.5 rounded-full">
                                                       Add-on
                                                  </span>
                                             </div>
                                        </div>
                                        <div class="text-right">
                                             <div class="font-bold text-gray-800" id="breakfast-price">
                                                  ₱{{ number_format($breakfast_price->price, 2) }}
                                             </div>
                                        </div>
                                   </div>
                              </div>
                         @endif

                         <div class="border-t border-gray-200 pt-4 mt-4">
                              <div class="flex justify-between items-center">
                                   <span class="text-lg font-bold text-gray-800">Total</span>
                                   <span class="text-base sm:text-lg md:text-2xl font-bold text-primary"
                                        id="total-price">₱0.00</span>
                              </div>
                         </div>

                         <div id="hold-status-message" class="rounded-lg text-sm flex items-center gap-2 mt-4 hidden">
                              <i class="fas fa-info-circle"></i>
                              <span class="msg-text"></span>
                         </div>

                         <button id="checkout-btn"
                              class="w-full mt-2 bg-gradient-to-r from-primary to-secondary hover:from-primary/90 hover:to-secondary/90 text-white font-bold py-4 px-6 rounded-xl transition-all duration-300 shadow-md hover:shadow-lg flex items-center justify-center disabled:opacity-70 disabled:transform-none hover:-translate-y-0.5 active:translate-y-0 btn-primary"
                              disabled>
                              <span id="button-text">Proceed to Your Details</span>
                              <!-- <div id="button-spinner" class="loading-spinner hidden"></div> -->
                         </button>
                    </div>

               </div>
          </div>




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
                         // Blade syntax for breakfast price
                         this.breakfastPrice = {{ $breakfast_price->status == 'Active' ? $breakfast_price->price : 0 }};
                         this.isSubmitting = false;
                         this.validDates = false;
                         this.csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

                         // Load breakfast state
                         const savedBreakfast = sessionStorage.getItem('breakfastIncluded');
                         this.breakfastIncluded = savedBreakfast === 'true';

                         this.initializeRoomsData();
                         this.init();
                    }

                    saveCartToStorage() {
                         sessionStorage.setItem('bookingCart', JSON.stringify(this.cart));
                         sessionStorage.setItem('breakfastIncluded', this.breakfastIncluded);
                    }

                    initializeRoomsData() {
                         document.querySelectorAll('.room-card').forEach(card => {
                              const roomId = card.dataset.roomId;
                              const images = JSON.parse(card.dataset.images);

                              // Initial load from DOM (fallback)
                              let bookedDates = [];
                              try {
                                   bookedDates = JSON.parse(card.dataset.bookedDates || '[]');
                              } catch (e) { bookedDates = []; }

                              if (!this.bookedDates[roomId]) {
                                   this.bookedDates[roomId] = bookedDates;
                              }

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
                         });
                    }

                    async init() {

                         this.initializeRoomsData();

                         this.setupEventListeners();
                         this.initDatePickers();
                         this.setDefaultDates();
                         this.setupScrollArrows();
                         this.setupUnavailableDatesToggle();
                         this.initializeCart();

                         this.fetchRealTimeAvailability().then(() => {
                              this.pollAvailability(); // Start the loop
                         });
                    }

                    pollAvailability() {
                         setTimeout(async () => {
                              // Only fetch if tab is active to save battery
                              if (!document.hidden) {
                                   await this.fetchRealTimeAvailability();
                              }
                              // Call self again ONLY after the request finished
                              this.pollAvailability();
                         }, 10000);
                    }

                    async fetchRealTimeAvailability() {
                         try {
                              const response = await fetch("{{ route('booked.dates') }}");
                              if (!response.ok) throw new Error('Network response was not ok');

                              const data = await response.json();
                              this.processUnavailableDates(data);
                              this.refreshCalendars();

                              //CRITICAL: Check if items currently in cart are still valid
                              this.validateCartRealTime();

                              // If we have a selected room in the cart, re-validate availability
                              if (this.cart.length > 0) {
                                   this.validateCartAvailability();
                              }

                         } catch (error) {
                              console.error('Error fetching real-time availability:', error);
                         }
                    }

                    processUnavailableDates(data) {
                         // Data structure is { facility_id: [ {checkin_date, checkout_date}, ... ] }
                         const processed = {};

                         Object.keys(data).forEach(facilityId => {
                              const ranges = data[facilityId];
                              let datesArray = [];

                              ranges.forEach(range => {
                                   // Convert range to individual dates
                                   const start = new Date(range.checkin_date);
                                   const end = new Date(range.checkout_date);

                                   // Logic: exclude checkout date from being "unavailable" for checkin
                                   // But include it for the visual calendar
                                   // Adjust based on your specific business logic (nightly vs daily)
                                   // Here we loop from start up to (but not including) end for booking logic
                                   // But usually, we block the specific nights.

                                   let loopDate = new Date(start);
                                   // We subtract 1 day from end because checkout day is usually available for new checkin
                                   const lastNight = new Date(end);
                                   lastNight.setDate(lastNight.getDate() - 1);

                                   while (loopDate <= lastNight) {
                                        datesArray.push(this.formatDate(loopDate));
                                        loopDate.setDate(loopDate.getDate() + 1);
                                   }
                              });

                              // Remove duplicates and store
                              processed[`facility-${facilityId}`] = [...new Set(datesArray)];
                         });

                         // Update local state
                         this.bookedDates = { ...this.bookedDates, ...processed };
                    }

                    refreshCalendars() {
                         // 1. Refresh individual room calendars
                         document.querySelectorAll('.calendar-container').forEach(el => {
                              const fp = el._flatpickr;
                              const roomId = el.closest('.room-card').dataset.roomId;

                              if (fp && this.bookedDates[roomId]) {
                                   // Update the disable configuration
                                   fp.set('disable', [
                                        (date) => {
                                             const dateStr = this.formatDate(date);
                                             return this.bookedDates[roomId].includes(dateStr);
                                        },
                                        (date) => date < new Date().setHours(0, 0, 0, 0)
                                   ]);
                                   fp.redraw();
                              }
                         });

                         // 2. Refresh main Datepickers if necessary
                         // Note: Main datepickers usually only disable dates if a specific room is contextually selected
                         // OR if you want to disable dates where ALL rooms are booked (complex logic).
                         // Currently keeping main pickers open, but validation happens on "Select Room".
                    }

                    validateCartRealTime() {
                         if (this.cart.length === 0) return;

                         let hasConflict = false;
                         let conflictRoomName = "";

                         this.cart.forEach(item => {
                              // Check availability against the LATEST bookedDates
                              const isAvailable = this.isDateRangeAvailable(item.id, item.checkin, item.checkout);

                              if (!isAvailable) {
                                   hasConflict = true;
                                   conflictRoomName = item.name;

                                   // Visual indicator on the cart item
                                   const removeBtn = document.querySelector(`.remove-btn[data-room="${item.id}"]`);
                                   if (removeBtn) {
                                        removeBtn.closest('.flex.justify-between').classList.add('bg-red-50');
                                   }
                              }
                         });

                         const checkoutBtn = document.getElementById('checkout-btn');
                         const buttonText = document.getElementById('button-text');

                         if (hasConflict) {
                              // BLOCK THE USER
                              checkoutBtn.disabled = true;
                              checkoutBtn.classList.add('cursor-not-allowed', 'opacity-50');
                              checkoutBtn.classList.remove('hover:-translate-y-0.5');
                              buttonText.innerHTML = `<i class="fas fa-exclamation-triangle mr-2"></i> ${conflictRoomName} is no longer available`;
                         } else {
                              // Restore button if conflict is resolved
                              if (!this.isSubmitting) {
                                   checkoutBtn.disabled = false;
                                   checkoutBtn.classList.remove('cursor-not-allowed', 'opacity-50');
                                   checkoutBtn.classList.add('hover:-translate-y-0.5');
                                   buttonText.textContent = 'Proceed to Your Details';
                                   this.hideHoldStatus();
                              }
                         }
                    }

                    validateCartAvailability() {
                         let hasConflict = false;

                         this.cart.forEach(item => {
                              const roomDates = this.bookedDates[item.id] || [];

                              // Simple intersection check
                              const isStillAvailable = this.isDateRangeAvailable(item.id, item.checkin, item.checkout);

                              if (!isStillAvailable) {
                                   hasConflict = true;
                                   // Visual indicator logic here (optional)
                              }
                         });

                         if (hasConflict) {
                              this.updateHoldStatus('Attention: One of your selected rooms has just been hold by another user.', 'error');
                         }
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
                                   // self.handleDateChange(); // Removed this line to avoid double calculation

                                   if (selectedDates.length > 0) {
                                        const checkInDate = new Date(selectedDates[0]);

                                        // Calculate next day (Check-in + 1 day)
                                        const nextDay = new Date(checkInDate);
                                        nextDay.setDate(nextDay.getDate() + 1);

                                        // Update constraints
                                        self.checkoutPicker.set('minDate', nextDay);

                                        // --- NEW LOGIC: Auto-fill Checkout ---
                                        // We pass 'true' as the second argument to trigger the onChange event 
                                        // of the checkout picker, which will run handleDateChange() automatically.
                                        self.checkoutPicker.setDate(nextDay, true);
                                   } else {
                                        // If checkin is cleared, handle recalculation
                                        self.handleDateChange();
                                   }
                              },
                              onDayCreate: function (dObj, dStr, fp, dayElem) {
                                   if (dayElem.classList.contains('flatpickr-disabled')) {
                                        dayElem.classList.add('unavailable');
                                   }
                              }
                         });

                         this.checkoutPicker = flatpickr("#checkout", {
                              minDate: new Date(Date.now() + 86400000), // Default to tomorrow
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
                         const disableFunction = (date) => {
                              const dateStr = this.formatDate(date);
                              return bookedDates.includes(dateStr);
                         };

                         this.checkinPicker.set('disable', [disableFunction]);
                         this.checkoutPicker.set('disable', [disableFunction]);
                    }

                    setDefaultDates() {
                         const today = new Date();
                         const tomorrow = new Date(today);
                         tomorrow.setDate(tomorrow.getDate() + 1);

                         // Only set defaults if inputs are empty
                         if (!document.getElementById('checkin').value) {
                              this.checkinPicker.setDate(today);
                         }
                         if (!document.getElementById('checkout').value) {
                              this.checkoutPicker.setDate(tomorrow);
                         }

                         this.calculateNightsAndPrices();
                    }

                    // ==========================================
                    //  THE FIX IS HERE
                    // ==========================================
                    handleDateChange() {
                         this.hideHoldStatus();
                         this.calculateNightsAndPrices();

                         const checkinDate = this.checkinPicker.selectedDates[0];
                         const checkoutDate = this.checkoutPicker.selectedDates[0];

                         if (checkinDate && checkoutDate) {
                              const newCheckin = this.formatDate(checkinDate);
                              const newCheckout = this.formatDate(checkoutDate);

                              if (this.cart.length > 0) {
                                   let hasUnavailable = false;

                                   this.cart.forEach(item => {
                                        item.checkin = newCheckin;
                                        item.checkout = newCheckout;
                                        item.nights = this.nights;

                                        // Check new dates against known booked dates
                                        if (!this.isDateRangeAvailable(item.id, newCheckin, newCheckout)) {
                                             hasUnavailable = true;
                                        }
                                   });

                                   this.saveCartToStorage();
                                   this.updateCartDisplay();

                                   // Immediately re-validate to block checkout if needed
                                   this.validateCartRealTime();

                                   if (hasUnavailable) {
                                        showNotification("Warning: Some rooms are unavailable for these new dates.", true);
                                   }
                              }
                         }
                    }

                    setupEventListeners() {
                         document.addEventListener('click', (e) => {
                              if (e.target.closest('.add-to-cart-btn')) {
                                   const button = e.target.closest('.add-to-cart-btn');
                                   // Prevent double clicks
                                   if (button.disabled) return;
                                   this.addToCart(button.dataset.room, button);
                              }
                              if (e.target.closest('.remove-btn')) {
                                   const button = e.target.closest('.remove-btn');
                                   this.removeFromCart(button.dataset.room);
                              }
                         });

                         const breakfastToggle = document.getElementById('breakfast-toggle');
                         if (breakfastToggle) {
                              breakfastToggle.addEventListener('change', (e) => {
                                   this.breakfastIncluded = e.target.checked;
                                   this.updateCartDisplay();
                              });
                         }

                         document.getElementById('checkout-btn').addEventListener('click', () => this.handleCheckout());
                    }

                    setupUnavailableDatesToggle() {
                         document.addEventListener('click', (e) => {
                              const toggleBtn = e.target.closest('.toggle-unavailable-dates');
                              if (!toggleBtn) return;

                              const content = toggleBtn.nextElementSibling;
                              const roomCard = toggleBtn.closest('.room-card');
                              const icon = toggleBtn.querySelector('i:last-child');
                              const isOpening = content.classList.contains('hidden');

                              toggleBtn.classList.toggle('active');
                              content.classList.toggle('hidden');

                              if (icon) {
                                   icon.style.transform = isOpening ? 'rotate(180deg)' : 'rotate(0deg)';
                              }

                              if (isOpening) {
                                   if (roomCard) roomCard.style.zIndex = "50";

                                   // Slight delay to allow the CSS transition 'hidden' to apply first
                                   // so Flatpickr can read the container width correctly
                                   requestAnimationFrame(() => {
                                        this.initializeCalendarIfNeeded(content);
                                        const cal = content.querySelector('.flatpickr-calendar');
                                        if (cal) {
                                             // Force flatpickr to re-calculate fluid width
                                             cal.style.width = '100%';
                                        }
                                   });
                              } else {
                                   if (roomCard) setTimeout(() => roomCard.style.zIndex = "1", 300);
                              }
                         });
                    }

                    initializeCalendarIfNeeded(content) {
                         const calendarEl = content.querySelector('.calendar-container');
                         if (!calendarEl) return;

                         // If exists, just force redraw to ensure sizing
                         if (calendarEl._flatpickr) {
                              calendarEl._flatpickr.redraw();
                              return;
                         }

                         calendarEl.innerHTML = '';
                         this.initializeFlatpickr(calendarEl);
                         this.addCalendarStyles();
                    }

                    initializeFlatpickr(calendarEl) {
                         const roomId = calendarEl.closest('.room-card').dataset.roomId;
                         const today = new Date().setHours(0, 0, 0, 0);

                         const fp = flatpickr(calendarEl, {
                              inline: true,
                              dateFormat: "Y-m-d",
                              minDate: "today",
                              disable: [
                                   (date) => {
                                        const dateStr = this.formatDate(date);
                                        const dates = this.bookedDates[roomId] || [];
                                        return dates.includes(dateStr);
                                   },
                                   (date) => date < today
                              ],
                              locale: {
                                   firstDayOfWeek: 1
                              },
                              clickOpens: true,
                              allowInput: true,
                              enableTime: false,
                              static: true,
                              onChange: (selectedDates, dateStr, instance) => {
                                   if (selectedDates.length > 0) {
                                        const clickedDate = selectedDates[0];
                                        // Update main pickers
                                        this.checkinPicker.setDate(clickedDate, true);
                                        const nextDay = new Date(clickedDate);
                                        nextDay.setDate(nextDay.getDate() + 1);
                                        this.checkoutPicker.setDate(nextDay, true);

                                        // Try adding to cart
                                        // We use the clicked room's ID
                                        const btn = calendarEl.closest('.room-card').querySelector('.add-to-cart-btn');
                                        if (btn) {
                                             setTimeout(() => {
                                                  this.addToCart(roomId, btn);
                                             }, 100);
                                        }
                                   }
                              },
                              onDayCreate: (dObj, dStr, fp, dayElem) => {
                                   const dateObj = new Date(dayElem.dateObj).setHours(0, 0, 0, 0);
                                   if (dayElem.classList.contains('flatpickr-disabled')) {
                                        if (dateObj < today) {
                                             dayElem.classList.add('past-day');
                                        } else {
                                             dayElem.classList.add('unavailable');
                                        }
                                   } else {
                                        dayElem.classList.add('available');
                                        dayElem.style.pointerEvents = 'auto';
                                        dayElem.style.cursor = 'pointer';
                                   }
                              },
                              onReady: (d, s, instance) => {
                                   setTimeout(() => instance.redraw(), 50);
                              }
                         });

                         calendarEl._flatpickr = fp;
                    }

                    addCalendarStyles() {
                         if (document.head.querySelector('[data-calendar-styles]')) return;
                         const style = document.createElement('style');
                         style.setAttribute('data-calendar-styles', 'true');
                         style.textContent = this.getCalendarStyles();
                         document.head.appendChild(style);
                    }

                    getCalendarStyles() {
                         return `
                                   .flatpickr-calendar.inline { width: 100% !important; max-width: 100% !important; box-shadow: none !important; }
                                   .flatpickr-rContainer, .flatpickr-days, .dayContainer { width: 100% !important; }
                                   .dayContainer { justify-content: space-around !important; }
                                   .flatpickr-day { width: 14.28% !important; max-width: none !important; margin: 0 !important; height: 30px !important; line-height: 30px !important; font-size: 12px !important; border-radius: 4px !important; }
                                   .flatpickr-day.unavailable { background: #fee2e2 !important; color: #dc2626 !important; border-color: #fecaca !important; text-decoration: line-through; pointer-events: none !important; opacity: 0.7; }
                                   .flatpickr-day.past-day { background: #f3f4f6 !important; color: #9ca3af !important; pointer-events: none !important; }
                                   .flatpickr-day.available { background: #dcfce7 !important; color: #16a34a !important; border: 1px solid #bbf7d0 !important; cursor: pointer !important; pointer-events: auto !important; transition: all 0.2s ease; }
                                   .flatpickr-day.available:hover { background: #2647dcff !important; color: white !important; border-color: #2647dcff !important; transform: scale(1.1); z-index: 10; }
                                   .flatpickr-day.selected { background: #2647dcff !important; border-color: #2651dcff !important; color: white !important; }
                              `;
                    }

                    validateCheckoutButton() {
                         const checkoutBtn = document.getElementById('checkout-btn');
                         // Basic check: is cart empty?
                         if (this.cart.length === 0) {
                              checkoutBtn.disabled = true;
                         } else {
                              checkoutBtn.disabled = false;
                         }
                         // Trigger deeper validation
                         this.validateCartRealTime();
                    }

                    formatDate(date) {
                         if (!date) return '';
                         const d = new Date(date);
                         if (isNaN(d.getTime())) return '';
                         const year = d.getFullYear();
                         const month = String(d.getMonth() + 1).padStart(2, '0');
                         const day = String(d.getDate()).padStart(2, '0');
                         return `${year}-${month}-${day}`;
                    }

                    formatDisplayDate(date) {
                         if (typeof date === 'string') date = new Date(date);
                         return date.toLocaleDateString('en-US', {
                              month: 'short',
                              day: 'numeric'
                         });
                    }

                    calculateNightsAndPrices() {
                         const checkinDate = this.checkinPicker.selectedDates[0];
                         const checkoutDate = this.checkoutPicker.selectedDates[0];

                         if (!checkinDate || !checkoutDate) return;

                         const timeDiff = checkoutDate.getTime() - checkinDate.getTime();
                         this.nights = Math.ceil(timeDiff / (1000 * 3600 * 24));

                         // Prevent 0 or negative nights
                         if (this.nights < 1) this.nights = 1;

                         document.getElementById('nights-display').textContent =
                              `${this.nights} night${this.nights !== 1 ? 's' : ''} selected (${this.formatDisplayDate(checkinDate)} - ${this.formatDisplayDate(checkoutDate)}) | 12NN to 10AM`;

                         if (this.cart.length > 0) {
                              this.updateCartDisplay();
                         }
                    }

                    isDateRangeAvailable(roomId, checkin, checkout) {
                         if (!roomId || !checkin || !checkout) return false;

                         const parseLocalDate = (dateStr) => {
                              if (dateStr instanceof Date) return new Date(dateStr.getFullYear(), dateStr.getMonth(), dateStr.getDate());
                              const [year, month, day] = dateStr.split('-').map(Number);
                              return new Date(year, month - 1, day);
                         };

                         const checkinDate = parseLocalDate(checkin);
                         const checkoutDate = parseLocalDate(checkout);

                         if (checkinDate >= checkoutDate) return false;

                         const bookedDates = Array.isArray(this.bookedDates[roomId]) ? this.bookedDates[roomId] : [];

                         for (const dateStr of bookedDates) {
                              try {
                                   const bookedDate = parseLocalDate(dateStr);
                                   // Check intersection
                                   if (bookedDate >= checkinDate && bookedDate < checkoutDate) {
                                        return false;
                                   }
                              } catch (e) {
                                   continue;
                              }
                         }
                         return true;
                    }

                    findNextAvailableDates(roomId, afterDate) {
                         try {
                              const bookedDates = this.bookedDates[roomId] || [];
                              // Sort dates chronologically
                              const unavailableDates = bookedDates
                                   .map(dateStr => new Date(dateStr))
                                   .sort((a, b) => a - b);

                              let currentDate = new Date(afterDate);
                              currentDate.setDate(currentDate.getDate() + 1);

                              // Simple algorithm: find first gap
                              // Real implementation might need to check contiguous blocks if stay > 1 night
                              for (const bookedDate of unavailableDates) {
                                   if (currentDate < bookedDate) {
                                        // Found a gap
                                        return {
                                             checkin: this.formatDisplayDate(currentDate),
                                             checkout: this.formatDisplayDate(new Date(currentDate.getTime() + 86400000))
                                        };
                                   }
                                   if (currentDate <= bookedDate) {
                                        currentDate = new Date(bookedDate);
                                        currentDate.setDate(currentDate.getDate() + 1);
                                   }
                              }

                              // If no more booked dates, return the next day
                              return {
                                   checkin: this.formatDisplayDate(currentDate),
                                   checkout: this.formatDisplayDate(new Date(currentDate.getTime() + 86400000))
                              };
                         } catch (error) {
                              return null;
                         }
                    }

                    async addToCart(roomId, buttonElement) {
                         this.hideHoldStatus();

                         // UI Feedback: Loading
                         if (buttonElement) {
                              const originalHtml = buttonElement.innerHTML;
                              buttonElement.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Checking...';
                              buttonElement.disabled = true;
                         }

                         try {
                              // 1. Force a refresh of data to be absolutely sure before adding
                              await this.fetchRealTimeAvailability();

                              if (!this.roomsData[roomId]) throw new Error('Room not found');

                              // Check if already in cart
                              if (this.cart.some(item => item.id === roomId)) {
                                   if (buttonElement) {
                                        buttonElement.innerHTML = 'Selected'; // Reset
                                        buttonElement.disabled = false;
                                   }
                                   return;
                              }

                              const checkinDate = this.checkinPicker.selectedDates[0];
                              const checkoutDate = this.checkoutPicker.selectedDates[0];

                              if (!checkinDate || !checkoutDate) {
                                   showNotification('Please select both check-in and check-out dates', true);
                                   if (buttonElement) buttonElement.disabled = false;
                                   return;
                              }

                              const checkin = this.formatDate(checkinDate);
                              const checkout = this.formatDate(checkoutDate);

                              if (new Date(checkin) >= new Date(checkout)) {
                                   showNotification('Check-out date must be after check-in date', true);
                                   if (buttonElement) buttonElement.disabled = false;
                                   return;
                              }

                              // 2. Perform the validation with fresh data
                              if (!this.isDateRangeAvailable(roomId, checkin, checkout)) {
                                   const roomName = this.roomsData[roomId].name;
                                   showNotification(`Sorry! ${roomName} is not available for these dates.`, true);

                                   if (buttonElement) {
                                        buttonElement.innerHTML = 'Unavailable';
                                        buttonElement.classList.add('bg-gray-400');
                                        setTimeout(() => {
                                             buttonElement.innerHTML = 'Select Room'; // Or original HTML
                                             buttonElement.classList.remove('bg-gray-400');
                                             buttonElement.disabled = false;
                                        }, 2000);
                                   }
                                   return;
                              }

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

                              this.saveCartToStorage();
                              this.updateCartDisplay();
                              this.updateDatePickerDisabledDates(roomId);
                              this.validateCheckoutButton();

                              // UI Feedback: Success
                              document.querySelectorAll('.room-card').forEach(card => {
                                   card.classList.toggle('selected', card.dataset.roomId === roomId);
                              });

                              if (buttonElement) {
                                   const originalHTML = '<span class="flex items-center justify-center">Select Room <svg xmlns="http://www.w3.org/2000/svg" class="ml-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" /></svg></span>';
                                   buttonElement.innerHTML = '<i class="fas fa-check mr-2"></i> Added!';
                                   buttonElement.classList.replace('bg-primary', 'bg-green-500');
                                   buttonElement.classList.replace('from-red-600', 'from-green-600'); // Gradient fix
                                   buttonElement.classList.replace('to-red-700', 'to-green-700'); // Gradient fix

                                   setTimeout(() => {
                                        buttonElement.innerHTML = originalHTML;
                                        buttonElement.classList.replace('bg-green-500', 'bg-primary');
                                        buttonElement.classList.replace('from-green-600', 'from-red-600');
                                        buttonElement.classList.replace('to-green-700', 'to-red-700');
                                        buttonElement.disabled = false;
                                   }, 2000);
                              }

                              showNotification(`${room.name} added to your summary`);

                         } catch (error) {
                              showNotification(error.message || 'Failed to add room to cart', true);
                              if (buttonElement) buttonElement.disabled = false;
                         }
                    }

                    removeFromCart(roomId) {
                         this.hideHoldStatus();
                         const room = this.roomsData[roomId];
                         this.cart = this.cart.filter(item => item.id !== roomId);

                         this.saveCartToStorage();
                         this.updateCartDisplay();

                         document.querySelectorAll('.room-card').forEach(card => {
                              if (card.dataset.roomId === roomId) card.classList.remove('selected');
                         });

                         const buttons = document.querySelectorAll(`.add-to-cart-btn[data-room="${roomId}"]`);
                         buttons.forEach(button => {
                              button.innerHTML = `
                                        <span class="flex items-center justify-center">
                                        Select Room 
                                        <svg xmlns="http://www.w3.org/2000/svg" class="ml-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                             <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                                        </svg>
                                        </span>`;
                              button.classList.remove('bg-green-500', 'bg-primary');
                              button.classList.add('bg-primary');
                              button.disabled = false;
                         });

                         if (this.cart.length === 0) {
                              this.checkinPicker.set('disable', []);
                              this.checkoutPicker.set('disable', []);
                         }

                         this.validateCheckoutButton();
                         showNotification(`${room.name} removed from your summary`);
                    }

                    hideHoldStatus() {
                         const statusDiv = document.getElementById('hold-status-message');
                         if (statusDiv) {
                              statusDiv.classList.add('hidden');
                              statusDiv.classList.remove('active');
                         }
                    }

                    updateCartDisplay() {
                         const container = document.getElementById('cart-items');
                         const checkoutBtn = document.getElementById('checkout-btn');
                         const totalElement = document.getElementById('total-price');
                         const breakfastSummary = document.getElementById('breakfast-summary');
                         const breakfastNights = document.getElementById('breakfast-nights');
                         const breakfastPrice = document.getElementById('breakfast-price');

                         // NEW: Get the warning element
                         const warningMsg = document.getElementById('cart-hold-warning');

                         if (this.cart.length === 0) {
                              container.innerHTML = '<div class="text-gray-400 text-center py-6"><i class="fas fa-shopping-cart text-3xl mb-3 opacity-50"></i><p>Your list is empty</p></div>';
                              totalElement.textContent = '₱0.00';
                              checkoutBtn.disabled = true;
                              if (breakfastSummary) breakfastSummary.classList.add('hidden');
                              // NEW: Hide the warning when cart is empty
                              if (warningMsg) warningMsg.classList.add('hidden');
                              return;
                         }

                         // NEW: Show the warning when cart has items
                         if (warningMsg) warningMsg.classList.remove('hidden');

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
                                                                   <div class="text-sm text-gray-600">${this.nights} night${this.nights !== 1 ? 's' : ''} × ₱${item.price.toLocaleString()}</div>
                                                                   <div class="text-xs text-gray-400 mt-1">${item.checkin} to ${item.checkout}</div>
                                                               </div>
                                                           </div>
                                                           <div class="text-right">
                                                               <div class="font-medium">₱${itemTotal.toLocaleString()}</div>
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
                              if (breakfastNights) breakfastNights.textContent = `${this.nights} night${this.nights !== 1 ? 's' : ''} × ${this.cart.length} room${this.cart.length !== 1 ? 's' : ''}`;
                              if (breakfastPrice) breakfastPrice.textContent = `₱${breakfastTotal.toLocaleString()}`;
                              breakfastSummary.classList.remove('hidden');
                              subtotal += breakfastTotal;
                         } else if (breakfastSummary) {
                              breakfastSummary.classList.add('hidden');
                         }

                         totalElement.textContent = `₱${subtotal.toLocaleString()}`;
                    }

                    setupBreakfastToggle() {
                         const breakfastToggle = document.getElementById('breakfast-toggle');
                         if (!breakfastToggle) return;
                         breakfastToggle.checked = this.breakfastIncluded;
                         breakfastToggle.addEventListener('change', (e) => {
                              this.breakfastIncluded = e.target.checked;
                              this.updateCartDisplay();
                              this.saveCartToStorage();
                         });
                    }

                    initializeCart() {
                         // Cart loading is handled in constructor

                         // If we have items in cart, re-apply UI state
                         if (this.cart.length > 0) {
                              this.cart.forEach(item => {
                                   document.querySelectorAll(`.room-card[data-room-id="${item.id}"]`).forEach(card => {
                                        card.classList.add('selected');
                                   });
                              });

                              this.updateCartDisplay();
                              this.validateCheckoutButton();

                              // If cart has dates, set the pickers to match the first item
                              if (this.cart[0].checkin && this.cart[0].checkout) {
                                   this.checkinPicker.setDate(this.cart[0].checkin, false);
                                   this.checkoutPicker.setDate(this.cart[0].checkout, false);
                                   this.updateDatePickerDisabledDates(this.cart[0].id);
                              }
                         }
                    }

                    calculateTotalPrice() {
                         const roomsTotal = this.cart.reduce((sum, item) => sum + (item.price * this.nights), 0);
                         const breakfastTotal = this.breakfastIncluded ? (this.breakfastPrice * this.nights * this.cart.length) : 0;
                         return roomsTotal + breakfastTotal;
                    }

                    formatForBackend(date) {
                         if (!date) return null;
                         const d = new Date(date);
                         const year = d.getFullYear();
                         const month = String(d.getMonth() + 1).padStart(2, '0');
                         const day = String(d.getDate()).padStart(2, '0');
                         return `${year}-${month}-${day}`;
                    }

                    updateHoldStatus(message, type = 'info') {
                         const statusDiv = document.getElementById('hold-status-message');
                         const msgSpan = statusDiv.querySelector('.msg-text');
                         const icon = statusDiv.querySelector('i');

                         msgSpan.textContent = message;
                         statusDiv.classList.remove('hidden', 'bg-blue-50', 'text-blue-700', 'bg-red-50', 'text-red-700', 'bg-green-50', 'text-green-700');

                         if (type === 'error') {
                              statusDiv.classList.add('bg-red-50', 'text-red-700', 'active');
                              icon.className = 'fas fa-exclamation-circle';
                         } else if (type === 'success') {
                              statusDiv.classList.add('bg-green-50', 'text-green-700', 'active');
                              icon.className = 'fas fa-check-circle';
                         } else {
                              statusDiv.classList.add('bg-blue-50', 'text-blue-700', 'active');
                              icon.className = 'fas fa-spinner fa-spin';
                         }
                    }

                    async createRoomHolds() {
                         const holdPromises = this.cart.map(item => {
                              return fetch("{{ route('room.hold.create') }}", {
                                   method: 'POST',
                                   headers: {
                                        'Content-Type': 'application/json',
                                        'X-CSRF-TOKEN': this.csrfToken
                                   },
                                   body: JSON.stringify({
                                        room_id: item.facilityId,
                                        date_from: item.checkin,
                                        date_to: item.checkout
                                   })
                              }).then(async response => {
                                   const data = await response.json();
                                   if (!response.ok) {
                                        return {
                                             success: false,
                                             roomId: item.id,
                                             name: item.name,
                                             // --- MODIFIED LINE: Capture the dates sent from controller ---
                                             conflictDates: data.conflict_dates || null,
                                             message: data.message || 'Room unavailable'
                                        };
                                   }
                                   return { success: true, roomId: item.id, holdId: data.hold_id };
                              });
                         });

                         return Promise.all(holdPromises);
                    }

                    async releaseSessionHolds() {
                         try {
                              await fetch("{{ route('room.hold.release.all') }}", {
                                   method: 'DELETE',
                                   headers: {
                                        'Content-Type': 'application/json',
                                        'X-CSRF-TOKEN': this.csrfToken
                                   }
                              });
                         } catch (e) {
                              console.error("Failed to rollback holds", e);
                         }
                    }

                    async handleCheckout() {
                         if (this.cart.length === 0 || this.isSubmitting) return;

                         const button = document.getElementById('checkout-btn');
                         const buttonText = document.getElementById('button-text');
                         // const spinner = document.getElementById('button-spinner');

                         try {
                              // 1. UI Loading State
                              this.isSubmitting = true;
                              button.disabled = true;
                              // spinner.classList.remove('hidden');
                              buttonText.textContent = 'Checking availability...';
                              this.updateHoldStatus('Securing your rooms for the selected dates...', 'loading');

                              // 2. Attempt to Hold Rooms
                              const results = await this.createRoomHolds();
                              const failedHolds = results.filter(r => !r.success);

                              // 3. Handle Failures
                              if (failedHolds.length > 0) {
                                   await this.releaseSessionHolds();

                                   // --- MODIFIED CODE START ---
                                   // Create a string like: "Deluxe Room (Dec 14 - Dec 15)"
                                   const errorDetails = failedHolds.map(f => {
                                        return f.conflictDates ? `${f.name} (${f.conflictDates})` : f.name;
                                   }).join(', ');

                                   this.updateHoldStatus(`Availability changed! ${errorDetails} has been placed on hold for a minute by another guest.`, 'error');

                                   // --- MODIFIED CODE END ---
                                   document.getElementById('checkout-btn-container').scrollIntoView({ behavior: 'smooth', block: 'center' });
                                   
                                   button.disabled = false;
                                   this.isSubmitting = false;
                                   buttonText.textContent = 'Proceed to Your Details';
                                   return;
                              }

                              // 4. Success: All rooms held
                              this.updateHoldStatus('Rooms successfully secured!', 'success');

                              // 5. Prepare and Save Booking Data
                              const bookingData = {
                                   checkin_date: this.formatForBackend(this.checkinPicker.selectedDates[0]),
                                   checkout_date: this.formatForBackend(this.checkoutPicker.selectedDates[0]),
                                   facilities: this.cart.map(item => {
                                        const roomCard = document.querySelector(`.room-card[data-room-id="${item.id}"]`);
                                        const pax = roomCard ? parseInt(roomCard.querySelector('.feature-item:last-child span').textContent.match(/\d+/)[0]) : 1;
                                        const categoryContainer = roomCard ? roomCard.closest('.category-container') : null;
                                        const category = categoryContainer ? categoryContainer.querySelector('.category-title').textContent.trim() : 'Standard';

                                        return {
                                             facility_id: item.facilityId,
                                             name: item.name,
                                             price: item.price,
                                             nights: this.nights,
                                             total_price: item.price * this.nights,
                                             mainImage: item.mainImage,
                                             pax: pax,
                                             category: category
                                        };
                                   }),
                                   breakfast_included: this.breakfastIncluded,
                                   breakfast_price: this.breakfastIncluded ? this.breakfastPrice * this.nights * this.cart.length : 0,
                                   total_price: this.calculateTotalPrice(),
                              };

                              // 6. Store booking data via AJAX call
                              buttonText.textContent = 'Saving booking...';

                              const saveResponse = await fetch("{{ route('bookings.store.session') }}", {
                                   method: 'POST',
                                   headers: {
                                        'Content-Type': 'application/json',
                                        'X-CSRF-TOKEN': this.csrfToken,
                                        'Accept': 'application/json'
                                   },
                                   body: JSON.stringify(bookingData)
                              });

                              const saveResult = await saveResponse.json();

                              if (!saveResult.success) {
                                   throw new Error(saveResult.message || 'Failed to save booking data');
                              }

                              // 7. Store booking data in sessionStorage as backup
                              sessionStorage.setItem('bookingData', JSON.stringify(bookingData));

                              // 8. Redirect to customer info page
                              buttonText.textContent = 'Redirecting...';

                              // Add a small delay to ensure session is saved
                              setTimeout(() => {
                                   window.location.href = "{{ route('bookings.customer-info') }}";
                              }, 500);

                         } catch (error) {
                              console.error('Checkout error:', error);

                              // Rollback holds if error occurs
                              await this.releaseSessionHolds();

                              this.updateHoldStatus(error.message || 'System error. Please try again.', 'error');
                              showNotification('Failed to process request. Please try again.', true);

                              buttonText.textContent = 'Proceed to Your Details';
                              // spinner.classList.add('hidden');
                              button.disabled = false;
                              this.isSubmitting = false;
                         }
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

                    if (!notification || !notificationMessage) return;

                    notificationMessage.textContent = message;
                    if (isError) {
                         notification.classList.add('error');
                    } else {
                         notification.classList.remove('error');
                    }

                    notification.classList.add('show');
                    notification.classList.remove('hidden');

                    if (notification.timeoutId) {
                         clearTimeout(notification.timeoutId);
                    }

                    notification.timeoutId = setTimeout(() => {
                         notification.classList.remove('show');
                         setTimeout(() => {
                              notification.classList.add('hidden');
                         }, 300);
                    }, 5000);
               }
          </script>
@endsection