<?php

namespace App\Http\Controllers;

use App\Models\Package;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Stripe\PaymentIntent;
use Stripe\Stripe;

class StripePaymentController extends Controller
{
    public function createPaymentIntent(Request $request)
    {
        $request->validate([
            'package_id' => 'required|exists:packages,id',
        ]);

        $package = Package::findOrFail($request->package_id);

        Stripe::setApiKey(config('services.stripe.secret'));

        $amount = (int) $package->price;

        $intent = PaymentIntent::create([
            'amount' => $amount, 
            'currency' => 'usd',
            'metadata' => [
                'user_id' => Auth::id(),
                'package_id' => $package->id,
            ],
        ]);

        Payment::create([
            'user_id' => Auth::id(),
            'package_id' => $package->id,
            'stripe_payment_intent_id' => $intent->id,
            'tier' => $package->name,
            'method' => 'card',
            'amount' => $amount,
            'currency' => 'usd',
            'status' => 'pending',
        ]);

        return response()->json([
            'status' => true,
            'client_secret' => $intent->client_secret,
        ]);
    }
}
