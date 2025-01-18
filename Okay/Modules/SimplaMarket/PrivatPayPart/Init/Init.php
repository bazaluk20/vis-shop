<?php


namespace Okay\Modules\SimplaMarket\PrivatPayPart\Init;


use Okay\Admin\Helpers\BackendCategoriesHelper;
use Okay\Admin\Helpers\BackendExportHelper;
use Okay\Admin\Helpers\BackendImportHelper;
use Okay\Admin\Helpers\BackendProductsHelper;
use Okay\Admin\Requests\BackendCategoriesRequest;
use Okay\Admin\Requests\BackendProductsRequest;
use Okay\Core\Modules\AbstractInit;
use Okay\Core\Modules\EntityField;
use Okay\Entities\CategoriesEntity;
use Okay\Entities\ProductsEntity;
use Okay\Helpers\OrdersHelper;
use Okay\Helpers\PaymentsHelper;
use Okay\Modules\SimplaMarket\PrivatPayPart\Entities\PrivatPaymentEntity;
use Okay\Modules\SimplaMarket\PrivatPayPart\Extenders\BackendExtender;
use Okay\Modules\SimplaMarket\PrivatPayPart\Extenders\FrontExtender;

class Init extends AbstractInit
{
    
    const TO_PAY_PART_FIELD     = 'to_privat_pay_part';
    const MAX_PAY_PP_FIELD      = 'privat_max_pay_pp';
    const MAX_PAY_II_FIELD      = 'privat_max_pay_ii';
    const INIDIVID_PRIVAT_VALUE_CHECKBOX = 'individual_privat_value';
    
    public function install()
    {
        $this->setModuleType(MODULE_TYPE_PAYMENT);

        $this->migrateEntityTable(PrivatPaymentEntity::class, [
            (new EntityField('id'))->setIndexPrimaryKey()->setTypeInt(11, false)->setAutoIncrement(),
            (new EntityField('order_id'))->setTypeInt(11)->setIndex(),
            (new EntityField('number_of_months'))->setTypeTinyInt(1),
            (new EntityField('pay_count'))->setTypeTinyInt(1),
            (new EntityField('value'))->setTypeDecimal('10,2'),
            (new EntityField('token'))->setTypeVarchar(255, true),
            (new EntityField('message'))->setTypeVarchar(255, true),
        ]);

        //  product
        $field = new EntityField(self::TO_PAY_PART_FIELD);
        $field->setTypeTinyInt(1)->setDefault(1);
        $this->migrateEntityField(ProductsEntity::class, $field);

        $field = new EntityField(self::INIDIVID_PRIVAT_VALUE_CHECKBOX);
        $field->setTypeTinyInt(1)->setDefault(0);
        $this->migrateEntityField(ProductsEntity::class, $field);

        $field = new EntityField(self::MAX_PAY_PP_FIELD);
        $field->setTypeTinyInt(1)->setDefault(null);
        $this->migrateEntityField(ProductsEntity::class, $field);
        
        $field = new EntityField(self::MAX_PAY_II_FIELD);
        $field->setTypeTinyInt(1)->setDefault(null);
        $this->migrateEntityField(ProductsEntity::class, $field);

        //  category
        $field = new EntityField(self::MAX_PAY_PP_FIELD);
        $field->setTypeTinyInt(1, null)->setDefault(null);
        $this->migrateEntityField(CategoriesEntity::class, $field);

        $field = new EntityField(self::MAX_PAY_II_FIELD);
        $field->setTypeTinyInt(1, null)->setDefault(null);
        $this->migrateEntityField(CategoriesEntity::class, $field);

        $this->setBackendMainController('DescriptionAdmin');
    }

