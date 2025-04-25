<?php

namespace Okay\Modules\BazPro\Vaga\Init;

use Okay\Core\Modules\AbstractInit;
use Okay\Core\Modules\EntityField;
use Okay\Entities\ProductsEntity;

class Init extends AbstractInit
{
    public function install()
    {
        $this->setBackendMainController('ModuleAdmin');

        $this->migrateEntityField(ProductsEntity::class, (new EntityField('vaga_in_cat'))->setTypeVarchar(200, true));
    }

    public function init()
    {
        $this->registerEntityField(ProductsEntity::class, 'vaga_in_cat');

        $this->registerBackendController('ModuleAdmin');
        $this->addBackendControllerPermission('ModuleAdmin', 'products');

        $this->addBackendBlock('product_custom_block', 'product_vaga.tpl');
    }
}
