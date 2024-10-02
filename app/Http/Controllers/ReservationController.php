<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReservationController extends Controller
{
    /**
     * Store a newly created reservation in storage.
     */
    public function store(Request $request)
    {
        // Validate the request data
        $request->validate([
            'room_id' => 'required|exists:rooms,id',
            'check_in' => 'required|date|after_or_equal:today',
            'check_out' => 'required|date|after:check_in',
        ]);

        // Find the room by ID
        $room = Room::findOrFail($request->room_id);

        // Check if the room is available for the given date range
        $existingReservation = Reservation::where('room_id', $room->id)
            ->where(function ($query) use ($request) {
                $query->whereBetween('check_in', [$request->check_in, $request->check_out])
                    ->orWhereBetween('check_out', [$request->check_in, $request->check_out])
                    ->orWhereRaw('? BETWEEN check_in AND check_out', [$request->check_in])
                    ->orWhereRaw('? BETWEEN check_in AND check_out', [$request->check_out]);
            })
            ->exists();

        // Return an error if the room is already booked
        if ($existingReservation) {
            return response()->json(['error' => 'Room is already booked for the selected dates'], 400);
        }

        // Calculate the total price
        $days = (strtotime($request->check_out) - strtotime($request->check_in)) / 86400;
        $totalPrice = $days * $room->price;

        // Create the reservation
        $reservation = Reservation::create([
            'user_id' => auth()->id(),
            'room_id' => $room->id,
            'check_in' => $request->check_in,
            'check_out' => $request->check_out,
        ]);

        // Return a success response
        return response()->json([
            'message' => 'Room booked successfully!',
            'total_price' => $totalPrice,
            'reservation' => $reservation,
        ]);
    }

    /**
     * Get booked dates for a specific room.
     */
    public function getBookedDates($roomId)
    {
        // Fetch the booked dates for the given room ID
        $bookedDates = Reservation::where('room_id', $roomId)
            ->join('users', 'reservations.user_id', '=', 'users.id')
            ->get([
                'reservations.check_in',
                'reservations.check_out',
                'users.first_name as booked_by'
            ]);

        // Return the booked dates in the response
        return response()->json($bookedDates);
    }
}
