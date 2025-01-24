<?php


namespace Okay\Modules\SimplaMarket\Sets\Plugins;


use Okay\Core\Design;
use Okay\Core\SmartyPlugins\Func;
use Okay\Modules\SimplaMarket\Sets\Helpers\SetsHelper;

class SetsInOrderPlugin extends Func
{
    protected $tag = 'sets_in_order';

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

    public function run($params)
    {
        $purchase = $params['purchase'];
        if (empty($purchase->set)) {
            return null;
        }

        $this->design->assign('set', $purchase->set);
        return $this->design->fetch('sets_in_order.tpl');
    }
}