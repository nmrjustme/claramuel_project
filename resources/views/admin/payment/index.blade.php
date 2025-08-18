@extends('layouts.admin')
@section('title', 'GCash Payments')

@php
    $active = 'payments';
@endphp

@section('content')
<div class="min-h-screen px-6 py-6">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Payments Verification</h1>
            <p class="text-gray-600">Manually verify customer GCash payments by reference number</p>
        </div>
        
        <div class="flex items-center space-x-4">
            <span class="text-sm text-gray-600">
                Total Payments: <span id="paymentCount" class="font-bold">{{ $payments->total() }}</span>
            </span>
            <button id="refreshBtn" class="flex items-center text-blue-600 hover:text-blue-800">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M4 2a1 1 0 011 1v2.101a7.002 7.002 0 0111.601 2.566 1 1 0 11-1.885.666A5.002 5.002 0 005.999 7H9a1 1 0 010 2H4a1 1 0 01-1-1V3a1 1 0 011-1zm.008 9.057a1 1 0 011.276.61A5.002 5.002 0 0014.001 13H11a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0v-2.101a7.002 7.002 0 01-11.601-2.566 1 1 0 01.61-1.276z" clip-rule="evenodd" />
                </svg>
                Refresh
            </button>
        </div>
    </div>
    
    <!-- Search and Filter Section -->
    <div class="mb-6 flex justify-between items-center">
        <div class="relative max-w-md">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <svg class="h-5 w-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd"></path>
                </svg>
            </div>
            <input id="searchPayments" type="text" value="{{ request('search') }}" class="block w-full pl-10 pr-3 py-2 border border-darkgray rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-blue-500 focus:border-blue-500 sm:text-sm" placeholder="Search by name, reference or amount">
        </div>
        <div class="flex space-x-2">
            <button id="filterVerified" class="px-4 py-2 rounded-md text-xs font-medium bg-green-100 text-green-800 hover:bg-green-200 focus:outline-none focus:ring-2 focus:ring-green-500 {{ request('status') === 'verified' ? 'ring-2 ring-offset-2 ring-green-500' : '' }}">
                Verified
            </button>
            <button id="filterUnderverification" class="px-4 py-2 rounded-md text-xs font-medium bg-red-100 text-red-800 hover:bg-red-200 focus:outline-none focus:ring-2 focus:ring-red-500 {{ request('status') === 'under_verification' ? 'ring-2 ring-offset-2 ring-red-500' : '' }}">
                Under Verification
            </button>
            <button id="filterPending" class="px-4 py-2 rounded-md text-xs font-medium bg-yellow-100 text-yellow-800 hover:bg-yellow-200 focus:outline-none focus:ring-2 focus:ring-yellow-500 {{ request('status') === 'Pending' ? 'ring-2 ring-offset-2 ring-red-500' : '' }}">
                Pending
            </button>
            <button id="filterRejected" class="px-4 py-2 rounded-md text-xs font-medium bg-red-100 text-red-800 hover:bg-red-200 focus:outline-none focus:ring-2 focus:ring-red-500 {{ request('status') === 'rejected' ? 'ring-2 ring-offset-2 ring-red-500' : '' }}">
                Rejected
            </button>
            <button id="filterAll" class="px-4 py-2 rounded-md text-xs font-medium bg-gray-100 text-gray-800 hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-500 {{ !request('status') ? 'ring-2 ring-offset-2 ring-gray-500' : '' }}">
                All
            </button>
        </div>
    </div>
    
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-100">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">To be paid amount</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Read Status</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">GCash Info</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200" id="paymentsTableBody">

                    <tr id="loadingRow" class="hidden">
                        <td colspan="5" class="px-6 py-4 text-center">
                            <div class="flex flex-col justify-center items-center gap-2">
                                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-red-600"></div>
                                <span>Loading payments...</span>
                            </div>
                        </td>
                    </tr>
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
                Showing <span id="showingCount">{{ $payments->firstItem() ?: 0 }}</span> to <span id="showingCountEnd">{{ $payments->lastItem() ?: 0 }}</span> of <span id="totalCount">{{ $payments->total() }}</span> payments
            </div>
            <div class="flex space-x-2">
                @if ($payments->previousPageUrl())
                    <a href="{{ $payments->previousPageUrl() }}" class="px-3 py-1 border border-gray-300 rounded-md text-sm hover:bg-gray-50">Previous</a>
                @else
                    <button class="px-3 py-1 border border-gray-300 rounded-md text-sm opacity-50 cursor-not-allowed" disabled>Previous</button>
                @endif
                
                @if ($payments->nextPageUrl())
                    <a href="{{ $payments->nextPageUrl() }}" class="px-3 py-1 border border-gray-300 rounded-md text-sm hover:bg-gray-50">Next</a>
                @else
                    <button class="px-3 py-1 border border-gray-300 rounded-md text-sm opacity-50 cursor-not-allowed" disabled>Next</button>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Payment Verification Modal -->
