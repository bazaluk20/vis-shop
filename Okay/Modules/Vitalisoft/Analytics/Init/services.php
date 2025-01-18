<?php

namespace Okay\Modules\Vitalisoft\Analytics\Init;

use Okay\Core\Design;
use Okay\Core\EntityFactory;
use Okay\Core\OkayContainer\Reference\ServiceReference as SR;
use Okay\Core\Request;
use Okay\Core\Settings;
use Okay\Modules\Vitalisoft\Analytics\Extenders\FrontExtender;
use Okay\Modules\Vitalisoft\Analytics\Helpers\AnalyticsHelper;

return [
    FrontExtender::class => [
        'class' => FrontExtender::class,
        'arguments' => [
            new SR(EntityFactory::class),
            new SR(Design::class),
            new SR(Settings::class),
            new SR(Request::class),
            new SR(AnalyticsHelper::class),
        ],
    ],
    AnalyticsHelper::class => [
        'class' => AnalyticsHelper::class,
        'arguments' => [
            new SR(Settings::class)
        ]
    ]
];