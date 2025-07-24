<!-- Payment Details Modal -->
<div id="paymentDetailsModal" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-[var(--z-modal)] hidden">
    <!-- Modal content will be loaded here -->
</div>

<script>
    // Function to open payment details modal
    function openPaymentDetailsModal(paymentData) {
        showLoadingModal('paymentDetailsModal', 'Loading payment details...');
        
        // Format the data to match expected structure
        const formattedData = {
            id: paymentData.id,
            amount: paymentData.amount || paymentData.total_amount,
            amount_lacking: paymentData.amount_lacking || paymentData.balance,
            gcash_number: paymentData.gcash_number || paymentData.gcash_no,
            reference_number: paymentData.reference_number || paymentData.reference_no,
            receipt_image: paymentData.receipt_image || paymentData.receipt_path,
            is_verified: paymentData.is_verified || (paymentData.status === 'verified'),
            payment_method: paymentData.payment_method || 'GCash' // Default to GCash if not specified
        };

        setTimeout(() => {
            const modal = document.getElementById('paymentDetailsModal');
            if (modal) {
                modal.classList.remove('hidden');
                document.body.classList.add('overflow-hidden');
                
                // Format the amount with PHP currency
                const formattedAmount = formattedData.amount ? 
                    `₱${parseFloat(formattedData.amount).toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}` : 
                    'Amount not specified';
                
                // Determine if amount is lacking
                const isAmountLacking = formattedData.amount_lacking && parseFloat(formattedData.amount_lacking) > 0;
                const lackingAmount = isAmountLacking ? 
                    `₱${parseFloat(formattedData.amount_lacking).toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}` : 
                    'None';
                
                // Payment method icon and display text
                const paymentMethodInfo = getPaymentMethodInfo(formattedData.payment_method);

                modal.innerHTML = `
                    <div class="bg-white rounded-xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto">
                        <!-- Modal Header -->
                        <div class="bg-red-600 text-white p-4 rounded-t-xl flex justify-between items-center sticky top-0 z-10">
                            <div class="flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <h2 class="text-xl font-bold">Payment Details</h2>
                            </div>
                            <button onclick="closeModal('paymentDetailsModal')" class="text-white hover:text-red-200">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                        
                        <!-- Modal Body -->
                        <div class="p-6">
                            <div class="space-y-6">
                                <!-- Payment Method -->
                                <div>
                                    <h4 class="text-md font-medium text-gray-900 mb-2">Payment Method</h4>
                                    <div class="flex items-center bg-gray-50 p-3 rounded-lg">
                                        <div class="mr-3 p-2 ${paymentMethodInfo.bgColor} rounded-full">
                                            ${paymentMethodInfo.icon}
                                        </div>
                                        <div>
                                            <p class="font-medium">${formattedData.payment_method}</p>
                                            <p class="text-sm text-gray-500">${paymentMethodInfo.description}</p>
                                        </div>
                                    </div>
                                </div>
                                
                                ${formattedData.payment_method.toLowerCase() === 'gcash' ? `
                                <!-- GCash Number -->
                                <div>
                                    <h4 class="text-md font-medium text-gray-900 mb-2">GCash Number</h4>
                                    <div class="bg-gray-50 p-3 rounded-lg">
                                        <p class="font-mono text-lg">${formattedData.gcash_number || 'Not provided'}</p>
                                    </div>
                                </div>
                                ` : ''}
                                
                                <!-- Transaction Reference -->
                                <div>
                                    <h4 class="text-md font-medium text-gray-900 mb-2">Transaction Reference</h4>
                                    <div class="bg-gray-50 p-3 rounded-lg">
                                        <p class="font-mono text-sm break-all">${formattedData.reference_number || 'Not provided'}</p>
                                    </div>
                                </div>
                                
                                <!-- Payment Amount -->
                                <div>
                                    <h4 class="text-md font-medium text-gray-900 mb-2">Payment Amount</h4>
                                    <div class="bg-gray-50 p-3 rounded-lg">
                                        <p class="text-xl font-semibold">${formattedAmount}</p>
                                    </div>
                                </div>
                                
                                <!-- Lacking Amount -->
                                <div>
                                    <h4 class="text-md font-medium text-gray-900 mb-2">Lacking Amount</h4>
                                    <div class="bg-gray-50 p-3 rounded-lg">
                                        <p class="text-lg ${isAmountLacking ? 'text-red-600 font-semibold' : 'text-gray-600'}">${lackingAmount}</p>
                                    </div>
                                </div>
                                
                                <!-- Payment Receipt -->
                                <div>
                                    <h4 class="text-md font-medium text-gray-900 mb-2">Payment Receipt</h4>
                                    <div class="bg-gray-50 p-3 rounded-lg">
                                        ${formattedData.receipt_image ? `
                                            <div class="mb-3">
                                                <img src="${formattedData.receipt_image}" alt="Payment Receipt" class="max-w-full h-auto rounded-lg border border-gray-200">
                                            </div>
                                            <a href="${formattedData.receipt_image}" target="_blank" class="text-blue-600 hover:text-blue-800 text-sm flex items-center">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                                </svg>
                                                Download Receipt
                                            </a>
                                        ` : `
                                            <p class="text-gray-500 italic">No receipt uploaded</p>
                                        `}
                                    </div>
                                </div>
                                
                                <!-- Verification Status -->
                                <div>
                                    <h4 class="text-md font-medium text-gray-900 mb-2">Verification Status</h4>
                                    <div class="flex items-center justify-between bg-gray-50 p-3 rounded-lg">
                                        <div class="flex items-center">
                                            <div class="mr-3 p-2 rounded-full ${formattedData.is_verified ? 'bg-green-100 text-green-600' : 'bg-yellow-100 text-yellow-600'}">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    ${formattedData.is_verified ? `
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                    ` : `
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                    `}
                                                </svg>
                                            </div>
                                            <div>
                                                <p class="font-medium">${formattedData.is_verified ? 'Verified' : 'Pending Verification'}</p>
                                                <p class="text-sm text-gray-500">${formattedData.is_verified ? 'Payment has been verified' : 'Waiting for admin verification'}</p>
                                            </div>
                                        </div>
                                        ${!formattedData.is_verified ? `
                                            <button onclick="verifyPayment('${formattedData.id || ''}')" 
                                                class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 transition-colors flex items-center">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                                </svg>
                                                Verify Payment
                                            </button>
                                        ` : ''}
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Modal Footer -->
                        <div class="bg-gray-50 p-4 rounded-b-xl sticky bottom-0 border-t">
                            <div class="flex justify-end">
                                <button onclick="closeModal('paymentDetailsModal')" 
                                    class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 transition-colors">
                                    Close
                                </button>
                            </div>
                        </div>
                    </div>
                `;
            }
        }, 500);
    }

    // Helper function to get payment method details
    function getPaymentMethodInfo(method) {
        const methods = {
            'GCash': {
                icon: `<svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                </svg>`,
                bgColor: 'bg-blue-100',
                description: 'Mobile Payment'
            },
            'Bank Transfer': {
                icon: `<svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z" />
                </svg>`,
                bgColor: 'bg-green-100',
                description: 'Bank Transfer'
            },
            'Cash': {
                icon: `<svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>`,
                bgColor: 'bg-gray-100',
                description: 'Cash Payment'
            }
        };
        
        return methods[method] || {
            icon: `<svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>`,
            bgColor: 'bg-purple-100',
            description: 'Other Payment Method'
        };
    }

    // Function to verify payment with actual AJAX call
    function verifyPayment(paymentId) {
        if (!paymentId) {
            alert('Error: No payment ID provided');
            return;
        }
        
        showLoadingModal('paymentDetailsModal', 'Verifying payment...');
        
        // Make actual AJAX call to verify payment
        fetch(`/payment-details/${paymentId}/verify`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Show success message
                const modal = document.getElementById('paymentDetailsModal');
                if (modal) {
                    modal.innerHTML = `
                        <div class="bg-white rounded-xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto">
                            <div class="p-8 text-center">
                                <div class="text-green-500 mx-auto mb-4">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                </div>
                                <h3 class="text-xl font-bold mb-2">Payment Verified!</h3>
                                <p class="text-gray-600 mb-6">${data.message || 'The payment has been successfully verified.'}</p>
                                <button onclick="closeModal('paymentDetailsModal')" 
                                    class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 transition-colors">
                                    Close
                                </button>
                            </div>
                        </div>
                    `;
                }
            } else {
                showErrorModal('paymentDetailsModal', data.message || 'Failed to verify payment');
            }
        })
        .catch(error => {
            showErrorModal('paymentDetailsModal', 'An error occurred while verifying the payment');
            console.error('Error:', error);
        });
    }

    // Basic modal functions (should be defined in a shared utilities file)
    function closeModal(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
        }
    }

    function showLoadingModal(modalId, message) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.classList.remove('hidden');
            document.body.classList.add('overflow-hidden');
            modal.innerHTML = `
                <div class="bg-white rounded-xl shadow-2xl w-full max-w-md p-6">
                    <div class="flex items-center justify-center">
                        <div class="animate-spin rounded-full h-10 w-10 border-b-2 border-red-600 mr-4"></div>
                        <p class="text-gray-700">${message}</p>
                    </div>
                </div>
            `;
        }
    }

    function showErrorModal(modalId, errorMessage) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.innerHTML = `
                <div class="bg-white rounded-xl shadow-2xl w-full max-w-md p-6">
                    <div class="text-center">
                        <div class="text-red-500 mx-auto mb-4">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold mb-2">Error</h3>
                        <p class="text-gray-600 mb-6">${errorMessage}</p>
                        <button onclick="closeModal('${modalId}')" 
                            class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 transition-colors">
                            Close
                        </button>
                    </div>
                </div>
            `;
        }
    }
</script>