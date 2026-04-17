@extends('layouts.bookings')
@section('title', 'Booking Confirmation')
@section('bookings')

<x-header />
<div class="container mx-auto px-6 py-8 max-w-12xl">
    <x-progress-steps
        :currentStep="1"
        :progress="'25%'"
        :steps="[['label' => 'Select Rooms'], ['label' => 'Payment'], ['label' => 'Completed']]"
    />
    
    <div class="container mx-auto px-4 py-12 max-w-4xl">
        <div class="bg-white rounded-xl shadow-lg p-8 text-center">
            <div class="mb-6">
                <i class="fas fa-envelope-open-text text-6xl text-primary mb-4"></i>
                <h1 class="text-3xl font-bold text-gray-800 mb-2">
                    @if($booking->verified_status === 'confirmed')
                        Booking Confirmed!
                    @else
                        Confirmation Email Sent!
                    @endif
                </h1>
                <p class="text-gray-600">We've sent a confirmation email to <span class="font-semibold">{{ $booking->user->email }}</span> 
                @if($booking->created_at)
                    at {{ $booking->created_at->timezone('Asia/Manila')->format('g:i A') }}
                @endif
                </p>
                
                @if($booking->verified_status === 'confirmed')
                    <p class="text-sm text-gray-500 mt-2">
                        Confirmed on {{ $booking->updated_at->timezone('Asia/Manila')->format('M j, Y \a\t g:i A') }}
                    </p>
                @endif
            </div>
            
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-6 mb-8 text-left">
                <h2 class="text-xl font-semibold text-blue-800 mb-4">What's Next?</h2>
                
                @if($booking->verified_status === 'confirmed')
                    <div class="mb-6">
                        <p class="text-gray-700 mb-4">Your booking has been confirmed. You can now proceed to make your payment.</p>
                        <a href="{{ route('booking.redirect', ['booking' => $booking->id]) }}" 
                           class="inline-block px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition"
                           target="_blank" rel="noopener noreferrer"
                           id="proceed-to-payment">
                            <i class="fas fa-credit-card mr-2"></i> Proceed to Payment
                        </a>
                    </div>
                @else
                    <ol class="list-decimal list-inside space-y-3 text-gray-700">
                        <li>Check your email inbox (and spam folder)</li>
                        <li>Click the confirmation link in the email</li>
                        <li>You'll be redirected to complete your payment</li>
                    </ol>
                    
                    <div class="mt-6 p-4 bg-white rounded border border-blue-100">
                        <p class="text-sm text-gray-600 mb-2">Didn't receive the email?</p>
                        <button id="resend-btn" class="text-primary font-medium hover:underline">
                            Click here to resend confirmation
                        </button>
                        <div id="resend-success" class="hidden mt-2 text-sm text-green-600">
                            <i class="fas fa-check-circle mr-1"></i> Email resent successfully!
                        </div>
                        <div id="resend-error" class="hidden mt-2 text-sm text-red-600"></div>
                    </div>
                @endif
            </div>
            
            <div class="text-center">
                <a href="{{ route('bookings') }}" 
                   class="inline-block px-6 py-3 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition">
                    <i class="fas fa-home mr-2"></i> Back to Home
                </a>
            </div>
        </div>
    </div>
</div>
@if($booking->verified_status !== 'confirmed')
<script>
document.getElementById('resend-btn').addEventListener('click', function() {
    const btn = this;
    const successMsg = document.getElementById('resend-success');
    const errorMsg = document.getElementById('resend-error');
    
    // Hide previous messages
    successMsg.classList.add('hidden');
    errorMsg?.classList.add('hidden');
    
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Sending...';

    fetch("{{ route('booking.resendConfirmation', ['booking' => $booking->id]) }}", {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        }
    })
    .then(response => {
        if (!response.ok) {
            return response.json().then(err => { throw err; });
        }
        return response.json();
    })
    .then(data => {
        successMsg.classList.remove('hidden');
    })
    .catch(error => {
        const errorElement = document.getElementById('resend-error') || 
            document.createElement('div');
            
        if (!document.getElementById('resend-error')) {
            errorElement.id = 'resend-error';
            errorElement.className = 'mt-2 text-sm text-red-600';
            btn.parentNode.insertBefore(errorElement, btn.nextSibling);
        }
        
        errorElement.innerHTML = `<i class="fas fa-exclamation-circle mr-1"></i> ${
            error.message || 'Failed to resend email. Please try again later.'
        }`;
        errorElement.classList.remove('hidden');
    })
    .finally(() => {
        setTimeout(() => {
            btn.disabled = false;
            btn.innerHTML = 'Click here to resend confirmation';
            // Hide success message after 5 seconds
            setTimeout(() => {
                successMsg.classList.add('hidden');
            }, 5000);
        }, 3000);
    });
});

// Payment button loading state
document.getElementById('proceed-to-payment')?.addEventListener('click', function(e) {
    this.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Processing...';
});
</script>
@endif

@endsection