    public function init() {
        $this->addPermission('sm__privat_pay_part');

        $this->registerEntityField(ProductsEntity::class, self::TO_PAY_PART_FIELD);
        $this->registerEntityField(ProductsEntity::class, self::INIDIVID_PRIVAT_VALUE_CHECKBOX);
        $this->registerEntityField(ProductsEntity::class, self::MAX_PAY_PP_FIELD);
        $this->registerEntityField(ProductsEntity::class, self::MAX_PAY_II_FIELD);

        //  category
        $this->registerEntityField(CategoriesEntity::class, self::MAX_PAY_PP_FIELD);
        $this->registerEntityField(CategoriesEntity::class, self::MAX_PAY_II_FIELD);

        $this->registerBackendController('DescriptionAdmin');
        $this->addBackendControllerPermission('DescriptionAdmin', 'sm__privat_pay_part');
        
        $this->addBackendBlock('product_switch_checkboxes', 'product_pay_part_switch.tpl');
        $this->addBackendBlock('product_general', 'product_pay_part_pay_num.tpl');
        
        $this->addFrontBlock('front_cart_payment', 'cart_calc.tpl');
        $this->addFrontBlock('front_scripts_after_validate', 'cart_calc_js.js');
        $this->addFrontBlock('front_before_footer_content', 'front_before_footer_content.tpl');

        //  добавляеми ico в карточку товара в интегрированный блок ранее через smarty-plugin
        $this->addFrontBlock('pay_part_block_product', 'product_icon.tpl');

        //  добавляеми ico в список товаров в превью товара в интегрированный блок ранее через smarty-plugin
        $this->addFrontBlock('pay_part_block_products_list', 'products_icon_in_list.tpl');

        //  добавляем в категорию поле ввода кол-ва месяцев Оплата частинами Private
        $this->addBackendBlock('category_heading', 'category_privat_part_pay_num.tpl');

        //  добавляем в списке товаров фильтрацию по галочке индивидуальных данных товаров для Моно
        $this->addBackendBlock('products_filter_custom_option', 'products_filter_custom_privat_option.tpl');

        //  добавляем в список иконок действие для перевода и снятия утоварв признака индивидуальногости для Моно
        $this->addBackendBlock('products_icon', 'products_icon_in_list.tpl');

        $this->registerChainExtension(
            [BackendProductsRequest::class, 'postProduct'],
            [BackendExtender::class, 'postProduct']
        );
        
        $this->registerChainExtension(
            [BackendProductsHelper::class, 'getProduct'],
            [BackendExtender::class, 'getProduct']
        );
        
        $this->registerChainExtension(
            [PaymentsHelper::class, 'getCartPaymentsList'],
            [FrontExtender::class, 'getCartPaymentsList']
        );
        
        $this->registerQueueExtension(
            [OrdersHelper::class, 'finalCreateOrderProcedure'],
            [FrontExtender::class, 'addPrivatPaymentData']
        );

        $this->registerChainExtension(
            ['class' => BackendImportHelper::class, 'method' => 'parseProductData'],
            ['class' => BackendExtender::class, 'method' => 'parseProductData']
        );

        $this->registerChainExtension(
            ['class' => BackendExportHelper::class, 'method' => 'getColumnsNames'],
            ['class' => BackendExtender::class, 'method' => 'extendExportColumnsNames']
        );

        $this->registerChainExtension(
            ['class' => BackendImportHelper::class, 'method' => 'getModulesColumnsNames'],
            ['class' => BackendExtender::class, 'method' => 'getModulesColumnsNames']
        );

        $this->registerQueueExtension(
            [BackendCategoriesHelper::class, 'uploadCategoryImage'],
            [BackendExtender::class,         'uploadCategoryImage']
        );

        //  фильтруем товары в списке товаров в админпанели по фильтру
        $this->registerChainExtension(
            [BackendProductsHelper::class, 'buildFilter'],
            [BackendExtender::class,       'buildFilter']
        );

        //  обновляем данные для категории
        $this->registerChainExtension(
            [BackendCategoriesRequest::class, 'postCategory'],
            [BackendExtender::class,          'postCategory']
        );

        $this->addBackendBlock('import_fields_association', 'import_fields_association.tpl');
        
    }

    public function update_1_2_2()
    {
        $field = new EntityField(self::INIDIVID_PRIVAT_VALUE_CHECKBOX);
        $field->setTypeTinyInt(1)->setDefault(0);
        $this->migrateEntityField(ProductsEntity::class, $field);

        //  category
        $field = new EntityField(self::MAX_PAY_PP_FIELD);
        $field->setTypeTinyInt(1, null)->setDefault(null);
        $this->migrateEntityField(CategoriesEntity::class, $field);

        $field = new EntityField(self::MAX_PAY_II_FIELD);
        $field->setTypeTinyInt(1, null)->setDefault(null);
        $this->migrateEntityField(CategoriesEntity::class, $field);
    }
}