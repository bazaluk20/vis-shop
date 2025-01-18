<?php

namespace Okay\Modules\SimplaMarket\DoNotCallBack\Extenders;

use Okay\Core\Modules\Extender\ExtensionInterface;
use Okay\Core\Request;
use Okay\Core\EntityFactory;
use Okay\Entities\OrdersEntity;

class FrontExtender implements ExtensionInterface
{
	private $request;
	private $ordersEntity;

	public function __construct(Request $request, EntityFactory $entityFactory)
	{
		$this->request = $request;
		$this->ordersEntity = $entityFactory->get(OrdersEntity::class);
	}

	public function saveDoNotCallBack($result, $order)
	{
		$value = $this->request->post('do_not_call_back') !== null ? 1 : 0;
		$this->ordersEntity->update($order->id, ['do_not_call_back' => $value]);
	}
}