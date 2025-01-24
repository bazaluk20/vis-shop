<?php


namespace Okay\Modules\SimplaMarket\Sets\Backend\Controllers;


use Okay\Entities\ProductsEntity;
use Okay\Entities\VariantsEntity;
use Okay\Admin\Controllers\IndexAdmin;
use Okay\Modules\SimplaMarket\Sets\Entities\SetsEntity;
use Okay\Modules\SimplaMarket\Sets\Entities\SetItemsEntity;
use Okay\Modules\SimplaMarket\Sets\Helpers\SetsHelper;

class SetsAdmin extends IndexAdmin
{
    public function fetch(
        SetsEntity     $setsEntity,
        SetItemsEntity $setItemsEntity,
        ProductsEntity $productsEntity,
        VariantsEntity $variantsEntity,
        SetsHelper $setsHelper
    ){
        if($this->request->method('post')) {
            $ids = $this->request->post('check');
            if(is_array($ids)) {
                switch ($this->request->post('action')) {
                    case 'enable': {
                        $setsEntity->update($ids, ['visible' => 1]);
                        break;
                    }
                    case 'disable': {
                        $setsEntity->update($ids, ['visible' => 0]);
                        break;
                    }
                    case 'set_dates_null': {
                        $setsEntity->update($ids, ['date_from' => null, 'date_to' => null]);
                        break;
                    }
                    case 'delete': {
                        $setsEntity->delete($ids);
                        break;
                    }
                }
            }
        }


        if (!empty($keyword = $this->request->get('keyword'))) {
            $sets = $setsHelper->searchSets($keyword);
            $this->design->assign('keyword', $keyword);
        } else {
            $sets = $setsEntity->mappedBy('id')->find();
        }

        foreach ($sets as $set) {
            $set->items          = [];
            $set->total_price    = 0;
            $set->total_discount = 0;
        }

        $setItems = $setItemsEntity->find(['set_id'=>array_keys($sets)]);
        if (!empty($setItems)) {
            $productIds = array_map(function($setItem) {
                return $setItem->product_id;
            }, $setItems);

            $products = $productsEntity->mappedBy('id')->find(['id' => $productIds]);
            $variants = $variantsEntity->mappedBy('id')->find(['product_id' => $productIds]);

            foreach ($setItems as $setItem) {
                if (isset($products[$setItem->product_id])) {
                    $setItem->product = $products[$setItem->product_id];
                }

                if (isset($variants[$setItem->variant_id])) {
                    $setItem->variant = $variants[$setItem->variant_id];

                    $setItem->cur_price = $setItem->variant->price * $setItem->amount;
                    $sets[$setItem->set_id]->total_price += $setItem->cur_price;

                    if ($setItem->discount_type == 'value') {
                        $setItem->cur_discount = $setItem->discount * $setItem->amount;
                    } else {
                        $setItem->cur_discount = $setItem->discount/100 * $setItem->variant->price * $setItem->amount;
                    }

                    $sets[$setItem->set_id]->total_discount += $setItem->cur_discount;
                }

                $sets[$setItem->set_id]->items[] = $setItem;
            }
        }

        $this->design->assign('sets', $sets);
        $this->design->assign('sets_count', count($sets));
        $this->response->setContent($this->design->fetch('sets.tpl'));
    }
}