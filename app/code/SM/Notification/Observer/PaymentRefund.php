<?php
/**
 * SMCommerce
 *
 * @category  SM
 * @package   SM_Notification
 *
 * Date: January, 08 2021
 * Time: 4:13 PM
 * User: VooThanh DEV
 *
 * @author    SMCommerce Core Team <connect@smartosc.com>
 * @copyright Copyright SMCommerce (http://smartosc.com/)
 */

namespace SM\Notification\Observer;

class PaymentRefund implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * @var \SM\Notification\Model\Notification\Generate
     */
    protected $generate;

    /**
     * @var \SM\Notification\Model\ResourceModel\Notification
     */
    protected $resource;

    /**
     * @var \Magento\Framework\Logger\Monolog|null
     */
    protected $logger;

    /**
     * PaymentRefund constructor.
     *
     * @param \SM\Notification\Model\Notification\Generate      $generate
     * @param \SM\Notification\Model\ResourceModel\Notification $resource
     * @param \Magento\Framework\Logger\Monolog|null            $logger
     */
    public function __construct(
        \SM\Notification\Model\Notification\Generate $generate,
        \SM\Notification\Model\ResourceModel\Notification $resource,
        \Magento\Framework\Logger\Monolog $logger = null
    ) {
        $this->generate = $generate;
        $this->resource = $resource;
        $this->logger = $logger;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /** @var \Magento\Sales\Model\Order\Creditmemo $creditMemo */
        $creditMemo = $observer->getEvent()->getData('creditmemo');
        $order = $creditMemo->getOrder();
        
        if (!$order->getData('parent_order')) {
            return;
        }

        $notify = $this->generate->refundOrder($order, $order->getSubtotal() == $order->getSubtotalRefunded());
        try {
            if ($notify) {
                $this->resource->save($notify);
            }
        } catch (\Exception $e) {
            if ($this->logger) {
                $this->logger->error(
                    'Can not create refund notification : ' . $e->getMessage(),
                    $e->getTrace()
                );
            }
        }
    }
}
