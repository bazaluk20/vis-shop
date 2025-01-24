<?php


namespace Okay\Modules\SimplaMarket\Sets\Extensions;


use Okay\Core\EntityFactory;
use Okay\Entities\VariantsEntity;
use Okay\Entities\ProductsEntity;
use Okay\Core\Modules\Extender\ExtensionInterface;
use Okay\Modules\SimplaMarket\Sets\Helpers\SetsHelper;
use Okay\Modules\SimplaMarket\Sets\Helpers\CartSetHelper;

class CartHelperExtension implements ExtensionInterface
{
    /**
     * @var SetsHelper
     */
    private $setsHelper;

    /**
     * @var CartSetHelper
     */
    private $cartSetHelper;

    /**
     * @var VariantsEntity
     */
    private $variantsEntity;

    /**
     * @var ProductsEntity
     */
    private $productsEntity;

    public function __construct(SetsHelper $setsHelper, CartSetHelper $cartSetHelper, EntityFactory $entityFactory)
    {
        $this->setsHelper     = $setsHelper;
        $this->cartSetHelper  = $cartSetHelper;
        $this->variantsEntity = $entityFactory->get(VariantsEntity::class);
        $this->productsEntity = $entityFactory->get(ProductsEntity::class);
    }

    public function extendPrepareCart($preparedCart, $cart, $orderId)
    {
        if (empty($preparedCart->sets)) {
            return $preparedCart;
        }

        foreach($preparedCart->sets as $set) {
            $purchase = new \stdClass();

            if ($set->include && isset($set->variant_id)) {
                $purchase->sm_sets_target_variant_id = $set->variant_id;
            }

            $purchase->product_id = 0;
            $purchase->variant_id = 0;
            $purchase->product_name   = 'Комплект';
            $purchase->variant_name   = '';
            $purchase->sm_sets_set_id = $set->id;
            $purchase->order_id       = $orderId;
            $purchase->amount         = $set->amount;
            $purchase->sku            = '';
            if ($set->include && isset($set->variant_id)) {
                $purchase->price          = $set->price + $set->target_variant->price;
                $purchase->undiscounted_price = $set->price + $set->target_variant->price;
            }else{
                $purchase->price          = $set->price;
                $purchase->undiscounted_price = $set->price;
            }
            $purchase->units          = '';

            $preparedCart->purchases[] = $purchase;
            $cart->purchases[] = $purchase;
            $cart->purchasesToDB[] = $purchase;
        }

        return $preparedCart;
    }
}