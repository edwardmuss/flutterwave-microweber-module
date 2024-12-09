<?php

namespace MicroweberPackages\Payment\Providers\Flutterwave;

use Illuminate\Support\ServiceProvider;

class FlutterwaveServiceProvider extends  ServiceProvider
{

    public function register()
    {
        app()->resolving(\MicroweberPackages\Payment\PaymentManager::class, function (\MicroweberPackages\Payment\PaymentManager $manager) {
            $manager->extend('shop/payments/gateways/flutterwave', function () {
                return new FlutterwavePaymentProvider();
            });
        });
    }
}
