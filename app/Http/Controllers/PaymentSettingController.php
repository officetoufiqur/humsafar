<?php

namespace App\Http\Controllers;

use App\Trait\ApiResponse;
use Illuminate\Http\Request;
use App\Models\PaymentSetting;

class PaymentSettingController extends Controller
{
    use ApiResponse;
    
    public function index()
    {
        $paymentSettings = PaymentSetting::first();

        if (! $paymentSettings) {
            return $this->errorResponse('Payment settings not found', 404);
        }

        return $this->successResponse($paymentSettings, 'Payment settings retrieved successfully');
    }

    public function update(Request $request)
    {
        $request->validate([
            'mollie_key' => 'nullable|string',
            'mollie_webhook' => 'nullable|string',
            'mollie_status' => 'nullable|boolean',
            'stripe_secret_key' => 'nullable|string',
            'stripe_publishable_key' => 'nullable|string',
            'stripe_webhook' => 'nullable|string',
            'stripe_status' => 'nullable|boolean',
        ]);

        $paymentSettings = PaymentSetting::first();

        if (! $paymentSettings) {
            return $this->errorResponse('Payment settings not found', 404);
        }

        $paymentSettings->mollie_key = $request->mollie_key;
        $paymentSettings->mollie_webhook = $request->mollie_webhook;
        $paymentSettings->mollie_status = $request->mollie_status;
        $paymentSettings->stripe_secret_key = $request->stripe_secret_key;
        $paymentSettings->stripe_publishable_key = $request->stripe_publishable_key;
        $paymentSettings->stripe_webhook = $request->stripe_webhook;
        $paymentSettings->stripe_status = $request->stripe_status;
        $paymentSettings->save();

        return $this->successResponse($paymentSettings, 'Payment settings updated successfully');
    }
}
