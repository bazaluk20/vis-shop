<?php


namespace Okay\Modules\SimplaMarket\ProductGallery\Extensions;


use Okay\Core\EntityFactory;
use Okay\Core\Modules\Extender\ExtensionInterface;
use Okay\Modules\SimplaMarket\ProductGallery\Entities\GalleryImagesEntity;

class ProductsHelperExtension implements ExtensionInterface
{
    /**
     * @var GalleryImagesEntity
     */
    private $galleryImagesEntity;

    public function __construct(EntityFactory $entityFactory)
    {
        $this->galleryImagesEntity = $entityFactory->get(GalleryImagesEntity::class);
    }

    public function extendAttachProductData($product)
    {
        $galleryImages         = $this->galleryImagesEntity->getProductImages((int) $product->id);
        $preparedGalleryImages = $this->prepareImagesData($galleryImages, $product->id);

        if (empty($product->images)) {
            $product->images = $preparedGalleryImages;
        } else {
            $product->images = array_merge($product->images, $preparedGalleryImages);
        }

        return $product;
    }

    private function prepareImagesData(array $images, $productId)
    {
        $preparedImages = [];
        foreach($images as $image) {
            $preparedImage = new \stdClass();
            $preparedImage->id         = $image->id;
            $preparedImage->name       = '';
            $preparedImage->filename   = $image->name;
            $preparedImage->product_id = $productId;
            $preparedImage->position   = 0;

            $preparedImages[] = $preparedImage;
        }

        return $preparedImages;
    }
}