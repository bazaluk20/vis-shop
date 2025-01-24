<?php


namespace Okay\Modules\SimplaMarket\Sets\Init;


use Okay\Admin\Helpers\BackendOrdersHelper;
use Okay\Admin\Helpers\BackendProductsHelper;
use Okay\Admin\Requests\BackendProductsRequest;
use Okay\Core\Cart;
use Okay\Core\Modules\EntityField;
use Okay\Core\Modules\AbstractInit;
use Okay\Entities\PurchasesEntity;
use Okay\Helpers\CartHelper;
use Okay\Helpers\OrdersHelper;
use Okay\Modules\SimplaMarket\Sets\Entities\SetsEntity;
use Okay\Modules\SimplaMarket\Sets\Entities\SetItemsEntity;
use Okay\Modules\SimplaMarket\Sets\Entities\DisplayObjectsEntity;
use Okay\Modules\SimplaMarket\Sets\Extenders\BackendExtender;
use Okay\Modules\SimplaMarket\Sets\Extenders\FrontExtender;
use Okay\Modules\SimplaMarket\Sets\Extensions\BackendOrdersHelperExtension;
use Okay\Modules\SimplaMarket\Sets\Extensions\CartExtension;
use Okay\Modules\SimplaMarket\Sets\Extensions\CartHelperExtension;
use Okay\Modules\SimplaMarket\Sets\Extensions\OrdersHelperExtension;
use Okay\Modules\SimplaMarket\Sets\Helpers\CartSetHelper;

class Init extends AbstractInit
{
    public function install()
    {
        $this->setBackendMainController('DescriptionAdmin');
        $this->migrateEntityTable(SetsEntity::class, [
            (new EntityField('id'))->setIndexPrimaryKey()->setTypeInt(11, false)->setAutoIncrement(),
            (new EntityField('name'))->setTypeVarchar(255)->setIsLang(),
            (new EntityField('annotation'))->setTypeText()->setIsLang(),
            (new EntityField('date_from'))->setTypeTimestamp()->setNullable(),
            (new EntityField('date_to'))->setTypeTimestamp()->setNullable(),
            (new EntityField('show_type'))->setTypeVarchar(40)->setNullable(),
            (new EntityField('visible'))->setTypeTinyInt(1)->setNullable(),
            (new EntityField('include'))->setTypeTinyInt(1)->setNullable(),
            (new EntityField('position'))->setTypeInt(11)->setNullable()
        ]);

        $this->migrateEntityTable(SetItemsEntity::class, [
            (new EntityField('id'))->setIndexPrimaryKey()->setTypeInt(11, false)->setAutoIncrement(),
            (new EntityField('set_id'))->setTypeInt(11),
            (new EntityField('variant_id'))->setTypeInt(11),
            (new EntityField('product_id'))->setTypeInt(11),
            (new EntityField('amount'))->setTypeInt(11)->setNullable(),
            (new EntityField('discount_type'))->setTypeVarchar(20)->setNullable(),
            (new EntityField('discount'))->setTypeDecimal('14,2')->setNullable(),
            (new EntityField('position'))->setTypeInt(11)->setNullable(),
        ]);

        $this->migrateEntityTable(DisplayObjectsEntity::class, [
            (new EntityField('id'))->setIndexPrimaryKey()->setTypeInt(11, false)->setAutoIncrement(),
            (new EntityField('set_id'))->setTypeInt(11),
            (new EntityField('object_id'))->setTypeInt(11),
        ]);

        $this->migrateEntityField(PurchasesEntity::class,
            (new EntityField('sm_sets_set_id'))->setTypeInt(11));

        $this->migrateEntityField(PurchasesEntity::class,
            (new EntityField('sm_sets_target_variant_id'))->setTypeInt(11));
    }

    public function init()
    {
        $this->registerEntityField(PurchasesEntity::class, 'sm_sets_set_id');
        $this->registerEntityField(PurchasesEntity::class, 'sm_sets_target_variant_id');

        $this->addPermission('simplamarket_sets');

        $this->registerBackendController('DescriptionAdmin');
        $this->addBackendControllerPermission('DescriptionAdmin', 'simplamarket_sets');

        $this->registerBackendController('SetsAdmin');
        $this->addBackendControllerPermission('SetsAdmin', 'simplamarket_sets');

        $this->registerBackendController('SetAdmin');
        $this->addBackendControllerPermission('SetAdmin', 'simplamarket_sets');

        $this->registerQueueExtension(
            [Cart::class,          'get'],
            [CartExtension::class, 'extendGet']);

//        $this->registerQueueExtension(
//            [Cart::class,          'getPurchases'],
//            [CartExtension::class, 'getPurchases']);

        $this->registerQueueExtension(
            [Cart::class,          'updateItem'],
            [CartExtension::class, 'extendUpdateItem']);

        $this->registerChainExtension(
            [Cart::class,          'deleteItem'],
            [CartExtension::class, 'extendDeleteItem']);

        $this->registerChainExtension(
            [Cart::class,          'addItem'],
            [CartExtension::class, 'extendAddItem']);

        $this->registerChainExtension(
            [Cart::class,          'updateTotals'],
            [FrontExtender::class, 'updateTotals']);

        $this->registerChainExtension(
            [Cart::class,          'applyPurchasesDiscounts'],
            [FrontExtender::class, 'applyPurchasesDiscounts']);

        $this->registerChainExtension(
            [CartHelper::class,          'prepareCart'],
            [CartHelperExtension::class, 'extendPrepareCart']);

        $this->registerChainExtension(
            [BackendOrdersHelper::class,          'findOrderPurchases'],
            [BackendOrdersHelperExtension::class, 'extendFindOrderPurchases']);

        $this->registerChainExtension(
            [BackendProductsHelper::class,          'getProduct'],
            [BackendExtender::class, 'postProduct']);

        $this->registerChainExtension(
            [OrdersHelper::class,          'getOrderPurchasesList'],
            [OrdersHelperExtension::class, 'extendGetOrderPurchasesList']);

        $this->registerQueueExtension(
            [Cart::class,          'clear'],
            [CartExtension::class, 'extendClear']);

        $this->addBackendBlock('order_purchase_name', 'set_in_order.tpl');

        $this->addBackendBlock('email_order_admin_purchase_name', 'email_order_set.tpl');

        $this->addBackendBlock('front_email_order_user_purchase_name', 'email_order_set.tpl');

        $this->addBackendBlock('order_custom_block', 'error_killer.tpl');

        $this->extendBackendMenu('left_catalog', [
            'left_sets_title' => ['SetsAdmin', 'SetAdmin']
        ]);

        $this->addBackendBlock('product_custom_block', 'products_sets.tpl');

        $this->extendUpdateObject('SimplaMarket.Sets.SetsEntity', 'simplamarket_sets', SetsEntity::class);
    }
}