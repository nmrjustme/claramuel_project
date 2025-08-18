<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Facility;
use Illuminate\Support\Facades\Validator;

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
}
