<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class VerifyEmailController extends Controller
{
    public function verify ()
    {
        return view('customer_pages.verify_email');
    }
}
