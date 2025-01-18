<?php

/* https://api.privatbank.ua/#p24/partPaymentApi */

namespace Okay\Modules\SimplaMarket\PrivatPayPart\Controllers;


use Okay\Controllers\AbstractController;
use Okay\Core\Money;
use Okay\Core\Notify;
use Okay\Core\Response;
use Okay\Core\Router;
use Okay\Entities\DeliveriesEntity;
use Okay\Entities\OrdersEntity;
use Okay\Entities\PaymentsEntity;
use Okay\Entities\PurchasesEntity;
use Okay\Entities\VariantsEntity;
use Okay\Modules\SimplaMarket\PrivatPayPart\Entities\PrivatPaymentEntity;
use Psr\Log\LoggerInterface;

class PrivatController extends AbstractController
{
    public function createPayment(
        OrdersEntity $ordersEntity,
        PaymentsEntity $paymentsEntity,
        PrivatPaymentEntity $privatPaymentEntity,
        PurchasesEntity $purchasesEntity,
        LoggerInterface $logger,
        Money $money,
        DeliveriesEntity $deliveriesEntity
    ) {
        $orderId = $this->request->post('order_id');
        $order = $ordersEntity->findOne(['id' => $orderId]);
        
        if (empty($orderId) || empty($order)) {
            $this->response->setStatusCode(400);
            $logger->error('PrivatBank: order "' . $orderId . '" not found');
            return;
        }

        $paymentMethod = $paymentsEntity->findOne(['id' => $order->payment_method_id]);
        $privatPaymentData = $privatPaymentEntity->findOne(['order_id' => $order->id]);
        
        if ($order->paid) {
            $this->response->setStatusCode(400);
            $logger->error('PrivatBank: order "' . $order->id . '" is paid');
            return;
        }
        
        if (!$paymentMethod || $paymentMethod->module != 'SimplaMarket/PrivatPayPart') {
            $this->response->setStatusCode(400);
            $logger->error('PrivatBank: for order "' . $order->id . '" payment method is not privat');
            return;
        }

        if (!empty($privatPaymentData->token)) {
            Response::redirectTo('https://payparts2.privatbank.ua/ipp/v2/payment?token=' . $privatPaymentData->token);
        }

        if (empty($privatPaymentData) || empty($privatPaymentData->pay_count)) {
            $this->response->setStatusCode(400);
            $logger->error('PrivatBank: for order "' . $order->id . '" is not setted pay_count');
            return;
        }
        
        $paymentSettings = $paymentsEntity->getPaymentSettings($paymentMethod->id);
        
        $merchantType = $paymentSettings['merchant_type'];  //  Тип кредиту, можливі значення: II – Миттєва розстрочка; PP - Оплата частинами; PB – Оплата частинами. Гроші в періоді. IA - Миттєва розстрочка. Акційна.



        $purchases = $purchasesEntity->find(['order_id'=>$order->id]);

        $products = [];
        $responseUrl = Router::generateUrl('SimplaMarket_PrivatPayPart_callback', [], true);
        $redirectUrl = Router::generateUrl('order', ['url' => $order->url], true);
        $productsString = '';
        $subtotal = 0;
        
        foreach ($purchases as $purchase) {

            $productName = $purchase->product_name . ($purchase->variant_name ? ' (' . $purchase->variant_name . ')' : '') . ($purchase->sku ? ' арт.' . $purchase->sku : '');

            $purchasePrice = $money->convert($purchase->price, $paymentMethod->currency_id, false);
            $subtotal += $purchasePrice * $purchase->amount;
            $products[] =   [
                'name'  => $productName,
                'count' => $purchase->amount,
                'price' => number_format(round($purchasePrice), 2, '.', ''),
            ];
            
            // Здесь ранее был ceil, с ним были проблемы в подписи
            $productsString .= $productName . $purchase->amount . (round((float)$purchasePrice,0)* 100);
        }

        $orderId = $orderId . "-" . rand(100000, 999999);
        $totalPrice = $subtotal;
        
        // Добавим информацию по доставке
        if ($order->delivery_price > 0 && !$order->separate_delivery) {
            $delivery = $deliveriesEntity->findOne(['id' => $order->delivery_id]);
            $deliveryName = 'Delivery: ' . $delivery->name;
            $deliveryPrice = $money->convert($order->delivery_price, $paymentMethod->currency_id, false);
            $totalPrice += $deliveryPrice;
            $products[] =   [
                'name'  => $deliveryName,
                'count' => 1,
                'price' => number_format($deliveryPrice, 2, '.', ''),
            ];

            $productsString .= $deliveryName . 1 . (round((float)$deliveryPrice * 100));
        }

        // Documentation: https://api.privatbank.ua/#p24/partPaymentApi
        $signature = base64_encode(sha1(
            $paymentSettings['password'] .
            $paymentSettings['storeId'] .
            $orderId .
            (int)(round($totalPrice,0)*100).
            $privatPaymentData->pay_count .
            $merchantType .
            $responseUrl .
            $redirectUrl .
            $productsString .
            $paymentSettings['password']
            , true));

        $request = [
            'storeId'       => $paymentSettings['storeId'],     //  идентификатор магазина
            'orderId'       => $orderId,
            'amount'        => number_format(round($totalPrice), 2, '.', ''),  // Double 	notNull, Min=300, Max=50 000 	Окончательная сумма покупки
            'products'      => $products,
            'partsCount'    => $privatPaymentData->pay_count,   // Integer 	notNull, min=2, max=25 	Количество частей на которые делится сумма транзакции (Для заключения кредитного договора) Должно быть > 1.
            'merchantType'  => $merchantType,
            'responseUrl'   => $responseUrl,
            'redirectUrl'   => $redirectUrl,
            'signature'     => $signature
        ];

        $createUrl = 'https://payparts2.privatbank.ua/ipp/v2/payment/create';

        $pb_curl = curl_init($createUrl);
        curl_setopt($pb_curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($pb_curl, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($pb_curl, CURLOPT_HTTPHEADER, ['Content-Type: application/json; charset=UTF-8;',
            'Accept: application/json;',
            'Accept-Encoding: UTF-8;']);
        curl_setopt($pb_curl, CURLOPT_POST, true);
        curl_setopt($pb_curl, CURLOPT_POSTFIELDS, json_encode($request));
        $response = curl_exec($pb_curl);
        curl_close($pb_curl);

        $response = json_decode($response);
        if (strtolower($response->state) == 'fail') {
            $message = '';
            if (isset($response->errorMessage) && !empty($response->errorMessage)) {
                $message .= $response->errorMessage;
            }
            if (isset($response->message) && !empty($response->message)) {
                $message .= $response->message;
            }
            $logger->error('PrivatBank: error from API: ' . $message);
            Response::redirectTo(Router::generateUrl('order', ['url' => $order->url], true));

        } elseif (strtolower($response->state) == 'success') {

            /*Проверяем подпись ответа*/
            $signature = base64_encode(sha1(
                $paymentSettings['password'] .
                $response->state .
                $paymentSettings['storeId'] .
                $orderId .
                $response->token .
                $paymentSettings['password']
                , true));
            if ($signature == $response->signature) {
                $privatPaymentEntity->update($privatPaymentData->id, ['token' => $response->token]);
                Response::redirectTo('https://payparts2.privatbank.ua/ipp/v2/payment?token=' . $response->token);
            } else {
                $logger->error('PrivatBank: wrong signature "' . $signature . '" for order "' . $order->id);
                Response::redirectTo(Router::generateUrl('order', ['url' => $order->url], true));
            }
        }
    }
    
    public function callback(
        OrdersEntity $ordersEntity, 
        LoggerInterface $logger,
        PaymentsEntity $paymentsEntity,
        PurchasesEntity $purchasesEntity,
        VariantsEntity $variantsEntity,
        PrivatPaymentEntity $privatPaymentEntity,
        Notify $notify
    ) {
        $callbackData = json_decode($this->request->post());

        $orderId = intval(substr($callbackData->orderId, 0, strpos($callbackData->orderId, '-')));

        if (empty($orderId)) {
            $this->response->setStatusCode(400);
            return;
        }

        $order = $ordersEntity->findOne(['id' => $orderId]);
        if (empty($order)) {

            $logger->error('PrivatBank: order "' . $orderId . '" not found');
            $this->response->setContent('Оплачиваемый заказ не найден', RESPONSE_TEXT);
            return;
        }

        // Нельзя оплатить уже оплаченный заказ  
        if ($order->paid) {
            $logger->error('PrivatBank: order "' . $orderId . '" already paid');
            $this->response->setContent('Этот заказ уже оплачен', RESPONSE_TEXT);
            return;
        }

        $privatPaymentData = $privatPaymentEntity->findOne(['order_id' => $order->id]);
        if (empty($privatPaymentData)) {
            $logger->error('PrivatBank: wrong payment data for order "' . $orderId);
            $this->response->setContent('Неизвестные данные оплаты', RESPONSE_TEXT);
            return;
        }

        $method = $paymentsEntity->findOne(['id' => $order->payment_method_id]);
        if (empty($method)) {
            $logger->error('PrivatBank: unknown payment for order "' . $orderId);
            $this->response->setContent('Неизвестный метод оплаты', RESPONSE_TEXT);
            return;
        }

        $settings = $paymentsEntity->getPaymentSettings($method->id);

        $signature = base64_encode(sha1(
            $settings['password'] .
            $settings['storeId'] .
            $orderId .
            $callbackData->paymentState .
            $callbackData->message .
            $settings['password']
            , true));

        // Проверяем контрольную подпись
        if ($signature != $callbackData->signature) {
            $logger->error('PrivatBank: callback wrong signature "' . $signature . '" for order "' . $order->id);
            $this->response->setStatusCode(400);
            return;
        }

        if (strtolower($callbackData->paymentState) == 'success') {
            // Проверка наличия товара
            $purchases = $purchasesEntity->find(['order_id'=>intval($order->id)]);
            foreach ($purchases as $purchase) {
                $variant = $variantsEntity->findOne(['id' => $purchase->variant_id]);
                if (empty($variant) || (!$variant->infinity && $variant->stock < $purchase->amount)) {
                    $logger->error('PrivatBank: few goods "' . $purchase->product_name . $purchase->variant_name);
                    $this->response->setContent("Нехватка товара $purchase->product_name $purchase->variant_name", RESPONSE_TEXT);
                    return;
                }
            }

            $payment_details = unserialize($order->payment_details);
            $payment_details['message'] = $callbackData->message;

            // Установим статус оплачен
            $ordersEntity->update($order->id, ['paid' => 1]);
            
            // Также обновим данные оплаты
            $paymentsEntity->update($privatPaymentData->id, [
                'message' => $callbackData->message,
                'token' => null,
            ]);
            
            // Спишем товары  
            $ordersEntity->close($order->id);
            $notify->emailOrderUser($order->id);
            $notify->emailOrderAdmin($order->id);
        }
    }
}