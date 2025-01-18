<?php


namespace Okay\Modules\SimplaMarket\EmailNotRequired\Extensions;


use Okay\Core\Modules\Extender\ExtensionInterface;

class ValidateExtension implements ExtensionInterface
{
    public function emailNotRequired($error, $order)
    {
        if ($error === 'empty_email') {
            $error = '';
        }
        return $error;
    }
}