<?php


namespace Okay\Modules\SimplaMarket\Redirects\Entities;

use Okay\Core\Entity\Entity;

class RedirectsEntity extends Entity
{
    protected static $fields = [
        'id',
        'name',
        'url_from',
        'url_to',
        'enabled',
        'status',
    ];

    protected static $defaultOrderFields = [
        'id',
    ];

    protected static $searchFields = [
        'name',
        'url_from',
        'url_to',
    ];

    protected static $table = '__simplamarket__redirects__redirects';
    
    protected static $tableAlias = 'eb_red';

    public function filter__disabled()
    {
        $this->select->where('enabled=0 OR enabled IS NULL');
    }
}