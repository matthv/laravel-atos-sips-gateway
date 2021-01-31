<?php

return [
    'test'              => env('ATOS_TEST', true),
    'merchant_id'       => env('ATOS_MERCHANT_ID', ''),
    'secret_key'        => env('ATOS_SECRET_KEY', ''),
    'key_version'       => env('ATOS_KEY_VERSION', ''),
    'interface_version' => env('ATOS_INTERFACE_VERSION', ''),

    'customer_return_route_name'    => 'atos.return',
    'customer_callback_route_name'  => 'atos.callback',

    'production_url'    => env('ATOS_PRODUCTION_URL', 'https://payment-webinit.mercanet.bnpparibas.net/paymentInit'),
    'test_url'          => env('ATOS_TEST_URL', 'https://payment-webinit-mercanet.test.sips-atos.com/paymentInit'),
];
