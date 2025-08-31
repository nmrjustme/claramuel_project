@extends('layouts.bookings')
@section('title', 'Payment')
@section('bookings')
<x-header />
<div class="container mx-auto px-6 py-8 max-w-12xl">

    <!-- Progress Steps -->
    <x-progress-step :currentStep="3" :steps="[
            ['label' => 'Select Rooms'],
            ['label' => 'Your Details'],
            ['label' => 'Payment'],
            ['label' => 'Processing']
        ]" />

    <div class="max-w-4xl mx-auto rounded-lg border border-lightgray overflow-hidden">
        <div class="md:flex">
            <!-- GCash Payment Form -->
            <div class="md:w-1/2 p-8">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-2xl font-semibold text-primary">GCash Payment</h2>
                </div>

                <div class="mb-6 p-4 bg-blue-50 rounded-lg border border-red-100">
                    <p class="text-sm text-gray-600 mb-1">Hi {{ $user_firstname }}, you're paying 50% of the total
                        amount</p>
                    <p class="text-3xl font-bold text-primary">₱{{ number_format($half_of_total_price, 2) }}</p>
                    <p class="text-sm text-gray-600 mt-1">Remaining 50% to be paid upon check-in</p>
                </div>

                <div class="mb-6 bg-blue-50 p-4 rounded-lg border border-red-100">
                    <h3 class="font-medium text-gray-800 mb-3">GCash Payment Instructions</h3>
                    <ol class="list-decimal list-inside text-sm space-y-2 text-gray-700">
                        <li>Open your GCash app</li>
                        <li>Go to "Send"</li>
                        <li>Select "Express Send"</li>
                        <li>Enter our GCash number: <span class="font-bold">9398806908 (Richter Mayandoc)</span></li>
                        <li>Input the amount: <span class="font-bold">₱{{ number_format($half_of_total_price, 2)
                                }}</span></li>
                        <li>Add note(Optional): <span class="font-bold">My reservation code {{ $reservation_code
                                }}</span></li>
                        <li>Complete the transaction</li>
                    </ol>
                </div>

                <div class="mb-6 text-center">
                    <h3 class="text-lg font-semibold text-gray-800 mb-3">Or scan to pay via GCash</h3>
                    <div class="bg-white p-4 rounded-2xl border-2 border-red-400 shadow-lg inline-block">
                        <img src="{{ asset('imgs/gcashQR/qr_code.jpg') }}" alt="GCash QR Code"
                            class="w-56 h-56 mx-auto object-contain">
                    </div>

                    <!-- Owner Name -->
                    <p class="mt-2 text-sm font-semibold text-gray-700">
                        Account Name: <span class="text-primary">Richter Mayandoc</span>
                    </p>

                    <p class="mt-3 text-sm text-gray-600">
                        Open your GCash app and scan this QR code to pay
                        <span class="font-bold text-primary">₱{{ number_format($half_of_total_price, 2) }}</span>
                    </p>
                </div>


                <form id="paymentForm" enctype="multipart/form-data" class="space-y-6">
                    @csrf
                    <input type="hidden" name="token" value="{{ $token }}">

                    <!-- Phone Number & Reference side by side (on large screens) -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Phone -->
                        <div>
                            <label for="gcash_number" class="block text-sm font-medium text-gray-700 mb-1">
                                GCash Registered Mobile Number <span class="text-red-500">*</span>
                            </label>
                            <input type="tel" name="gcash_number" id="gcash_number"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg" maxlength="11"
                                placeholder="09123456789" oninput="validatePhoneInput(this)"
                                onblur="validatePhone(this)" required>
                            <div id="phone-error" class="hidden text-red-500 text-xs mt-1">
                                Please enter a valid 11-digit phone number starting with 09
                            </div>
                            <p class="text-xs text-gray-500 mt-1">Kindly provide a valid contact number starting with 09
                                (11 digits)</p>
                        </div>

                        <!-- Reference -->
                        <div>
                            <label for="reference" class="block text-sm font-medium text-gray-700 mb-1">
                                Transaction Reference Number <span class="text-red-500">*</span>
                            </label>
                            <input class="w-full px-3 py-2 border border-gray-300 rounded-lg" id="reference"
                                name="reference_no" type="text" placeholder="Ex: 1234567890" required>
                            <p class="text-xs text-gray-500 mt-1">Found in your GCash transaction receipt</p>
                        </div>
                    </div>

                    <!-- Amount Paid -->
                    <div class="mt-4">
                        <label for="amount_paid" class="block text-sm font-medium text-gray-700 mb-1">
                            Amount Paid (₱) <span class="text-red-500">*</span>
                        </label>
                        <input class="w-full px-3 py-2 border border-gray-300 rounded-lg" id="amount_paid"
                            name="amount_paid" type="number" step="0.01" min="1" placeholder="Enter amount you sent"
                            required>
                        <p class="text-xs text-gray-500 mt-1">
                            Enter the exact amount you transferred.
                        </p>
                    </div>


                    <!-- Upload Proof -->
                    <div>
                        <label for="proof" class="block text-sm font-medium text-gray-700 mb-2">
                            Upload GCash Payment Proof <b>(Screenshot)</b> <span class="text-red-500">*</span>
                        </label>
                        <label
                            class="flex flex-col items-center justify-center w-full h-32 border-2 border-dashed border-gray-300 hover:border-primary hover:bg-red-50 rounded-md cursor-pointer transition duration-150">
                            <svg xmlns="http://www.w3.org/2000/svg"
                                class="w-10 h-10 text-gray-400 group-hover:text-primary" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                            </svg>
                            <p class="pt-2 text-sm tracking-wider text-gray-400 group-hover:text-primary">
                                Click to upload screenshot of your GCash transaction
                            </p>
                            <input type="file" class="hidden" id="proof" name="receipt" accept="image/*" required />
                        </label>

                        <!-- Preview -->
                        <div id="imagePreview" class="mt-4 hidden">
                            <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                                <div class="flex justify-between items-center mb-3">
                                    <h4 class="text-base font-medium text-gray-800">Payment Proof Preview</h4>
                                    <button type="button" id="removeImage"
                                        class="text-sm font-medium text-red-600 hover:text-red-800 flex items-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" viewBox="0 0 20 20"
                                            fill="currentColor">
                                            <path fill-rule="evenodd"
                                                d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z"
                                                clip-rule="evenodd" />
                                        </svg>
                                        Remove Image
                                    </button>
                                </div>
                                <div class="flex justify-center bg-white p-2 rounded border border-gray-300">
                                    <img id="previewImage" src="#" alt="GCash Payment Proof Preview"
                                        class="w-auto max-h-[50vh] object-contain shadow-sm">
                                </div>
                                <p class="mt-3 text-xs text-gray-600">
                                    <span class="font-medium">Verify:</span> Reference number and payment amount (₱{{
                                    number_format($half_of_total_price, 2) }}) must be clearly visible
                                </p>
                            </div>
                        </div>
                        <p class="text-xs text-gray-500 mt-1">Accepted formats: JPG, PNG (Max: 2MB)</p>
                    </div>

                    <!-- Why We Need -->
                    <div class="py-4 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                        <h3 class="font-medium text-yellow-800 mb-2">Why do we need these details?</h3>
                        <ul class="text-sm text-yellow-700 list-disc list-inside space-y-1">
                            <li><b>GCash Number:</b> To confirm the payment came from the registered account.</li>
                            <li><b>Transaction Reference Number:</b> This unique transaction ID allows us to verify your
                                payment in our records.</li>
                            <li><b>Amount Paid:</b> Lets us confirm that you sent the correct amount (₱{{
                                number_format($half_of_total_price) }}) required to secure your booking.</li>
                            <li><b>Screenshot of Transaction:</b> Serves as proof of payment and helps us process your
                                booking faster in case of system delays.</li>
                        </ul>
                        <p class="mt-2 text-xs text-yellow-600">
                            Providing these ensures we can quickly verify and secure your reservation without delays.
                        </p>
                    </div>
                    
                    <!-- Submit -->
                    <button type="submit" id="submitBtn"
                        class="w-full bg-green-600 hover:bg-green-500 text-white font-medium py-3 px-4 rounded-md transition duration-300 flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20"
                            fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                clip-rule="evenodd" />
                        </svg>
                        Submit Booking Request
                    </button>
                </form>

            </div>

            <!-- Booking Summary -->
            <div class="md:w-1/2 bg-red-50 p-8">
                <h2 class="text-2xl font-semibold text-primary mb-6">Booking Summary</h2>

                <!-- Reservation Code -->
                <div class="mb-6 bg-white p-4 shadow-sm rounded-lg border border-red-200">
                    <!-- Section Title -->
                    <h3 class="font-medium text-gray-800 mb-3">
                        Reservation Code
                    </h3>

                    <!-- Reservation Code Display -->
                    <p
                        class="mt-2 text-lg font-mono font-bold tracking-widest text-center text-red-700 bg-red-50 px-6 py-4 rounded-xl border border-red-300 shadow-sm">
                        {{ $reservation_code }}
                    </p>
                </div>


                <div class="mb-6 bg-white p-4 rounded-lg shadow-sm">
                    <h3 class="font-medium text-gray-800 mb-3">Stay Details</h3>
                    <div class="flex justify-between text-sm mb-2">
                        <span class="text-gray-600">Check-in:</span>
                        <span class="font-medium">{{ $checkin->format('F j, Y') }}</span>
                    </div>
                    <div class="flex justify-between text-sm mb-2">
                        <span class="text-gray-600">Check-out:</span>
                        <span class="font-medium">{{ $checkout->format('F j, Y') }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Duration:</span>
                        <span class="font-medium">{{ $nights }} night(s)</span>
                    </div>
                </div>

                <div class="mb-6 bg-white p-4 rounded-lg shadow-sm">
                    <h3 class="font-medium text-gray-800 mb-3">Personal Information</h3>
                    <div class="flex justify-between text-sm mb-2">
                        <span class="text-gray-600">Name:</span>
                        <span class="font-medium">{{ $user_firstname }} {{ $user_lastname }}</span>
                    </div>
                    <div class="flex justify-between text-sm mb-2">
                        <span class="text-gray-600">Phone:</span>
                        <span class="font-medium">{{ $user_phone }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Email:</span>
                        <span class="font-medium">{{ $user_email }}</span>
                    </div>
                </div>

                <div class="bg-white p-4 rounded-lg shadow-sm">
                    <h3 class="font-medium text-gray-800 mb-3">Room Details</h3>

                    @foreach($facilities as $facility)
                    <div class="mb-3 pb-3 border-b border-gray-100">
                        <div class="flex justify-between text-sm mb-1">
                            <span class="font-medium">{{ $facility['name'] }}</span>
                            <span class="font-medium">₱{{ number_format($facility['price'], 2) }}</span>
                        </div>

                        <!-- Add Guest Details Section -->
                        @if(isset($facility['guest_details']) && count($facility['guest_details']) > 0)
                        <div class="mt-2">
                            <p class="text-xs font-medium text-gray-600 mb-1">Guest Composition:</p>
                            <ul class="text-xs text-gray-500 space-y-1">
                                @foreach($facility['guest_details'] as $guest)
                                <li class="flex justify-between">
                                    <span>{{ $guest['type'] }}:</span>
                                    <span>{{ $guest['quantity'] }} guest(s)</span>
                                </li>
                                @endforeach
                            </ul>
                        </div>
                        @endif

                        <div class="flex justify-between text-xs text-gray-500">
                            @if ($breakfastPrice)
                            <span>Breakfast Included:</span>
                            <span>₱{{ number_format($breakfastPrice->price, 2) }}/morning(s)</span>
                            @else
                            <span>Breakfast not included</span>
                            @endif
                        </div>

                        <div class="flex justify-between text-xs text-gray-500">
                            <span>Price per night:</span>
                            <span>₱{{ number_format($facility['price'], 2) }} x {{ $nights }} night(s)</span>
                        </div>
                        <div class="flex justify-between text-sm mt-1">
                            <span>Subtotal:</span>
                            @if ($breakfastPrice)
                            <span class="font-medium">₱{{ number_format((($facility['price']+ $breakfastPrice->price) *
                                $nights ), 2) }}</span>
                            @else
                            <span class="font-medium">₱{{ number_format($facility['price'] * $nights, 2) }}</span>
                            @endif
                        </div>
                    </div>
                    @endforeach

                    <div class="pt-3">
                        <div class="flex justify-between text-lg font-bold mt-4 pt-2 border-t border-gray-200">
                            <span>Total:</span>
                            <span>₱{{ number_format($total_price, 2) }}</span>
                        </div>
                        <div class="flex justify-between text-sm font-medium text-primary mt-2">
                            <span>50% Deposit Due:</span>
                            <span>₱{{ number_format($half_of_total_price, 2) }}</span>
                        </div>
                    </div>
                </div>


                <div class="mt-6 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                    <h3 class="font-medium text-yellow-800 mb-2">Important Reminder</h3>
                    <p class="text-sm text-yellow-700">Your booking will be complete only after we verify your GCash
                        payment. Please allow 1-2 business hours for processing.</p>
                </div>
            </div>
        </div>
    </div>

    <div class="text-center mt-8 text-sm text-gray-500">
        <p>Need help? Contact us at reservations@mtclaramuelresort.com or +63 917 123 4567</p>
        <p class="mt-2">© {{ date('Y') }} Mt.Claramuel Resort. All rights reserved.</p>
    </div>
</div>

<!-- Success Modal - Fixed -->
<div id="successModal"
    class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-50 hidden opacity-0 transition-opacity duration-300">
    <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4 transform transition-all duration-300 scale-95">
        <div class="text-center">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-green-100">
                <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
            </div>
            <h3 class="text-lg font-medium text-gray-900 mt-3">Payment Submitted Successfully!</h3>
            <div class="mt-2">
                <p class="text-sm text-gray-500">
                    Your payment details have been received. We'll verify your payment and send a confirmation email
                    shortly.
                </p>
            </div>
            <div class="mt-4">
                <button id="closeModal" type="button"
                    class="px-4 py-2 bg-primary text-white rounded-md hover:bg-secondary focus:outline-none transition-colors duration-200">
                    Continue
                </button>
            </div>
        </div>
    </div>
</div>


<script>
    // Phone number validation - pure numbers only
        function validatePhoneInput(input) {
            // Remove all non-digit characters
            const digitsOnly = input.value.replace(/\D/g, '');
            
            // Update the input value with digits only
            input.value = digitsOnly;
            
            // Validate the number
            validatePhone(input);
        }

        function validatePhone(input) {
            const digitsOnly = input.value.replace(/\D/g, '');
            const phoneRegex = /^09\d{9}$/; // 11 digits starting with 09
            
            if (digitsOnly.length > 0 && !phoneRegex.test(digitsOnly)) {
                document.getElementById('phone-error').classList.remove('hidden');
                input.classList.add('border-red-500');
                return false;
            }
            
            document.getElementById('phone-error').classList.add('hidden');
            input.classList.remove('border-red-500');
            return true;
        }

        // Image preview functionality
        const proofInput = document.getElementById('proof');
        const imagePreview = document.getElementById('imagePreview');
        const previewImage = document.getElementById('previewImage');
        const removeImage = document.getElementById('removeImage');
        
        proofInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                // Validate file size (2MB max)
                if (file.size > 2 * 1024 * 1024) {
                    showError('proof', 'File size exceeds 2MB limit. Please choose a smaller file.');
                    this.value = '';
                    return;
                }
                
                // Validate file type
                const validTypes = ['image/jpeg', 'image/png', 'image/jpg'];
                if (!validTypes.includes(file.type)) {
                    showError('proof', 'Only JPG, PNG images are allowed.');
                    this.value = '';
                    return;
                }
                
                const reader = new FileReader();
                reader.onload = function(event) {
                    previewImage.src = event.target.result;
                    imagePreview.classList.remove('hidden');
                    
                    // Clear any existing error for this field
                    clearError('proof');
                };
                reader.readAsDataURL(file);
            }
        });
        
        // Remove image functionality
        removeImage.addEventListener('click', function() {
            proofInput.value = '';
            imagePreview.classList.add('hidden');
            clearError('proof');
        });

        // Modal functionality
        const successModal = document.getElementById('successModal');
        const modalContent = successModal.querySelector('.bg-white');
        const closeModal = document.getElementById('closeModal');
        
        // Function to show modal with animation
        function showSuccessModal() {
            successModal.classList.remove('hidden');
            document.body.classList.add('overflow-hidden');
            
            // Trigger reflow for animation
            void successModal.offsetWidth;
            
            // Animate modal in
            setTimeout(() => {
                successModal.classList.remove('opacity-0');
                modalContent.classList.remove('scale-95');
                modalContent.classList.add('scale-100');
            }, 10);
        }
        
        // Function to hide modal with animation
        function hideSuccessModal() {
            successModal.classList.add('opacity-0');
            modalContent.classList.remove('scale-100');
            modalContent.classList.add('scale-95');
            
            setTimeout(() => {
                successModal.classList.add('hidden');
                document.body.classList.remove('overflow-hidden');
                // Redirect after hiding modal/booking-awaiting/${token}
                window.location.href = `/booking-awaiting/`;
            }, 300);
        }
        
        // Close modal when clicking the button
        closeModal.addEventListener('click', function() {
            hideSuccessModal();
        });
        
        
        // Close modal when clicking outside the content
        successModal.addEventListener('click', function() {
            if (e.target === successModal) {
                hideSuccessModal();
            }
        });
        
        // Form submission with enhanced validation
        document.getElementById('paymentForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            // Validate phone first
            const phoneValid = validatePhone(document.getElementById('gcash_number'));
            
            // Rest of validation
            const form = e.target;
            const formData = new FormData(form);
            const submitBtn = document.getElementById('submitBtn');
            
            // Get form values
            const referenceNo = formData.get('reference_no').trim();
            const receipt = formData.get('receipt');
            const amountPaid = parseFloat(formData.get('amount_paid'));
            
            // Validation flags
            let isValid = phoneValid; // Start with phone validation result
            
            // Clear previous error styles and messages for other fields
            document.querySelectorAll('.error-message').forEach(el => {
                if (el.id !== 'phone-error') el.remove();
            });
            document.querySelectorAll('.border-red-500').forEach(el => {
                if (el.id !== 'gcash_number') el.classList.remove('border-red-500');
            });
            
            // Validate Reference Number (required, at least 6 characters)
            if (!referenceNo) {
                showError('reference', 'Reference number is required');
                isValid = false;
            } else if (referenceNo.length < 6) {
                showError('reference', 'Reference number must be at least 6 characters');
                isValid = false;
            }

                // ✅ Validate Amount Paid
            if (isNaN(amountPaid)) {
                showError('amount_paid', 'Amount paid is required');
                isValid = false;
            } else if (amountPaid <= 0) {
                showError('amount_paid', 'Amount must be greater than 0');
                isValid = false;
            } else if (amountPaid < {{ $half_of_total_price }}) {
                showError('amount_paid', 'Amount cannot be less than ₱{{ number_format($half_of_total_price, 2) }}');
                isValid = false;
            }
            
            
            // Validate Payment Proof (required, proper file)
            if (!receipt || receipt.size === 0) {
                showError('proof', 'Payment proof is required');
                isValid = false;
            }
            
            if (!isValid) {
                // Scroll to the first error
                const firstErrorField = document.querySelector('.border-red-500');
                if (firstErrorField) {
                    firstErrorField.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
                return;
            }
            
            submitBtn.disabled = true;
            submitBtn.innerHTML = `
                <svg class="animate-spin -ml-1 mr-2 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Processing...
            `;
            
            try {
                const token = document.querySelector('input[name="token"]').value;
                const urlCustomerPayment = `/submit/booking/${token}`;
            
                const response = await fetch(urlCustomerPayment, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });
                
                const data = await response.json();
                
                if (!response.ok) {
                    // Handle validation errors
                    if (data.errors) {
                        // Clear all previous errors
                        document.querySelectorAll('.error-message').forEach(el => el.remove());
                        document.querySelectorAll('.border-red-500').forEach(el => el.classList.remove('border-red-500'));
                        
                        // Display new errors
                        for (const [field, messages] of Object.entries(data.errors)) {
                            const input = document.querySelector(`[name="${field}"]`);
                            if (input) {
                                input.classList.add('border-red-500');
                                const errorElement = document.createElement('p');
                                errorElement.className = 'error-message text-red-500 text-xs mt-1';
                                errorElement.textContent = messages[0];
                                input.parentNode.appendChild(errorElement);
                            }
                        }
                        throw new Error('Please fix the validation errors');
                    }
                    throw new Error(data.message || 'An error occurred. Please try again.');
                }
                
                if (data.success) {
                    // Show the success modal
                    showSuccessModal();
                } else {
                    throw new Error(data.message || 'An error occurred. Please try again.');
                }
            } catch (error) {
                console.error('Error:', error);
                // Show a more specific error message
                alert(error.message);
                submitBtn.disabled = false;
                submitBtn.innerHTML = `
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                    </svg>
                    Submit Payment Verification
                `;
            }
        });
        
        // Helper function to show error messages
        function showError(fieldId, message) {
            const field = document.getElementById(fieldId);
            field.classList.add('border-red-500');
            
            // Check if error message already exists
            let errorElement = field.parentNode.querySelector('.error-message');
            
            if (!errorElement) {
                errorElement = document.createElement('p');
                errorElement.className = 'error-message text-red-500 text-xs mt-1';
                field.parentNode.appendChild(errorElement);
            }
            
            errorElement.textContent = message;
        }
        
        // Helper function to clear error
        function clearError(fieldId) {
            const field = document.getElementById(fieldId);
            field.classList.remove('border-blue-500');
            
            const errorElement = field.parentNode.querySelector('.error-message');
            if (errorElement && errorElement.id !== 'phone-error') {
                errorElement.remove();
            }
        }
        
</script>
@endsection