<?php


use Okay\Core\Design;
use Okay\Modules\SimplaMarket\Sets\Helpers\SetsHelper;
use Okay\Modules\SimplaMarket\Sets\Plugins\SetsInCartPlugin;
use Okay\Core\OkayContainer\Reference\ServiceReference as SR;
use Okay\Modules\SimplaMarket\Sets\Plugins\SetsInOrderPlugin;
use Okay\Modules\SimplaMarket\Sets\Plugins\SetsInPopUpCartPlugin;
use Okay\Modules\SimplaMarket\Sets\Plugins\SetsInProductPlugin;

return [
    SetsInProductPlugin::class => [
        'class' => SetsInProductPlugin::class,
        'arguments' => [
            new SR(Design::class),
            new SR(SetsHelper::class),
        ],
    ],
    SetsInOrderPlugin::class => [
        'class' => SetsInOrderPlugin::class,
        'arguments' => [
            new SR(Design::class),
            new SR(SetsHelper::class),
        ],
    ],
    SetsInCartPlugin::class => [
        'class' => SetsInCartPlugin::class,
        'arguments' => [
            new SR(Design::class),
        ],
    ],
    SetsInPopUpCartPlugin::class => [
        'class' => SetsInPopUpCartPlugin::class,
        'arguments' => [
            new SR(Design::class),
        ],
    ],
];