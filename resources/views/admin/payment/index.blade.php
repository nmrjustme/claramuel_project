@extends('layouts.admin')
@section('title', 'GCash Payments')

@php
    $active = 'payments';
@endphp

@section('content')
<div class="min-h-screen p-6">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-3xl font-bold text-white">Payments Verification</h1>
            <p class="text-gray-200">Manually verify customer GCash payments by reference number</p>
        </div>
        <div class="flex items-center space-x-4">
            <div class="relative">
                <input type="text" id="searchPayments" placeholder="Search by reference..." 
                    class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd"></path>
                    </svg>
                </div>
            </div>
            <div class="flex space-x-2">
                <button id="filterPending" class="px-3 py-1 bg-yellow-100 text-yellow-800 rounded-full text-xs font-medium">Pending</button>
                <button id="filterVerified" class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-xs font-medium">Verified</button>
                <button id="filterRejected" class="px-3 py-1 bg-red-100 text-red-800 rounded-full text-xs font-medium">Rejected</button>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Payment Details</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">GCash Info</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200" id="paymentsTableBody">
                    @forelse ($payments as $payment)
                        @include('admin.payment.payment_row', ['payment' => $payment])
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">
                            No payments found.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-3 bg-gray-50 border-t border-gray-200 flex items-center justify-between">
            <div class="text-sm text-gray-500">
                Showing <span id="showingCount">{{ $payments->count() }}</span> of <span id="totalCount">{{ $payments->total() }}</span> payments
            </div>
            <div class="flex space-x-2">
                <button id="prevPage" class="px-3 py-1 border border-gray-300 rounded-md text-sm disabled:opacity-50" disabled>Previous</button>
                <button id="nextPage" class="px-3 py-1 border border-gray-300 rounded-md text-sm disabled:opacity-50" disabled>Next</button>
            </div>
        </div>
    </div>
</div>

<!-- Payment Verification Modal -->
<div id="paymentModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-2xl">
        <div class="p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-bold text-gray-800">Verify Payment</h3>
                <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            
            <div id="paymentDetails" class="space-y-4">
                <!-- Dynamic content loaded via AJAX -->
                <div class="text-center py-8">
                    <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600 mx-auto"></div>
                    <p class="mt-2 text-gray-500">Loading payment details...</p>
                </div>
            </div>

            <!-- Rejection Form (hidden by default) -->
            <div id="rejectForm" class="mt-4 hidden">
                <div class="bg-red-50 border-l-4 border-red-400 p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-red-800">Reject Payment</h3>
                            <div class="mt-2 text-sm text-red-700">
                                <div class="mb-3">
                                    <label for="rejectReason" class="block text-sm font-medium text-red-700">Reason for rejection</label>
                                    <textarea id="rejectReason" rows="3" class="mt-1 block w-full border border-red-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 sm:text-sm"></textarea>
                                </div>
                                <div class="flex items-center">
                                    <input id="gcashMismatch" name="gcashMismatch" type="checkbox" class="h-4 w-4 text-red-600 focus:ring-red-500 border-red-300 rounded">
                                    <label for="gcashMismatch" class="ml-2 block text-sm text-red-700">GCash reference number doesn't match</label>
                                </div>
                            </div>
                            <div class="mt-4 flex justify-end space-x-3">
                                <button type="button" onclick="hideRejectForm()" class="px-3 py-1 text-sm font-medium text-red-700 hover:text-red-600">
                                    Cancel
                                </button>
                                <button type="button" onclick="rejectPayment()" class="px-3 py-1 bg-red-600 text-white rounded-md text-sm font-medium hover:bg-red-700">
                                    Confirm Rejection
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Confirmation Modal (Hidden by Default) -->
<div id="verifyConfirmationModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 max-w-md w-full">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Confirm Verification</h3>
        <p class="text-sm text-gray-500 mb-6">Are you sure you want to verify this payment? This action cannot be undone.</p>
        
        <div class="flex justify-end space-x-3">
            <button type="button" onclick="hideVerifyConfirmation()" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md text-sm font-medium hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                Cancel
            </button>
            <button type="button" onclick="proceedWithVerification()" class="px-4 py-2 bg-green-600 text-white rounded-md text-sm font-medium hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                Confirm Verify
            </button>
        </div>
    </div>
