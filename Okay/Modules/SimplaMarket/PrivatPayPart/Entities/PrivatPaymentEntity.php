<?php


namespace Okay\Modules\SimplaMarket\PrivatPayPart\Entities;


use Okay\Core\Entity\Entity;

class PrivatPaymentEntity extends Entity
{
    protected static $fields = [
        'id',
        'order_id',
        'number_of_months',
        'pay_count',
        'value',
        'token',
        'message',
    ];

    protected static $table = 'sm_privat_pp_payment';
    protected static $tableAlias = 'pp';
    
    protected static $defaultOrderFields = [
        'id'
    ];
}