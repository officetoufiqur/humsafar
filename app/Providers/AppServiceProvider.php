<?php

namespace App\Providers;

use App\Models\PaymentSetting;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (! Schema::hasTable('payment_settings')) {
            return; 
        }

        $settings = Cache::rememberForever('payment_settings', function () {
            return PaymentSetting::first();
        });

        if (! $settings) {
            throw new \Exception('Payment settings not found in database');
        }

        // Stripe
        if ($settings->stripe_status) {
            Config::set('services.stripe.key', $settings->stripe_publishable_key);
            Config::set('services.stripe.secret', $settings->stripe_secret_key);
            Config::set('services.stripe.webhook', $settings->stripe_webhook);
        }

        // Mollie
        if ($settings->mollie_status) {
            Config::set('services.mollie.key', $settings->mollie_key);
            Config::set('services.mollie.webhook', $settings->mollie_webhook);
        }
    }
}
