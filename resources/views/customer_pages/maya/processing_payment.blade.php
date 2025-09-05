@extends('layouts.bookings')
@section('title', 'Payment')
@section('bookings')
<x-header />
<div class="container mx-auto px-4 sm:px-6 py-8 sm:py-12 max-w-7xl">

      <!-- Progress Steps -->
      <x-progress-step :currentStep="3" :steps="[
            ['label' => 'Select Rooms'],
            ['label' => 'Your Details'],
            ['label' => 'Payment'],
            ['label' => 'Completed']
      ]" />`

      <!-- Fancy Loading Section -->
      <div class="flex items-center justify-center mt-10 sm:mt-16 px-2">
            <div
                  class="bg-white rounded-lg  border border-lightgray p-6 sm:p-10 flex flex-col items-center w-full max-w-sm sm:max-w-md md:max-w-lg">

                  <!-- Glowing Spinner -->
                  <div class="relative">
                        <div
                              class="w-16 h-16 sm:w-20 sm:h-20 border-6 sm:border-8 border-red-200 border-t-red-600 rounded-full animate-spin">
                        </div>
                        <div
                              class="absolute inset-0 w-16 h-16 sm:w-20 sm:h-20 rounded-full animate-ping bg-red-500 opacity-20">
                        </div>
                  </div>

                  <!-- Text with subtle animation -->
                  <p class="mt-6 sm:mt-8 text-lg sm:text-xl font-bold text-red-600 animate-pulse text-center"
                        id="status-message">
                        Waiting for your payment…
                  </p>

                  <p class="mt-2 sm:mt-3 text-sm sm:text-base text-gray-600 text-center leading-relaxed">
                        We’ve opened a new tab for you to complete your payment.
                        Once your payment is successful, this page will automatically move to the next step.
                  </p>

                  <!-- Fun dots animation -->
                  <div class="flex space-x-2 mt-4 sm:mt-6">
                        <div class="w-2.5 h-2.5 sm:w-3 sm:h-3 bg-red-600 rounded-full animate-bounce"></div>
                        <div class="w-2.5 h-2.5 sm:w-3 sm:h-3 bg-red-500 rounded-full animate-bounce delay-150"></div>
                        <div class="w-2.5 h-2.5 sm:w-3 sm:h-3 bg-red-400 rounded-full animate-bounce delay-300"></div>
                  </div>

                  <!-- Fallback button if automatic checking fails -->
                  <button id="manual-check-btn"
                        class="mt-6 bg-red-600 hover:bg-red-700 text-white font-medium py-2 px-4 rounded-lg transition hidden">
                        Check Payment Status Manually
                  </button>

            </div>
      </div>

</div>

<script>
      document.addEventListener('DOMContentLoaded', function() {
            const statusMessage = document.getElementById('status-message');
            const manualCheckBtn = document.getElementById('manual-check-btn');
            let checkInterval = null;
            let retryCount = 0;
            const maxRetries = 20; // Limit retries to prevent infinite loops
            const token = '{{ $token }}';
            console.log(token);
            // Function to check payment status
            function checkPaymentStatus() {
                  const statusCheckUrl = '{{ route('payment.status', ['token' => $token]) }}';
                  // Get order data from the server
                  fetch(statusCheckUrl)
                        .then(response => {
                              if (!response.ok) {
                                    throw new Error(`HTTP error! Status: ${response.status}`);
                              }
                              return response.json();
                        })
                        .then(data => {
                              // If we have a valid order with status
                              if (data.order && data.order.status) {
                                    if (data.order.status === 'paid') {
                                          // Payment successful - redirect to success page
                                          window.location.href = `/booking-awaiting`;
                                    } else if (data.order.status === 'failed' || 
                                                data.order.status === 'cancelled' || 
                                                data.order.status === 'expired') {
                                          // Payment failed - redirect to error page
                                          window.location.href = `/booking/payment-failed?reason=${data.order.status}&order=${data.order.reference_number}`;
                                    } else {
                                          // Payment still processing, continue checking
                                          statusMessage.textContent = 'Waiting for your payment...';
                                          retryCount = 0; // Reset retry count if we have a valid order
                                    }
                              } else {
                                    // No valid order data yet
                                    retryCount++;
                                    statusMessage.textContent = 'Setting up payment, waiting...';
                                    
                                    // Stop after max retries to prevent infinite loop
                                    if (retryCount >= maxRetries) {
                                          clearInterval(checkInterval);
                                          statusMessage.textContent = 'Payment setup taking longer than expected.';
                                          manualCheckBtn.classList.remove('hidden');
                                    }
                              }
                        })
                        .catch(error => {
                              console.error('Error checking payment status:', error);
                              retryCount++;
                              statusMessage.textContent = 'Having trouble connecting...';
                              
                              // Stop after max retries
                              if (retryCount >= maxRetries) {
                                    clearInterval(checkInterval);
                                    statusMessage.textContent = 'Unable to verify payment status.';
                                    manualCheckBtn.classList.remove('hidden');
                              }
                        });
            }
            
            // Start checking payment status immediately and then every 3 seconds
            checkPaymentStatus(); // Initial check
            checkInterval = setInterval(checkPaymentStatus, 3000);
            
            // Manual check button handler
            manualCheckBtn.addEventListener('click', function() {
                  retryCount = 0;
                  statusMessage.textContent = 'Checking payment status...';
                  checkPaymentStatus();
                  
                  // Restart the interval if it was cleared
                  if (!checkInterval) {
                        checkInterval = setInterval(checkPaymentStatus, 3000);
                  }
            });
            
            // Clean up interval if user navigates away
            window.addEventListener('beforeunload', function() {
                  if (checkInterval) {
                        clearInterval(checkInterval);
                  }
            });
      });
</script>

@endsection