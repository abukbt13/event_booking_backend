<?php

namespace App\Http\Controllers;

use App\Models\Venue;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class VenueController extends Controller
{
    public function createVenue(Request $request)
    {
        $data = $request->all();

        // Validation rules
        $validator = Validator::make($data, [
            'venue' => 'unique:venues|required',
            'location' => 'required',
            'capacity' => 'required',
            'description' => 'required',
            'amenities' => 'required',
            'price_per_hour' => 'required',
            'contact_email' => 'required',
            'contact_phone' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'failed',
                'message' => 'Enter correct data',
                'errors' => $validator->errors()
            ]);
        }

        // Check if event already exists


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
            $venue->save();

            return response([
                'status' => 'success',
                'message' => 'Venue  created successfully!',
                'events' => $venue
            ]);}
    public function showVenues(){
        $venues = Venue::all();
        return response([
            'status' => 'success',
            'venues' => $venues
        ]);
    }

}
