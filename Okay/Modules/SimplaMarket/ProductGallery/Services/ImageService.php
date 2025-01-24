<?php


namespace Okay\Modules\SimplaMarket\ProductGallery\Services;


use Okay\Core\Config;
use Okay\Core\Image;
use Okay\Core\EntityFactory;
use Okay\Modules\SimplaMarket\ProductGallery\Entities\GalleryImagesEntity;

class ImageService
{
    /**
     * @var Image
     */
    private $imageCore;

    /**
     * @var string
     */
    private $rootDir;

    /**
     * @var GalleryImagesEntity
     */
    private $galleryImagesEntity;

    /**
     * @var Config
     */
    private $config;

    public function __construct(
        EntityFactory $entityFactory,
        Image         $imageCore,
        Config        $config,
        $rootDir
    ){
        $this->config              = $config;
        $this->rootDir             = $rootDir;
        $this->imageCore           = $imageCore;
        $this->galleryImagesEntity = $entityFactory->get(GalleryImagesEntity::class);
    }

    public function uploadImage($file, $directoryId)
    {
        $filename = $this->imageCore->uploadImage($file['tmp_name'], $file['name']);
        return $this->galleryImagesEntity->add([
            'directory_id' => $directoryId,
            'name'         => $filename,
        ]);
    }

    public function deleteImage($id)
    {
        $originalDir = $this->config->get('original_images_dir');
        $resizedDir  = $this->config->get('resized_images_dir');

        $image    = $this->galleryImagesEntity->get((int) $id);
        $filename = $image->name;
        $file     = pathinfo($filename, PATHINFO_FILENAME);
        $ext      = pathinfo($filename, PATHINFO_EXTENSION);

        if (!empty($resizedDir)) {
            $rezisedImages = glob($this->rootDir . $resizedDir . $file . ".*x*." . $ext);
            if (is_array($rezisedImages)) {
                foreach ($rezisedImages as $f) {
                    @unlink($f);
                }
            }
        }

        @unlink($this->rootDir. $originalDir . $filename);
        return $this->galleryImagesEntity->delete((int) $id);
    }
}