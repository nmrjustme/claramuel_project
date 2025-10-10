<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\GuestType;

class GuestTypeController extends Controller
{
    public function index()
    {
        try {
            $guestTypes = GuestType::select('id', 'type', 'rate')->get();
            
            return response()->json([
                'success' => true,
                'data' => $guestTypes
            ]);
            
        } catch (\Exception $e) {
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve guest types',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