<div id="paymentModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm hidden flex items-center justify-center z-[999]">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-2xl max-h-[90vh] overflow-y-auto">
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
                    <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-red-600 mx-auto"></div>
                    <p class="mt-2 text-gray-500">Loading payment details...</p>
                </div>
            </div>

                <!-- Action Buttons (hidden by default) -->
                <div id="actionButtons" class="mt-6">
                    <div class="flex justify-end space-x-3">
                        <button onclick="verifyPayment()" class="px-4 py-2 bg-green-600 text-white rounded-md text-sm font-medium hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                            Verify Payment
                        </button>
                        <button onclick="showRejectForm()" class="px-4 py-2 bg-red-600 text-white rounded-md text-sm font-medium hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                            Reject Payment
                        </button>
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

<!-- Toast Container -->
<div id="toastContainer" class="fixed top-4 right-4 space-y-2 z-50"></div>
@endsection

@section('content_js')
<script>
// Global variables
let currentPaymentId = null;
let eventSource = null;
let currentPage = {{ $payments->currentPage() }};
let totalPages = {{ $payments->lastPage() }};
let currentSearchTerm = "{{ request('search') }}";
let currentFilter = "{{ request('status') }}";

// DOM elements
const searchInput = document.getElementById('searchPayments');
const refreshBtn = document.getElementById('refreshBtn');

