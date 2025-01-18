<?php


namespace Okay\Modules\SimplaMarket\PrivatPayPart;


use Okay\Core\BackendTranslations;
use Okay\Core\EntityFactory;
use Okay\Core\Money;
use Okay\Core\OkayContainer\Reference\ParameterReference as PR;
use Okay\Core\OkayContainer\Reference\ServiceReference as SR;
use Okay\Core\Request;
use Okay\Modules\SimplaMarket\PrivatPayPart\Extenders\BackendExtender;
use Okay\Modules\SimplaMarket\PrivatPayPart\Extenders\FrontExtender;

return [
    PaymentForm::class => [
        'class' => PaymentForm::class,
        'arguments' => [
            new SR(EntityFactory::class),
            new SR(Money::class),
        ],
    ],
    FrontExtender::class => [
        'class' => FrontExtender::class,
        'arguments' => [
            new SR(EntityFactory::class),
            new SR(Request::class),
        ],
    ],
    BackendExtender::class => [
        'class' => BackendExtender::class,
        'arguments' => [
            new SR(Request::class),
            new SR(BackendTranslations::class),
            new SR(EntityFactory::class),
        ],
    ],
];