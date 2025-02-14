<?php


namespace Okay\Modules\SimplaMarket\ProductsAdvantages\Init;


use Okay\Admin\Controllers\ProductAdmin;
use Okay\Admin\Helpers\BackendProductsHelper;
use Okay\Admin\Helpers\BackendValidateHelper;
use Okay\Admin\Requests\BackendProductsRequest;
use Okay\Controllers\ProductController;
use Okay\Core\Modules\AbstractInit;
use Okay\Core\Modules\EntityField;
use Okay\Core\Router;
use Okay\Entities\ProductsEntity;
use Okay\Helpers\ProductsHelper;
use Okay\Modules\SimplaMarket\ProductsAdvantages\Extensions\ProductAdminExtension;
use Okay\Modules\SimplaMarket\ProductsAdvantages\Entities\ProductsAdvanatgesEntity;
use Okay\Modules\SimplaMarket\ProductsAdvantages\Extensions\ProductControllerExtension;

class Init extends AbstractInit
{
    public function install()
    {
        $this->setBackendMainController('DescriptionAdmin');

        if (!is_dir('files/originals/products_advantages')) {
            mkdir('files/originals/products_advantages');
        }

        if (!is_dir('files/resized/products_advantages')) {
            mkdir('files/resized/products_advantages');
        }

        $this->migrateEntityTable(ProductsAdvanatgesEntity::class, [
            (new EntityField('id'))->setIndexPrimaryKey()->setTypeInt(11, false)->setAutoIncrement(),
            (new EntityField('product_id'))->setTypeInt(11),
            (new EntityField('filename'))->setTypeVarchar(255, true),
            (new EntityField('text'))->setTypeText()->setIsLang(),
            (new EntityField('position'))->setTypeInt(11, false)->setDefault(0)
        ]);
    }

    public function init()
    {
        $this->addPermission('simplamarket__products_advantages');
        $this->registerBackendController('DescriptionAdmin');
        $this->addBackendControllerPermission('DescriptionAdmin', 'simplamarket__products_advantages');

        $this->addResizeObject('original_products_advantages_dir', 'resized_products_advantages_dir');

        $this->registerQueueExtension(
            ['class' => ProductAdmin::class, 'method' => 'fetch'],
            ['class' => ProductAdminExtension::class, 'method' => 'setProductAdmin']
        );

        $this->registerQueueExtension(
            ['class' => BackendValidateHelper::class, 'method' => 'getProductValidateError'],
            ['class' => ProductAdminExtension::class, 'method' => 'handleExistingProductAdvantages']
        );

        $this->registerQueueExtension(
            ['class' => BackendProductsHelper::class, 'method' => 'getProduct'],
            ['class' => ProductAdminExtension::class, 'method' => 'getProduct']
        );

        $this->registerQueueExtension(
            ['class' => ProductsEntity::class, 'method' => 'delete'],
            ['class' => ProductAdminExtension::class, 'method' => 'deleteByProductsIds']
        );

        $this->registerQueueExtension(
            ['class' => ProductsHelper::class, 'method' => 'attachProductData'],
            ['class' => ProductControllerExtension::class, 'method' => 'assignProductAdvantages']
        );

        $this->addBackendBlock('product_custom_block', 'productAdvantages.tpl');
    }
}