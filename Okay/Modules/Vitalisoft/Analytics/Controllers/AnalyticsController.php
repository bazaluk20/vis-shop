<?php

namespace Okay\Modules\Vitalisoft\Analytics\Controllers;

use Okay\Controllers\AbstractController;
use Okay\Entities\OrdersEntity;

class AnalyticsController extends AbstractController
{
    public function purchase(OrdersEntity $ordersEntity, $order_url)
    {
        $id = $ordersEntity->findOne(['url' => $order_url])->id;
        $ordersEntity->update($id, ['analytics_sent' => 1]);
    }
}
