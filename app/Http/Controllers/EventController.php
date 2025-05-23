<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class EventController extends Controller
{
    public function createEvent(Request $request)
    {
        $data = $request->all();

        // Validation rules
        $validator = Validator::make($data, [
           'title' => 'required',
            'description' => 'required',
            'event_date' => 'required|date',
            'capacity' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'failed',
                'message' => 'Enter correct data',
                'errors' => $validator->errors()
            ]);
        }

        // Check if event already exists
//        $existingEvent = Event::where('title', $data['title'])
//            ->where('event_date', $data['event_date'])
//            ->where('user_id', Auth::id()) // Ensure it's checked per user
//            ->first();
////
//        if ($existingEvent) {
//            return response()->json([
//                'status' => 'failed',
//                'message' => 'This event already exists!'
//            ]);
//        }

        // Save the new event
        $event = new Event();
        $event->user_id = Auth::id();
        $event->title = $data['title'];
        $event->description = $data['description'];
        $event->event_date = $data['event_date'];
        $event->capacity = $data['capacity'];
        $event->save();

        $body = "Thank you for creating your event <b>" . $event->title . "</b>, which is scheduled for " . $event->event_date . ". I am reminding you to keep the date and time.";
        sendNotification("+254" . Auth::user()->phone, $body);
        return response()->json([
            'status' => 'success',
            'message' => 'Event created successfully!',
        ],201);
    }
    public function BookVenue(Request $request,$id)
    {
        $data = $request->all();
        // Validation rules
        $validator = Validator::make($data, [
            'start_time' => 'required',
            'event_date' => 'required',
            'end_time' => 'required',
            'capacity' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'failed',
                'message' => 'Enter correct data',
                'errors' => $validator->errors()
            ]);
        }


        // Save the new event
        $conflict = Booking::where('date', $data['event_date'])
            ->where('venue_id', $data['venue_id'])
            ->where(function ($query) use ($data) {
                $query->where('start_time', '<', $data['end_time'])
                    ->where('end_time', '>', $data['start_time']);
            })
            ->first(); // get the first conflicting booking

        if ($conflict) {
            return response()->json([
                'status' => 'error',
                'message' => 'This venue is already booked on this day from ' . $conflict->start_time . ' to ' . $conflict->end_time
            ]);
        }

        // Else, go ahead and save the booking



        $booking = new Booking();
        $booking->user_id = Auth::id();
        $booking->venue_id = $data['venue_id'];
        $booking->event_id = $id;
        $booking->start_time = $data['start_time'];
        $booking->end_time = $data['end_time'];
        $booking->date = $data['event_date'];
        $booking->price_per_hour = $data['price_per_hour'];
        $booking->total_price = $data['total_price'];
        $booking->capacity = $data['capacity'];
        $booking->save();

        $body = "Your booking was made successfully. Please remember the date and time: " .
            $booking->date . ", from " . $booking->start_time . " to " . $booking->end_time . ".";

        sendNotification("+254" . Auth::user()->phone, $body);



        if ($booking) {
            $event = Event::findOrFail($id);
            $event->booked = '1';
            $event->update();

            return response()->json([
                'status' => 'success',
                'message' => 'Booking created successfully!',
                'booking' =>$booking,
            ]);
        }

    }

    public function showEvents(){
        $events = Event::where('user_id', Auth::id())->get();
        return response()->json([
            'status' => 'success',
            'events' => $events
        ]);

    }

    public function deleteEvent($id)
    {
        // Find the event by user_id and id
        $event = Event::where('user_id', Auth::id())->where('id', $id)->first();

        // Check if the event exists
        if ($event) {
            // Delete the event
            $event->delete();

            // Return success response
            return response()->json([
                'status' => 'success',
                'message' => 'Event deleted successfully.',
            ]);
        }

        // Return error response if event not found
        return response()->json([
            'status' => 'error',
            'message' => 'Event not found or unauthorized.',
        ], 404);
    }
    public function DeleteBooking($id)
    {
//        dd(Auth::user());
        // Find the event by user_id and id
        $book = Booking::where('user_id', Auth::id())->where('status','pending')->where('id', $id)->first();
//            dd($book);
        // Check if the event exists
        if ($book) {
            // Delete the event
            $book->delete();
            $event =Event::where('user_id', Auth::id())->where('id', $book->event_id)->first();
            $event->booked = 0;
            if ($event) {
                $event->update();
            }
            // Return success response
            return response()->json([
                'status' => 'success',
                'message' => 'Booking deleted successfully.',
            ]);
        }

        // Return error response if event not found
        return response()->json([
            'status' => 'error',
            'message' => 'Event not found or unauthorized.',
        ], 404);
    }
    public function UpdateEvent(Request $request,$id)
    {
        $data = $request->all();
        // Find the event by user_id and id
        $event = Event::where('user_id', Auth::id())->where('id', $id)->first();

        // Check if the event exists
        if ($event) {
            // Delete the event
            $event->title = $data['title'];
            $event->description = $data['description'];
            $event->event_date = $data['event_date'];
            $event->capacity = $data['capacity'];
            $event->Update();

            // Return success response
            return response()->json([
                'status' => 'success',
                'message' => 'Event updated successfully.',
            ]);
        }

        // Return error response if event not found
        return response()->json([
            'status' => 'error',
            'message' => 'Event not found or unauthorized.',
        ], 404);
    }

}
