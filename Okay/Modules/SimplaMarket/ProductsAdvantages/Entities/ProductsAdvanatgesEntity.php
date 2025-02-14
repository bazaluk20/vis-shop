<?php


namespace Okay\Modules\SimplaMarket\ProductsAdvantages\Entities;


use Okay\Core\Entity\Entity;

class ProductsAdvanatgesEntity extends Entity
{
    protected static $fields = [
        'id',
        'product_id',
        'filename',
        'position',
    ];

    protected static $langFields = [
        'text',
    ];

    protected static $defaultOrderFields = [
        'position ASC',
    ];

    protected static $table = '__simplamarket__products_advantages';
    protected static $langObject = 'product_advantage';
    protected static $langTable = 'simplamarket__products_advantages';
    protected static $tableAlias = 'sm_pa';

}
