<?php


namespace Okay\Modules\SimplaMarket\ProductsAdvantages\Backend\Helpers;


use Okay\Core\Config;
use Okay\Core\EntityFactory;
use Okay\Core\Image;
use Okay\Modules\SimplaMarket\ProductsAdvantages\Entities\ProductsAdvanatgesEntity;

class BackendProductsAdvantagesHelper
{
    private $productsAdvantagesEntity;
    private $imageCore;
    private $config;

    public function __construct(
        EntityFactory $entityFactory,
        Image $imageCore,
        Config $config
    )
    {
        $this->productsAdvantagesEntity = $entityFactory->get(ProductsAdvanatgesEntity::class);
        $this->imageCore = $imageCore;
        $this->config = $config;
    }

    public function updateProductAdvantage(
        $advantageId,
        $updates,
        $advantageImagesToUpload,
        $advantageImagesToDelete
    )
    {
        if (in_array($advantageId, $advantageImagesToDelete)) {
            $this->deleteProductAdvantageImage($advantageId);
        }

        if (in_array($advantageId, array_keys($advantageImagesToUpload))) {
            $this->uploadProductAdvantageImage($advantageId, $advantageImagesToUpload[$advantageId]);
        }

        $this->productsAdvantagesEntity->update($advantageId, $updates);
    }

    public function sortPositionsProductAdvantages($positions)
    {
        $positions = (array) $positions;
        $ids       = array_keys($positions);
        sort($positions);
        return [$ids, $positions];
    }

    public function updatePositionsProductAdvantages($ids, $positions)
    {
        foreach ($positions as $i => $position) {
            $this->productsAdvantagesEntity->update($ids[$i], ['position' => (int)$position]);
        }
    }

    public function deleteProductAdvantages($ids)
    {
        foreach ($ids as $id) {
            $this->deleteProductAdvantageImage((int) $id);
        }
        $result = $this->productsAdvantagesEntity->delete($ids);

        return $result;
    }

    public function uploadProductAdvantageImage($advantageId, $fileImage)
    {
        if (!empty($fileImage['name']) &&
            ($filename = $this->imageCore->uploadImage(
                $fileImage['tmp_name'],
                $fileImage['name'],
                $this->config->original_products_advantages_dir))
        ) {
            $this->imageCore->deleteImage(
                $advantageId,
                'filename',
                ProductsAdvanatgesEntity::class,
                $this->config->original_products_advantages_dir,
                $this->config->resized_products_advantages_dir
            );

            $this->productsAdvantagesEntity->update($advantageId, ['filename' => $filename]);
        }
    }

    public function deleteProductAdvantageImage($advantageId)
    {
        $this->imageCore->deleteImage(
            $advantageId,
            'filename',
            ProductsAdvanatgesEntity::class,
            $this->config->original_products_advantages_dir,
            $this->config->resized_products_advantages_dir
        );

        $this->productsAdvantagesEntity->update($advantageId, ['filename' => '']);
    }
}