<?php


namespace Okay\Modules\SimplaMarket\Sets\Helpers;


use Okay\Core\EntityFactory;
use Okay\Entities\CurrenciesEntity;
use Okay\Entities\ImagesEntity;
use Okay\Entities\ProductsEntity;
use Okay\Entities\VariantsEntity;
use Okay\Entities\CategoriesEntity;
use Okay\Helpers\MoneyHelper;
use Okay\Modules\SimplaMarket\Sets\Entities\SetsEntity;
use Okay\Modules\SimplaMarket\Sets\Entities\SetItemsEntity;
use Okay\Modules\SimplaMarket\Sets\Entities\DisplayObjectsEntity;

class SetsHelper
{
    /**
     * @var SetsEntity
     */
    private $setsEntity;

    /**
     * @var SetItemsEntity
     */
    private $setItemsEntity;

    /**
     * @var DisplayObjectsEntity
     */
    private $displayObjectsEntity;

    /**
     * @var VariantsEntity
     */
    private $variantsEntity;

    /**
     * @var CategoriesEntity
     */
    private $categoriesEntity;

    /**
     * @var CurrenciesEntity
     */
    private $currenciesEntity;

    /**
     * @var ImagesEntity
     */
    private $imagesEntity;

    /**
     * @var ProductsEntity
     */
    private $productsEntity;

    /**
     * @var MoneyHelper
     */
    private $moneyHelper;

    public function __construct(EntityFactory $entityFactory, MoneyHelper $moneyHelper)
    {
        $this->imagesEntity         = $entityFactory->get(ImagesEntity::class);
        $this->setsEntity           = $entityFactory->get(SetsEntity::class);
        $this->setItemsEntity       = $entityFactory->get(SetItemsEntity::class);
        $this->displayObjectsEntity = $entityFactory->get(DisplayObjectsEntity::class);
        $this->categoriesEntity     = $entityFactory->get(CategoriesEntity::class);
        $this->currenciesEntity     = $entityFactory->get(CurrenciesEntity::class);
        $this->variantsEntity       = $entityFactory->get(VariantsEntity::class);
        $this->productsEntity       = $entityFactory->get(ProductsEntity::class);
        $this->moneyHelper          = $moneyHelper;
    }

    public function add($set)
    {
        $set->id = $this->setsEntity->add([
            'name'       => $set->name,
            'visible'    => $set->visible,
            'annotation' => $set->annotation,
            'include'    => $set->include,
            'show_type'  => $set->show_type,
            'date_to'    => $set->date_to,
            'date_from'  => $set->date_from,
        ]);

        foreach($set->items as $item) {
            $this->setItemsEntity->add([
                'set_id'        => $set->id,
                'product_id'    => $item->product_id,
                'variant_id'    => $item->variant_id,
                'amount'        => $item->amount,
                'discount'      => $item->discount,
                'discount_type' => $item->discount_type,
                'position'      => $item->position,
            ]);
        }

        if (! empty($set->id) && ! empty($set->display_objects)) {
            $this->displayObjectsEntity->updateObjectsBySetId($set->id, $set->display_objects);
        }

        return $set->id;
    }

    public function update($id, $set)
    {
        $this->setsEntity->update($id, [
            'name'       => $set->name,
            'visible'    => $set->visible,
            'annotation' => $set->annotation,
            'include'    => $set->include,
            'show_type'  => $set->show_type,
            'date_to'    => $set->date_to,
            'date_from'  => $set->date_from,
        ]);

        foreach($set->items as $item) {
            $itemData = [
                'id'            => $item->id,
                'set_id'        => $set->id,
                'product_id'    => $item->product_id,
                'variant_id'    => $item->variant_id,
                'amount'        => $item->amount,
                'discount'      => $item->discount,
                'discount_type' => $item->discount_type,
                'position'      => $item->position,
            ];
            if (empty($item->id)) {
                $item->id = $this->setItemsEntity->add($itemData);
            } else {
                $this->setItemsEntity->update($item->id, $itemData);
            }
        }
        $itemIds = array_map(function($item) {
            return $item->id;
        }, $set->items);
        $this->setItemsEntity->deleteAllNotInByIds($id, $itemIds);

        $this->displayObjectsEntity->updateObjectsBySetId($set->id, $set->display_objects);
    }

