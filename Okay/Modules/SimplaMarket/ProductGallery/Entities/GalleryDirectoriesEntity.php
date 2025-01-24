<?php


namespace Okay\Modules\SimplaMarket\ProductGallery\Entities;


use Okay\Core\Entity\Entity;
use Okay\Core\ServiceLocator;
use Okay\Modules\SimplaMarket\ProductGallery\Services\ImageService;

class GalleryDirectoriesEntity extends Entity
{
    protected static $fields = [
        'id',
        'parent_id',
        'name',
    ];

    protected static $table = '__s_m_product_gallery__directory_images';
    protected static $tableAlias = 's_m_gi';

    public function delete($id)
    {
        if (is_array($id)) {
            throw new \Exception('Current entity working only with single ids');
        }

        $serviceLocator = ServiceLocator::getInstance();

        /** @var ImageService $imageService */
        $imageService = $serviceLocator->getService(ImageService::class);

        /** @var GalleryImagesEntity $galleryImagesEntity */
        $galleryImagesEntity = $this->entity->get(GalleryImagesEntity::class);

        $directoryIds = $this->matchAllSubDirectoryIds($id);
        $imageIds     = $galleryImagesEntity->cols(['id'])->find(['directory_id' => $directoryIds]);
        foreach($imageIds as $imageId) {
            $imageService->deleteImage($imageId);
        }

        return parent::delete($directoryIds);
    }

    private function matchAllSubDirectoryIds($id)
    {
        $subdirectoriesTree = $this->buildSubDirectoryTree($id);

        $subDirectoryIds = [];
        $traversalTree = function($tree) use (&$subDirectoryIds, &$traversalTree) {
            if (empty($tree->id)) {
                return;
            }

            $subDirectoryIds[] = $tree->id;
            if (empty($tree->sub_directories) || !is_array($tree->sub_directories)) {
                return;
            }

            foreach($tree->sub_directories as $subDirectoryTree) {
                $traversalTree($subDirectoryTree);
            }
        };
        $traversalTree($subdirectoriesTree);
        return $subDirectoryIds;
    }

    private function buildSubDirectoryTree($rootId)
    {
        $directories = $this->mappedBy('id')->find();

        $directoriesTree = [];
        foreach($directories as &$directory) {
            if ($directory->id == $rootId) {
                $directoriesTree[$directory->id] = $directory;
            } elseif (!empty($directories[$directory->parent_id])) {
                $directories[$directory->parent_id]->sub_directories[] = $directory;
            }
        }

        return reset($directoriesTree);
    }
}