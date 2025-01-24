<?php


namespace Okay\Modules\SimplaMarket\Sets\Extensions;


use Okay\Core\Modules\Extender\ExtensionInterface;
use Okay\Modules\SimplaMarket\Sets\Helpers\SetsHelper;

class OrdersHelperExtension implements ExtensionInterface
{
    /**
     * @var SetsHelper
     */
    private $setsHelper;

    public function __construct(SetsHelper $setsHelper)
    {
        $this->setsHelper = $setsHelper;
    }

    public function extendGetOrderPurchasesList($purchases)
    {
        foreach($purchases as $purchase) {
            if (!empty($purchase->sm_sets_set_id) && empty($purchase->variant)) {
                $purchase->variant = new \stdClass();
                $purchase->variant->stock = 1;
            }

            if (!empty($purchase->sm_sets_set_id)) {
                $purchase->set = $this->setsHelper->get((int) $purchase->sm_sets_set_id);
                $purchase->set = $this->setsHelper->attachProductDataToSet($purchase->set, $purchase);
            }
        }

        return $purchases;
    }
}