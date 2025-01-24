<?php


namespace Okay\Modules\SimplaMarket\Sets\Entities;


use Okay\Core\Entity\Entity;

class SetItemsEntity extends Entity
{
    protected static $fields = [
        'id',
        'set_id',
        'variant_id',
        'product_id',
        'amount',
        'discount_type',
        'discount',
        'position',
    ];

    protected static $defaultOrderFields = [
        'position'
    ];

    protected static $table = '__simplamarket__sets__set_items';
    protected static $tableAlias = 'sssi';

    public function deleteAllNotInByIds($setId, $ids)
    {
        if (empty($setId)) {
            return;
        }

        $this->queryFactory
             ->newDelete()
             ->from(self::getTable())
             ->where('id NOT IN(:ids)')
             ->where('set_id = :set_id')
             ->bindValue('ids', $ids)
             ->bindValue('set_id', $setId)
             ->execute();
    }
}