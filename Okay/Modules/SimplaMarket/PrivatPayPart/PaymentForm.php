<?php


namespace Okay\Modules\SimplaMarket\PrivatPayPart;


use Okay\Core\EntityFactory;
use Okay\Core\Modules\AbstractModule;
use Okay\Core\Modules\Interfaces\PaymentFormInterface;
use Okay\Core\Money;
use Okay\Entities\OrdersEntity;
use Okay\Entities\PaymentsEntity;
use Okay\Modules\SimplaMarket\PrivatPayPart\Entities\PrivatPaymentEntity;

class PaymentForm extends AbstractModule implements PaymentFormInterface
{	
    
    private $entityFactory;
    private $money;
    
    public function __construct(EntityFactory $entityFactory, Money $money)
    {
        parent::__construct();
        $this->entityFactory = $entityFactory;
        $this->money = $money;
    }

    /**
     * @inheritDoc
     */
    public function checkoutForm($orderId)
	{
	    /** @var OrdersEntity $ordersEntity */
        $ordersEntity = $this->entityFactory->get(OrdersEntity::class);
        
	    /** @var PrivatPaymentEntity $privatPaymentEntity */
        $privatPaymentEntity = $this->entityFactory->get(PrivatPaymentEntity::class);

        /** @var PaymentsEntity $paymentsEntity */
        $paymentsEntity = $this->entityFactory->get(PaymentsEntity::class);

        $order            = $ordersEntity->get((int)$orderId);
        $paymentMethod    = $paymentsEntity->get($order->payment_method_id);
        $paymentSettings  = $paymentsEntity->getPaymentSettings($paymentMethod->id);
        $merchantType     = $paymentSettings['merchant_type'];
        
        $res['order_id'] = $order->id;

        $privatPaymentData = $privatPaymentEntity->findOne(['order_id' => $order->id]);
        
        if (empty($privatPaymentData)) {
            $this->design->assign('needSetPaidData', true);
        } else {
            $this->design->assign('privatPaymentData', $privatPaymentData);
        }
        
        $this->design->assign('order_id', $order->id);
        $this->design->assign('merchant_type', $merchantType);
        return $this->design->fetch('form.tpl');
	}
}