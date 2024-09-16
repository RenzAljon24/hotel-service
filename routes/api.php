<?php

use App\Models\Hotel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RoomController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('rooms', [RoomController::class, 'index']); // Get all rooms
Route::get('rooms/{id}', [RoomController::class, 'show']); // Get single room by ID
Route::post('rooms', [RoomController::class, 'store']); // Create a new room