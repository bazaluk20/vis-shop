<?php


namespace Okay\Modules\SimplaMarket\Sets\Extenders;


use Okay\Core\EntityFactory;
use Okay\Core\Modules\Extender\ExtensionInterface;
use Okay\Core\Request;
use Okay\Modules\SimplaMarket\Sets\Entities\SetItemsEntity;
use Okay\Modules\SimplaMarket\Sets\Entities\SetsEntity;

class BackendExtender implements ExtensionInterface
{
    private $request;
    private $entityFactory;

    public function __construct(Request $request, EntityFactory $entityFactory)
    {
        $this->request = $request;
        $this->entityFactory = $entityFactory;
    }
    public function postProduct($product)
    {
    $setsItemEntity = $this->entityFactory->get(SetItemsEntity::class);
    $setsEntity = $this->entityFactory->get(SetsEntity::class);

    foreach ($setsItemEntity->find(['product_id'=>$product->id]) as $setItem){
        $set_ids[] = $setItem->id;
    }
    if (!empty($set_ids)){
        $product->sets = $setsEntity->find(['id'=>$set_ids]);
    }

        return $product;
    }

}