<?php


namespace Okay\Modules\SimplaMarket\CartLastNameRequired\Extensions;


use Okay\Core\FrontTranslations;
use Okay\Core\Modules\Extender\ExtensionInterface;
use Okay\Core\Validator;

class ValidateExtension implements ExtensionInterface
{
    /**
     * @var Validator
     */
    private $validator;
    /**
     * @var FrontTranslations
     */
    private $frontTranslations;

    public function __construct(Validator $validator, FrontTranslations $frontTranslations)
    {
        $this->validator = $validator;
        $this->frontTranslations = $frontTranslations;
    }

    public function validateLastName($error, $order)
    {
        if (empty($error)) {
            if (!$this->validator->isName($order->last_name, true)) {
                $error = $this->frontTranslations->getTranslation('form_enter_last_name');
            }
        }

        return $error;
    }
}