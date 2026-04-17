<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class FacilityDiscountController extends Controller
{
    public function index($facilityId)
    {
        $discounts = Discount::where('facility_id', $facilityId)
            ->where('end_date', '>=', now())
            ->orderBy('end_date', 'asc')
            ->get();
    
        return response()->json([
            'success' => true,
            'discounts' => $discounts
        ]);
    }
    
    public function store(Request $request)
    {
        $validated = $request->validate([
            'facility_id' => 'required|exists:facilities,id',
            'discount_type' => 'required|in:percentage,fixed',
            'discount_value' => 'required|numeric|min:0',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date'
        ]);
    
        $discount = Discount::create($validated);
    
        return response()->json([
            'success' => true,
            'message' => 'Discount added successfully',
            'discount' => $discount
        ]);
    }
    
    public function destroy($id)
    {
        $discount = Discount::findOrFail($id);
        $discount->delete();
    
        return response()->json([
            'success' => true,
            'message' => 'Discount deleted successfully'
        ]);
    }
}
