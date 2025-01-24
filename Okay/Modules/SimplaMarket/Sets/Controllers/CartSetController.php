<?php


namespace Okay\Modules\SimplaMarket\Sets\Controllers;


use Okay\Controllers\AbstractController;
use Okay\Modules\SimplaMarket\Sets\Helpers\CartSetHelper;

class CartSetController extends AbstractController
{
    public function addToCart(CartSetHelper $cartSetHelper)
    {
        $variantId = $this->request->get('variant_id');
        $setId     = $this->request->get('set_id');
        $cartSetHelper->addToCart($variantId, $setId);

        $this->response->setContent(json_encode(['ok' => 1]), RESPONSE_JSON);
    }
}