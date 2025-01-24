<?php


use Okay\Core\Cart;
use Okay\Core\Design;
use Okay\Core\Request;
use Okay\Core\EntityFactory;
use Okay\Helpers\MoneyHelper;
use Okay\Modules\SimplaMarket\Sets\Extenders\BackendExtender;
use Okay\Modules\SimplaMarket\Sets\Extenders\FrontExtender;
use Okay\Modules\SimplaMarket\Sets\Helpers\SetsHelper;
use Okay\Modules\SimplaMarket\Sets\Requests\SetsRequest;
use Okay\Modules\SimplaMarket\Sets\Helpers\CartSetHelper;
use Okay\Modules\SimplaMarket\Sets\Extensions\CartExtension;
use Okay\Core\OkayContainer\Reference\ServiceReference as SR;
use Okay\Modules\SimplaMarket\Sets\Extensions\CartHelperExtension;
use Okay\Modules\SimplaMarket\Sets\Extensions\OrdersHelperExtension;
use Okay\Modules\SimplaMarket\Sets\Extensions\BackendOrdersHelperExtension;

return [
    SetsHelper::class => [
        'class' => SetsHelper::class,
        'arguments' => [
            new SR(EntityFactory::class),
            new SR(MoneyHelper::class),
        ],
    ],
    SetsRequest::class => [
        'class' => SetsRequest::class,
        'arguments' => [
            new SR(Request::class),
        ],
    ],
    OrdersHelperExtension::class => [
        'class' => OrdersHelperExtension::class,
        'arguments' => [
            new SR(SetsHelper::class),
        ],
    ],
    CartSetHelper::class => [
        'class' => CartSetHelper::class,
        'arguments' => [
            new SR(EntityFactory::class),
            new SR(SetsHelper::class),
            new SR(MoneyHelper::class),
            new SR(Cart::class),
        ],
    ],
    CartExtension::class => [
        'class' => CartExtension::class,
        'arguments' => [
            new SR(EntityFactory::class),
            new SR(SetsHelper::class),
            new SR(CartSetHelper::class),
            new SR(Cart::class),
            new SR(MoneyHelper::class),
            new SR(Request::class),
        ],
    ],
    CartHelperExtension::class => [
        'class' => CartHelperExtension::class,
        'arguments' => [
            new SR(SetsHelper::class),
            new SR(CartSetHelper::class),
            new SR(EntityFactory::class),
        ],
    ],
    BackendOrdersHelperExtension::class => [
        'class' => BackendOrdersHelperExtension::class,
        'arguments' => [
            new SR(Design::class),
            new SR(SetsHelper::class),
        ],
    ],
    BackendExtender::class => [
        'class' => BackendExtender::class,
        'arguments' => [
            new SR(Request::class),
            new SR(EntityFactory::class),
        ]
    ],
    FrontExtender::class => [
        'class' => FrontExtender::class,
        'arguments' => [
            new SR(EntityFactory::class),
            new SR(Design::class),
            new SR(CartSetHelper::class),
            new SR(Cart::class),
        ],
    ],
];