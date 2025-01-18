<?php


namespace Okay\Modules\SimplaMarket\PrivatPayPart\Extenders;


use Okay\Core\EntityFactory;
use Okay\Core\Modules\Extender\ExtensionInterface;
use Okay\Core\Request;
use Okay\Entities\PaymentsEntity;
use Okay\Modules\SimplaMarket\PrivatPayPart\Entities\PrivatPaymentEntity;

class FrontExtender implements ExtensionInterface
{

    private $entityFactory;
    private $request;

    public function __construct(EntityFactory $entityFactory, Request $request)
    {
        $this->entityFactory = $entityFactory;
        $this->request = $request;
    }

    public function getCartPaymentsList($payments)
    {

        foreach ($payments as $payment) {
            if (!empty($payment->settings) && is_string($payment->settings)) {
                $payment->settings = @unserialize($payment->settings);
            }
        }

        return $payments;
    }

    // Записываем информацию о кол-ве платежей и прочем
    public function addPrivatPaymentData($result, $order)
    {
        if (!empty($order->payment_method_id)) {
            /** @var PaymentsEntity $paymentsEntity */
            $paymentsEntity = $this->entityFactory->get(PaymentsEntity::class);
            $paymentMethod = $paymentsEntity->findOne(['id' => $order->payment_method_id]);

            if (!empty($paymentMethod)
                && ($paymentMethod->module == 'SimplaMarket/PrivatPayPart')
            ) {
                /** @var PrivatPaymentEntity $privatPaymentEntity */
                $privatPaymentEntity = $this->entityFactory->get(PrivatPaymentEntity::class);
                $paymentMethodSettings = unserialize($paymentMethod->settings);

                if (!empty($paymentMethodSettings)
                    && (is_object($paymentMethodSettings) && property_exists('paymentMethodSettings', 'merchant_type')
                        || is_array($paymentMethodSettings) && !empty($paymentMethodSettings['merchant_type']))
                    && !empty($merchant_type = unserialize($paymentMethod->settings)['merchant_type'])
                    && !empty($this->request->post('privat_number_of_months')[$merchant_type])
                ) {
                    $privatPaymentEntity->add([
                        'order_id'          => $order->id,
                        'number_of_months'  => $this->request->post('privat_number_of_months')[$merchant_type],
                        'pay_count'         => $this->request->post('privat_pay_count')[$merchant_type],
                        'value'             => $this->request->post('privat_pp_value')[$merchant_type],
                    ]);
                }
            }
        }
    }

}