// Handle new payment notification
function handleNewPayment(payment) {
    fetch(`/admin/payments/${payment.id}/row`)
        .then(response => response.text())
        .then(html => {
            const wrapper = document.createElement('tbody');
            wrapper.innerHTML = html;
            const newRow = wrapper.firstElementChild;
            
            newRow.classList.add('bg-red-100', 'animate-pulse');
            setTimeout(() => newRow.classList.remove('bg-red-100', 'animate-pulse'), 5000);
            
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

// Filter payments
function filterPayments(status) {
    const url = new URL(window.location.href);
    
    if (status) {
        url.searchParams.set('status', status);
    } else {
        url.searchParams.delete('status');
    }
    
    // Keep search term if it exists
    if (currentSearchTerm) {
        url.searchParams.set('search', currentSearchTerm);
    }
    
    window.location.href = url.toString();
}

// Search payments with debounce
let searchTimer;
function searchPayments(query) {
    clearTimeout(searchTimer);
    currentSearchTerm = query.trim();
    
    if (query.length < 2 && query.length > 0) return;
    
    searchTimer = setTimeout(() => {
        const url = new URL(window.location.href);
        
        if (currentSearchTerm) {
            url.searchParams.set('search', currentSearchTerm);
        } else {
            url.searchParams.delete('search');
        }
        
        // Keep status filter if it exists
        if (currentFilter) {
            url.searchParams.set('status', currentFilter);
        }
        
        window.location.href = url.toString();
    }, 500);
}

// Refresh payments
function refreshPayments() {
    // Show loading state
    const loadingRow = document.getElementById('loadingRow');
    const tableBody = document.getElementById('paymentsTableBody');
    
    // Hide all rows except loading
    Array.from(tableBody.children).forEach(row => {
        if (row.id !== 'loadingRow') {
            row.classList.add('hidden');
        }
    });
    loadingRow.classList.remove('hidden');
    
    // Build URL with current filters/search
    const url = new URL(window.location.href);
    if (currentFilter) url.searchParams.set('status', currentFilter);
    if (currentSearchTerm) url.searchParams.set('search', currentSearchTerm);
    
    // Fetch updated data
    fetch(url)
        .then(response => response.text())
        .then(html => {
            // Parse the response
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');
            
            // Replace the table body
            const newTableBody = doc.getElementById('paymentsTableBody');
            if (newTableBody) {
                tableBody.innerHTML = newTableBody.innerHTML;
            }
            
            // Update pagination info
            const showingCount = doc.getElementById('showingCount')?.textContent;
            const showingCountEnd = doc.getElementById('showingCountEnd')?.textContent;
            const totalCount = doc.getElementById('totalCount')?.textContent;
            
            if (showingCount) document.getElementById('showingCount').textContent = showingCount;
            if (showingCountEnd) document.getElementById('showingCountEnd').textContent = showingCountEnd;
            if (totalCount) document.getElementById('totalCount').textContent = totalCount;
        })
        .catch(error => {
            console.error('Refresh failed:', error);
            showToast('Failed to refresh payments', 'error');
        })
        .finally(() => {
            loadingRow.classList.add('hidden');
        });
}

// Update counts display
function updateCounts(change = 0) {
    const showingCount = document.getElementById('showingCount');
    const showingCountEnd = document.getElementById('showingCountEnd');
    const totalCount = document.getElementById('totalCount');
    
    if (change !== 0) {
        const currentTotal = parseInt(totalCount.textContent) || 0;
        totalCount.textContent = currentTotal + change;
    }
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
            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-red-600 mx-auto"></div>
            <p class="mt-2 text-gray-500">Loading payment details...</p>
        </div>
    `;

    rejectForm.classList.add('hidden');
    actionButtons.classList.add('hidden');
    
    fetch(`/admin/payments/${paymentId}/details`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                content.innerHTML = data.html;
                if (data.payment.status === 'Pending') {
                    actionButtons.classList.add('hidden');
                }else if(data.payment.status === 'under_verification'){
                    actionButtons.classList.remove('hidden');
                }else if(data.payment.status === 'Pending'){
                    actionButtons.classList.add('hidden');
                }else if(data.payment.status === 'Verified'){
                    actionButtons.classList.add('hidden');
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
    document.body.style.overflow = 'hidden'; 
}

// Close modal
function closeModal() {
    document.getElementById('paymentModal').classList.add('hidden');
    document.body.style.overflow = 'auto';
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
    const button = document.querySelector('#actionButtons button:first-child');
    const originalText = button.innerHTML;
    button.innerHTML = '<span class="animate-pulse">Verifying...</span>';
    button.disabled = true;

    // Get the amount from the modal (you'll need to add this field to your modal)
    const amountPaid = document.getElementById('amountPaid').value;

    fetch(`/payments/${currentPaymentId}/verify-with-receipt`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            amount_paid: amountPaid
        })
    })
    .then(response => {
        if (!response.ok) {
            return response.json().then(err => { throw new Error(err.message || 'Verification failed'); });
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            showToast('Payment verified successfully', 'success');
            updatePaymentRow(data.payment);
            closeModal();
        } else {
            throw new Error(data.message || 'Verification failed');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast(error.message, 'error');
    })
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
        fetch(`/payments/${payment.id}/row`)
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

// Initialize event listeners
function initEventListeners() {
    // Filter buttons
    document.getElementById('filterUnderverification').addEventListener('click', () => filterPayments('under_verification'));
    document.getElementById('filterPending').addEventListener('click', () => filterPayments('Pending'));
    document.getElementById('filterVerified').addEventListener('click', () => filterPayments('verified'));
    document.getElementById('filterRejected').addEventListener('click', () => filterPayments('rejected'));
    document.getElementById('filterAll').addEventListener('click', () => filterPayments(''));
    
    // Search input
    searchInput.addEventListener('input', (e) => {
        searchPayments(e.target.value);
    });
    
    // Refresh button
    refreshBtn.addEventListener('click', refreshPayments);
}

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    initEventListeners();
    requestNotificationPermission();
    
});
</script>
@endsection