    public function attachTargetProductData($purchase)
    {
        if (empty($purchase->set) || empty($purchase->sm_sets_target_variant_id)) {
            return $purchase;
        }

        $purchase->set->target_variant = $this->variantsEntity->get((int) $purchase->sm_sets_target_variant_id);
        if (empty($purchase->set->target_variant)) {
            return $purchase;
        }
        $purchase->set->target_variant = $this->moneyHelper->convertVariantPriceToMainCurrency($purchase->set->target_variant);

        $purchase->set->target_product = $this->productsEntity->get((int) $purchase->set->target_variant->product_id);
        if (empty($purchase->set->target_product) || empty($purchase->set->target_product->main_image_id)) {
            return $purchase;
        }

        $purchase->set->target_product->image = $this->imagesEntity->get((int) $purchase->set->target_product->main_image_id);
        return $purchase;
    }

    public function attachProductDataToSet($set, $purchase)
    {
        if (!$set->include || empty($purchase->sm_sets_target_variant_id)) {
            return $set;
        }

        $set->target_variant = $this->variantsEntity->get((int) $purchase->sm_sets_target_variant_id);
        $set->target_variant = $this->moneyHelper->convertVariantPriceToMainCurrency($set->target_variant);
        if (empty($set->target_variant)) {
            return $set;
        }

        $set->target_product = $this->productsEntity->get((int) $set->target_variant->product_id);
        if (empty($set->target_product)) {
            return $set;
        }

        $set->target_product->image = $this->imagesEntity->get((int) $set->target_product->main_image_id);
        return $set;
    }

    public function get($id)
    {
        $set = $this->setsEntity->findOne(['id' => (int) $id]);
        if (empty($set)) {
            return false;
        }

        $set->items = $this->attachProductDataToSetItems(
            $this->setItemsEntity->find(['set_id' => $id])
        );

        $set->items = $this->convertPricesToMainCurrency($set->items);
        $set->items = $this->calculateSetDiscountPerItem($set->items);
        $set->items = $this->calculateSetPricePerItem($set->items);

        $displayObjectIds = $this->displayObjectsEntity->cols(['object_id'])->find(['set_id' => $id]);

        if ($set->show_type === 'product') {
            $products = $this->productsEntity->find(['id' => $displayObjectIds]);

            foreach($products as $product) {
                $product->image = $this->imagesEntity->findOne(['id' => $product->main_image_id]);
            }

            $set->display_objects = $products;
            return $set;
        }

        if ($set->show_type === 'category') {
            $categories = $this->categoriesEntity->find(['id' => $displayObjectIds]);
            $set->display_objects = $categories;
            return $set;
        }

        throw new \Exception('Incorrect display object type');
    }

    public function isNotActiveSet($setId)
    {
        return ! $this->isActiveSet($setId);
    }

    public function isActiveSet($setId)
    {
        $set = $this->setsEntity->findOne(['id' => $setId]);
        if (empty($set->visible)) {
            return false;
        }

        if (strtotime($set->date_from) < time() && strtotime($set->date_to) > time()) {
            return true;
        }

        if (empty(strtotime($set->date_to)) && ! empty(strtotime($set->date_from)) && strtotime($set->date_from) < time()) {
            return true;
        }

        return false;
    }

    public function getSetsByProduct($product)
    {
        $productSets = $this->getSetsByProductId($product->id);
        $categorySets = $this->getSetsByCategoryId($product->main_category_id);
        if (!empty($productSets) && !empty($categorySets)) {
            $sets = array_merge($productSets, $categorySets);
        } elseif (!empty($productSets)) {
            $sets = $productSets;
        } elseif (!empty($categorySets)) {
            $sets = $categorySets;
        }


        if (!empty($sets)) {

            foreach ($sets as $set) {
                $set->target_product = $product;

                if ($set->include) {
                    $set->total_price += $product->variant->price;
                }
            }
        }


        return $sets;
    }

    public function getSingleMatchSet($filter)
    {
        $matchSets = $this->getList($filter);
        if (empty($matchSets)) {
            return false;
        }
        return reset($matchSets);
    }

    public function getList($filter)
    {
        $sets = $this->setsEntity->mappedBy('id')->find($filter);
        if (empty($sets)) {
            return [];
        }

        $setItems = $this->setItemsEntity->find(['set_id' => array_keys($sets)]);

        $setItems = $this->attachProductDataToSetItems($setItems);
        $sets     = $this->attachSetItemsToSets($sets, $setItems);

        foreach($sets as $set) {
            $set->total_price    = 0;
            $set->total_discount = 0;

            if (empty($set->items)) {
                continue;
            }

            $set->items = $this->convertPricesToMainCurrency($set->items);
            $set->items = $this->calculateSetDiscountPerItem($set->items);
            $set->items = $this->calculateSetPricePerItem($set->items);

            $set->total_price    = $this->calculateSetTotalPrice($set);
            $set->total_discount = $this->calculateSetTotalDiscount($set);
        }

        return $sets;
    }

