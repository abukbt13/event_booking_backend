<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Event;
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
    public function ShowSingleEvent($event_id)
    {
        $event= Booking::where('user_id', Auth::id())
                ->where('event_id', $event_id)
                ->where('status', 'pending')
                ->get();
//            ->where('events.id', $event_id)
//            ->join('bookings', 'bookings.event_id', '=', 'bookings.id')
//            ->join('venues', 'venues.id', '=', 'bookings.venue_id')
//            ->select('events.*','bookings.status','bookings.start_time','bookings.end_time','bookings.total_price','bookings.date','bookings.price_per_hour','bookings.id as booking_id', 'events.id', 'venues.venue')
//            ->first();

//        dd($booking);
        if (!$event) {
            return response()->json([
                'status' => 'error',
                'message' => 'Booking not found'
            ]);
        }

        return response()->json([
            'status' => 'success',
            'events' => $event
        ]);
    }
    public function ShowSingleBooking($event_id)
    {
        $booking = Booking::where('bookings.user_id', Auth::id())
            ->where('bookings.event_id', $event_id)
            ->where('bookings.status', 'pending')
            ->join('venues', 'bookings.venue_id', '=', 'venues.id')
            ->select('bookings.*', 'venues.venue','venues.picture')
            ->first();

//        dd($booking);
        if (!$booking) {
            return response()->json([
                'status' => 'error',
                'message' => 'Booking not found'
            ]);
        }

        return response()->json([
            'status' => 'success',
            'booking' => $booking
        ]);
    }
    public function ShowBookingDetails($booking_id)
    {
        $booking = Booking::where('bookings.user_id', Auth::id())
            ->where('bookings.id', $booking_id)
            ->where('bookings.status', 'pending')
            ->join('venues', 'bookings.venue_id', '=', 'venues.id')
            ->select('bookings.*', 'venues.venue','venues.picture')
            ->first();

//        dd($booking);
        if (!$booking) {
            return response()->json([
                'status' => 'error',
                'message' => 'Booking not found'
            ]);
        }

        return response()->json([
            'status' => 'success',
            'booking' => $booking
        ]);
    }
    public function CompleteCheckout(Request $request,$id)
    {
//        dd($request->all());
//        $amount =$request->total_price;
//        $user = Auth::user();
//        $mpesa = new MpesaRepository();
//        $mpesa->C2BMpesaApi($id,$user->phone,$amount);
        $data = $request->all();

        // Find the event by user_id and id
//        dd($id);
//        code for updating payment if success
        $book = Booking::findOrFail($id);
        // Check if the event exists
        if ($book) {
            $book->start_time = $data['start_time'];
            $book->end_time = $data['end_time'];
            $book->total_price = $data['total_price'];
            $book->date = $data['date'];
            $book->payment_status = 1;
            $book->Update();

            // Return success response
            return response()->json([
                'status' => 'success',
                'message' => 'Booked updated successfully.',
            ]);
        }

//         Return error response if event not found
        return response()->json([
            'status' => 'error',
            'message' => 'Event not found or unauthorized.',
        ], 404);
    }

}
