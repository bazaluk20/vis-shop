<?php


namespace Okay\Modules\SimplaMarket\Sets\Entities;


use Okay\Core\Entity\Entity;

class DisplayObjectsEntity extends Entity
{
    protected static $fields = [
        'id',
        'set_id',
        'object_id',
    ];

    protected static $defaultOrderFields = [];

    protected static $table = '__simplamarket__sets__display_objects';
    protected static $tableAlias = 'ssd';

    public function updateObjectsBySetId($setId, $newObjectIds)
    {
        $currentObjectIds = $this->cols(['object_id'])->find(['set_id' => $setId]);

        $objectIdsToDelete = array_diff($currentObjectIds, $newObjectIds);
        $this->queryFactory
            ->newDelete()
            ->from(self::getTable())
            ->where('set_id = :set_id')
            ->where('object_id IN(:object_ids)')
            ->bindValue('set_id', $setId)
            ->bindValue('object_ids', $objectIdsToDelete)
            ->execute();

        $objectIdsToAdd = array_diff($newObjectIds, $currentObjectIds);
        foreach($objectIdsToAdd as $objectId) {
            $this->add([
                'set_id'    => $setId,
                'object_id' => $objectId
            ]);
        }
    }
}