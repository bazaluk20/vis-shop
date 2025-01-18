<?php


namespace Okay\Modules\SimplaMarket\EmailNotRequired\Init;

use Okay\Core\Modules\AbstractInit;
use Okay\Helpers\ValidateHelper;
use Okay\Modules\SimplaMarket\EmailNotRequired\Extensions\ValidateExtension;

class Init extends AbstractInit
{
    public function install()
    {
        $this->setBackendMainController('DescriptionAdmin');
    }

    public function init()
    {
        $this->registerBackendController('DescriptionAdmin');
        $this->addBackendControllerPermission('DescriptionAdmin', 'simplamarket_email_not_required');

        $this->registerChainExtension(
            ['class' => ValidateHelper::class,    'method' => 'getCartValidateError'],
            ['class' => ValidateExtension::class, 'method' => 'emailNotRequired']);

        $this->addFrontBlock('front_scripts_after_validate', 'email_not_validate.js');
    }
}