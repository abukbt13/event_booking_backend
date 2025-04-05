<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Repositories\MpesaRepository;
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
    public function ShowBookings()
    {
        $bookings = Booking::where('user_id', Auth::id())->get();
        return response([
            'status' => 'success',
            'bookings' => $bookings
        ]);
    }
    public function ShowSingleBooking($id)
    {
        $booking = Booking::where('user_id', Auth::id())
            ->where('event_id', $id)
            ->first(); // Use first() if expecting a single record
//        dd($booking);
        if (!$booking) {
            return response()->json([
                'status' => 'error',
                'message' => 'Booking not found'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'booking' => $booking
        ]);
    }
    public function CompleteCheckout(Request $request,$id)
    {
        $user = Auth::user();
        $mpesa = new MpesaRepository();
        $mpesa->C2BMpesaApi($user->id,$id,$user->phone);
        $data = $request->all();

        // Find the event by user_id and id

//        code for updating payment if success
//        $book = Booking::where('user_id', Auth::id())->where('id', $id)->first();
//
//        // Check if the event exists
//        if ($book) {
//            $book->venue = $data['venue'];
//            $book->start_time = $data['start_time'];
//            $book->end_time = $data['end_time'];
//            $book->total_price = $data['total_price'];
//            $book->date = $data['date'];
//            $book->status = 'booked';
//            $book->Update();
//
//            // Return success response
//            return response()->json([
//                'status' => 'success',
//                'message' => 'Booked updated successfully.',
//            ]);
//        }

        // Return error response if event not found
//        return response()->json([
//            'status' => 'error',
//            'message' => 'Event not found or unauthorized.',
//        ], 404);
    }

}