</div>


<!-- Notification Sound -->
<audio id="notificationSound" preload="auto">
    <source src="{{ asset('sounds/notification.mp3') }}" type="audio/mpeg">
</audio>

<!-- Toast Container -->
<div id="toastContainer" class="fixed top-4 right-4 space-y-2 z-50"></div>
@endsection

@section('content_js')
<script>
    // Show confirmation dialog
    function confirmVerifyPaymentButton() {
        document.getElementById('verifyConfirmationModal').classList.remove('hidden');
    }
    
    // Hide confirmation dialog
    function hideVerifyConfirmation() {
        document.getElementById('verifyConfirmationModal').classList.add('hidden');
    }
    
    // Proceed with verification (your existing function)
    function proceedWithVerification() {
        hideVerifyConfirmation();
        verifyPayment(); // Your existing verifyPayment function
    }
</script>
<script>
// Global variables
let currentPaymentId = null;
let eventSource = null;
const notificationSound = document.getElementById('notificationSound');

// Initialize SSE connection
function initSSE() {
    if (eventSource) eventSource.close();
    
    eventSource = new EventSource("{{ route('admin.payments.stream') }}");
    
    eventSource.onmessage = function(e) {
        const data = JSON.parse(e.data);
        if (data.type === 'new_payment') {
            handleNewPayment(data.payment);
        } else if (data.type === 'payment_updated') {
            updatePaymentRow(data.payment);
        }
    };
    
    eventSource.onerror = function() {
        setTimeout(initSSE, 5000);
    };
}

// Handle new payment notification
function handleNewPayment(payment) {
    notificationSound.play().catch(e => console.log('Audio play failed:', e));
    
    fetch(`/admin/payments/${payment.id}/row`)
        .then(response => response.text())
        .then(html => {
            const wrapper = document.createElement('tbody');
            wrapper.innerHTML = html;
            const newRow = wrapper.firstElementChild;
            
            newRow.classList.add('bg-blue-50', 'animate-pulse');
            setTimeout(() => newRow.classList.remove('bg-blue-50', 'animate-pulse'), 5000);
            
            const tableBody = document.getElementById('paymentsTableBody');
            const emptyRow = tableBody.querySelector('tr:first-child td[colspan]');
            if (emptyRow) tableBody.innerHTML = '';
            
            tableBody.prepend(newRow);
            updateCounts(1);
            
            if ('Notification' in window && Notification.permission === 'granted') {
                new Notification('New GCash Payment', {
                    body: `Ref: ${payment.reference_no}\nAmount: ₱${payment.amount.toFixed(2)}`,
                    icon: '/favicon.ico'
                });
            }
        });
}

// View payment details
function viewPayment(paymentId) {
    currentPaymentId = paymentId;
    const modal = document.getElementById('paymentModal');
    const content = document.getElementById('paymentDetails');
    const actionButtons = document.getElementById('actionButtons');
    const rejectForm = document.getElementById('rejectForm');
    
    content.innerHTML = `
        <div class="text-center py-8">
            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600 mx-auto"></div>
            <p class="mt-2 text-gray-500">Loading payment details...</p>
        </div>
    `;

    rejectForm.classList.add('hidden');
    
    fetch(`/admin/payments/${paymentId}/details`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                content.innerHTML = data.html;
                if (data.payment.status === 'pending') {
                    actionButtons.classList.remove('hidden');
                }
            } else {
                showToast(data.message || 'Failed to load details', 'error');
                closeModal();
            }
        })
        .catch(error => {
            showToast('Network error loading details', 'error');
            closeModal();
        });
    
    modal.classList.remove('hidden');
    document.body.classList.add('overflow-hidden');
}