    private function convertPricesToMainCurrency($setItems)
    {
        foreach($setItems as $setItem) {
            $setItem->variant = $this->moneyHelper->convertVariantPriceToMainCurrency($setItem->variant);
        }
        return $setItems;
    }

    private function calculateSetPricePerItem($setItems)
    {
        foreach ($setItems as $setItem) {
            $setItem->cur_price      = $setItem->variant->price * $setItem->amount - $setItem->cur_discount;
            $setItem->price_per_item = $setItem->variant->price - ($setItem->cur_discount / $setItem->amount);
        }
        return $setItems;
    }

    private function calculateSetDiscountPerItem($setItems)
    {
        foreach ($setItems as $setItem) {
            if ($setItem->discount_type == 'value') {
                $setItem->cur_discount = $setItem->discount * $setItem->amount;
            } else {
                $setItem->cur_discount = $setItem->discount/100 * $setItem->variant->price * $setItem->amount;
            }
        }
        return $setItems;
    }

    private function calculateSetTotalPrice($set)
    {
        $totalPrice = 0;
        foreach ($set->items as $setItem) {
            $totalPrice  += $setItem->cur_price;
        }
        return $totalPrice;
    }

    private function calculateSetTotalDiscount($set)
    {
        $totalDiscount = 0;
        foreach ($set->items as $setItem) {
            $totalDiscount += $setItem->cur_discount;
        }
        return $totalDiscount;
    }

    private function attachSetItemsToSets($sets, $setItems)
    {
        foreach($setItems as $setItem) {
            if (empty($sets[$setItem->set_id])) {
                $sets[$setItem->set_id]->items = [];
            }

            $sets[$setItem->set_id]->items[] = $setItem;
        }
        return $sets;
    }

    private function attachProductDataToSetItems($setItems)
    {
        $productIds = array_map(function($setItem) {
            return $setItem->product_id;
        }, $setItems);

        $products = $this->productsEntity->mappedBy('id')->find(['id' => $productIds]);
        foreach($setItems as $setItem) {
            if (empty($products[$setItem->product_id])) {
                continue;
            }

            $setItem->product           = $products[$setItem->product_id];
            $setItem->product->image    = $this->imagesEntity->findOne(['product_id' => $setItem->product_id]);
            $setItem->product->variants = $this->variantsEntity->find(['product_id' => $setItem->product_id]);

            foreach($setItem->product->variants as $variant) {
                if ($variant->id != $setItem->variant_id) {
                    continue;
                }

                $setItem->variant = $variant;
                break;
            }
        }

        return $setItems;
    }

    private function getSetsByProductId($id)
    {
        $setIds = $this->displayObjectsEntity->cols(['set_id'])->find(['object_id' => $id]);
        if (!empty($setIds)){
            $sets   = $this->getList(['id' => $setIds]);
            foreach($sets as $key => $set) {
                if ($set->show_type != 'product') {
                    unset($sets[$key]);
                }
            }
        }
        return $sets;
    }

    private function getSetsByCategoryId($id)
    {
        $setIds = $this->displayObjectsEntity->cols(['set_id'])->find(['object_id' => $id]);
        if (!empty($setIds)){
            $sets   = $this->getList(['id' => $setIds]);
            foreach($sets as $key => $set) {
                if ($set->show_type != 'category') {
                    unset($sets[$key]);
                }
            }
        }

        return $sets;
    }

    public function searchSets($keyword)
    {
        $filterProducts = [];
        $filterProducts['keyword'] = $keyword;

        $setsIdsProductsIds = $this->setItemsEntity->cols(['set_id','product_id'])->find();

        if (!empty($setsIdsProductsIds)) {
            $mappedSetsIdsProductsIds = [];
            foreach ($setsIdsProductsIds as $setIdProductsId) {
                $mappedSetsIdsProductsIds[$setIdProductsId->product_id][] = $setIdProductsId->set_id;
            }

            $filterProducts['id'] = array_keys($mappedSetsIdsProductsIds);
            $productsFoundIds = $this->productsEntity->col('id')->find($filterProducts);

            if (!empty($productsFoundIds)) {
                foreach ($productsFoundIds as $productFoundId) {

                    foreach ($mappedSetsIdsProductsIds[$productFoundId] as $foundSetId) {
                        $foundSetsIds[] = $foundSetId;
                    }
                }
            }
        }

        $setsSearchFromProducts = [];
        if (!empty($foundSetsIds)) $setsSearchFromProducts = $this->setsEntity->mappedBy('id')->find(['id'=>$foundSetsIds]);

        $setsSearchFromName = $this->setsEntity->mappedBy('id')->find(['keyword'=>$keyword]);

        return $setsSearchFromName + $setsSearchFromProducts;
    }

}