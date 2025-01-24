<?php

use Okay\Core\Design;
use Okay\Core\EntityFactory;
use Okay\Core\Image;
use Okay\Core\Languages;
use Okay\Core\OkayContainer\Reference\ServiceReference;
use Okay\Core\QueryFactory;
use Okay\Core\Request;
use Okay\Modules\Vitalisoft\ProductsGroups\Extenders\FrontExtender;
use Okay\Modules\Vitalisoft\ProductsGroups\Helpers\ProductsGroupsHelper;
use Psr\Log\LoggerInterface;

return [
    ProductsGroupsHelper::class => [
        'class' => ProductsGroupsHelper::class,
        'arguments' => [
            new ServiceReference(LoggerInterface::class),
            new ServiceReference(Languages::class),
            new ServiceReference(Image::class),
            new ServiceReference(Request::class),
            new ServiceReference(Design::class),
            new ServiceReference(QueryFactory::class),
            new ServiceReference(EntityFactory::class),
        ]
    ],
    FrontExtender::class => [
        'class' => FrontExtender::class,
        'arguments' => [
            new ServiceReference(ProductsGroupsHelper::class),
            new ServiceReference(Design::class),
        ]
    ]
];