<?php


namespace Okay\Modules\SimplaMarket\Sets\Extensions;

use Okay\Core\Cart;
use Okay\Core\EntityFactory;
use Okay\Core\Request;
use Okay\Entities\VariantsEntity;
use Okay\Core\Modules\Extender\ExtensionInterface;
use Okay\Helpers\MoneyHelper;
use Okay\Modules\SimplaMarket\Sets\Helpers\SetsHelper;
use Okay\Modules\SimplaMarket\Sets\Helpers\CartSetHelper;

class CartExtension implements ExtensionInterface
{
    /**
     * @var VariantsEntity
     */
    private $variantsEntity;

    /**
     * @var SetsHelper
     */
    private $setsHelper;

    /**
     * @var CartSetHelper
     */
    private $cartSetHelper;

    /**
     * @var Cart
     */
    private $cartCore;

    /**
     * @var MoneyHelper
     */
    private $moneyHelper;

    /**
     * @var Request
     */
    private $request;

    public function __construct(
        EntityFactory $entityFactory,
        SetsHelper    $setsHelper,
        CartSetHelper $cartSetHelper,
        Cart          $cartCore,
        MoneyHelper   $moneyHelper,
        Request       $request
    ){
        $this->variantsEntity = $entityFactory->get(VariantsEntity::class);
        $this->setsHelper     = $setsHelper;
        $this->cartSetHelper  = $cartSetHelper;
        $this->cartCore       = $cartCore;
        $this->moneyHelper    = $moneyHelper;
        $this->request        = $request;
    }

    public function extendClear()
    {
        $this->cartSetHelper->clear();

    }

    public function extendGet($cart)
    {

        return $this->cartSetHelper->attachSetsToCart($this->cartCore);

    }

    public function getPurchases()

    {
        return $this->cartSetHelper->attachSetsToCart($this->cartCore);
    }

    public function extendAddItem()
    {
        $cartSetKey = $this->request->get('variant_id');

        if ($this->isIncorrectCartSetKey($cartSetKey)) {
            return;
        }

        list(, $variantId, $setId) = explode('_', $cartSetKey);

        $this->cartSetHelper->addToCart($variantId, $setId);
    }

    public function extendUpdateItem()
    {
        $cartSetKey = $this->request->get('variant_id');
        $amount     = $this->request->get('amount');

        if ($this->isIncorrectCartSetKey($cartSetKey)) {
            return;
        }

        list(, $variantId, $setId) = explode('_', $cartSetKey);

        $this->cartSetHelper->updateCartSet($variantId, $setId, $amount);
    }

    public function extendDeleteItem()
    {
        $cartSetKey = $this->request->get('variant_id');

        if ($this->isIncorrectCartSetKey($cartSetKey)) {
            return;
        }

        list(, $variantId, $setId) = explode('_', $cartSetKey);

        $this->cartSetHelper->deleteFromCart($variantId, $setId);
    }

    private function isIncorrectCartSetKey($cartSetKey)
    {
        return ! preg_match("/^set\_[0-9]+\_[0-9]+$/ui", $cartSetKey);
    }
}