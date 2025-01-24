<?php


namespace Okay\Modules\SimplaMarket\Sets\Plugins;


use Okay\Core\Design;
use Okay\Core\SmartyPlugins\Func;
use Okay\Modules\SimplaMarket\Sets\Helpers\SetsHelper;

class SetsInProductPlugin extends Func
{
    protected $tag = 'sets_in_product';

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

    public function run()
    {
        $product = $this->design->getVar('product');
        if (empty($product)) {
            return null;
        }

        $sets = $this->setsHelper->getSetsByProduct($product);
        if (!empty($sets)){
            foreach($sets as $key => $set) {
                if ($this->setsHelper->isNotActiveSet($set->id)) {
                    unset($sets[$key]);
                }
            }
        }else{
            return null;
        }


        $this->design->assign('sets', $sets);
        return $this->design->fetch('sets_in_product.tpl');
    }
}