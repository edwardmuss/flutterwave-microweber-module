<?php

$config = array();
$config['name'] = "Flutterwave";
$config['author'] = "Edward Muss (flutterwave.png)";
$config['ui'] = false;
$config['categories'] = "online shop";
$config['position'] = 130;
$config['type'] = "payment_gateway";

$config['settings']['autoload_namespace'] = [
    [
        'path' => __DIR__ . '/src/',
        'namespace' => 'MicroweberPackages\\Payment\\Providers\\Flutterwave\\'
    ],
];
$config['settings']['service_provider'] = [
    \MicroweberPackages\Payment\Providers\Flutterwave\FlutterwaveServiceProvider::class,
];
