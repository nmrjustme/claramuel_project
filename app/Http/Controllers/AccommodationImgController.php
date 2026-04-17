<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Facility;
use Illuminate\Support\Facades\DB;

class AccommodationImgController extends Controller
{
    public function index($id)
    {
        $img = Facility::with('images')->where('id', $id)->first();
        $facility_name = DB::table('facilities')->select('name')->where('id', $id)->first();

        return view('customer_pages.images', [
            'images' => $img ? $img->images : collect(),
            'name' => $facility_name, 
        ]);
    }
}
