<?php

namespace Okay\Modules\Vitalisoft\Analytics\Init;

use Okay\Core\BrowsedProducts;
use Okay\Core\Comparison;
use Okay\Core\Modules\AbstractInit;
use Okay\Core\Modules\EntityField;
use Okay\Core\WishList;
use Okay\Entities\OrdersEntity;
use Okay\Entities\UsersEntity;
use Okay\Helpers\CatalogHelper;
use Okay\Helpers\MainHelper;
use Okay\Helpers\OrdersHelper;
use Okay\Helpers\ProductsHelper;
use Okay\Helpers\RelatedProductsHelper;
use Okay\Helpers\UserHelper;
use Okay\Modules\Vitalisoft\Analytics\Extenders\FrontExtender;
use Okay\Requests\CartRequest;

class Init extends AbstractInit
{

    public function install()
    {
        $this->setBackendMainController('AnalyticsAdmin');
        $cidField = (new EntityField('cid'))->setTypeVarchar(100);
        $sentField = (new EntityField('analytics_sent'))->setTypeTinyInt(1, false);
        $this->migrateEntityField(UsersEntity::class, $cidField);
        $this->migrateEntityField(OrdersEntity::class, $cidField);
        $this->migrateEntityField(OrdersEntity::class, $sentField);
    }

    public function init()
    {
        $this->addPermission('vitalisoft__analytics');
        $this->registerBackendController('AnalyticsAdmin');
        $this->addBackendControllerPermission('AnalyticsAdmin', 'vitalisoft__analytics');
        $this->registerEntityField(UsersEntity::class, 'cid');
        $this->registerEntityField(OrdersEntity::class, 'cid');
        $this->registerEntityField(OrdersEntity::class, 'analytics_sent');

        $this->addFrontBlock('front_after_head_content', 'code_after_head.tpl');
        $this->addFrontBlock('front_start_body_content', 'code_start_body.tpl');

        $this->registerQueueExtension(
            [OrdersEntity::class, 'markedPaid'],
            [FrontExtender::class, 'sendAnalyticsPurchase']
        );
        
        $this->registerQueueExtension(
            [ProductsHelper::class, 'getList'],
            [FrontExtender::class, 'addProductList']
        );
        
        $this->registerQueueExtension(
            [BrowsedProducts::class, 'get'],
            [FrontExtender::class, 'addBrowsedProductsList']
        );
        
        $this->registerQueueExtension(
            [Comparison::class, 'get'],
            [FrontExtender::class, 'addComparisonProductsList']
        );
        
        $this->registerQueueExtension(
            [RelatedProductsHelper::class, 'getRelatedProductsList'],
            [FrontExtender::class, 'addRelatedProductsList']
        );

        $this->registerQueueExtension(
            [WishList::class, 'get'],
            [FrontExtender::class, 'addWishlistProductsList']
        );
        
        $this->registerChainExtension(
            [CatalogHelper::class, 'getAjaxFilterData'],
            [FrontExtender::class, 'getAjaxFilterData']
        );
        
        $this->registerQueueExtension(
            [ProductsHelper::class, 'attachProductData'],
            [FrontExtender::class, 'addProduct']
        );
        
        $this->registerQueueExtension(
            [MainHelper::class, 'commonAfterControllerProcedure'],
            [FrontExtender::class, 'setPurchases']
        );
        
        $this->registerQueueExtension(
            [OrdersHelper::class, 'getOrderPurchasesList'],
            [FrontExtender::class, 'setOrderPurchases']
        );

        $this->registerQueueExtension(
            [UserHelper::class, 'login'],
            [FrontExtender::class, 'set_cid']
        );

        $this->registerQueueExtension(
            [UserHelper::class, 'register'],
            [FrontExtender::class, 'set_cid']
        );

        $this->registerChainExtension(
            [CartRequest::class, 'postOrder'],
            [FrontExtender::class, 'post_cid']
        );
    }
}