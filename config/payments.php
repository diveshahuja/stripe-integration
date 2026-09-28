<?php
return [
    'default' => env('DEFAULT_PAYMENT_GATEWAY'),

    'gateways' => [
        'paypal' => [
            'key' => env('PAYPAL_API_KEY'),
            'secret' => env('PAYPAL_API_SECRET'),
        ],
        'stripe' => [
            'key' => env('STRIPE_API_KEY'),
            'secret' => env('STRIPE_API_SECRET'),
        ],
        'razorpay' => [
            'key' => env('RAZORPAY_API_KEY'),
            'secret' => env('RAZORPAY_API_SECRET'),
        ],
    ]
];
