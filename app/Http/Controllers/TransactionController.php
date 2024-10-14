<?php

namespace App\Http\Controllers;
use App\Models\Transaction;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function getUserTransactions()
    {
        // Get all transactions for the authenticated user
        $transactions = Transaction::where('user_id', auth()->id())
            ->orderBy('created_at', 'desc') // Order by most recent
            ->get()
            ->map(function ($transaction) {
                $transaction->room_image = asset('storage/' . $transaction->room_image); // Adjust the image path
                return $transaction;
            });

        // Return as JSON response
        return response()->json($transactions);
    }
}
