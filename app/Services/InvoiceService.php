<?php

namespace App\Services;

use App\Models\FacilityBookingLog;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\View;

class InvoiceService
{
    public function generateInvoice(FacilityBookingLog $booking)
    {
        // Calculate totals
        $totals = $this->calculateTotals($booking);

        $data = [
            'booking' => $booking,
            'totals' => $totals,
            'invoiceNumber' => 'CLM-' . date('Y-m') . '-' . str_pad($booking->id, 3, '0', STR_PAD_LEFT),
            'issueDate' => now()->format('F j, Y'),
            'dueDate' => now()->format('F j, Y'),
        ];

        // Generate PDF
        $pdf = Pdf::loadHTML($this->generateInvoiceHTML($data));
        
        return $pdf;
    }

    private function calculateTotals($booking)
    {
        $facilitiesTotal = 0;
        $breakfastTotal = 0;
        $addonsTotal = 0;

        // Calculate facilities total
        foreach ($booking->summaries as $summary) {
            $nights = \Carbon\Carbon::parse($summary->bookingDetails->first()->checkin_date)
                ->diffInDays($summary->bookingDetails->first()->checkout_date);
            
            $facilitySubtotal = $summary->facility_price * $nights;
            $facilitiesTotal += $facilitySubtotal;

            // Calculate breakfast cost if included
            if ($summary->breakfast) {
                $breakfastTotal += $summary->breakfast_price * $nights;
            }
        }

        // Calculate addons total
        if ($booking->guestAddons && $booking->guestAddons->count() > 0) {
            $addonsTotal = $booking->guestAddons->sum('total_cost');
        }

        $subtotal = $facilitiesTotal + $breakfastTotal + $addonsTotal;
        $taxes = 0; // Adjust if you have taxes
        $totalAmount = $subtotal + $taxes;

        $advancePaid = $booking->payments->sum('amount');
        $balance = $totalAmount - $advancePaid;

        return [
            'facilitiesTotal' => $facilitiesTotal,
            'breakfastTotal' => $breakfastTotal,
            'addonsTotal' => $addonsTotal,
            'subtotal' => $subtotal,
            'taxes' => $taxes,
            'totalAmount' => $totalAmount,
            'advancePaid' => $advancePaid,
            'balance' => $balance,
        ];
    }

    private function generateInvoiceHTML($data)
    {
        return View::make('pdf.invoice', $data)->render();
    }
}