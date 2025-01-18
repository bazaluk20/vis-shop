<?php


namespace Okay\Modules\SimplaMarket\PhoneRequired\Init;


use Okay\Core\OkayContainer\Reference\ServiceReference as SR;
use Okay\Core\Validator;
use Okay\Modules\SimplaMarket\PhoneRequired\Extensions\ValidateExtension;

return [
    ValidateExtension::class => [
        'class' => ValidateExtension::class,
        'arguments' => [
            new SR(Validator::class),
        ]
    ]
];