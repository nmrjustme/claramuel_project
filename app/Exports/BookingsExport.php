<?php

namespace App\Exports;

use App\Models\FacilityBookingLog;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class BookingsExport implements FromCollection, WithHeadings, WithMapping
{
    protected $status;

    public function __construct($status)
    {
        $this->status = $status;
    }

    public function collection()
    {
        return FacilityBookingLog::with([
                'user', 
                'details', 
                'payments',
                'summaries.facility'
            ])
            ->whereHas('payments', function ($query) {
                $query->where('status', $this->status);
            })
            ->get();
    }

    public function headings(): array
    {
        return [
            'Status',
            'Guest Name',
            'Reservation Code',
            'Check-in Date',
            'Check-out Date',
            'Amount',
            'Payment Collected By',
            'Facilities Booked'
        ];
    }

    public function map($booking): array
    {
        $detail = $booking->details->first();
        $payment = $booking->payments->first();
        
        // Get facility names
        $facilities = $booking->summaries->map(function($summary) {
            return $summary->facility->name ?? 'Unknown Facility';
        })->implode(', ');

        return [
            $payment->status ?? 'N/A',
            $booking->user ? ($booking->user->firstname . ' ' . $booking->user->lastname) : 'N/A',
            $booking->id, // Using booking ID as reservation code
            $detail ? $detail->checkin_date->format('Y-m-d') : 'N/A',
            $detail ? $detail->checkout_date->format('Y-m-d') : 'N/A',
            $detail ? '₱' . number_format($detail->total_price, 2) : 'N/A',
            $payment ? ($payment->user->firstname ?? 'Payment Gateway') : 'N/A',
            $facilities
        ];
    }
}