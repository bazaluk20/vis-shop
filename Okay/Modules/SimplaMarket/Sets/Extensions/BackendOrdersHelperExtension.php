<?php


namespace Okay\Modules\SimplaMarket\Sets\Extensions;


use Okay\Core\Design;
use Okay\Core\Modules\Extender\ExtensionInterface;
use Okay\Modules\SimplaMarket\Sets\Helpers\SetsHelper;

class BackendOrdersHelperExtension implements ExtensionInterface
{
    /**
     * @var Design
     */
    private $design;

    /**
     * @var SetsHelper
     */
    private $setsHelper;

    public function __construct(Design $design, SetsHelper $setsHelper)
    {
        $this->design     = $design;
        $this->setsHelper = $setsHelper;
    }

    public function extendFindOrderPurchases($purchases)
    {
        foreach($purchases as $purchase) {
            if (empty($purchase->sm_sets_set_id)) {
                continue;
            }

            $purchase->set = $this->setsHelper->get((int) $purchase->sm_sets_set_id);
            if (!empty($purchase->sm_sets_target_variant_id)) {
                $this->setsHelper->attachTargetProductData($purchase);
            }
        }

        $this->design->assign('simplamarket_sets_need_kill_warnings', $this->needKillWarnings($purchases));
        return $purchases;
    }

    private function needKillWarnings($purchases)
    {
        foreach($purchases as $purchase) {
            if (!empty($purchase->set)) {
                if ($purchase->set->include && !is_null($purchase->set->target_variant->stock) && $purchase->set->target_variant->stock < $purchase->amount) {
                    return false;
                }

                foreach($purchase->set->items as $item) {
                    if (!is_null($item->variant->stock) && $item->variant->stock < $item->amount * $purchase->amount) {
                        return false;
                    }
                }
            } else {
                if ((empty($purchase->variant) || $purchase->amount > $purchase->variant->stock || !$purchase->variant->stock)) {
                    return false;
                }
            }
        }

        return true;
    }
}