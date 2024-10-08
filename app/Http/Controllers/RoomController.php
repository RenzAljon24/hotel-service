<?php

namespace App\Http\Controllers;

use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class RoomController extends Controller
{
    // List all rooms
    public function index()
    {
        $rooms = Room::all();

        // Loop through each room and append the image URL if it exists
        foreach ($rooms as $room) {
            if ($room->image) {
                $room->image = url('storage/' . $room->image); // Correct URL to access the image
            }
        }

        return response()->json($rooms, 200);
    }

    // Show a single room by ID
    public function show($id)
    {
        $room = Room::find($id);

        if (!$room) {
            return response()->json(['message' => 'Room not found'], 404);
        }

        if ($room->image) {
            $room->image = url('storage/' . $room->image); // Correct URL to access the image
        }

        return response()->json($room, 200);
    }

    // Store a new room
    public function store(Request $request)
    {
        $validated = $request->validate([
            'room_name' => 'required|string|max:255',
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

    // Get the latest 5 rooms
    public function getLatestRooms()
    {
        $rooms = Room::latest()->take(4)->get(); // Get the latest 5 rooms

        // Loop through each room and append the image URL if it exists
       foreach ($rooms as $room)  {
            if ($room->image) {
                $room->image = url('storage/' . $room->image); // Correct URL to access the image
            }
        }

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

        // Loop through each room and append the image URL if it exists
        foreach ($rooms as $room) {
            if ($room->image) {
                $room->image = url('storage/' . $room->image); // Correct URL to access the image
            }
        }

        return response()->json($rooms, 200);
    }

    public function destroy($id){
        $room = Room::find($id);
        $room->delete();

        return response()->json(['message' => 'Room deleted successfully'], 200);
    }

    public function update(Request $request, Room $room)
    {
        $validatedData = $request->validate([
            "room_number" => "required|string|max:255",
            "type" => "required|in:single,double,suite",
            "description" => "nullable|string",
            "price" => "required|numeric",
            "image" => "nullable|image|mimes:jpeg,png,jpg|max:2048",
        ]);

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('room_images', 'public');
            $validatedData['image'] = $imagePath;
        }

        $room->update($validatedData);

        return response()->json(['message' => 'Room updated successfully'], 200);
    }
}
