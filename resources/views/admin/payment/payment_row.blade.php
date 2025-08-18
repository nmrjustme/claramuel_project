<tr data-id="{{ $payment->id }}" class="{{ $payment->is_read ? '' : 'bg-red-100 highlight-new' }} {{ $payment->status == 'Pending' ? 'opacity-50' : '' }}">
    <td class="px-6 py-4 whitespace-nowrap">
        <div class="text-sm font-medium text-gray-900">
            ₱{{ number_format($payment->amount, 2) }}
        </div>
    </td>
    <td class="px-6 py-4">
        <div class="text-sm font-medium text-gray-900">
            {{ $payment->bookingLog->user->firstname }} {{ $payment->bookingLog->user->lastname }}
        </div>
        <div class="text-sm text-gray-500">
            {{ $payment->bookingLog->user->email }}
        </div>
    </td>
    <td class="px-6 py-4 whitespace-nowrap">
        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $payment->is_read ? 'bg-green-100 text-green-800' : 'bg-red-200 text-red-800' }}">
            {{ $payment->is_read ? 'Read' : 'Unread' }}
        </span>
    </td>
    <td class="px-6 py-4">
        <div class="text-sm font-medium text-gray-900">
            {{ $payment->gcash_number ?? 'No GCash number provided yet' }}
        </div>
        <div class="text-sm text-gray-500">
            {{ $payment->reference_no ?? 'No reference' }}
        </div>
    </td>
    <td class="px-6 py-4 whitespace-nowrap">
        @if($payment->status == 'N/A')
            <span class="px-2 py-1 rounded-full text-xs bg-gray-600 text-white">N/A</span>
        @elseif($payment->status == 'advance_paid')
            <span class="px-2 py-1 rounded-full text-xs bg-green-600 text-white">50% Paid</span>
        @elseif($payment->status == 'fully_paid')
            <span class="px-2 py-1 rounded-full text-xs bg-blue-600 text-white">Fully Paid</span>
        @elseif($payment->status == 'Pending')
            <div class="flex flex-col items-start space-y-1">
                <span class="px-2 py-1 rounded-full text-xs bg-yellow-600 text-white">Pending</span>
                <span class="text-xs text-gray-500">Awaiting Payment</span>
            </div>
        @elseif($payment->status == 'verified')
            <div class="flex flex-col items-start space-y-1">
                <span class="px-2 py-1 rounded-full text-xs bg-green-600 text-white">Verified</span>
                <span class="text-xs text-gray-500">₱{{ number_format($payment->amount_paid) }}</span>
            </div>
        @elseif($payment->status == 'not_paid')
            <span class="px-2 py-1 rounded-full text-xs bg-yellow-600 text-white">Not Paid</span>
            @if($payment->verified_at)
                <div class="text-xs text-gray-500 mt-1">
                    {{ $payment->verified_at->format('M d, Y') }}
                </div>
            @endif
        @elseif($payment->status == 'under_verification')
            <span class="px-2 py-1 rounded-full text-xs bg-red-600 text-white">Under Verification</span>
        @elseif($payment->status == 'rejected')
            <span class="px-2 py-1 rounded-full text-xs bg-red-100 text-red-800">Rejected</span>
            @if($payment->rejection_reason)
                <div class="text-xs text-gray-500 mt-1 truncate max-w-xs" title="{{ $payment->rejection_reason }}">
                    {{ Str::limit($payment->rejection_reason, 30) }}
                </div>
            @endif
        @endif
    </td>
    <td class="px-6 py-4 whitespace-nowrap">
        @if($payment->status != 'Pending')
            <button onclick="viewPayment('{{ $payment->id }}')" class="text-blue-600 hover:text-blue-900">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline" viewBox="0 0 20 20" fill="currentColor">
                    <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
                    <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd" />
                </svg>
                View
            </button>
        @else
            <span class="text-gray-400">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline" viewBox="0 0 20 20" fill="currentColor">
                    <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
                    <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd" />
                </svg>
                View
            </span>
        @endif
    </td>
</tr>