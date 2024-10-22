<?php
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\TransactionController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PaymentController;




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

Route::middleware('auth:sanctum')->get('/user/transactions', [TransactionController::class, 'getUserTransactions']);


// Room routes
Route::prefix('rooms')->group(function () {
    Route::get('latest', [RoomController::class, 'getLatestRooms']);
    Route::get('/', [RoomController::class, 'index']); // Get all rooms
    Route::get('{id}', [RoomController::class, 'show']); // Get single room by ID
    Route::post('/', [RoomController::class, 'store']); // Create a new room
    Route::delete('/{id}', [RoomController::class, 'destroy']);
    Route::put('{rooms}', [RoomController::class, 'update']);
});
Route::get('type/{type}', [RoomController::class, 'getRoomsByType']);


Route::get('/rooms/{roomId}/booked-dates', [ReservationController::class, 'getBookedDates']);

// Auth routes
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

Route::get('/create-admin', function () {
    $user = User::create([
        'first_name' => 'Admin',
        'last_name' => 'User',
        'email' => 'admin@example.com',
        'password' => Hash::make('password'), // Use a secure password
    ]);

    return 'Admin user created successfully!';
});

//payment route 
Route::post('/create-charge', [PaymentController::class, 'createCharge']);


class UserController extends Controller {
    public function updateProfile(Request $request)
    {
        try {
            // Get the authenticated user
            $user = $request->user();

            // Validate the incoming request
            $validatedData = $request->validate([
                'first_name' => 'nullable|string|max:255',
                'last_name' => 'nullable|string|max:255',
                'profile' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Profile image validation
            ]);

            // Update first name and last name if provided
            if ($request->filled('first_name')) {
                $user->first_name = $validatedData['first_name'];
            }

            if ($request->filled('last_name')) {
                $user->last_name = $validatedData['last_name'];
            }

            // Handle the profile image if provided
            if ($request->hasFile('profile')) {
                // If user already has a profile image, delete the old one
                if ($user->profile) {
                    Storage::disk('public')->delete($user->profile);
                }

                // Store the new profile image and generate its path
                $imagePath = $request->file('profile')->store('profiles', 'public');
                $user->profile = $imagePath;
            }

            // Save the updated user data
            $user->save();

            // Return a success response with the updated user information including the image URL
            return response()->json([
                'message' => 'Profile updated successfully',
                'user' => [
                    'id' => $user->id,
                    'first_name' => $user->first_name,
                    'last_name' => $user->last_name,
                    'profile' => $user->profile ? url('storage/' . $user->profile) : null, // Return the public URL of the profile image
                ],
            ], 200);
        } catch (\Exception $e) {
            // Return error response if an exception occurs
            return response()->json([
                'message' => 'An error occurred while updating the profile',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    
}
