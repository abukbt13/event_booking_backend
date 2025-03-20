<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Venue;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class VenueController extends Controller
{
    public function createVenue(Request $request)
    {
        $data = $request->all();
        // Validation rules
        $validator = Validator::make($data, [
            'venue' => 'required|unique:venues',
            'location' => 'required',
            'capacity' => 'required|integer',
            'description' => 'required',
            'amenities' => 'required',
            'price_per_hour' => 'required|numeric',
            'contact_email' => 'required|email',
            'contact_phone' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'failed',
                'message' => 'Enter correct data',
                'errors' => $validator->errors()
            ], 422);
        }

        // Create venue
        $venue = new Venue();
        $venue->user_id = Auth::id();
        $venue->venue = $data['venue'];
        $venue->location = $data['location'];
        $venue->capacity = $data['capacity'];
        $venue->description = $data['description'];
        $venue->amenities = $data['amenities'];
        $venue->price_per_hour = $data['price_per_hour'];
        $venue->contact_email = $data['contact_email'];
        $venue->contact_phone = $data['contact_phone'];

        // Handle image upload
        if ($request->hasFile('picture')) {
            $file = $request->file('picture');
            $filename = time() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('Events', $filename, 'public'); // Correct way to store the file

            $venue->picture = $path; // Save the path to the database
        }

        $venue->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Venue created successfully!',
            'venue' => $venue
        ]);
    }
    public function showVenues(){
        $venues = Venue::all();
        return response([
            'status' => 'success',
            'venues' => $venues
        ]);
    }
    public function showBookings(){
        $bookings = Booking::all();
        return response([
            'status' => 'success',
            'bookings' => $bookings
        ]);
    }

}
