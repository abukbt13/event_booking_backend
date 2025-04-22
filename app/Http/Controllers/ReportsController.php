<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Booking;
use App\Models\Review;
use Illuminate\Http\Request;

class ReportsController extends Controller
{
    public function Reports()
    {

        return response()->json([
            'users' => User::count(),
            'active_bookings' => Booking::where('status', 'active')->count(),
            'reviews' => Review::count(),
        ]);
    }
    public function usersReports()
    {

        return response()->json([
            'users' => User::latest()->get()
        ]);
    }

}
