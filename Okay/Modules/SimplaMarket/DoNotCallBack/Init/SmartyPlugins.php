<?php


use Okay\Core\Design;
use Okay\Modules\SimplaMarket\DoNotCallBack\Plugins\DoNotCallBackPlugin;
use Okay\Core\OkayContainer\Reference\ServiceReference as SR;

return [
    DoNotCallBackPlugin::class => [
        'class' => DoNotCallBackPlugin::class,
        'arguments' => [
            new SR(Design::class),
        ],
    ],
];