<?php


namespace Okay\Modules\SimplaMarket\Sets\Entities;


use Okay\Core\Entity\Entity;

class SetsEntity extends Entity
{
    protected static $fields = [
        'id',
        'date_from',
        'date_to',
        'show_type',
        'visible',
        'include',
        'position',
        'name',
        'annotation',
    ];

    protected static $langFields = [
        'name',
        'annotation',
    ];

    protected static $defaultOrderFields = [
        'position'
    ];

    protected static $table = '__simplamarket__sets__sets';
    protected static $langTable = 'simplamarket__sets__sets';
    protected static $langObject = 'set';
    protected static $tableAlias = 'sss';


    protected function filter__keyword($keywords)
    {
        $keywords = explode(' ', $keywords);

        $langAlias = $this->lang->getLangAlias(
            $this->getTableAlias()
        );

        foreach ($keywords as $keyNum=>$keyword) {
            $keyword = trim($keyword);

            $keywordFilter = [];
            $keywordFilter[] = "{$langAlias}.name LIKE :keyword_name_{$keyNum}";

            $this->select->bindValues([
                "keyword_name_{$keyNum}" => '%' . $keyword . '%',
            ]);

            $this->select->where('(' . implode(' OR ', $keywordFilter) . ')');
        }
    }
}