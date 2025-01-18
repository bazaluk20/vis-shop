<?php


namespace Okay\Modules\SimplaMarket\Redirects;

use Okay\Core\EntityFactory;
use Okay\Core\OkayContainer\Reference\ServiceReference as SR;
use Okay\Core\Request;
use Okay\Modules\SimplaMarket\Redirects\Extensions\Redirects;

return [
    Redirects::class => [
        'class' => Redirects::class,
        'arguments' => [
            new SR(EntityFactory::class),
            new SR(Request::class),
        ],
    ],
];
