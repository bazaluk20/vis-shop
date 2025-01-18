<?php

namespace Okay\Modules\SimplaMarket\PrivatPayPart;

return [
    'SimplaMarket_PrivatPayPart_create_payment' => [
        'slug' => 'payment/SimplaMarket/PrivatPayPart/create_payment',
        'params' => [
            'controller' => __NAMESPACE__ . '\Controllers\PrivatController',
            'method' => 'createPayment',
        ],
    ],
    'SimplaMarket_PrivatPayPart_callback' => [
        'slug' => 'payment/SimplaMarket/PrivatPayPart/callback',
        'params' => [
            'controller' => __NAMESPACE__ . '\Controllers\PrivatController',
            'method' => 'callback',
        ],
    ],
];