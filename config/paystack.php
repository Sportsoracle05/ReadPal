<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Paystack Configuration
    |--------------------------------------------------------------------------
    */

    'secret_key'     => env('PAYSTACK_SECRET_KEY'),
    'public_key'     => env('PAYSTACK_PUBLIC_KEY'),
    'payment_url'    => env('PAYSTACK_PAYMENT_URL', 'https://api.paystack.co'),
    'merchant_email' => env('PAYSTACK_MERCHANT_EMAIL'),

    /*
    |--------------------------------------------------------------------------
    | Subscription Tiers
    |--------------------------------------------------------------------------
    | All amounts stored in kobo (Naira × 100).
    | tier_rank is used to prevent accidental downgrades in fulfilment logic.
    | Tiers ranked: premium (1) < vip (2) < vvip (3)
    |--------------------------------------------------------------------------
    */

    'subscriptions' => [

        'premium' => [
            'amount'        => 50000,      // ₦500
            'duration_days' => 30,
            'label'         => 'Premium',
            'description'   => '1 Month Access',
            'plan_code'     => 'PREMIUM_1M',
            'tier_rank'     => 1,
            'color'         => 'forest',   // used for UI theming
            'perks'         => [
                'All lecture notes & PDFs',
                'Unlimited quiz attempts',
                'Karls community access',
                'CGPA calculator',
                'Push notifications',
            ],
        ],

        'vip' => [
            'amount'        => 130000,     // ₦1,300
            'duration_days' => 90,
            'label'         => 'VIP',
            'description'   => '3 Months Access',
            'plan_code'     => 'VIP_3M',
            'tier_rank'     => 2,
            'color'         => 'amber',
            'perks'         => [
                'Everything in Premium',
                'Priority support',
                'Early access to new materials',
                'VIP badge on Karls',
                '3× longer study streak tracking',
            ],
        ],

        'vvip' => [
            'amount'        => 250000,     // ₦2,500
            'duration_days' => 180,
            'label'         => 'VVIP',
            'description'   => '6 Months · Full Semester',
            'plan_code'     => 'VVIP_6M',
            'tier_rank'     => 3,
            'color'         => 'gold',
            'perks'         => [
                'Everything in VIP',
                'Full-semester coverage',
                'Exclusive VVIP study materials',
                'VVIP badge on Karls',
                'Direct admin support channel',
                'Exam prep priority access',
            ],
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Security
    |--------------------------------------------------------------------------
    */

    'webhook_ip_whitelist' => [
        '52.31.139.75',
        '52.49.173.169',
        '52.214.14.220',
    ],

];
