<?php


use Okay\Modules\SimplaMarket\Sets\Controllers\SearchController;
use Okay\Modules\SimplaMarket\Sets\Controllers\CartSetController;

return [
    'SimplaMarket.Sets.SearchProducts' => [
        'slug' => '/simplamarket/sets/product/search',
        'params' => [
            'controller' => SearchController::class,
            'method' => 'searchProducts',
        ],
        'to_front' => true,
    ],
    'SimplaMarket.Sets.SearchCategories' => [
        'slug' => '/simplamarket/sets/category/search',
        'params' => [
            'controller' => SearchController::class,
            'method' => 'searchCategories',
        ],
        'to_front' => true,
    ],
    'SimplaMarket.Sets.AddToCart' => [
        'slug' => '/simplamarket/sets/add-to-cart',
        'params' => [
            'controller' => CartSetController::class,
            'method' => 'addToCart',
        ],
    ],
];