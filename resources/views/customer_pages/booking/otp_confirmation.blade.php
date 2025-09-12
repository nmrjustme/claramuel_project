@extends('layouts.bookings')
@section('title', 'OTP Verification')
@section('bookings')
<div class="container mx-auto px-4 py-8 min-h-screen flex items-center justify-center">
    <div class="max-w-md w-full bg-white rounded-lg border border-lightgray p-6">
        <h2 class="text-2xl font-bold text-gray-800 mb-4 text-center">Verify Your Email</h2>

        <div class="text-red-500 mb-4 text-center">
            <svg class="w-16 h-16 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z">
                </path>
            </svg>
        </div>

        <p class="text-gray-600 mb-6 text-center">
            We've sent a 6-digit OTP to <strong id="user-email">{{ $email }}</strong>.
            Please enter it below to continue with your payment.
        </p>

        <form id="otp-form">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">
            <input type="hidden" name="otp" id="full-otp">

            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-3 text-center">Enter OTP Code</label>
                <div class="flex justify-center space-x-2" id="otp-container">
                    <input type="text" maxlength="1" class="otp-input w-12 h-12 text-center text-xl border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500" data-index="1">
                    <input type="text" maxlength="1" class="otp-input w-12 h-12 text-center text-xl border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500" data-index="2">
                    <input type="text" maxlength="1" class="otp-input w-12 h-12 text-center text-xl border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500" data-index="3">
                    <input type="text" maxlength="1" class="otp-input w-12 h-12 text-center text-xl border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500" data-index="4">
                    <input type="text" maxlength="1" class="otp-input w-12 h-12 text-center text-xl border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500" data-index="5">
                    <input type="text" maxlength="1" class="otp-input w-12 h-12 text-center text-xl border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500" data-index="6">
                </div>
                <div id="otp-error" class="text-red-500 text-sm mt-2 text-center hidden"></div>
            </div>

            <button type="submit"
                class="w-full bg-red-500 text-white py-3 px-4 rounded-md hover:bg-red-600 focus:outline-none focus:ring-2 focus:ring-red-500 transition duration-200">
                Verify OTP
            </button>
        </form>

        <div class="mt-6 text-center">
            <p class="text-sm text-gray-500">
                Didn't receive the OTP?
                <a href="#" id="resend-otp" class="text-blue-500 hover:underline font-medium">Resend OTP</a>
                <span id="resend-status" class="hidden text-sm ml-2"></span>
            </p>
        </div>

        <div id="success-message"
            class="hidden bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mt-4"
            role="alert">
            <strong class="font-bold">Success!</strong>
            <span class="block sm:inline"> OTP verified successfully!</span>
        </div>

        <div id="error-message"
            class="hidden bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mt-4"
            role="alert">
            <strong class="font-bold">Error!</strong>
            <span class="block sm:inline" id="error-text"></span>
        </div>

        <div class="bg-gray-100 p-4 rounded-lg mt-6">
            <p class="text-sm text-gray-600 text-center">
                The OTP will expire in 30 minutes.
            </p>
        </div>
    </div>
</div>

