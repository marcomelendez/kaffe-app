<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class RoomRateController extends Controller
{
    public function index()
    {
        return view('rates');
    }
}
