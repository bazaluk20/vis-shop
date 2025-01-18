<?php


namespace Okay\Modules\SimplaMarket\PhoneRequired\Init;

use Okay\Core\Modules\AbstractInit;
use Okay\Helpers\ValidateHelper;
use Okay\Modules\SimplaMarket\PhoneRequired\Extensions\ValidateExtension;

class Init extends AbstractInit
{
    public function install()
    {
        $this->setBackendMainController('DescriptionAdmin');
    }

    public function init()
    {
        $this->registerBackendController('DescriptionAdmin');
        $this->addBackendControllerPermission('DescriptionAdmin', 'sm_hide_phone_required');


        $this->registerChainExtension(
            [ValidateHelper::class, 'getCartValidateError'],
            [ValidateExtension::class, 'validatePhone']
        );

        $this->addFrontBlock('front_scripts_after_validate', 'validate.js');
    }
}