<!-- Modal for leaving confirmation -->
<div id="leave-confirmation-modal" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
        <h3 class="text-xl font-bold text-gray-800 mb-4">Leave Site?</h3>
        <p class="text-gray-600 mb-6">
            You're about to leave Mt.Claramuel website to pay. Are you sure you want to proceed?
        </p>
        <div class="flex justify-end space-x-4">
            <button id="cancel-leave" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-100 transition duration-200">
                Cancel
            </button>
            <button id="proceed-leave" class="px-4 py-2 bg-red-500 text-white rounded-md hover:bg-red-600 transition duration-200">
                Proceed
            </button>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const otpForm = document.getElementById('otp-form');
    const fullOtpInput = document.getElementById('full-otp');
    const otpInputs = document.querySelectorAll('.otp-input');
    const otpError = document.getElementById('otp-error');
    const resendLink = document.getElementById('resend-otp');
    const statusElement = document.getElementById('resend-status');
    const successAlert = document.getElementById('success-message');
    const errorAlert = document.getElementById('error-message');
    const errorText = document.getElementById('error-text');
    const token = document.querySelector('input[name="token"]').value;
    const userEmail = '{{ $email }}';
    
    // Modal elements
    const modal = document.getElementById('leave-confirmation-modal');
    const cancelButton = document.getElementById('cancel-leave');
    const proceedButton = document.getElementById('proceed-leave');
    
    // Store redirect data for later use
    let redirectData = null;

    // OTP input navigation logic
    otpInputs.forEach((input, index) => {
        // Focus on first input on load
        if (index === 0) {
            input.focus();
        }

        // Handle input
        input.addEventListener('input', function(e) {
            const value = e.target.value;
            
            // Only allow numbers
            if (!/^\d*$/.test(value)) {
                e.target.value = '';
                return;
            }

            // If a digit is entered, move to next input
            if (value.length === 1 && index < otpInputs.length - 1) {
                otpInputs[index + 1].focus();
            }

            updateFullOtp();
        });

        // Handle backspace
        input.addEventListener('keydown', function(e) {
            if (e.key === 'Backspace' && e.target.value === '' && index > 0) {
                otpInputs[index - 1].focus();
            }
        });

        // Handle paste
        input.addEventListener('paste', function(e) {
            e.preventDefault();
            const pastedData = e.clipboardData.getData('text').slice(0, 6);
            
            if (/^\d+$/.test(pastedData)) {
                pastedData.split('').forEach((digit, i) => {
                    if (otpInputs[i]) {
                        otpInputs[i].value = digit;
                    }
                });
                
                // Focus on the last input that got a value
                const lastIndex = Math.min(pastedData.length - 1, otpInputs.length - 1);
                otpInputs[lastIndex].focus();
                
                updateFullOtp();
            }
        });
    });

    // Update the hidden full OTP input
    function updateFullOtp() {
        const fullOtp = Array.from(otpInputs).map(input => input.value).join('');
        fullOtpInput.value = fullOtp;
    }

    // Validate all OTP fields are filled
    function validateOtpFields() {
        const allFilled = Array.from(otpInputs).every(input => input.value.length === 1);
        if (!allFilled) {
            otpError.textContent = 'Please enter all 6 digits';
            otpError.classList.remove('hidden');
            return false;
        }
        
        const fullOtp = fullOtpInput.value;
        if (!fullOtp.match(/^\d{6}$/)) {
            otpError.textContent = 'Please enter a valid 6-digit OTP';
            otpError.classList.remove('hidden');
            return false;
        }
        
        otpError.classList.add('hidden');
        return true;
    }

    // Show the leave confirmation modal
    function showLeaveConfirmation(data) {
        redirectData = data;
        modal.classList.remove('hidden');
    }

    // Hide the leave confirmation modal
    function hideLeaveConfirmation() {
        modal.classList.add('hidden');
        redirectData = null;
    }

    // Proceed with the redirection
    function proceedWithRedirection() {
        if (redirectData) {
            window.open(redirectData.redirect_url, '_blank');
            window.location.href = '/bookings/customer-info';
        }
        hideLeaveConfirmation();
    }

    // Modal event listeners
    cancelButton.addEventListener('click', hideLeaveConfirmation);
    proceedButton.addEventListener('click', proceedWithRedirection);

    // OTP form submission
    otpForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Hide previous alerts
        successAlert.classList.add('hidden');
        errorAlert.classList.add('hidden');
        otpError.classList.add('hidden');

        // Validate OTP fields
        if (!validateOtpFields()) {
            return;
        }

        const otp = fullOtpInput.value;
        
        // Show loading state
        const submitButton = otpForm.querySelector('button[type="submit"]');
        const originalText = submitButton.textContent;
        submitButton.textContent = 'Verifying...';
        console.log('Route URL:', '{{ route("verify.otp") }}');
        console.log('CSRF Token:', '{{ csrf_token() }}');
        submitButton.disabled = true;
        
        // Make AJAX request
        fetch('{{ route("verify.otp") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                token: token,
                otp: otp
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                successAlert.classList.remove('hidden');
                // Show confirmation modal instead of automatically redirecting
                setTimeout(() => {
                    showLeaveConfirmation(data);
                    submitButton.textContent = originalText;
                    submitButton.disabled = false;
                }, 1000);
            } else {
                errorText.textContent = data.message;
                errorAlert.classList.remove('hidden');
                submitButton.textContent = originalText;
                submitButton.disabled = false;
                
                // Clear OTP fields on error
                otpInputs.forEach(input => input.value = '');
                otpInputs[0].focus();
            }
        })
        .catch(error => {
            errorText.textContent = 'An error occurred. Please try again.';
            errorAlert.classList.remove('hidden');
            submitButton.textContent = originalText;
            submitButton.disabled = false;
        });
    });

    // Resend OTP functionality
    resendLink.addEventListener('click', function(e) {
        e.preventDefault();
        
        // Hide previous alerts
        successAlert.classList.add('hidden');
        errorAlert.classList.add('hidden');

        // Show loading state
        resendLink.classList.add('hidden');
        statusElement.textContent = 'Sending...';
        statusElement.classList.remove('hidden');

        // Make AJAX request
        fetch('{{ route("resend.otp") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                email: userEmail,
                token: token
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                statusElement.textContent = 'OTP sent!';
                statusElement.classList.add('text-green-500');
                
                // Clear OTP fields
                otpInputs.forEach(input => input.value = '');
                otpInputs[0].focus();
                updateFullOtp();
                
                // Reset after 5 seconds
                setTimeout(() => {
                    statusElement.classList.add('hidden');
                    resendLink.classList.remove('hidden');
                }, 5000);
            } else {
                statusElement.classList.add('hidden');
                errorText.textContent = data.message;
                errorAlert.classList.remove('hidden');
                resendLink.classList.remove('hidden');
            }
        })
        .catch(error => {
            statusElement.classList.add('hidden');
            errorText.textContent = 'An error occurred. Please try again.';
            errorAlert.classList.remove('hidden');
            resendLink.classList.remove('hidden');
        });
    });

    // Auto-submit when all fields are filled
    otpInputs[otpInputs.length - 1].addEventListener('input', function(e) {
        if (e.target.value.length === 1) {
            const allFilled = Array.from(otpInputs).every(input => input.value.length === 1);
            if (allFilled) {
                otpForm.dispatchEvent(new Event('submit'));
            }
        }
    });
});
</script>

<style>
.otp-input {
    transition: all 0.2s ease;
}

.otp-input:focus {
    transform: scale(1.05);
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.otp-input:invalid {
    border-color: #ef4444;
}

#leave-confirmation-modal {
    transition: opacity 0.3s ease;
}
</style>
@endsection