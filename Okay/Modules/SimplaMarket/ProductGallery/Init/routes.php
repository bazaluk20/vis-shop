<?php

use Okay\Modules\SimplaMarket\ProductGallery\Controllers\ApiGalleryController;

return [
    'SimplaMarket.ProductGallery.DeleteDirectory' => [
        'slug' => '/api/sm/products-gallery/directory/delete/{$directoryId}',
        'params' => [
            'controller' => ApiGalleryController::class,
            'method' => 'deleteDirectory',
        ],
    ],
    'SimplaMarket.ProductGallery.CreateDirectory' => [
        'slug' => '/api/sm/products-gallery/directory/create',
        'params' => [
            'controller' => ApiGalleryController::class,
            'method' => 'createDirectory',
        ],
    ],
    'SimplaMarket.ProductGallery.GetDirectory' => [
        'slug' => '/api/sm/products-gallery/directory/{$directoryId}',
        'params' => [
            'controller' => ApiGalleryController::class,
            'method' => 'getDirectory',
        ],
    ],
    'SimplaMarket.ProductGallery.UploadImage' => [
        'slug' => '/api/sm/products-gallery/image/upload/{$directoryId}',
        'params' => [
            'controller' => ApiGalleryController::class,
            'method' => 'uploadImage',
        ],
    ],
    'SimplaMarket.ProductGallery.DropImage' => [
        'slug' => '/api/sm/products-gallery/image/delete/{$imageId}',
        'params' => [
            'controller' => ApiGalleryController::class,
            'method' => 'dropImage',
        ],
    ],
    'SimplaMarket.ProductGallery.AttachImageToProduct' => [
        'slug' => '/api/sm/products-gallery/image/attach',
        'params' => [
            'controller' => ApiGalleryController::class,
            'method' => 'attachImageToProduct',
        ],
    ],
    'SimplaMarket.ProductGallery.DetachImageFromProduct' => [
        'slug' => '/api/sm/products-gallery/image/detach',
        'params' => [
            'controller' => ApiGalleryController::class,
            'method' => 'detachImageFromProduct',
        ],
    ],
];