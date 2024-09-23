<?php

use App\Models\User; 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Hash; 
use Illuminate\Validation\ValidationException;

use App\Http\Controllers\RoomController;

// para makuha or ung user na validated/authenticated
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return response()->json($request->user());
});

// Room routes
Route::get('rooms/latest' , [RoomController::class, 'getLatestRooms']);
Route::get('/rooms/type/{type}', [RoomController::class, 'getRoomsByType']);
Route::get('rooms', [RoomController::class, 'index']); // Get all rooms
Route::get('rooms/{id}', [RoomController::class, 'show']); // Get single room by ID
Route::post('rooms', [RoomController::class, 'tore']); // Create a new room

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

    //register route
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

    //logout/deleting accesstoken
    Route::middleware('auth:sanctum')->post('/logout', function(Request $request){
        $request->user()->currentAccessToken()->delete();

        return response()->json('Logged out', 200);

    });

    // Update user profile 
Route::middleware('auth:sanctum')->put('/user/profile', function (Request $request) {
    $user = $request->user();

    // Validate the request
    $request->validate([
        'first_name' => 'required|string|max:255',
        'last_name' => 'required|string|max:255',
        'profile' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048', // Image validation
    ]);

    // Update user's first and last name
    $user->first_name = $request->first_name;
    $user->last_name = $request->last_name;

    // Check if a profile image was uploaded
    if ($request->hasFile('profile')) {
        $profileImage = $request->file('profile');

        // Store the profile image and generate its path
        $path = $profileImage->store('profiles', 'public');

        // Save the path to the user's profile
        $user->profile = $path;
    }

    // Save the updated user data
    $user->save();

    // Return a success response with updated user information
    return response()->json([
        'message' => 'Profile updated successfully',
        'user' => $user->only('id', 'first_name', 'last_name', 'profile'),
    ], 200);
});


