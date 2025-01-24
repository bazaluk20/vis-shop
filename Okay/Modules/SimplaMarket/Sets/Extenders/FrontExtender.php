<?php


namespace Okay\Modules\SimplaMarket\Sets\Extenders;

use Okay\Core\Design;
use Okay\Core\EntityFactory;
use Okay\Core\Modules\Extender\ExtensionInterface;
use Okay\Modules\SimplaMarket\Sets\Helpers\CartSetHelper;
use Okay\Core\Cart;


class FrontExtender implements ExtensionInterface
{
    
    private $entityFactory;
    private $design;
    private $cartSetHelper;
    private $cartCore;

    public function __construct(
        EntityFactory $entityFactory,
        Design $design,
        CartSetHelper $cartSetHelper,
        Cart $cartCore)
    {
        $this->entityFactory = $entityFactory;
        $this->design = $design;
        $this->cartSetHelper  = $cartSetHelper;
        $this->cartCore       = $cartCore;

    }

    public function updateTotals()
    {
        return $this->cartSetHelper->updateTotals($this->cartCore);
    }

    public function applyPurchasesDiscounts()
    {
        $this->cartSetHelper->applyPurchasesDiscounts();
    }

}