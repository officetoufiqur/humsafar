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
use Illuminate\Support\Facades\DB;
use Stripe\Webhook;

class StripeWebhookController extends Controller
{
    public function handle(Request $request)
    {
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');

        try {
            $event = Webhook::constructEvent(
                $payload,
                $sigHeader,
                config('services.stripe.webhook')
            );
        } catch (\Exception $e) {
            return response()->json(['error' => 'Invalid signature'], 400);
        }

        if ($event->type === 'checkout.session.completed') {

            $session = $event->data->object;

            $payment = Payment::where(
                'stripe_session_id',
                $session->id
            )->first();

            if (! $payment || $payment->status === 'completed') {
                return response()->json(['received' => true]);
            }

            $user = User::find($session->metadata->user_id);
            $package = Package::find($session->metadata->package_id);

            if (! $user || ! $package) {
                return response()->json(['error' => 'Invalid metadata'], 400);
            }

            DB::transaction(function () use ($payment, $user, $package) {

                $payment->update([
                    'status' => 'completed',
                    'payment_method' => 'stripe',
                ]);

                $expireDate = now()->addDays($package->duration_days);

                if ($user->vip_expires_at && $user->vip_expires_at > now()) {
                    $expireDate = Carbon::parse($user->vip_expires_at)
                        ->addDays($package->duration_days);
                }

                $user->update([
                    'membership_type' => 'vip',
                    'vip_expires_at' => $expireDate,
                ]);

                $invoiceNumber = GenerateNumber::generate('INV', Invoice::class);

                Invoice::create([
                    'user_id' => $user->id,
                    'payment_id' => $payment->id,
                    'package_id' => $package->id,
                    'invoice_number' => $invoiceNumber,
                    'invoice_date' => now(),
                    'description' => $package->description,
                    'total_amount' => $package->price,
                    'paid_amount' => $package->price,
                    'remaining_amount' => 0
                ]);
            });
        }

        return response()->json(['received' => true]);
    }
}