// Close modal
function closeModal() {
    document.getElementById('paymentModal').classList.add('hidden');
    document.body.classList.remove('overflow-hidden');
    currentPaymentId = null;
}

// Show reject form
function showRejectForm() {
    document.getElementById('actionButtons').classList.add('hidden');
    document.getElementById('rejectForm').classList.remove('hidden');
}

// Hide reject form
function hideRejectForm() {
    document.getElementById('rejectForm').classList.add('hidden');
    document.getElementById('actionButtons').classList.remove('hidden');
    document.getElementById('rejectReason').value = '';
    document.getElementById('gcashMismatch').checked = false;
}

// Verify payment
function verifyPayment() {
    if (!currentPaymentId) return;
    
    const paymentDetails = document.getElementById('paymentDetails');
    const isGCash = paymentDetails.querySelector('[data-payment-method="gcash"]') !== null;
    const gcashRef = isGCash ? paymentDetails.querySelector('[data-gcash-reference]').textContent.trim() : null;
    
    if (isGCash && (!gcashRef || gcashRef === 'Not provided')) {
        const refNumber = prompt('Please enter the GCash reference number for this payment:');
        if (!refNumber) {
            showToast('GCash reference is required', 'error');
            return;
        }
        
        updateReference(currentPaymentId, refNumber)
            .then(() => verifyPaymentWithReceipt(currentPaymentId))
            .catch(error => showToast(error.message, 'error'));
        return;
    }
    
    verifyPaymentWithReceipt(currentPaymentId);
}

function verifyPaymentWithReceipt(paymentId) {
    const button = document.querySelector('#actionButtons button:first-child');
    const originalText = button.innerHTML;
    button.innerHTML = '<span class="animate-pulse">Verifying and sending receipt...</span>';
    button.disabled = true;
    
    fetch(`/payments/${paymentId}/verify-with-receipt`, {
        method: 'POST',
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast('Payment verified and receipt sent', 'success');
            updatePaymentRow(data.payment);
            closeModal();
        } else {
            throw new Error(data.message || 'Verification failed');
        }
    })
    .catch(error => showToast(error.message, 'error'))
    .finally(() => {
        button.innerHTML = originalText;
        button.disabled = false;
    });
}

// Update GCash reference
function updateReference(paymentId, reference) {
    return fetch(`/admin/payments/${paymentId}/update-reference`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ gcash_reference: reference })
    })
    .then(response => response.json())
    .then(data => {
        if (!data.success) throw new Error(data.message);
        return data;
    });
}

// Confirm verification
function confirmVerification(paymentId) {
    const button = document.querySelector('#actionButtons button:first-child');
    const originalText = button.innerHTML;
    button.innerHTML = '<span class="animate-pulse">Verifying...</span>';
    button.disabled = true;
    
    fetch(`/admin/payments/${paymentId}/verify`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast('Payment verified successfully', 'success');
            updatePaymentRow(data.payment);
            closeModal();
        } else {
            throw new Error(data.message || 'Verification failed');
        }
    })
    .catch(error => showToast(error.message, 'error'))
    .finally(() => {
        button.innerHTML = originalText;
        button.disabled = false;
    });
}

// Reject payment
function rejectPayment() {
    if (!currentPaymentId) return;
    
    const reason = document.getElementById('rejectReason').value.trim();
    if (!reason) {
        showToast('Please provide a rejection reason', 'error');
        return;
    }
    
    const gcashMismatch = document.getElementById('gcashMismatch').checked;
    const button = document.querySelector('#rejectForm button:last-child');
    const originalText = button.innerHTML;
    button.innerHTML = '<span class="animate-pulse">Processing...</span>';
    button.disabled = true;
    
    fetch(`/admin/payments/${currentPaymentId}/reject`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ 
            reason: reason,
            gcash_reference_mismatch: gcashMismatch 
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast('Payment rejected', 'success');
            updatePaymentRow(data.payment);
            closeModal();
        } else {
            throw new Error(data.message || 'Rejection failed');
        }
    })
    .catch(error => showToast(error.message, 'error'))
    .finally(() => {
        button.innerHTML = originalText;
        button.disabled = false;
    });
}

