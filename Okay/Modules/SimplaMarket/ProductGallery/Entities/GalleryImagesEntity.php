<?php


namespace Okay\Modules\SimplaMarket\ProductGallery\Entities;


use Okay\Core\Entity\Entity;

class GalleryImagesEntity extends Entity
{
    protected $productId = null;

    protected static $fields = [
        'id',
        'directory_id',
        'name',
    ];

    protected static $table = '__s_m_product_gallery__images';

    protected static $imagesProductsTable = '__s_m_product_gallery__products_images';

    protected static $tableAlias = 's_m_gi';

    public function attachInProductField($productId, $images)
    {
        $imageIds = array_map(function($image) {
            return $image->id;
        }, $images);

        $select = $this->queryFactory->newSelect();
        $inProductImageIds = $select->cols(['image_id'])->from(self::$imagesProductsTable)
            ->where('image_id IN (?)', $imageIds)
            ->where('product_id = ?', $productId)
            ->results('image_id');

        foreach($images as $image) {
            if (in_array($image->id, $inProductImageIds)) {
                $image->in_product = true;
            } else {
                $image->in_product = false;
            }
        }

        return $images;
    }

    public function getProductImages($productId)
    {
        $imageIds = $this->getImageIdsAttachedProduct($productId);
        return $this->find(['id' => $imageIds]);
    }

    public function attachToProduct($imageId, $productId)
    {
        $insert = $this->queryFactory->newInsert();
        $insert->ignore()
            ->into(self::$imagesProductsTable)
            ->cols([
            'image_id'   => $imageId,
            'product_id' => $productId,
        ])
        ->execute();
    }

    public function detachFromProduct($imageId, $productId)
    {
        $delete = $this->queryFactory->newDelete();
        $delete->from(self::$imagesProductsTable)
            ->where('image_id = :image_id')
            ->where('product_id = :product_id')
            ->bindValue('image_id', $imageId)
            ->bindValue('product_id', $productId)
            ->execute();
    }

    private function getImageIdsAttachedProduct($productId)
    {
        $select = $this->queryFactory->newSelect();
        return $select->cols(['image_id'])
            ->from(self::$imagesProductsTable)
            ->where('product_id = ?', $productId)
            ->results('image_id');
    }
}
