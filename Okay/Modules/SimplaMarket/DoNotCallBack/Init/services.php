<?php

use Okay\Modules\SimplaMarket\DoNotCallBack\Extenders\FrontExtender;
use Okay\Core\Request;
use Okay\Core\OkayContainer\Reference\ServiceReference as SR;
use Okay\Core\EntityFactory;
return [
    FrontExtender::class => [
        'class' => FrontExtender::class,
        'arguments' => [
            new SR(Request::class),
            new SR(EntityFactory::class),
        ],
    ],
];
