<?php

namespace App\Http\Controllers;

use App\Helpers\GenerateNumber;
use App\Models\Invoice;
use App\Models\Package;
use App\Models\Payment;
use App\Models\User;
use App\Notifications\UserNotification;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Mollie\Laravel\Facades\Mollie;

class MolliePaymentController extends Controller
{
    public function create(Request $request)
    {
        $request->validate([
            'package_id' => 'required|exists:packages,id',
            'tier' => 'required|string',
            'redirect_url' => 'required|url',
        ]);

        $package = Package::findOrFail($request->package_id);

        if (! $package->status) {
            return response()->json([
                'message' => 'Package is not active',
            ], 403);
        }

        $molliePayment = Mollie::api()->payments->create([
            'amount' => [
                'currency' => 'USD',
                'value' => number_format($package->price, 2, '.', ''),
            ],
            'description' => 'Package: '.$package->name,
            'redirectUrl' => $request->redirect_url,
            'webhookUrl' => 'https://nonpluralistic-princeton-subaqueous.ngrok-free.dev/api/mollie/webhook',
            'metadata' => [
                'package_id' => $package->id,
                'user_id' => Auth::id(),
            ],
        ]);

        Payment::create([
            'user_id' => Auth::id(),
            'package_id' => $package->id,
            'stripe_payment_intent_id' => $molliePayment->id,
            'tier' => $request->tier,
            'method' => 'mollie',
            'amount' => $package->price,
            'currency' => 'usd',
            'status' => 'pending',
        ]);

        return response()->json([
            'payment_id' => $molliePayment->id,
            'checkout_url' => $molliePayment->getCheckoutUrl(),
        ]);
    }

    public function webhook(Request $request)
    {
        if (! $request->id) {
            return response()->json(['error' => 'Invalid payload'], 400);
        }

        $molliePayment = Mollie::api()
            ->payments()
            ->get($request->id);

        $dbPayment = Payment::where(
            'mollie_payment_id',
            $molliePayment->id
        )->first();

        if (! $dbPayment) {
            return response()->json(['error' => 'Payment not found'], 404);
        }

        if ($dbPayment->status === 'completed') {
            return response()->json(['received' => true]);
        }

        if (
            $molliePayment->isFailed() ||
            $molliePayment->isCanceled() ||
            $molliePayment->isExpired()
        ) {
            $dbPayment->update(['status' => 'failed']);

            return response()->json(['received' => true]);
        }

        if ($molliePayment->isPaid()) {
            if (
                (float) $molliePayment->amount->value
                !== (float) $dbPayment->amount
            ) {
                return response()->json(['error' => 'Amount mismatch'], 400);
            }

            $user = User::find($dbPayment->user_id);
            $package = Package::find($dbPayment->package_id);

            if (! $user || ! $package) {
                return response()->json(['error' => 'Invalid data'], 400);
            }

            DB::transaction(function () use ($dbPayment, $user, $package, $molliePayment) {
                $dbPayment->update([
                    'status' => 'completed',
                    'method' => $molliePayment->method ?? 'mollie',
                ]);

                $expireDate = now()->addDays($package->validity);

                if (
                    $user->membership_type === 'vip' &&
                    $user->vip_expires_at &&
                    $user->vip_expires_at > now()
                ) {
                    $expireDate = Carbon::parse($user->vip_expires_at)
                        ->addDays($package->validity);
                }

                $user->update([
                    'membership_type' => 'vip',
                    'vip_expires_at' => $expireDate,
                ]);
            });

            $invoiceNumber = GenerateNumber::generate('INV', Invoice::class);

            Invoice::create([
                'user_id' => $user->id,
                'payment_id' => $dbPayment->id,
                'package_id' => $package->id,
                'invoice_number' => $invoiceNumber,
                'invoice_date' => now(),
                'description' => $package->description,
                'total_amount' => $package->price,
                'paid_amount' => $package->price,
                'remaining_amount' => 0
            ]);
        }

         $admins = User::role('admin')->first();

        $admins->notify(new UserNotification([
            'type' => 'admin',
            'user' => [
                'id' => $user->id,
                'fname' => $user->fname,
                'lname' => $user->lname,
                'photo' => $user->photo
            ],
        ], "Package {$package->name} has been purchased by {$user->fname} {$user->lname}."));

        return response()->json(['received' => true]);
    }

    public function status($id)
    {
        $payment = Payment::where(
            'mollie_payment_id',
            $id
        )->firstOrFail();

        return response()->json([
            'status' => $payment->status,
        ]);
    }
}
