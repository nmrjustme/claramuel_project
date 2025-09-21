<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Facility;
use Illuminate\Support\Facades\Validator;
use App\Models\BookingGuestDetails;
use Illuminate\Support\Facades\DB;

class DayTourController extends Controller
{
    public function index()
    {
        try {
            $cottages = Facility::cottages()->active()->get();
            return response()->json([
                'success' => true,
                'data' => $cottages
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve cottages',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getCottages()
    {
        $cottages = Facility::where('category', 'Cottage')
            ->where('status', 'Active')
            ->get(['id', 'name', 'price', 'quantity', 'description']); 
        
        return response()->json($cottages);
    }

    public function register(Request $request)
    {   
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
            'tour_type' => 'required|in:pool,park,both',
            'pool_adults' => 'required_if:tour_type,pool,both|integer|min:0',
            'pool_kids' => 'required_if:tour_type,pool,both|integer|min:0',
            'pool_seniors' => 'required_if:tour_type,pool,both|integer|min:0',
            'park_adults' => 'required_if:tour_type,park,both|integer|min:0',
            'park_kids' => 'required_if:tour_type,park,both|integer|min:0',
            'park_seniors' => 'required_if:tour_type,park,both|integer|min:0',
            'cottage_ids' => 'nullable|array',
            'cottage_ids.*' => 'exists:cottages,id',
            'total_amount' => 'required|numeric|min:0'
        ]);
    }

    public function checkAvailability(Request $request)
{
    $date = $request->query('date');
    
    // Get all facilities
    $facilities = Facility::all();

    // Get booked quantities for that date
    $booked = BookingGuestDetails::whereHas('dayTourLog', function($q) use ($date) {
        $q->where('date_tour', $date)
          ->whereIn('status', ['pending', 'paid', 'approved']); // consider these as booked
    })->select('facility_id', DB::raw('SUM(facility_quantity) as total_booked'))
      ->groupBy('facility_id')
      ->pluck('total_booked', 'facility_id')
      ->toArray();

    $availability = $facilities->map(function($facility) use ($booked) {
        $used = $booked[$facility->id] ?? 0;
        return [
            'id' => $facility->id,
            'available' => max($facility->quantity - $used, 0),
        ];
    });

    return response()->json($availability);
}

}
