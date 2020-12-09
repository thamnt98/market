<?php

namespace SM\Checkout\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

/**
 * Class CreateSubOrders
 * @package SM\Checkout\Observer
 */
class CreateSubOrders implements ObserverInterface
{
    /**
     * @var \Magento\Sales\Model\ResourceModel\Order\CollectionFactory
     */
    protected $orderCollectionFactory;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $checkoutSession;

    /**
     * @var \SM\Checkout\Model\SendOMS
     */
    protected $sendOMS;

    /**
     * @var \SM\Checkout\Model\Checkout\Type\Multishipping
     */
    protected $multiShipping;

    /**
     * CreateSubOrders constructor.
     * @param \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \SM\Checkout\Model\SendOMS $sendOMS
     * @param \SM\Checkout\Model\Checkout\Type\Multishipping $multiShipping
     */
    public function __construct(
        \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory,
        \Magento\Checkout\Model\Session $checkoutSession,
        \SM\Checkout\Model\SendOMS $sendOMS,
        \SM\Checkout\Model\Checkout\Type\Multishipping $multiShipping
    ) {
        $this->orderCollectionFactory = $orderCollectionFactory;
        $this->checkoutSession = $checkoutSession;
        $this->sendOMS = $sendOMS;
        $this->multiShipping = $multiShipping;
    }

    /**
     * @param Observer $observer
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Exception
     */
    public function execute(Observer $observer)
    {
        $order = $observer->getEvent()->getOrder();
        $quote = $observer->getEvent()->getQuote();
        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/order-status.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
        $logger->info('SM\Checkout\Observer\CreateSubOrders' . '. Main-order-Id: ' . $order->getId() . '. Status: ' . $order->getStatus());
        if (!$quote->getIsVirtual()) {
            $area = $this->checkoutSession->getArea();
            if ($area == \SM\Checkout\Helper\OrderReferenceNumber::AREA_APP) {
                $isMobile = true;
            } else {
                $isMobile = false;
            }
            $this->multiShipping->createSuborders($order, $quote, $isMobile);

            $suborderIds = $this->orderCollectionFactory->create()->addAttributeToSelect('entity_id')
                ->addFieldToFilter('parent_order', $order->getId())->getAllIds();
            $this->sendOMS->processOrderOms($suborderIds, $order);
        } else {
            $this->multiShipping->createDigitalSuborder($order, $quote);
        }
    }
}
