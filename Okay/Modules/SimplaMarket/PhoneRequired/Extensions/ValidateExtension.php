<?php


namespace Okay\Modules\SimplaMarket\PhoneRequired\Extensions;


use Okay\Core\Modules\Extender\ExtensionInterface;
use Okay\Core\Validator;

class ValidateExtension implements ExtensionInterface
{
    /**
     * @var Validator
     */
    private $validator;

    public function __construct(Validator $validator)
    {
        $this->validator = $validator;
    }

    public function validatePhone($error, $order)
    {
        if (empty($error)) {
            if (!$this->validator->isPhone($order->phone, true)) {
                $error = 'empty_phone';
            }
        }

        return $error;
    }
}