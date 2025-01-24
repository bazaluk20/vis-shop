<?php


namespace Okay\Modules\SimplaMarket\ProductGallery\Init;


use Okay\Core\Modules\EntityField;
use Okay\Core\Modules\AbstractInit;
use Okay\Helpers\ProductsHelper;
use Okay\Modules\SimplaMarket\ProductGallery\Entities\GalleryDirectoriesEntity;
use Okay\Modules\SimplaMarket\ProductGallery\Entities\GalleryImagesEntity;
use Okay\Modules\SimplaMarket\ProductGallery\Extensions\ProductsHelperExtension;

class Init extends AbstractInit
{
    public function install()
    {
        $this->setBackendMainController('DescriptionAdmin');

        $this->migrateEntityTable(GalleryDirectoriesEntity::class, [
            (new EntityField('id'))->setIndexPrimaryKey()->setTypeInt(11, false)->setAutoIncrement(),
            (new EntityField('parent_id'))->setTypeInt(11)->setNullable(),
            (new EntityField('name'))->setTypeVarchar(255)->setNullable(),
        ]);

        $this->migrateEntityTable(GalleryImagesEntity::class, [
            (new EntityField('id'))->setIndexPrimaryKey()->setTypeInt(11, false)->setAutoIncrement(),
            (new EntityField('directory_id'))->setTypeInt(11)->setNullable(),
            (new EntityField('name'))->setTypeVarchar(255)->setNullable(),
        ]);

        $this->migrateCustomTable('__s_m_product_gallery__products_images', [
            (new EntityField('image_id'))->setTypeInt(11),
            (new EntityField('product_id'))->setTypeInt(255),
        ]);
    }

    public function init()
    {
        $this->registerBackendController('DescriptionAdmin');
        $this->addBackendControllerPermission('DescriptionAdmin', 'products');

        $this->registerChainExtension(
            [ProductsHelper::class,          'attachProductData'],
            [ProductsHelperExtension::class, 'extendAttachProductData']);

        $this->addBackendBlock('product_images', 'product_gallery.tpl');
    }
}