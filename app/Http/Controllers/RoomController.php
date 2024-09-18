<?php

namespace App\Http\Controllers;

use App\Models\Room;
use Illuminate\Http\Request;

class RoomController extends Controller
{
    // List all rooms
    public function index()
    {
        $rooms = Room::all();
        return response()->json($rooms, 200);
    }

    // Show a single room by ID
    public function show($id)
    {
        $room = Room::find($id);

        if (!$room) {
            return response()->json(['message' => 'Room not found'], 404);
        }

        return response()->json($room, 200);
    }

    // Store a new room
    public function store(Request $request)
    {
        $validated = $request->validate([
            'room_number' => 'required|string|max:255',
            'type' => 'required|in:single,double,suite',
            'description' => 'nullable|string',
            'price' => 'required|numeric',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Image validation
        ]);

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('room_images', 'public'); // Save image
            $validated['image'] = $imagePath;
        }

        Room::create($validated);

        return response()->json(['message' => 'Room created successfully'], 201);
    }

        // Get latest 10 rooms
        public function getLatestRooms()
        {
            $rooms = Room::latest()->take(5)->get(); // Get the latest 10 rooms
            return response()->json($rooms, 200);
        }




     // Fetch rooms by their type
    public function getRoomsByType($type)
    {
        // Validate that the type is one of the allowed values
        if (!in_array($type, ['single', 'double', 'suite'])) {
            return response()->json(['message' => 'Invalid room type'], 400);
        }

        // Fetch rooms by their type
        $rooms = Room::where('type', $type)->get();

        if ($rooms->isEmpty()) {
            return response()->json(['message' => 'No rooms found for this type'], 404);
        }

        return response()->json($rooms, 200);
    }

}
