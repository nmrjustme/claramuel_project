@extends('layouts.bookings')
@section('title', 'Customer Information')
@section('bookings')
    <style>
        /* Base Styles */
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');

        /* Form Elements - Enhanced */
        .input-group {
            margin-bottom: 1.5rem;
            position: relative;
            transition: all 0.3s ease;
        }

        label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: #374151;
            font-size: 0.9375rem;
            transition: all 0.2s ease;
        }

        .form-input {
            width: 100%;
            padding: 0.75rem 1rem;
            border-radius: 0.375rem;
            font-size: 0.9375rem;
            transition: all 0.2s ease;
        }

        .form-input:hover {
            border-color: #9ca3af;
        }

        .form-input:focus {
            outline: none;
            border-color: #DC2626;
            box-shadow: 0 0 0 3px rgba(220, 38, 38, 0.1);
            background-color: #fffafa;
        }

        .input-error {
            border-color: #DC2626 !important;
            background-color: #FEF2F2;
        }

        .error-message {
            color: #DC2626;
            font-size: 0.8125rem;
            margin-top: 0.25rem;
            display: none;
            animation: fadeIn 0.3s ease;
        }

        .input-hint {
            font-size: 0.8125rem;
            color: #444850;
            margin-top: 0.25rem;
            line-height: 1.4;
            transition: all 0.2s ease;
        }

        /* Buttons - Enhanced */
        .btn-primary {
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

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(220, 38, 38, 0.2);
        }

        .btn-primary:active {
            transform: translateY(0);
            box-shadow: 0 2px 4px rgba(220, 38, 38, 0.2);
        }

        .btn-primary:disabled {
            opacity: 0.7;
            cursor: not-allowed;
            transform: none !important;
            box-shadow: none !important;
        }

        .btn-primary::after {
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

        .btn-primary:hover::after {
            left: 100%;
        }

        .section-title {
            font-size: 1.25rem;
            font-weight: 700;
            margin-bottom: 1.5rem;
            color: #1f2937;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            position: relative;
            padding-bottom: 0.75rem;
        }

        .section-title i {
            color: #DC2626;
        }

        .guest-section {
            margin-top: 2rem;
            padding-top: 1.5rem;
            border-top: 1px solid #e5e7eb;
        }

        .guest-section-title {
            margin-bottom: 1rem;
            color: #1f2937;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }


        .booking-summary-title {
            font-size: 1.25rem;
            font-weight: 700;
            margin-bottom: 1rem;
            color: #1f2937;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            position: relative;
            padding-bottom: 0.75rem;
        }

        .booking-summary-title i {
            color: #DC2626;
        }

        .booking-item {
            display: flex;
            justify-content: space-between;
            padding: 0.75rem 0;
            border-bottom: 1px solid #f3f4f6;
            transition: all 0.2s ease;
        }

        .booking-item:hover {
            background-color: #f9fafb;
        }

        .booking-item-label {
            color: #6b7280;
            font-size: 0.9375rem;
        }

        .booking-item-value {
            font-weight: 500;
            color: #1f2937;
            font-size: 0.9375rem;
        }

        .room-item {
            display: flex;
            margin-bottom: 1rem;
            padding-bottom: 1rem;
            border-bottom: 1px dashed #e5e7eb;
            transition: all 0.3s ease;
        }

        .room-item:hover {
            transform: translateX(5px);
        }

        .room-image {
            width: 80px;
            height: 80px;
            border-radius: 0.5rem;
            object-fit: cover;
            margin-right: 1rem;
            transition: all 0.3s ease;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .room-image:hover {
            transform: scale(1.05);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
        }

        .room-details {
            flex: 1;
        }

        .room-name {
            font-weight: 600;
            margin-bottom: 0.25rem;
            font-size: 0.9375rem;
            color: #111827;
        }

        .room-price {
            color: #6b7280;
            font-size: 0.875rem;
        }

        .total-section {
            margin-top: 1.5rem;
            padding-top: 1.5rem;
            border-top: 1px solid #e5e7eb;
        }

        .total-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 0.5rem;
        }

        .total-label {
            font-weight: 600;
            font-size: 1rem;
        }

        .total-amount {
            font-weight: 700;
            font-size: 1.25rem;
            color: #DC2626;
        }

        /* Guest Selection Styles - Enhanced */
        .guest-type-container {
            margin-bottom: 2rem;
            background-color: #f9fafb;
            padding: 1rem;
            border-radius: 0.5rem;
            transition: all 0.3s ease;
        }

        .guest-type-container:hover {
            background-color: #f3f4f6;
        }

        .guest-type-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 0.75rem;
        }

        .guest-type-title {
            font-weight: 600;
            font-size: 1rem;
            color: #1f2937;
        }

        .guest-count {
            font-size: 0.875rem;
            color: #6b7280;
            transition: all 0.3s ease;
            font-weight: 500;
            padding: 0.25rem 0.5rem;
            border-radius: 0.375rem;
        }

        .guest-count.not-full {
            color: #059669;
            /* Green text */
            background-color: #ECFDF5;
            /* Light green background */
            border: 1px solid #D1FAE5;
        }

        .guest-count.full {
            color: #DC2626;
            /* Red text */
            background-color: #FEF2F2;
            /* Light red background */
            border: 1px solid #FECACA;
            font-weight: 600;
        }

        .guest-count.warning {
            color: #D97706;
            /* Amber text */
            background-color: #FFFBEB;
            /* Light amber background */
            border: 1px solid #FED7AA;
            font-weight: 500;
        }

        .guest-type-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, auto));
            gap: 1rem;
            justify-content: center;
            /* centers the whole grid */
            justify-items: center;
            /* centers each item inside its column */
        }


        .guest-type-group {
            margin-bottom: 0.5rem;
        }

        .guest-type-label {
            display: block;
            margin-bottom: 0.25rem;
            font-size: 0.875rem;
            color: #4b5563;
            font-weight: 500;
        }

        /* Notification Styles - Enhanced */
        .notification {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background-color: #10B981;
            color: white;
            padding: 1rem 1.5rem;
            border-radius: 0.5rem;
            box-shadow: 0 10px 15px rgba(0, 0, 0, 0.1);
            transform: translateY(100px);
            opacity: 0;
            transition: all 0.3s cubic-bezier(0.68, -0.55, 0.265, 1.55);
            z-index: 1000;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            max-width: 350px;
        }

        .notification.show {
            transform: translateY(0);
            opacity: 1;
        }

        .notification.error {
            background-color: #DC2626;
        }

        .notification.warning {
            background-color: #F59E0B;
        }

        .notification i {
            font-size: 1.25rem;
        }

        /* Loading Spinner - Enhanced */
        .loading-spinner {
            display: inline-block;
            width: 1.25rem;
            height: 1.25rem;
            border: 3px solid rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            border-top-color: white;
            animation: spin 1s ease-in-out infinite;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        /* Terms Checkbox - Enhanced */
        .checkbox-container {
            display: flex;
            align-items: flex-start;
            gap: 0.75rem;
            margin-bottom: 1rem;
            background-color: #f9fafb;
            padding: 1rem;
            border-radius: 0.5rem;
            transition: all 0.3s ease;
        }

        .checkbox-container:hover {
            background-color: #f3f4f6;
        }

        .checkbox-container input[type="checkbox"] {
            margin-top: 0.25rem;
            accent-color: #DC2626;
            cursor: pointer;
            width: 1.1em;
            height: 1.1em;
        }

        .checkbox-container label {
            font-size: 0.875rem;
            line-height: 1.5;
            margin-bottom: 0;
            color: #4b5563;
            cursor: pointer;
        }

        .checkbox-container a {
            color: #DC2626;
            font-weight: 500;
            text-decoration: underline;
            transition: all 0.2s ease;
        }

        .checkbox-container a:hover {
            text-decoration: none;
            color: #B91C1C;
        }

        /* Utility Classes */
        .uppercase-input {
            text-transform: uppercase;
        }

        .hidden {
            display: none;
        }

        /* Animations */
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-5px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes pulse {
            0% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.05);
            }

            100% {
                transform: scale(1);
            }
        }

        /* Responsive Adjustments */
        @media (max-width: 768px) {
            .form-card {
                padding: 1.5rem;
            }

            .guest-type-grid {
                grid-template-columns: 1fr;
            }

            .section-title::after,
            .booking-summary-title::after {
                width: 30px;
            }
        }

        /* New Additions */
        .input-icon {
            position: absolute;
            right: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: #9ca3af;
            pointer-events: none;
        }

        .input-with-icon {
            padding-right: 2.5rem !important;
        }

        .tooltip {
            position: relative;
            display: inline-block;
        }

        .tooltip .tooltip-text {
            visibility: hidden;
            width: 200px;
            background-color: #1f2937;
            color: #fff;
            text-align: center;
            border-radius: 6px;
            padding: 0.5rem;
            position: absolute;
            z-index: 1;
            bottom: 125%;
            left: 50%;
            transform: translateX(-50%);
            opacity: 0;
            transition: opacity 0.3s;
            font-size: 0.75rem;
            font-weight: normal;
        }

        .tooltip:hover .tooltip-text {
            visibility: visible;
            opacity: 1;
        }

        .counter-btn {
            background-color: #e5e7eb;
            border: none;
            width: 2rem;
            height: 2rem;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 0.25rem;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .counter-btn:hover {
            background-color: #d1d5db;
        }

        .counter-btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        .counter-value {
            width: 3rem;
            text-align: center;
            border: 1px solid #d1d5db;
            border-radius: 0.25rem;
            padding: 0.25rem;
            margin: 0 0.5rem;
        }

        .counter-container {
            display: flex;
            align-items: center;
        }

        /* Floating Labels */
        .floating-label-group {
            position: relative;
            margin-bottom: 1.5rem;
        }

        .floating-label {
            position: absolute;
            left: 1rem;
            top: 0.75rem;
            color: #6b7280;
            font-size: 0.9375rem;
            transition: all 0.2s ease;
            pointer-events: none;
            background-color: white;
            padding: 0 0.25rem;
            border-radius: 0.25rem;
        }

        .floating-input:focus+.floating-label,
        .floating-input:not(:placeholder-shown)+.floating-label {
            top: -0.5rem;
            left: 0.75rem;
            font-size: 0.75rem;
            color: #DC2626;
            background-color: white;
        }

        .page-title {
            color: #1F2937;
            padding-left: 0;
            position: relative;
            display: inline-block;

            font-size: clamp(1.25rem, 4vw, 1.75rem);
        }

        @media (max-width: 480px) {
            .page-title {
                font-size: 1.125rem;
                /* Smaller category titles */
                margin-bottom: 0.5rem;
            }
        }

        .page-title:after {
            content: '';
            position: absolute;
            bottom: -0.25rem;
            left: 0;
            width: 100%;
            height: 4px;
            background: linear-gradient(90deg, #DC2626 0%, rgba(220, 38, 38, 0.2) 100%);
            border-radius: 4px;
        }

        /* Payment Preference Styles */
        .payment-option {
            display: flex;
            align-items: center;
            padding: 1rem;
            border: 2px solid #e5e7eb;
            border-radius: 0.5rem;
            margin-bottom: 0.75rem;
            cursor: pointer;
            transition: all 0.3s ease;
            background-color: #f9fafb;
        }

        .payment-option:hover {
            background-color: #f3f4f6;
            border-color: #d1d5db;
        }

        .payment-option.selected {
            border-color: #DC2626;
            background-color: #FEF2F2;
            box-shadow: 0 0 0 3px rgba(220, 38, 38, 0.1);
        }

        .payment-option input[type="radio"] {
            margin-right: 0.75rem;
            accent-color: #DC2626;
        }

        .payment-option-label {
            font-weight: 500;
            color: #374151;
            flex: 1;
        }

        .payment-amount {
            font-weight: 600;
            color: #DC2626;
        }

        .payment-description {
            font-size: 0.75rem;
            color: #6b7280;
            margin-top: 0.25rem;
        }
    </style>

    <x-header />

    <div class="container mx-auto px-6 py-8 max-w-12xl">
        <!-- Progress Steps - Enhanced with Tooltips -->
        <x-progress-step :currentStep="2" :steps="[
            ['label' => 'Select Rooms'],
            ['label' => 'Your Details'],
            ['label' => 'Payment'],
            ['label' => 'Completed']
        ]" />


        <div class="flex flex-col lg:flex-row gap-8">
            <!-- Left Column - Customer Information -->
            <div class="lg:w-2/3 space-y-6">
                <h3 class="page-title font-bold text-dark text-lg sm:text-xl md:text-2xl">Request To Book</h3>

                <div class="rounded-lg p-8 mb-6 mt-4 border border-lightgray">
                    <h2 class="section-title">
                        <i class="fas fa-user-circle font-bold text-dark text-md md:text-xl"></i>
                        Personal Information
                    </h2>

                    <form id="customer-info-form">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <!-- First Name -->
                            <div class="flex flex-col">
                                <label for="firstname" class="text-sm font-medium text-gray-700 mb-1">
                                    First Name <span class="text-red-500">*</span>
                                </label>
                                <input type="text" id="firstname" name="firstname"
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-400 focus:outline-none uppercase"
                                    required placeholder="Enter first name" oninput="this.value = this.value.toUpperCase()">
                                <p id="firstname-error" class="text-red-500 text-xs mt-1 hidden">Please enter your first
                                    name</p>
                            </div>

                            <!-- Last Name -->
                            <div class="flex flex-col">
                                <label for="lastname" class="text-sm font-medium text-gray-700 mb-1">
                                    Last Name <span class="text-red-500">*</span>
                                </label>
                                <input type="text" id="lastname" name="lastname"
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-400 focus:outline-none uppercase"
                                    required placeholder="Enter last name" oninput="this.value = this.value.toUpperCase()">
                                <p id="lastname-error" class="text-red-500 text-xs mt-1 hidden">Please enter your last name
                                </p>
                            </div>

                            <!-- Email -->
                            <div class="flex flex-col sm:col-span-1 md:col-span-2">
                                <label for="email" class="text-sm font-medium text-gray-700 mb-1">
                                    Email Address <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <input type="email" id="email" name="email"
                                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-400 focus:outline-none"
                                        required placeholder="john.doe@example.com">
                                    <i class="fas fa-envelope absolute right-3 top-3 text-gray-400"></i>
                                </div>
                                <p id="email-error" class="text-red-500 text-xs mt-1 hidden">Please enter a valid email
                                    address</p>
                                <p class="text-gray-500 text-xs mt-1">Your booking confirmation will be sent to this email
                                </p>
                            </div>

                            <!-- Phone -->
                            <div class="flex flex-col sm:col-span-1 md:col-span-2">
                                <label for="phone" class="text-sm font-medium text-gray-700 mb-1">
                                    Phone Number <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <input type="tel" id="phone" name="phone"
                                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-400 focus:outline-none"
                                        maxlength="12" placeholder="9123 456 789" oninput="formatPhone(this)"
                                        onblur="validatePhone(this)">
                                    <i class="fas fa-phone absolute right-3 top-3 text-gray-400"></i>
                                </div>
                                <p id="phone-error" class="text-red-500 text-xs mt-1 hidden">
                                    Please enter a valid 10-digit phone number starting with 9
                                </p>
                                <p class="text-gray-500 text-xs mt-1">We'll contact you if needed regarding your booking</p>
                            </div>
                        </div>

                        <!-- Guest Section -->
                        <div class="mt-8">
                            <h3 class="flex items-center text-lg font-semibold text-gray-700 mb-2">
                                <i class="fas fa-users mr-2 text-blue-600"></i>
                                Guest Details
                            </h3>
                            <p class="text-gray-500 text-sm mb-4">
                                Please specify the number of guests for each room type
                            </p>

                            <div id="guest-selection-container" class="space-y-3">
                                <!-- Guest fields will be dynamically added here -->
                            </div>
                        </div>
                    </form>

                </div>

                <div class="rounded-lg mb-6 p-8 border border-lightgray bg-white">
                    <h3 class="text-lg sm:text-xl font-bold text-gray-800 mb-6 sm:mb-8 text-center">
                        Accepted Payment Methods
                    </h3>

                    <div class="flex flex-wrap justify-center gap-3 sm:gap-4">

                        <!-- E-Wallets -->
                        <div class="p-2 rounded-lg select-none pointer-events-none cursor-default">
                            <img src="{{ asset('imgs/banks/Maya_logo.svg') }}" class="h-3 sm:h-4">
                        </div>
                        <div class="p-2 rounded-lg select-none pointer-events-none cursor-default">
                            <img src="{{ asset('imgs/banks/gcash_wallet_logo.png') }}" class="h-3 sm:h-4">
                        </div>
                        <div class="p-2 rounded-lg select-none pointer-events-none cursor-default">
                            <img src="{{ asset('imgs/banks/QR_Ph_Logo.svg.png') }}" class="h-3 sm:h-4">
                        </div>

                        <!-- Cards -->
                        <div class="p-2 rounded-lg select-none pointer-events-none cursor-default">
                            <img src="{{ asset('imgs/banks/960px-Visa_Inc._logo.svg.png') }}" class="h-3 sm:h-4">
                        </div>
                        <div class="p-2 rounded-lg select-none pointer-events-none cursor-default">
                            <img src="{{ asset('imgs/banks/Mastercard_2019_logo.svg') }}" class="h-3 sm:h-4">
                        </div>
                        <div class="p-2 rounded-lg select-none pointer-events-none cursor-default">
                            <img src="{{ asset('imgs/banks/JCB_logo.svg') }}" class="h-3 sm:h-4">
                        </div>
                    </div>
                </div>

                <div class="w-full h-96 rounded-lg overflow-hidden border border-gray-200 animate-fadeInUp"
                    style="animation-delay: 0.6s;">
                    <iframe
                        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d789.1338574963425!2d121.9251491!3d17.142796!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x33856b144a6449f5%3A0xea1ad60f5e068495!2sMt.%20Claramuel%20Resort%20and%20Events%20Place!5e0!3m2!1sen!2sph!4v1711431557"
                        width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
                </div>
            </div>

            <!-- Right Column - Booking Summary -->
            <div class="lg:w-1/3 space-y-6">
                <div class="sticky top-4 space-y-6">
                    <!-- Date Summary Card -->
                    <div class="border border-lightgray rounded-lg p-6">
                        <h3 class="booking-summary-title">
                            Booking Summary
                        </h3>

                        <div class="booking-item flex justify-between mb-2">
                            <span class="booking-item-label">Check-in</span>
                            <span class="booking-item-value" id="summary-checkin">-</span>
                        </div>

                        <div class="booking-item flex justify-between mb-2">
                            <span class="booking-item-label">Check-out</span>
                            <span class="booking-item-value" id="summary-checkout">-</span>
                        </div>

                        <div class="booking-item flex justify-between mb-4">
                            <span class="booking-item-label">Duration</span>
                            <span class="booking-item-value" id="summary-nights">-</span>
                        </div>

                        <!-- Important Reminder Box -->
                        <div class="bg-red-50 border-l-4 border-red-400 p-4 rounded-lg shadow-sm mt-4">
                            <p class="text-sm font-semibold text-gray-800">Important Reminder</p>
                            <p class="text-sm text-gray-600 mt-1 leading-relaxed">
                                Check-in time is <span class="font-medium text-gray-900">12:00 NN</span>
                                and Check-out time is <span class="font-medium text-gray-900">10:00 AM</span> only.
                            </p>
                        </div>

                        <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded-lg shadow-sm mt-3">
                            <p class="text-sm font-semibold text-gray-800">Notice</p>
                            <p class="text-sm text-gray-600 mt-1 leading-relaxed">
                                Cancellation is <span class="font-medium text-gray-900">not available for now</span>.
                            </p>
                        </div>
                    </div>


                    <!-- Rooms Summary Card - Enhanced -->
                    <div class="rounded-lg p-8 mb-6 border border-lightgray">
                        <h3 class="booking-summary-title">
                            Your Rooms
                        </h3>

                        <div id="rooms-list">
                            <!-- Rooms will be populated here -->
                            <div class="text-gray-400 text-center py-4">
                                <i class="fas fa-shopping-cart text-2xl mb-2 opacity-50"></i>
                                <p>No rooms selected</p>
                            </div>
                        </div>

                        <div id="breakfast-summary" class="hidden">
                            <div class="booking-item">
                                <span class="booking-item-label">Breakfast</span>
                                <span class="booking-item-value" id="breakfast-price">-</span>
                            </div>
                        </div>

                        <!-- Total Section with Animation -->
                        <div class="total-section">
                            <div class="total-row">
                                <span class="total-label">Total Amount</span>
                                <span class="text-base sm:text-lg md:text-2xl font-bold text-primary" id="summary-total">
                                    ₱0.00
                                </span>

                            </div>
                        </div>


                    </div>

                    <div class="rounded-lg p-8 mb-6 border border-lightgray">
                        <h3 class="booking-summary-title mb-4">
                            Payment Preference
                        </h3>

                        <div class="space-y-3" id="payment-options">
                            <div class="payment-option" data-value="full">
                                <input type="radio" name="payment_option" value="full" class="h-4 w-4 text-blue-600">
                                <div>
                                    <div class="payment-option-label">Pay Total Amount</div>
                                    <div class="payment-amount" id="full-amount">₱0.00</div>
                                    <div class="payment-description">Pay the full amount now</div>
                                </div>
                            </div>

                            <div class="payment-option" data-value="half">
                                <input type="radio" name="payment_option" value="half" class="h-4 w-4 text-blue-600">
                                <div>
                                    <div class="payment-option-label">Pay 50% Deposit</div>
                                    <div class="payment-amount" id="half-amount">₱0.00</div>
                                    <div class="payment-description">Pay half now, the rest upon check-in</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Payment CTA Card - Enhanced -->
                    <div class="rounded-lg p-6 border border-lightgray">
                        <div class="checkbox-container">
                            <input type="checkbox" id="terms-checkbox"
                                class="h-4 w-4 rounded border-gray-300 text-primary focus:ring-primary mt-0.5">
                            <label for="terms-checkbox">
                                I agree to the <a href="#" class="text-primary hover:underline">terms and conditions</a>
                                <!-- <a href="#" class="text-primary hover:underline">privacy policy</a> -->
                            </label>
                        </div>
                        <div id="terms-error" class="error-message hidden text-sm text-red-500 mb-4">
                            Please accept the terms and conditions to proceed
                        </div>

                        <button id="confirm-booking-btn" class="btn-primary disabled:opacity-70 disabled:cursor-not-allowed"
                            disabled>
                            <span id="button-text">I'll Reserve</span>
                            <div id="button-spinner" class="loading-spinner hidden"></div>
                        </button>

                        <!-- Secure Payment Info -->
                        <div class="mt-6 flex flex-col items-center justify-center gap-2 text-sm text-gray-500">
                            <div class="flex items-center gap-2">
                                <i class="fas fa-lock text-green-600"></i>
                                <span class="font-medium">Powered by Maya Checkout</span>
                            </div>
                        </div>

                    </div>

                    <!-- Customer Support -->
                    <div class="rounded-lg p-4 border border-lightgray text-center">
                        <h4 class="font-medium text-gray-700 mb-2">Need Help?</h4>
                        <div class="flex items-center justify-center gap-2 text-sm text-gray-600">
                            <i class="fas fa-phone-alt text-red-500"></i>
                            <span>+63 995 290 1333</span>
                        </div>
                        <div class="flex items-center justify-center gap-2 text-sm text-gray-600 mt-1">
                            <i class="fas fa-envelope text-red-500"></i>
                            <span>mtclaramuelresort@gmail.com</span>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <!-- Notification element - Enhanced -->
    <div id="notification" class="notification hidden">
        <i class="fas fa-check-circle"></i>
        <span id="notification-message"></span>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Retrieve booking data from sessionStorage
            const bookingData = JSON.parse(sessionStorage.getItem('bookingData'));

            if (!bookingData) {
                // Redirect back if no booking data found
                showNotification('No booking data found. Please select rooms first.', true);
                setTimeout(() => {
                    window.location.href = '/bookings';
                }, 2000);
                return;
            }

            // Display booking summary with animations
            displayBookingSummary(bookingData);

            // Setup form validation
            setupFormValidation(bookingData);

            // Setup guest selection fields
            setupGuestSelection(bookingData);

            // Setup payment preference functionality
            setupPaymentPreference(bookingData);

            // Add real-time validation for form fields
            document.getElementById('firstname').addEventListener('input', validateForm);
            document.getElementById('lastname').addEventListener('input', validateForm);
            document.getElementById('email').addEventListener('input', validateForm);
            document.getElementById('phone').addEventListener('input', validateForm);
        });

        function setupPaymentPreference(bookingData) {
            const totalAmount = bookingData.total_price;
            const halfAmount = totalAmount * 0.5;

            // Update payment amounts
            document.getElementById('full-amount').textContent = `₱${totalAmount.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
            document.getElementById('half-amount').textContent = `₱${halfAmount.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;

            // Set default selection to full payment
            document.querySelector('input[name="payment_option"][value="full"]').checked = true;
            document.querySelector('.payment-option[data-value="full"]').classList.add('selected');

            // Add click handlers for payment options
            document.querySelectorAll('.payment-option').forEach(option => {
                option.addEventListener('click', function () {
                    const value = this.getAttribute('data-value');

                    // Update radio button
                    document.querySelector(`input[name="payment_option"][value="${value}"]`).checked = true;

                    // Update UI
                    document.querySelectorAll('.payment-option').forEach(opt => {
                        opt.classList.remove('selected');
                    });
                    this.classList.add('selected');

                });
            });
        }


        function setupGuestSelection(bookingData) {
            const container = document.getElementById('guest-selection-container');
            container.innerHTML = '';

            fetch('/customer/guest-types', {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                credentials: 'same-origin'
            })
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`Server responded with ${response.status}`);
                    }
                    return response.json();
                })
                .then(responseData => {
                    // Extract guest types from the response structure
                    const guestTypes = responseData.data || responseData;

                    // Filter unique types or handle duplicates (example: taking first occurrence)
                    const uniqueTypes = [];
                    const seenTypes = new Set();

                    guestTypes.forEach(type => {
                        if (!seenTypes.has(type.type)) {
                            seenTypes.add(type.type);
                            uniqueTypes.push(type);
                        }
                    });

                    console.log('Processed guest types:', uniqueTypes); // Debug log

                    bookingData.facilities.forEach((room, index) => {
                        const roomDiv = document.createElement('div');
                        roomDiv.className = 'guest-type-container';

                        roomDiv.innerHTML = `
                    <div class="guest-type-header">
                        <h4 class="guest-type-title">${room.name}</h4>
                        <span class="guest-count" id="guest-count-${index}">0 / ${room.pax} guests</span>
                    </div>
                    <div class="guest-type-grid" id="guest-selection-room-${index}">
                        ${uniqueTypes.map(type => `
                        <div class="guest-type-group">
                            <label for="guest-type-${index}-${type.id}" class="guest-type-label">
                                ${type.type}
                            </label>
                            <div class="counter-container">
                                <button class="counter-btn decrement" type="button" data-for="guest-type-${index}-${type.id}">
                                    <i class="fas fa-minus"></i>
                                </button>
                                <input type="number"
                                    id="guest-type-${index}-${type.id}"
                                    name="guest_types[${room.facility_id}][${type.id}]"
                                    class="counter-value form-input"
                                    min="0"
                                    max="${room.pax}"
                                    value="0"
                                    data-room-index="${index}"
                                    data-room-id="${room.facility_id}"
                                    data-room-pax="${room.pax}"
                                    data-guest-type-id="${type.id}">
                                <button class="counter-btn increment" type="button" data-for="guest-type-${index}-${type.id}">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                        </div>
                        `).join('')}
                    </div>
                `;

                        container.appendChild(roomDiv);
                    });

                    // Initialize colors for all guest counts (all start at 0)
                    bookingData.facilities.forEach((room, index) => {
                        const guestCountElement = document.getElementById(`guest-count-${index}`);
                        updateGuestCountColor(guestCountElement, 0, room.pax);
                    });

                    // Attach change listeners to guest quantity inputs
                    document.querySelectorAll('.counter-value').forEach(input => {
                        input.addEventListener('change', function () {
                            updateGuestCounts.call(this);
                            validateForm();
                        });
                    });

                    // Add increment/decrement button handlers
                    document.querySelectorAll('.counter-btn').forEach(button => {
                        button.addEventListener('click', function () {
                            const targetId = this.getAttribute('data-for');
                            const input = document.getElementById(targetId);
                            if (!input) return;

                            if (this.classList.contains('increment')) {
                                input.value = parseInt(input.value) + 1;
                            } else if (this.classList.contains('decrement')) {
                                input.value = Math.max(0, parseInt(input.value) - 1);
                            }

                            // Trigger change event
                            const event = new Event('change');
                            input.dispatchEvent(event);
                        });
                    });
                })
                .catch(error => {
                    console.error('Error loading guest types:', error);
                    container.innerHTML = `
                <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-yellow-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                        </svg>
                        </div>
                        <div class="ml-3">
                        <p class="text-sm text-yellow-700">
                            Could not load guest types. ${error.message}
                            <button onclick="window.location.reload()" class="mt-2 text-yellow-600 hover:text-yellow-500 font-medium">
                                Try Again
                            </button>
                        </p>
                        </div>
                    </div>
                </div>
            `;
                });
        }

        function updateGuestCounts() {
            const roomIndex = this.dataset.roomIndex;
            const roomPax = parseInt(this.dataset.roomPax);

            // Get all inputs for this room
            const roomInputs = document.querySelectorAll(`.counter-value[data-room-index="${roomIndex}"]`);

            // Calculate total selected guests
            let totalGuests = 0;
            roomInputs.forEach(input => {
                totalGuests += parseInt(input.value) || 0; // Important: Use ||0 to handle NaN
            });

            // Enforce max pax limit
            if (totalGuests > roomPax) {
                // Find which input caused the overflow
                const overflow = totalGuests - roomPax;
                this.value = Math.max(0, parseInt(this.value) - overflow);

                // Recalculate after adjustment
                totalGuests = 0;
                roomInputs.forEach(input => {
                    totalGuests += parseInt(input.value) || 0;
                });
            }

            // Update the display and color
            const guestCountElement = document.getElementById(`guest-count-${roomIndex}`);
            guestCountElement.textContent = `${totalGuests} / ${roomPax} guests`;

            // Update the color based on guest count
            updateGuestCountColor(guestCountElement, totalGuests, roomPax);
        }

        function updateGuestCountColor(element, currentGuests, maxGuests) {
            // Remove all color classes
            element.classList.remove('not-full', 'full', 'warning');

            if (currentGuests === 0) {
                // No guests selected - neutral/gray
                element.classList.remove('not-full', 'full', 'warning');
            } else if (currentGuests < maxGuests) {
                // Not full yet - green
                element.classList.add('not-full');
            } else if (currentGuests === maxGuests) {
                // Exactly full - amber/orange as warning
                element.classList.add('warning');
            } else {
                // Over capacity (shouldn't happen due to validation) - red
                element.classList.add('full');
            }
        }

        function displayBookingSummary(bookingData) {
            // Format dates
            const checkinDate = new Date(bookingData.checkin_date);
            const checkoutDate = new Date(bookingData.checkout_date);
            const nights = Math.ceil((checkoutDate - checkinDate) / (1000 * 60 * 60 * 24));

            document.getElementById('summary-checkin').textContent = formatDisplayDate(checkinDate);
            document.getElementById('summary-checkout').textContent = formatDisplayDate(checkoutDate);
            document.getElementById('summary-nights').textContent = `${nights} night${nights !== 1 ? 's' : ''}`;
            document.getElementById('summary-total').textContent = `₱${bookingData.total_price.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;

            // Display rooms
            const roomsList = document.getElementById('rooms-list');
            if (bookingData.facilities.length > 0) {
                roomsList.innerHTML = '';
                bookingData.facilities.forEach(room => {
                    const roomElement = document.createElement('div');
                    roomElement.className = 'room-item';
                    roomElement.innerHTML = `
                                        <img src="${room.mainImage}" alt="${room.name}" class="room-image" onerror="this.src='https://via.placeholder.com/500x300?text=Room+Image'">
                                        <div class="room-details">
                                                <!-- Room Name & Type -->
                                                <div class="flex flex-col">
                                                    <div class="room-name text-gray-800 font-semibold text-base sm:text-lg">${room.name}</div>
                                                    <div class="room-type text-gray-500 text-sm sm:text-base">${room.category}</div>
                                                </div>

                                                <!-- Price per Night -->
                                                <div class="room-price text-gray-700 text-sm sm:text-base mt-1 sm:mt-0">
                                                    ${nights} night${nights !== 1 ? 's' : ''} × ₱${room.price.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}
                                                </div>

                                                <!-- Total Price -->
                                                <div class="room-total text-gray-900 font-semibold text-sm sm:text-base mt-1 sm:mt-0">
                                                    ₱${(room.price * nights).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}
                                                </div>
                                        </div>
                                    `;
                    roomsList.appendChild(roomElement);
                });
            }

            // Display breakfast if included
            if (bookingData.breakfast_included) {
                document.getElementById('breakfast-summary').classList.remove('hidden');
                document.getElementById('breakfast-price').textContent = `₱${bookingData.breakfast_price.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
            } else {
                document.getElementById('breakfast-summary').classList.add('hidden');
            }
        }

        function setupFormValidation(bookingData) {
            const form = document.getElementById('customer-info-form');
            const confirmBtn = document.getElementById('confirm-booking-btn');
            const termsCheckbox = document.getElementById('terms-checkbox');
            const termsError = document.getElementById('terms-error');

            // Validate fields on blur
            document.getElementById('firstname').addEventListener('blur', validateNameField);
            document.getElementById('lastname').addEventListener('blur', validateNameField);
            document.getElementById('email').addEventListener('blur', validateEmailField);
            document.getElementById('phone').addEventListener('blur', validatePhoneField);

            // Validate terms checkbox
            termsCheckbox.addEventListener('change', function () {
                termsError.classList.add('hidden');
                validateForm();
            });

            // Confirm booking button click handler
            confirmBtn.addEventListener('click', function () {
                if (!termsCheckbox.checked) {
                    termsError.classList.remove('hidden');
                    document.getElementById('terms-checkbox').focus();
                    return;
                }

                if (validateForm()) {
                    proceedEmailConfirmation(bookingData);
                }
            });
        }

        function validateForm() {
            const firstNameValid = validateNameField({ target: document.getElementById('firstname') });
            const lastNameValid = validateNameField({ target: document.getElementById('lastname') });
            const emailValid = validateEmailField({ target: document.getElementById('email') });
            const phoneValid = validatePhoneField({ target: document.getElementById('phone') });
            const termsChecked = document.getElementById('terms-checkbox').checked;

            // Validate guest counts for each room
            let guestCountsValid = true;
            document.querySelectorAll('[id^="guest-count-"]').forEach(element => {
                const countText = element.textContent.split('/');
                const selectedGuests = parseInt(countText[0]);
                if (selectedGuests <= 0) {
                    guestCountsValid = false;
                    // Highlight the room that needs attention
                    const roomIndex = element.id.split('-')[2];
                    document.getElementById(`guest-count-${roomIndex}`).classList.add('text-red-500', 'font-medium');
                    element.style.animation = 'pulse 0.5s ease';
                    setTimeout(() => {
                        element.style.animation = '';
                    }, 500);
                }
            });

            const isValid = firstNameValid && lastNameValid && emailValid && phoneValid && termsChecked && guestCountsValid;
            document.getElementById('confirm-booking-btn').disabled = !isValid;
            return isValid;
        }

        function validateNameField(e) {
            const field = e.target;
            const errorElement = document.getElementById(`${field.id}-error`);

            if (!field.value.trim()) {
                field.classList.add('input-error');
                errorElement.style.display = 'block';
                return false;
            }

            field.classList.remove('input-error');
            errorElement.style.display = 'none';
            return true;
        }

        function validateEmailField(e) {
            const field = e.target;
            const errorElement = document.getElementById(`${field.id}-error`);
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

            if (!field.value.trim() || !emailRegex.test(field.value)) {
                field.classList.add('input-error');
                errorElement.style.display = 'block';
                return false;
            }

            field.classList.remove('input-error');
            errorElement.style.display = 'none';
            return true;
        }

        function validatePhoneField(e) {
            return validatePhone(e.target);
        }

        function formatDisplayDate(date) {
            if (typeof date === 'string') {
                date = new Date(date);
            }
            return date.toLocaleDateString('en-US', {
                weekday: 'short',
                month: 'short',
                day: 'numeric',
                year: 'numeric'
            });
        }

        function proceedEmailConfirmation(bookingData) {
            const button = document.getElementById('confirm-booking-btn');
            const buttonText = document.getElementById('button-text');
            const spinner = document.getElementById('button-spinner');

            button.disabled = true;
            buttonText.textContent = 'Processing...';
            spinner.classList.remove('hidden');

            // Get selected payment option
            const paymentOption = document.querySelector('input[name="payment_option"]:checked').value;
            const totalAmount = bookingData.total_price;
            const amountToPay = paymentOption === 'half' ? totalAmount * 0.5 : totalAmount;

            const formData = {
                firstname: document.getElementById('firstname').value.trim(),
                lastname: document.getElementById('lastname').value.trim(),
                email: document.getElementById('email').value.trim(),
                phone: document.getElementById('phone').value.trim(),
                checkin_date: bookingData.checkin_date,
                checkout_date: bookingData.checkout_date,
                facilities: bookingData.facilities.map(facility => ({
                    facility_id: facility.facility_id,
                    price: facility.price,
                    nights: facility.nights,
                    total_price: facility.price * facility.nights,
                    name: facility.name
                })),
                breakfast_included: bookingData.breakfast_included,
                breakfast_price: bookingData.breakfast_included
                    ? bookingData.breakfast_price * bookingData.facilities.length * bookingData.facilities[0].nights
                    : 0,
                total_price: bookingData.total_price,
                amount_to_pay: amountToPay,
                guest_types: {}
            };

            document.querySelectorAll('.counter-value').forEach(input => {
                const roomId = input.dataset.roomId;
                const guestTypeId = input.dataset.guestTypeId;
                const quantity = parseInt(input.value) || 0;

                if (!formData.guest_types[roomId]) {
                    formData.guest_types[roomId] = {};
                }

                if (quantity > 0) {
                    formData.guest_types[roomId][guestTypeId] = quantity;
                }
            });

            // Changed route to email confirmation
            fetch(`{{ route('booking.send_otp') }}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: JSON.stringify(formData)
            })
                .then(async response => {
                    const data = await response.json();

                    if (!response.ok) {
                        console.error('Server error response:', data);
                        showNotification(data.message || 'Failed to send confirmation email.', true);
                        button.disabled = false;
                        buttonText.textContent = 'Confirm Booking';
                        spinner.classList.add('hidden');
                        return;
                    }

                    if (data.success) {
                        button.classList.add('bg-green-500');
                        buttonText.textContent = 'OTP Sent!';
                        spinner.classList.add('hidden');

                        // Redirect with both email AND token
                        window.location.href = '{{ route("booking.pending") }}?email=' +
                            encodeURIComponent(formData.email) +
                            '&token=' + encodeURIComponent(data.token);

                    } else {
                        showNotification(data.message || 'Failed to send confirmation email.', true);
                        button.disabled = false;
                        buttonText.textContent = 'Confirm Booking';
                        spinner.classList.add('hidden');
                    }
                })
                .catch(error => {
                    console.error('Fetch failed:', error);
                    showNotification('An unexpected error occurred. Please try again.', true);
                    button.disabled = false;
                    buttonText.textContent = 'Confirm Booking';
                    spinner.classList.add('hidden');
                });
        }

        // Helper function to show notifications
        function showNotification(message, isError = false) {
            const notification = document.getElementById('notification');
            const notificationMessage = document.getElementById('notification-message');

            notification.className = 'notification hidden'; // Reset classes
            notificationMessage.textContent = message;

            if (isError) {
                notification.classList.add('error');
                notification.querySelector('i').className = 'fas fa-exclamation-circle';
            } else {
                notification.querySelector('i').className = 'fas fa-check-circle';
            }

            notification.classList.remove('hidden');
            notification.classList.add('show');

            // Clear any existing timeout to prevent hiding a new notification prematurely
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

        // Phone number validation
        function validatePhone(input) {
            const phoneRegex = /^9\d{3}\s\d{3}\s\d{3}$/;
            const phone = input.value;

            if (!phoneRegex.test(phone)) {
                document.getElementById('phone-error').style.display = 'block';
                input.classList.add('input-error');
                return false;
            }

            document.getElementById('phone-error').style.display = 'none';
            input.classList.remove('input-error');
            return true;
        }

        function formatPhone(input) {
            // Remove all non-digit characters
            let phone = input.value.replace(/\D/g, '');

            // Remove leading 0 if it exists
            if (phone.startsWith('0')) {
                phone = phone.substring(1);
            }

            // Add spaces for formatting
            if (phone.length > 4) {
                phone = phone.substring(0, 4) + ' ' + phone.substring(4);
            }
            if (phone.length > 8) {
                phone = phone.substring(0, 8) + ' ' + phone.substring(8);
            }

            // Limit to 12 characters (formatted)
            input.value = phone.substring(0, 12);
        }
    </script>
@endsection