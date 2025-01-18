<?php

namespace Okay\Modules\SimplaMarket\DoNotCallBack\Init;

use Okay\Core\Modules\AbstractInit;
use Okay\Core\Modules\EntityField;
use Okay\Entities\OrdersEntity;
use Okay\Helpers\OrdersHelper;
use Okay\Modules\SimplaMarket\DoNotCallBack\Extenders\FrontExtender;



class Init extends AbstractInit{

    public function install(){

        $this->setBackendMainController('DescriptionAdmin');
        $this->migrateEntityField(OrdersEntity::class, (new EntityField('do_not_call_back'))->setTypeInt(1)->setDefault(0));
    }

    public function init(){

        $this->registerBackendController('DescriptionAdmin');
        $this->addBackendControllerPermission('DescriptionAdmin', 'users');

        $this->registerEntityField(OrdersEntity::class, 'do_not_call_back');

        $this->registerChainExtension(
            [OrdersHelper::class, 'finalCreateOrderProcedure'],
            [FrontExtender::class, 'saveDoNotCallBack']
        );

    }
}