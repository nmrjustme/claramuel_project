<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AdminBookingsController extends Controller
{
    public function index()
    {
        return view('admin.book_manage.index');
    }
}