// Update payment row
function updatePaymentRow(payment) {
    const row = document.querySelector(`#paymentsTableBody tr[data-id="${payment.id}"]`);
    if (row) {
        fetch(`/admin/payments/${payment.id}/row`)
            .then(response => response.text())
            .then(html => {
                const wrapper = document.createElement('tbody');
                wrapper.innerHTML = html;
                const newRow = wrapper.firstElementChild;
                row.replaceWith(newRow);
            });
    }
}

// Show toast notification
function showToast(message, type = 'success') {
    const toastContainer = document.getElementById('toastContainer');
    const toast = document.createElement('div');
    toast.className = `px-6 py-3 rounded-md shadow-lg text-white flex items-center ${
        type === 'success' ? 'bg-green-500' : 'bg-red-500'
    } animate-fade-in`;
    toast.innerHTML = `
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="${type === 'success' ? 'M5 13l4 4L19 7' : 'M6 18L18 6M6 6l12 12'}" />
        </svg>
        <span>${message}</span>
    `;
    toastContainer.appendChild(toast);
    
    setTimeout(() => {
        toast.classList.add('animate-fade-out');
        setTimeout(() => toast.remove(), 300);
    }, 3000);
}

// Request notification permission
function requestNotificationPermission() {
    if ('Notification' in window && Notification.permission !== 'granted') {
        Notification.requestPermission();
    }
}

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    initSSE();
    requestNotificationPermission();
    
    // Filter buttons
    document.getElementById('filterPending').addEventListener('click', () => filterPayments('pending'));
    document.getElementById('filterVerified').addEventListener('click', () => filterPayments('verified'));
    document.getElementById('filterRejected').addEventListener('click', () => filterPayments('rejected'));
    
    // Search functionality
    document.getElementById('searchPayments').addEventListener('input', (e) => {
        searchPayments(e.target.value);
    });
});

// Filter payments by status
function filterPayments(status) {
    const url = new URL(window.location.href);
    url.searchParams.set('status', status);
    window.location.href = url.toString();
}

// Search payments
function searchPayments(query) {
    if (query.length < 2 && query.length > 0) return;
    
    fetch(`/admin/payments/search?q=${encodeURIComponent(query)}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById('paymentsTableBody').innerHTML = data.html;
                updateCounts(data.count, data.total);
            }
        });
}

// Update counts
function updateCounts(showing, total) {
    if (showing !== undefined) document.getElementById('showingCount').textContent = showing;
    if (total !== undefined) document.getElementById('totalCount').textContent = total;
}

// Close SSE connection when page is unloaded
window.addEventListener('beforeunload', function() {
    if (eventSource) eventSource.close();
});
</script>
@endsection

@section('content_css')
<style>
.animate-fade-in {
    animation: fadeIn 0.3s ease forwards;
}

.animate-fade-out {
    animation: fadeOut 0.3s ease forwards;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}

@keyframes fadeOut {
    from { opacity: 1; transform: translateY(0); }
    to { opacity: 0; transform: translateY(10px); }
}

.animate-pulse {
    animation: pulse 1.5s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}

@keyframes pulse {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.5; }
}

/* Highlight new payments */
@keyframes highlight {
    0% { background-color: rgba(219, 234, 254, 1); }
    100% { background-color: rgba(219, 234, 254, 0); }
}

.highlight-new {
    animation: highlight 3s ease-out;
}
</style>
@endsection