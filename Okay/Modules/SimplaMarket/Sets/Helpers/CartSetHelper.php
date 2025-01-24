<?php


namespace Okay\Modules\SimplaMarket\Sets\Helpers;


use Okay\Core\Cart;
use Okay\Core\EntityFactory;
use Okay\Core\Image;
use Okay\Core\Request;
use Okay\Entities\ImagesEntity;
use Okay\Helpers\MoneyHelper;
use Okay\Entities\VariantsEntity;
use Okay\Entities\ProductsEntity;

class CartSetHelper
{
    /**
     * @var ProductsEntity
     */
    private $productsEntity;

    /**
     * @var VariantsEntity
     */
    private $variantsEntity;

    /**
     * @var ImagesEntity
     */
    private $imagesEntity;

    /**
     * @var SetsHelper
     */
    private $setsHelper;

    /**
     * @var MoneyHelper
     */
    private $moneyHelper;
    private $cart;

    public function __construct(
        EntityFactory $entityFactory,
        SetsHelper    $setsHelper,
        MoneyHelper   $moneyHelper,
        Cart          $cart
    ){
        $this->productsEntity = $entityFactory->get(ProductsEntity::class);
        $this->variantsEntity = $entityFactory->get(VariantsEntity::class);
        $this->imagesEntity   = $entityFactory->get(ImagesEntity::class);
        $this->setsHelper     = $setsHelper;
        $this->moneyHelper    = $moneyHelper;
        $this->cart = $cart;
    }

    public function clear()
    {
        unset($_SESSION['sm_sets_cart']);
    }

    public function applyPurchasesDiscounts()
    {
        if (!empty($this->cart->sets)) {
            foreach ($this->cart->sets as $set) {
                $this->cart->undiscounted_total_price += $set->total_price;
            }
        }
    }
    public function updateTotals(Cart $cart)
    {
        if (!empty($cart->sets)) {
            foreach ($cart->sets as $set) {
                $cart->basic_total_price += $set->total_price;
                $cart->total_products    += $set->amount;
            }
            $cart->isEmpty = false;
        }
        return $cart;
    }

    public function attachSetsToCart($cart)
    {
        if (!empty($cart->sets) && isset($_SESSION['coupon_code'])){

            $this->cart->updateTotals();
            return $cart;
        }

        $cart->sets = [];

        if (!isset($_SESSION['sm_sets_cart'])) {
            return $cart;
        }

        if (!empty($_SESSION['sm_sets_cart'])) {
            $cart->isEmpty = false;
        }

        foreach($_SESSION['sm_sets_cart'] as $cartSetKey => $amount) {
            list($variantId, $setId) = explode('_', $cartSetKey);

            $set = $this->setsHelper->getSingleMatchSet(['id' => $setId]);

            $set->variant_id = $variantId;

            // TODO подумать над переименованием total_price
            $set->price = $set->total_price;

            if ($this->mainProductInSet($set)) {
                $variant = $this->variantsEntity->get((int) $variantId);
                $variant = $this->moneyHelper->convertVariantPriceToMainCurrency($variant);
                $set->target_variant = $variant;

                $set->target_product        = $this->productsEntity->get((int) $variant->product_id);
                $set->target_product->image = $this->imagesEntity->get((int) $set->target_product->main_image_id);

                $set->total_price = ($variant->price + $set->price) * $amount;
            } else {
                $set->total_price = $set->price * $amount;
            }

            $set->amount = $amount;

            $cart->sets[] = $set;
            $pTotalProd = 0;
            $percent = 0;

            foreach ($cart->purchases as $p){
                $pTotalProd += $p->amount;
            }

            if ($pTotalProd + $amount != $cart->total_products){
                $cart->total_products = $pTotalProd + $amount;
            }
           /* if ($cart->basic_total_price + $set->total_price != $cart->total_price){
                $cart->total_price = $cart->basic_total_price + $set->total_price;
            }*/
            if ($cart->undiscounted_total_price + $set->total_price != $cart->total_price){
                $cart->total_price = $cart->undiscounted_total_price + $set->total_price;
            }
            foreach ($cart->discounts as $d){
                if ($d->type == 'percent'){
                    $percent += intval($d->value);
                    $d->priceBeforeDiscount = $cart->total_price;
                    $d->priceAfterDiscount = $cart->total_price * (1 - $percent/100);
                    $d->absoluteDiscount = $cart->total_price - $cart->total_price * (1 - $percent/100);
                }
            }
            if ($percent){
                $cart->total_price = $cart->total_price * (1 - $percent/100);
            }
        }
       $this->cart->updateTotals();
        return $cart;
    }

    public function addToCart($variantId, $setId, $amount = 1)
    {
        if ($_REQUEST['amount']){
            $amount = $_REQUEST['amount'];
        }
        if (! isset($_SESSION['sm_sets_cart'])) {
            $_SESSION['sm_sets_cart'] = [];
        }

        $setCartKey = $this->compileSetCartKey($variantId, $setId);
        if ($this->setExistsInCart($setCartKey)) {
            $_SESSION['sm_sets_cart'][$setCartKey] = $amount;
            return;
        }
       // $this->cart->updateTotals();
        $_SESSION['sm_sets_cart'][$setCartKey] = $amount;
    }

    public function updateCartSet($variantId, $setId, $amount)
    {
        $setCartKey = $this->compileSetCartKey($variantId, $setId);

        if (! $this->setExistsInCart($setCartKey)) {
            return;
        }
     //   $this->cart->updateTotals();
        $_SESSION['sm_sets_cart'][$setCartKey] = $amount;
    }

    public function deleteFromCart($variantId, $setId)
    {
        $setCartKey = $this->compileSetCartKey($variantId, $setId);
     //   $this->cart->updateTotals();
        unset($_SESSION['sm_sets_cart'][$setCartKey]);
    }

    public function compileSetCartKey($variantId, $setId)
    {
        return $variantId.'_'.$setId;
    }

    private function mainProductInSet($set)
    {
        return ! empty($set->include);
    }

    private function setExistsInCart($cartSetKey)
    {
        return isset($_SESSION['sm_sets_cart'][$cartSetKey]);
    }
}