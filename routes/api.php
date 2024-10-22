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
    public function updateProfile(Request $request) {
        try {
            $user = $request->user();
    
            // Validate request
            $validatedData = $request->validate([
                'first_name' => 'nullable|string|max:255',
                'last_name' => 'nullable|string|max:255',
                'profile' => 'nullable|string', // Expecting a base64-encoded string
            ]);
    
            // Update first name and last name
            if ($request->filled('first_name')) {
                $user->first_name = $validatedData['first_name'];
            }
    
            if ($request->filled('last_name')) {
                $user->last_name = $validatedData['last_name'];
            }
    
            // Handle base64 image
            if ($request->filled('profile')) {
                $base64Image = $validatedData['profile'];
    
                // Extract the base64 string without the data:image/... prefix
                preg_match('/data:image\/(\w+);base64,(.*)/', $base64Image, $matches);
                if (isset($matches[2])) {
                    $imageContent = base64_decode($matches[2]);
                    $extension = $matches[1];
                    $fileName = 'profiles/' . uniqid() . '.' . $extension;
                    Storage::disk('public')->put($fileName, $imageContent);
                    $user->profile = $fileName; // Save the path to the profile image
                }
            }
    
            $user->save();
    
            // Return the updated user information with the profile URL
            return response()->json([
                'message' => 'Profile updated successfully',
                'user' => [
                    'id' => $user->id,
                    'first_name' => $user->first_name,
                    'last_name' => $user->last_name,
                    'profile' => $user->profile ? url('storage/' . $user->profile) : null, // Return the public URL of the image
                ],
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred while updating the profile',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    
}
