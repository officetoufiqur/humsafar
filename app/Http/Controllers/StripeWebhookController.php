<?php

namespace App\Http\Controllers;

use App\Models\Package;
use App\Models\Payment;
use App\Models\User;
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

        if ($event->type === 'payment_intent.succeeded') {

            $intent = $event->data->object;

            $payment = Payment::where(
                'stripe_payment_intent_id',
                $intent->id
            )->first();

            if (! $payment) {
                return response()->json(['error' => 'Payment not found'], 404);
            }

            if ($payment->status === 'completed') {
                return response()->json(['received' => true]);
            }

            if ((int) $intent->amount !== (int) $payment->amount) {
                return response()->json(['error' => 'Amount mismatch'], 400);
            }

            $user = User::find($intent->metadata->user_id ?? null);
            $package = Package::find($intent->metadata->package_id ?? null);

            if (! $user || ! $package) {
                return response()->json(['error' => 'Invalid metadata'], 400);
            }

            DB::transaction(function () use ($payment, $user, $package, $intent) {

                $payment->update([
                    'status' => 'completed',
                    'payment_method' =>
                        $intent->payment_method_types[0] ?? 'card',
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
            });
        }

        if ($event->type === 'payment_intent.payment_failed') {

            $intent = $event->data->object;

            Payment::where(
                'stripe_payment_intent_id',
                $intent->id
            )->update([
                'status' => 'failed',
            ]);
        }

        return response()->json(['received' => true]);
    }
}
