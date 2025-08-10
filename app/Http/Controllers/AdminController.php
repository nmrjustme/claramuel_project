<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FacilityBookingLog;
use App\Events\InquiryRead;
use App\Events\AllInquiriesRead;
use Illuminate\Support\Facades\DB;
use App\Models\Facility;
use App\Models\FacilityBookingDetails;

class AdminController extends Controller
{
    public function index()
    {
        $inquirers = FacilityBookingLog::with([
                'user:id,firstname,lastname,phone,email',
                'bookingDetails.facilitySummary.facility',
                'bookingDetails.facilitySummary.breakfast',
            ])
            ->whereHas('user', function ($query) {
                $query->where('role', 'Customer');
            })
            ->orderBy('created_at', 'desc')
            ->get();
            
        $totalInquiries = $inquirers->count();  
        $newInquiriesCount = $inquirers->where('is_read', false)->count();
        
        $available_room = DB::table('facilities')
            ->join('image_fac', 'image_fac.fac_id', '=', 'facilities.id')
            ->where([
                ['status', '=', 'Active'],
                ['category', '=', 'Room']
            ])
            ->select('image_fac.image', 'facilities.name')
            ->get();
            
        $total_rooms = $available_room->count();
        
        return view('admin.index', [
            'inquirers' => $inquirers,
            'totalInquiries' => $totalInquiries,
            'newInquiriesCount' => $newInquiriesCount,
            'total_rooms' => $total_rooms,
            'available_room' => $available_room,
        ]);
    }
    
    public function inquiries(){
        $inquiries = FacilityBookingLog::with(['user', 'details', 'payments'])->get();
        return view('admin.Log.index', compact('inquiries'));
    }
    
    public function getBookings()
    {
        return view('admin.bookings.index');
    }
    
    public function calendar()
    {
        return view('admin.calendar.index');
    }
    
    private function BookingFacilityData()
    {
        return Facility::all(['id', 'name', 'category', 'price', 'description']);
    }
    
    public function getOccupiedFacilities()
    {
        $today = now()->toDateString();
        
        $occupiedFacilities = FacilityBookingDetails::with([
                'facilitySummary.facility.images', // Using direct facility relationship if available
                'bookingLog.user', 
                'facilitySummary.breakfast:id',
                'bookingLog.payments' // Check payments through bookingLog
            ])
            ->whereDate('checkin_date', '<=', $today)
            ->whereDate('checkout_date', '>=', $today)
            ->whereHas('bookingLog', function ($query) {
                    $query->where('status', 'confirmed')
                        ->whereNotNull('checked_in_at')
                        ->whereHas('payments', function ($q) {
                            $q->where('status', 'paid');
                        });
            })
            ->get()
            ->map(function ($detail) {
                return [
                    'name' => $detail->facility->name ?? ($detail->facilitySummary->facility->name ?? 'N/A'),
                    'user_name' => $detail->bookingLog->user->firstname . ' ' . $detail->bookingLog->user->lastname,
                    'has_breakfast' => !is_null($detail->facilitySummary->breakfast),
                    'image_url' => $detail->facilitySummary->facility->images->first() 
                        ? asset('imgs/facility_img/' . $detail->facilitySummary->facility->images->first()->image) 
                        : ($detail->facilitySummary->facility->images->first() 
                            ? asset('imgs/facility_img/' . $detail->facilitySummary->facility->images->first()->image)
                            : null),
                ];
            });
    
        return response()->json($occupiedFacilities);
    }
    
    public function getStats()
    {
        return response()->json([
            'pending' => FacilityBookingLog::where('status', 'pending_confirmation')->count(),
            
            'pending_payments' => FacilityBookingLog::whereHas('payments', function ($query) {
                $query->where('status', 'not_paid');
            })->count(),
            
        ]);
    }
    
}