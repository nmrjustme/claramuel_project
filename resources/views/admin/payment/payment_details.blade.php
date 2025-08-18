<div class="space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                <div class="bg-gray-50 p-4 rounded-lg">
                        <h4 class="text-sm font-medium text-gray-500">Payment Information</h4>
                        <dl class="mt-2 space-y-2">
                                <div class="flex justify-between">
                                        <dt class="text-sm text-gray-500">Reference Number</dt>
                                        <dd class="text-sm font-medium text-gray-900">{{ $payment->reference_no }}</dd>
                                </div>
                                <div class="flex justify-between">
                                        <dt class="text-sm text-gray-500">GCash Number</dt>
                                        <dd class="text-sm font-medium text-gray-900">{{ $payment->gcash_number }}</dd>
                                </div>
                                <div class="flex justify-between">
                                        <dt class="text-sm text-gray-500">Date</dt>
                                        <dd class="text-sm text-gray-900">
                                                {{ $payment->payment_date ? \Carbon\Carbon::parse($payment->payment_date)->format('M d, Y h:i A') : 'N/A' }}
                                        </dd>
                                </div>
                                <div class="flex justify-between">
                                        <dt class="text-sm text-gray-500">Amount</dt>
                                        <dd class="text-sm font-medium text-red-900">
                                                ₱{{ number_format($payment->amount, 2) }}</dd>
                                </div>
                                <!-- Amount Paid Input Field -->
                                <div class="flex justify-between items-center">
                                        <dt class="text-sm text-gray-500">Amount Paid</dt>
                                        <dd>
                                                <input type="number" step="0.01" name="amount_paid" id="amountPaid"
                                                        class="form-input border border-gray-300 rounded-md px-2 py-1 text-sm w-32"
                                                        placeholder="₱0.00" value="{{ old('amount_paid') }}">
                                        </dd>
                                </div>
                        </dl>
                </div>

                

                <div class="bg-gray-50 p-4 rounded-lg">
                        <h4 class="text-sm font-medium text-gray-500">Customer Information</h4>
                        <dl class="mt-2 space-y-2">
                                <div class="flex justify-between">
                                        <dt class="text-sm text-gray-500">Name</dt>
                                        <dd class="text-sm font-medium text-gray-900">
                                                {{ $payment->bookingLog->user->firstname }}
                                                {{ $payment->bookingLog->user->lastname }}
                                        </dd>
                                </div>
                                <div class="flex justify-between">
                                        <dt class="text-sm text-gray-500">Email</dt>
                                        <dd class="text-sm text-gray-900">{{ $payment->bookingLog->user->email }}</dd>
                                </div>
                                <div class="flex justify-between">
                                        <dt class="text-sm text-gray-500">Phone</dt>
                                        <dd class="text-sm text-gray-900">
                                                {{ $payment->bookingLog->user->phone ?? 'N/A' }}</dd>
                                </div>
                        </dl>
                </div>
        </div>

        @if ($payment->method === 'gcash')
                <div class="bg-blue-50 p-4 rounded-lg border border-blue-100">
                        <h4 class="text-sm font-medium text-blue-800">GCash Payment Details</h4>
                        <dl class="mt-2 space-y-2">
                                <div class="flex justify-between">
                                        <dt class="text-sm text-blue-700">GCash Number</dt>
                                        <dd class="text-sm font-medium text-blue-900" data-gcash-number>
                                                {{ $payment->gcash_number ?? 'N/A' }}
                                        </dd>
                                </div>
                                <div class="flex justify-between">
                                        <dt class="text-sm text-blue-700">Reference Number</dt>
                                        <dd class="text-sm font-medium text-blue-900" data-gcash-reference>
                                                {{ $payment->reference_no ?? 'Not provided' }}
                                        </dd>
                                </div>
                        </dl>
                </div>
        @endif

        @if ($payment->receipt_path)
                <div class="bg-white p-4 rounded-lg border border-gray-200">
                        <h4 class="text-sm font-medium text-gray-700 mb-2">Payment Receipt</h4>
                        <div class="mt-2 flex justify-center">
                                <!-- Container with hover effect -->
                                <div class="relative group transition-all duration-300">
                                        <img src="{{ url($payment->receipt_path) }}" alt="Payment receipt"
                                                class="max-w-full h-auto rounded-lg border border-gray-300 cursor-pointer 
                           transition-transform duration-500 ease-in-out 
                           transform origin-center hover:scale-[3] hover:z-50"
                                                style="max-height: 250px;"
                                                onclick="window.open('{{ url($payment->receipt_path) }}', '_blank')">
                                </div>
                        </div>
                </div>
        @endif

        @if ($payment->status == 'verified')
                <div class="bg-green-50 p-4 rounded-lg border border-green-100">
                        <div class="flex items-center">
                                <svg class="h-5 w-5 text-green-400" xmlns="http://www.w3.org/2000/svg"
                                        viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd"
                                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                                clip-rule="evenodd" />
                                </svg>
                                <div class="ml-3">
                                        <h3 class="text-sm font-medium text-green-800">Verified Payment</h3>
                                        <div class="mt-1 text-sm text-green-700">
                                                <p>Verified by:
                                                        {{ $payment->verifiedBy?->firstname ?? 'System Admin' }}
                                                </p>
                                                <p>Verified at: {{ $payment->verified_at->format('M d, Y h:i A') }}</p>
                                        </div>
                                </div>
                        </div>
                </div>
        @endif

        @if ($payment->status == 'rejected')
                <div class="bg-red-50 p-4 rounded-lg border border-red-100">
                        <div class="flex">
                                <svg class="h-5 w-5 text-red-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                                        fill="currentColor">
                                        <path fill-rule="evenodd"
                                                d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                                clip-rule="evenodd" />
                                </svg>
                                <div class="ml-3">
                                        <h3 class="text-sm font-medium text-red-800">Rejected Payment</h3>
                                        <div class="mt-1 text-sm text-red-700">
                                                <p><span class="font-medium">Reason:</span>
                                                        {{ $payment->rejection_reason }}</p>
                                                @if (strpos($payment->rejection_reason, 'GCash reference mismatch') !== false)
                                                        <p class="mt-1">The GCash reference number provided did not
                                                                match our records.</p>
                                                @endif
                                        </div>
                                </div>
                        </div>
                </div>
        @endif
</div>
