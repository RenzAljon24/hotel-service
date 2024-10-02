<?php
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\ReservationController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Constants for image storage paths and validation rules
const PROFILE_IMAGE_PATH = 'profiles';
const ROOM_IMAGE_PATH = 'room_images';
const IMAGE_VALIDATION_RULES = 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048';
const USER_UPDATE_VALIDATION_RULES = [
    'first_name' => 'required|string|max:255',
    'last_name' => 'required|string|max:255',
    'profile' => IMAGE_VALIDATION_RULES,
];

// Middleware group for authenticated routes
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return response()->json($request->user());
    });
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::put('/user/profile', [UserController::class, 'updateProfile']);
    Route::post('/reservations', [ReservationController::class, 'store']);
});

// Room routes
Route::prefix('rooms')->group(function () {
    Route::get('latest', [RoomController::class, 'getLatestRooms']);
    Route::get('type/{type}', [RoomController::class, 'getRoomsByType']);
    Route::get('/', [RoomController::class, 'index']); // Get all rooms
    Route::get('{id}', [RoomController::class, 'show']); // Get single room by ID
    Route::post('/', [RoomController::class, 'store']); // Create a new room
});

Route::get('/rooms/{roomId}/booked-dates', [ReservationController::class, 'getBookedDates']);

// Auth routes
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

class UserController extends Controller {
    public function updateProfile(Request $request) {
        $user = $request->user();

        // Validate the request
        $validatedData = $request->validate(USER_UPDATE_VALIDATION_RULES);

        // Update user's first and last name
        $user->first_name = $validatedData['first_name'];
        $user->last_name = $validatedData['last_name'];

        // Check if a profile image was uploaded
        if ($request->hasFile('profile')) {
            $profileImage = $request->file('profile');
            // Store the profile image and generate its path
            $path = $profileImage->store(PROFILE_IMAGE_PATH, 'public');
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
    }
}
