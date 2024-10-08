<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\PaymentIntent;

class PaymentController extends Controller
{
    public function createCharge(Request $request)
    {
        // Validate request
        $request->validate([
            'amount' => 'required|numeric',
        ]);

        // Set Stripe secret key
        Stripe::setApiKey(env('STRIPE_SECRET'));

        try {
            // Create a Payment Intent
            $paymentIntent = PaymentIntent::create([
                'amount' => $request->amount * 100, // Amount in cents
                'currency' => 'php',
                'payment_method_types' => ['card'], // Specify card payment method
            ]);

            // Return client secret to the frontend
            return response()->json(['client_secret' => $paymentIntent->client_secret]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
