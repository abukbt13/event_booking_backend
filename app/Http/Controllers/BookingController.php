<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class BookingController extends Controller
{
    public function BookVenue(Request $request){
        $validator = Validator::make($data, [
            'venue' => 'required',
            'time_start' => 'required',
            'time_end' => 'required',
            'date' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'failed',
                'message' => 'Enter correct data',
                'errors' => $validator->errors()
            ]);
        }
        $booking = new Booking();
        $booking->user_id = Auth::id();
        $booking->venue = $data['venue'];
        $booking->time_start = $data['time_start'];
        $booking->time_end = $data['time_end'];
        $booking->date = $data['date'];
        $booking->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Booked successfully!',
            'booking' => $booking
        ]);
    }
}
