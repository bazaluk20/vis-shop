<?php


namespace Okay\Modules\SimplaMarket\ProductsAdvantages\Init;


use Okay\Core\Config;
use Okay\Core\Design;
use Okay\Core\EntityFactory;
use Okay\Core\Image;
use Okay\Core\OkayContainer\Reference\ServiceReference as SR;
use Okay\Core\Request;
use Okay\Core\Router;
use Okay\Modules\SimplaMarket\ProductsAdvantages\Extensions\ProductAdminExtension;
use Okay\Modules\SimplaMarket\ProductsAdvantages\Backend\Helpers\BackendProductsAdvantagesHelper;
use Okay\Modules\SimplaMarket\ProductsAdvantages\Backend\Requests\BackendProductsAdvantagesRequest;
use Okay\Modules\SimplaMarket\ProductsAdvantages\Extensions\ProductControllerExtension;

return [
    BackendProductsAdvantagesHelper::class => [
        'class' => BackendProductsAdvantagesHelper::class,
        'arguments' => [
            new SR(EntityFactory::class),
            new SR(Image::class),
            new SR(Config::class)
        ]
    ],
    BackendProductsAdvantagesRequest::class => [
        'class' => BackendProductsAdvantagesRequest::class,
        'arguments' => [
            new SR(Request::class)
        ]
    ],
    ProductAdminExtension::class => [
        'class' => ProductAdminExtension::class,
        'arguments' => [
            new SR(Request::class),
            new SR(BackendProductsAdvantagesRequest::class),
            new SR(BackendProductsAdvantagesHelper::class),
            new SR(EntityFactory::class),
            new SR(Design::class)
        ]
    ],
    ProductControllerExtension::class => [
        'class' => ProductControllerExtension::class,
        'arguments' => [
            new SR(EntityFactory::class),
            new SR(Design::class),
            new SR(Router::class)
        ]
    ]
];