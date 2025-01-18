<?php


use Okay\Core\Design;
use Okay\Core\EntityFactory;
use Okay\Modules\SimplaMarket\PrivatPayPart\Plugins\PrivatPayPartPlugin;
use Okay\Core\OkayContainer\Reference\ParameterReference as PR;
use Okay\Core\OkayContainer\Reference\ServiceReference as SR;
use Okay\Core\Modules\Module;

return [
    PrivatPayPartPlugin::class => [
        'class' => PrivatPayPartPlugin::class,
        'arguments' => [
            new SR(Design::class),
            new SR(Module::class),
            new SR(EntityFactory::class),
        ],
    ],
];