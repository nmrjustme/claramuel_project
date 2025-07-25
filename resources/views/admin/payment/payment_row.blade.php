<tr data-id="{{ $payment->id }}" class="{{ $payment->is_read ? '' : 'bg-blue-50 highlight-new' }}">
    <td class="px-6 py-4">
        <div class="text-sm font-medium text-gray-900">
            {{ $payment->bookingLog->reference }}
            @if($payment->method === 'gcash' && $payment->bookingLog->reference)
                <div class="text-xs text-gray-500 mt-1">
                    GCash Ref: {{ $payment->bookingLog->reference }}
                </div>
            @endif
        </div>
        <div class="text-sm text-gray-500 mt-1">
            ₱{{ number_format($payment->amount, 2) }} • 
            {{ $payment->payment_date ? \Carbon\Carbon::parse($payment->payment_date)->format('M d, Y') : 'N/A' }}
        </div>
    </td>
    <td class="px-6 py-4">
        <div class="text-sm text-gray-900">
            {{ $payment->bookingLog->user->firstname }} {{ $payment->bookingLog->user->lastname }}
        </div>
        <div class="text-sm text-gray-500">
            {{ $payment->bookingLog->user->email }}
        </div>
    </td>
    <td class="px-6 py-4">

            <div class="text-sm text-gray-900">
                {{ $payment->gcash_number ?? 'N/A' }}
            </div>
            <div class="text-sm text-gray-500">
                {{ $payment->reference_no ?? 'No reference' }}
            </div>

    </td>
    <td class="px-6 py-4">
        @if($payment->status == 'N/A')
            <span class="px-2 py-1 rounded-full text-xs bg-gray-600 text-white">N/A</span>
        @elseif($payment->status == 'advance_paid')
            <span class="px-2 py-1 rounded-full text-xs bg-green-600 text-white">50% Paid</span>
        @elseif($payment->status == 'paid')
            <span class="px-2 py-1 rounded-full text-xs bg-green-600 text-white">Paid</span>
        @elseif($payment->status == 'verified')
            <span class="px-2 py-1 rounded-full text-xs bg-green-600 text-white">Verified</span>
        @elseif($payment->status == 'not_paid')
            <span class="px-2 py-1 rounded-full bg-yellow-600 text-white">Not Paid</span>
            @if($payment->verified_at)
                <div class="text-xs text-gray-500 mt-1">
                    {{ $payment->verified_at->format('M d, Y') }}
                </div>
            @endif
        @elseif($payment->status == 'under_verification')
            <span class="px-2 py-1 rounded-full bg-red-600 text-white">Under Verification</span>
        @elseif($payment->status == 'rejected')
            <span class="px-2 py-1 rounded-full text-xs bg-red-100 text-red-800">Rejected</span>
            @if($payment->rejection_reason)
                <div class="text-xs text-gray-500 mt-1 truncate max-w-xs" title="{{ $payment->rejection_reason }}">
                    {{ Str::limit($payment->rejection_reason, 30) }}
                </div>
            @endif
        @endif
    </td>
    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
        <button onclick="viewPayment('{{ $payment->id }}')" class="text-blue-600 hover:text-blue-900 mr-3">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline" viewBox="0 0 20 20" fill="currentColor">
                <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
                <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd" />
            </svg>
            View
        </button>
    </td>
</tr>