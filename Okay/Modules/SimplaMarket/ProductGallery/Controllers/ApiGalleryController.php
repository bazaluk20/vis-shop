<?php


namespace Okay\Modules\SimplaMarket\ProductGallery\Controllers;


use Okay\Controllers\AbstractController;
use Okay\Core\Image;
use Okay\Core\QueryFactory;
use Okay\Core\Request;
use Okay\Core\Router;
use Okay\Modules\SimplaMarket\ProductGallery\Entities\GalleryDirectoriesEntity;
use Okay\Modules\SimplaMarket\ProductGallery\Entities\GalleryImagesEntity;
use Okay\Modules\SimplaMarket\ProductGallery\Services\ImageService;

class ApiGalleryController extends AbstractController
{
    // directory
    public function getDirectory(
        GalleryImagesEntity      $galleryImagesEntity,
        GalleryDirectoriesEntity $galleryDirectoriesEntity,
        Image                    $imageCore,
        $directoryId = 0
    ) {
        $this->applyMiddleware();

        $productId = $this->request->get('product_id', 'integer');
        if (empty($productId)) {
            return $this->response->setContent(json_encode([
                'ok'   => false,
                'data' => [
                    'message' => 'product_id parameter not found'
                ]
            ]), RESPONSE_JSON);
        }

        $directory         = $galleryDirectoriesEntity->get((int) $directoryId);
        $directoryParentId = 0;
        if (!empty($directory->parent_id)) {
            $directoryParentId = $directory->parent_id;
        }

        $subDirectories = $galleryDirectoriesEntity->find(['parent_id' => $directoryId]);
        foreach($subDirectories as $subDirectory) {
            $subDirectory->is_dir = true;
        }

        $images = $galleryImagesEntity->find(['directory_id' => $directoryId]);
        $images = $galleryImagesEntity->attachInProductField($productId, $images);
        foreach($images as $image) {
            $image->src    = $imageCore->getResizeModifier($image->name, 200, 200, false);
            $image->is_dir = false;
        }

        $items = array_merge($subDirectories, $images);
        return $this->response->setContent(json_encode([
            'ok'   => true,
            'data' => [
                'images'              => $items,
                'parent_directory_id' => $directoryParentId
            ]
        ]), RESPONSE_JSON);
    }

    public function createDirectory(
        GalleryDirectoriesEntity $galleryDirectoriesEntity
    ) {
        $this->applyMiddleware();

        $directoryId = $galleryDirectoriesEntity->add([
            'parent_id' => $this->request->post('parentDirectoryId', 'integer'),
            'name'      => $this->request->post('name', 'string')
        ]);

        return $this->response->setContent(json_encode([
            'ok'   => true,
            'data' => [
                'directory_id' => $directoryId
            ]
        ]), RESPONSE_JSON);
    }

    public function deleteDirectory(GalleryDirectoriesEntity $galleryDirectoriesEntity, $directoryId)
    {
        $this->applyMiddleware();
        $galleryDirectoriesEntity->delete((int) $directoryId);
        return $this->response->setContent(json_encode(['ok' => true]), RESPONSE_JSON);
    }

    // images
    public function attachImageToDirectory(GalleryImagesEntity $galleryImagesEntity, $directoryId, $imageId)
    {
        $this->applyMiddleware();
        $galleryImagesEntity->update($imageId, ['directory_id' => $directoryId]);
        return $this->response->setContent(json_encode(['ok' => true]), RESPONSE_JSON);
    }

    public function uploadImage(Image $imageCore, GalleryImagesEntity $galleryImagesEntity, $directoryId)
    {
        $this->applyMiddleware();

        if (empty($_FILES)) {
            exit;
        }

        foreach($_FILES as $file) {
            $filename = $imageCore->uploadImage($file['tmp_name'], $file['name']);
            $galleryImagesEntity->add([
                'directory_id' => $directoryId,
                'name'         => $filename,
            ]);
        }

        return $this->response->setContent(json_encode(['ok'   => true]), RESPONSE_JSON);
    }

    public function dropImage(ImageService $imageService, $imageId)
    {
        $this->applyMiddleware();

        $imageService->deleteImage((int) $imageId);
        return $this->response->setContent(json_encode(['ok'   => true]), RESPONSE_JSON);
    }

    public function attachImageToProduct(GalleryImagesEntity $galleryImagesEntity)
    {
        $this->applyMiddleware();

        $imageId   = $this->request->post('image_id', 'integer');
        $productId = $this->request->post('product_id', 'integer');

        if (empty($imageId) || empty($productId)) {
            return $this->response->setContent(json_encode(['ok' => false]), RESPONSE_JSON);
        }

        $galleryImagesEntity->attachToProduct($imageId, $productId);
        return $this->response->setContent(json_encode(['ok' => true]), RESPONSE_JSON);
    }

    public function detachImageFromProduct(GalleryImagesEntity $galleryImagesEntity)
    {
        $this->applyMiddleware();

        $imageId   = $this->request->post('image_id', 'integer');
        $productId = $this->request->post('product_id', 'integer');

        if (empty($imageId) || empty($productId)) {
            return $this->response->setContent(json_encode(['ok' => false]), RESPONSE_JSON);
        }

        $galleryImagesEntity->detachFromProduct($imageId, $productId);
        return $this->response->setContent(json_encode(['ok' => true]), RESPONSE_JSON);
    }

    private function applyMiddleware()
    {
        if (empty($_SESSION['admin'])) {
            exit;
        }
    }
}