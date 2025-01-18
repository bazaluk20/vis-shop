<?php


namespace Okay\Modules\SimplaMarket\CartLastNameRequired\Init;


use Okay\Core\FrontTranslations;
use Okay\Core\OkayContainer\Reference\ServiceReference as SR;
use Okay\Core\Validator;
use Okay\Modules\SimplaMarket\CartLastNameRequired\Extensions\ValidateExtension;

return [
    ValidateExtension::class => [
        'class' => ValidateExtension::class,
        'arguments' => [
            new SR(Validator::class),
            new SR(FrontTranslations::class),
        ]
    ]
];