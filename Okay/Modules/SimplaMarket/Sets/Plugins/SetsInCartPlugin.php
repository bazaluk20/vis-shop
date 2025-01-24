<?php


namespace Okay\Modules\SimplaMarket\Sets\Plugins;


use Okay\Core\Design;
use Okay\Core\SmartyPlugins\Func;
use Okay\Modules\SimplaMarket\Sets\Helpers\SetsHelper;

class SetsInCartPlugin extends Func
{
    protected $tag = 'sets_in_cart';

    /**
     * @var Design
     */
    private $design;

    public function __construct(Design $design)
    {
        $this->design = $design;
    }

    public function run()
    {
        return $this->design->fetch('sets_in_cart.tpl');
    }
}