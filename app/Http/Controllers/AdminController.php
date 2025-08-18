<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FacilityBookingLog;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\FacilityBookingDetails;
use Webklex\IMAP\Facades\Client;
use App\Models\User;
use App\Models\Payments;
use App\Events\UnreadCountsUpdated;

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
    
    public function inquiries()
    {
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
        $totalBookings = FacilityBookingLog::get()->count();
        $pendingConfirmation = FacilityBookingLog::where('status', 'pending_confirmation')->count();
        $under_verification_payments = FacilityBookingLog::whereHas('payments', function ($query) {
                $query->where('status', 'under_verification');
            })->count();

        $pending_payments = FacilityBookingLog::whereHas('payments', function ($query) {
                $query->where('status', 'Pending');
            })->count();
        
        return response()->json([
            'total_booking' => $totalBookings,
            'pending' => $pendingConfirmation,
            'under_verification_payments' => $under_verification_payments,
            'pending_payments' => $pending_payments
        ]);
    }
    
    public function getActiveAdmins()
    {
        $activeAdmins = User::where('role', 'Admin')
                            ->select('id', 'firstname', 'lastname', 'email', 'phone', 'profile_img', 'is_active')
                            ->orderBy('created_at', 'desc')
                            ->get()
                            ->map(function ($admin) {
                                return [
                                    'id' => $admin->id,
                                    'fullname' => $admin->firstname . ' ' . $admin->lastname,
                                    'email' => $admin->email,
                                    'phone' => $admin->phone,
                                    'profile_img' => $admin->profile_img,
                                    'is_active' => $admin->is_active
                                ];
                            });
        
        return response()->json($activeAdmins);
    }

    public function updateActiveHost(Request $request)
    {
        $request->validate([
            'is_active' => 'required|boolean'
        ]);

        // authenticated user with proper type hinting
        $user = Auth::user();
        
        if (!$user instanceof User) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid user instance'
            ], 401);
        }

        // Update using both methods for maximum compatibility
        $user->is_active = $request->is_active;
        $user->save();

        return response()->json([
            'success' => true,
            'is_active' => $user->is_active_host
        ]);
    }

    public function getAllUnreadCounts()
    {
        // Email Badge 
        $client = Client::account('default');
        $client->connect();
        $folder = $client->getFolder('INBOX');
        $unreadMessages = $folder->query()->unseen()->get();
        
        // Sidebar Badge
        $inquiriesCount = FacilityBookingLog::where('is_read', false)->count();
        $paymentCount = Payments::where('is_read', false)->count();
        
        $counts = [
            'emailBadgeCount' => $unreadMessages->count(),
            'inquiriesCount' => $inquiriesCount,
            'paymentCount' => $paymentCount // Fixed: Changed comma to =>
        ];
        
        // Broadcast the event
        event(new UnreadCountsUpdated($counts));
        
        return response()->json($counts);
    }
    
}