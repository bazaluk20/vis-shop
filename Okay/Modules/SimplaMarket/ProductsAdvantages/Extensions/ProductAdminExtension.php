<?php


namespace Okay\Modules\SimplaMarket\ProductsAdvantages\Extensions;


use Okay\Core\Design;
use Okay\Core\EntityFactory;
use Okay\Core\Modules\Extender\ExtensionInterface;
use Okay\Core\Request;
use Okay\Core\Router;
use Okay\Modules\SimplaMarket\ProductsAdvantages\Entities\ProductsAdvanatgesEntity;
use Okay\Modules\SimplaMarket\ProductsAdvantages\Backend\Helpers\BackendProductsAdvantagesHelper;
use Okay\Modules\SimplaMarket\ProductsAdvantages\Backend\Requests\BackendProductsAdvantagesRequest;

class ProductAdminExtension implements ExtensionInterface
{
    private $request;
    private $design;

    private $productsAdvantagesRequest;
    private $productsAdvantagesHelper;

    private $productsAdvantagesEntity;

    private $productAdmin = false;
    private $product;

    public function __construct(
        Request $request,
        BackendProductsAdvantagesRequest $productsAdvantagesRequest,
        BackendProductsAdvantagesHelper $productsAdvantagesHelper,
        EntityFactory $entityFactory,
        Design $design
    )
    {
        $this->request = $request;
        $this->productsAdvantagesRequest = $productsAdvantagesRequest;
        $this->productsAdvantagesHelper = $productsAdvantagesHelper;
        $this->productsAdvantagesEntity = $entityFactory->get(ProductsAdvanatgesEntity::class);
        $this->design = $design;
    }

    public function setProductAdmin()
    {
        $this->productAdmin = true;
    }

    public function getProduct($product)
    {
        if (isset($product->id) ) {
            if ($this->productAdmin) {
                $this->product = $product;

                if ($this->request->method('POST')) {
                    $this->handleNewProductAdvantages();
                }

                $productAdvantages = $this->productsAdvantagesEntity->find(['product_id' => $product->id]);
                $this->design->assign('product_advantages', $productAdvantages);
            }
        }
    }

    public function handleExistingProductAdvantages($error)
    {
        if ($this->request->method('POST') && $this->productAdmin && !$error) {
            $PARequest = $this->productsAdvantagesRequest;
            $PAHelper = $this->productsAdvantagesHelper;

            $advantageImagesToUpload = $PARequest->filesProductAdvantagesImages();
            $advantageUpdates        = $PARequest->postProductAdvantagesUpdates();
            $advantageImagesToDelete = $PARequest->postProductAdvantagesImagesToDelete();
            foreach($advantageUpdates as $advantageId => $advantageUpdate) {
                $PAHelper->updateProductAdvantage(
                    $advantageId,
                    $advantageUpdate,
                    $advantageImagesToUpload,
                    $advantageImagesToDelete
                );
            }

            $positions = $PARequest->postPositionsProductAdvantages();
            list($ids, $positions) = $PAHelper->sortPositionsProductAdvantages($positions);
            $PAHelper->updatePositionsProductAdvantages($ids, $positions);

            // Действия с выбранными
            $ids = $PARequest->postCheckProductAdvantages();

            if(!empty($ids)) {
                switch($PARequest->postActionProductAdvantages()) {
                    case 'delete': {
                        $PAHelper->deleteProductAdvantages($ids);
                        break;
                    }
                }
            }
        }
    }

    private function handleNewProductAdvantages()
    {
        $PARequest = $this->productsAdvantagesRequest;
        $PAHelper = $this->productsAdvantagesHelper;
        $product = $this->product;

        $newAdvantages      = $PARequest->postNewProductAdvantages($product->id);
        $newAdvantageImages = $PARequest->filesNewProductAdvantagesImages();
        if (!empty($newAdvantages)) {
            foreach($newAdvantages as $key => $newAdvantage) {
                $advantageId = $this->productsAdvantagesEntity->add($newAdvantage);
                $PAHelper->uploadProductAdvantageImage($advantageId, $newAdvantageImages[$key]);
            }
        }
    }

    public function deleteByProductsIds($status, $ids)
    {
        if ($status) {
            $advantagesIds = [];
            $advantages = $this->productsAdvantagesEntity->find(['product_id' => $ids]);
            foreach ($advantages as $advantage) {
                $advantagesIds[] = $advantage->id;
            }
            $this->productsAdvantagesHelper->deleteProductAdvantages($advantagesIds);
        }
    }
}