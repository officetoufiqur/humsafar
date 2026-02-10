<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Stripe\Stripe;
use Stripe\Webhook;
use App\Models\User;
use App\Models\Invoice;
use App\Models\Package;
use App\Models\Payment;
use Stripe\PaymentIntent;
use Illuminate\Http\Request;
use Stripe\Checkout\Session;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class StripePaymentController extends Controller
{
    public function checkout(Request $request)
    {
        $request->validate([
            'package_id' => 'required|exists:packages,id',
        ]);

        $package = Package::findOrFail($request->package_id);
 
        Stripe::setApiKey(config('services.stripe.secret'));

        $session = Session::create([
            'mode' => 'payment',
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency' => $package->currency,
                    'product_data' => [
                        'name' => $package->name,
                    ],
                    'unit_amount' => $package->price * 100,
                ],
                'quantity' => 1,
            ]],
            'metadata' => [
                'user_id' => Auth::id(),
                'package_id' => $package->id,
            ],
            'success_url' => config('app.url').'/payment-success',
            'cancel_url' => config('app.url').'/payment-cancel',
        ]);

        Payment::create([
            'user_id' => Auth::id(),
            'package_id' => $package->id,
            'stripe_session_id' => $session->id,
            'tier' => $package->name,
            'amount' => $package->price,
            'currency' => $package->currency,
            'status' => 'pending',
            'method' => 'stripe',
        ]);

        return response()->json([
            'url' => $session->url,
        ]);
    }

    
}
