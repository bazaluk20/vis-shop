<?php

namespace Okay\Modules\Vitalisoft\Analytics\Init;

use Okay\Modules\Vitalisoft\Analytics\Controllers\AnalyticsController;

return [
    'Vitalisoft_Analytics_purchase' => [
        'slug' => 'ajax/analytics/purchase/{$order_url}',
        'to_front' => true,
        'params' => [
            'controller' => AnalyticsController::class,
            'method' => 'purchase'
        ],
    ],
];
