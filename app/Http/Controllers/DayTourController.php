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
}
