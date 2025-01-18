<?php


namespace Okay\Modules\SimplaMarket\CartLastNameRequired\Init;

use Okay\Core\Modules\AbstractInit;
use Okay\Helpers\ValidateHelper;
use Okay\Modules\SimplaMarket\CartLastNameRequired\Extensions\ValidateExtension;

class Init extends AbstractInit
{
    public function install()
    {
        $this->setBackendMainController('DescriptionAdmin');
    }

    public function init()
    {
        $this->registerBackendController('DescriptionAdmin');
        $this->addBackendControllerPermission('DescriptionAdmin', 'sm_cart_last_name_required');

        $this->registerChainExtension(
            [ValidateHelper::class, 'getCartValidateError'],
            [ValidateExtension::class, 'validateLastName']
        );

        $this->addFrontBlock('front_scripts_after_validate', 'validate.js');
    }
}