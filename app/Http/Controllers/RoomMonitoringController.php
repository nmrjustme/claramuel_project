<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Facility;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class RoomMonitoringController extends Controller
{
    public function index()
    {
        return view('admin.monitoring.index');
    }
    
    public function data()
    {
        $facilities = Facility::where('type', 'room')->get();
        $unavailableDates = $this->getUnavailableToday();

        return response()->json([
            'facilities' => $facilities,
            'unavailableDates' => $unavailableDates,
            'today' => now()->format('Y-m-d')
        ]);
    }
    
    protected function getUnavailableToday()
    {
        $today = now()->format('Y-m-d'); // Current date in app timezone

        $dates = DB::table('facilities as fac')
            ->join('facility_summary as fac_sum', 'fac_sum.facility_id', '=', 'fac.id')
            ->join('facility_booking_details as fac_details', 'fac_details.facility_summary_id', '=', 'fac_sum.id')
            ->join('facility_booking_log as fac_log', 'fac_log.id', '=', 'fac_details.facility_booking_log_id')
            ->join('payments', 'payments.facility_log_id', '=', 'fac_log.id')
            ->where('fac_log.status', '!=', 'pending_confirmation')
            ->where('payments.status', 'verified')
            ->whereDate('fac_details.checkin_date', '<=', $today)   // started on or before today
            ->whereDate('fac_details.checkout_date', '>=', $today)  // still ongoing today
            ->select([
                'fac.id as facility_id',
                'fac_details.checkin_date',
                'fac_details.checkout_date'
            ])
            ->get()
            ->groupBy('facility_id')
            ->map(function ($dates) {
                return $dates->map(function ($date) {
                    return [
                        'checkin_date' => Carbon::parse($date->checkin_date)
                            ->setTimezone(config('app.timezone'))
                            ->format('Y-m-d'),
                        'checkout_date' => Carbon::parse($date->checkout_date)
                            ->setTimezone(config('app.timezone'))
                            ->format('Y-m-d')
                    ];
                });
            });

        logger('Unavailable Today:', $dates->toArray());
        return $dates;
    }

}
