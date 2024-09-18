<?php

use App\Models\User; // Corrected namespace for User model
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Hash; // Added import for Hash
use Illuminate\Validation\ValidationException;

use App\Http\Controllers\RoomController;

// Route to get the authenticated user
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return response()->json($request->user());
});

// Room routes
Route::get('rooms/latest' , [RoomController::class, 'getLatestRooms']);
Route::get('/rooms/type/{type}', [RoomController::class, 'getRoomsByType']);
Route::get('rooms', [RoomController::class, 'index']); // Get all rooms
Route::get('rooms/{id}', [RoomController::class, 'show']); // Get single room by ID
Route::post('rooms', [RoomController::class, 'store']); // Create a new room

// Login route
Route::post('/login', function (Request $request) {
    $request->validate([
        'email' => 'required|email',
        'password' => 'required',
        'device_name' => 'required',
    ]);

    $user = User::where('email', $request->email)->first();

    if (! $user || ! Hash::check($request->password, $user->password)) {
        throw ValidationException::withMessages([
            'email' => ['The provided credentials are incorrect.'],
        ]);
    }

    $token = $user->createToken($request->device_name)->plainTextToken;

    return response()->json([
        'token' => $token,
        'user' => $user->only('id', 'first_name', 'last_name', 'profile', 'email')
    ], 201);

});

    Route::post('/register', function(Request $request){
        $request->validate([
            'first_name' =>'required',
            'last_name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6|confirmed',

        ]);

        $user = User::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);
        return response()->json($user, 201);
    });

    Route::middleware('auth:sanctum')->post('/logout', function(Request $request){
        $request->user()->currentAccessToken()->delete();

        return response()->json('Logged out', 200);

    });

