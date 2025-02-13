<?php

namespace App\Http\Controllers;

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
        $existingEvent = Event::where('title', $data['title'])
            ->where('description', $data['description'])
            ->where('event_date', $data['event_date'])
            ->where('user_id', Auth::id()) // Ensure it's checked per user
            ->first();

        if ($existingEvent) {
            return response()->json([
                'status' => 'failed',
                'message' => 'This event already exists!'
            ]);
        }

        // Save the new event
        $event = new Event();
        $event->user_id = Auth::id();
        $event->title = $data['title'];
        $event->description = $data['description'];
        $event->event_date = $data['event_date'];
        $event->capacity = $data['capacity'];
        $event->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Event created successfully!',
            'event' => $event
        ]);
    }

    public function showEvents(){
        $events = Event::where('user_id', Auth::id())->get();
        return response()->json([
            'status' => 'success',
            'events' => $events
        ]);

    }
}
