<?php

namespace Okay\Modules\SimplaMarket\DoNotCallBack\Plugins;

use Okay\Core\SmartyPlugins\Func;
use Okay\Core\Design;

class DoNotCallBackPlugin extends Func{

    protected $tag = 'do_not_call_back';

    protected $design;

    public function __construct(Design $design)
    {
        $this->design = $design;
    }

    public function run()
    {
        return $this->design->fetch('do_not_call_back.tpl');
    }
}