<?php


use Okay\Core\Image;
use Okay\Core\Config;
use Okay\Core\EntityFactory;
use Okay\Core\OkayContainer\Reference\ServiceReference as SR;
use Okay\Core\OkayContainer\Reference\ParameterReference as PR;
use Okay\Modules\SimplaMarket\ProductGallery\Services\ImageService;
use Okay\Modules\SimplaMarket\ProductGallery\Extensions\ProductsHelperExtension;

return [
    ImageService::class => [
        'class' => ImageService::class,
        'arguments' => [
            new SR(EntityFactory::class),
            new SR(Image::class),
            new SR(Config::class),
            new PR('root_dir'),
        ],
    ],
    ProductsHelperExtension::class => [
        'class' => ProductsHelperExtension::class,
        'arguments' => [
            new SR(EntityFactory::class),
        ],
    ],
];