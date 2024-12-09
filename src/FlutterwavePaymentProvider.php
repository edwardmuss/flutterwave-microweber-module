<?php

namespace MicroweberPackages\Payment\Providers\Flutterwave;

use MicroweberPackages\Payment\Providers\AbstractPaymentProvider;
use MicroweberPackages\Payment\Traits\LegacyPaymentProviderHelperTrait;

class FlutterwavePaymentProvider extends AbstractPaymentProvider
{
    public $module = 'shop/payments/gateways/flutterwave';
    public $name = 'Flutterwave';

    use LegacyPaymentProviderHelperTrait;
}
