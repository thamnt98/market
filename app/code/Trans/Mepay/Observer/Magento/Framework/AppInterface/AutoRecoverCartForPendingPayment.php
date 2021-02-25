<?php 
/**
 * @category Trans
 * @package  Trans_Mepay
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   Anan Fauzi <anan.fauzi@transdigital.co.id>
 *
 * Copyright © 2020 PT CT Corp Digital. All rights reserved.
 * http://www.ctcorpora.com
 */
namespace Trans\Mepay\Observer\Magento\Framework\AppInterface;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
use Magento\Customer\Model\SessionFactory as CustomerSession;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Model\Order;
use Trans\Mepay\Helper\Restore as RestoreHelper;
use Trans\Mepay\Helper\Order as OrderHelper;

class AutoRecoverCartForPendingPayment implements ObserverInterface
{
    /**
     * @var array
     */
    const ORDER_RESTORE_CONDITIONS = [
        OrderInterface::STATUS => Order::STATE_PENDING_PAYMENT
    ];

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @var \Trans\Mepay\Helper\Restore
     */
    protected $restoreHelper;

    /**
     * @var \Trans\Mepay\Helper\Order
     */
    protected $orderHelper;

    /**
     * Constructor
     * @param CustomerSession $customerSession
     * @param RestoreHelper $restoreHelper
     * @param OrderHelper $orderHelper
     */
    public function __construct(
        CustomerSession $customerSession,
        RestoreHelper $restoreHelper,
        OrderHelper $orderHelper
    ) {
        $this->customerSession = $customerSession;
        $this->restoreHelper = $restoreHelper;
        $this->orderHelper = $orderHelper;
    }

    /**
     * Execution gateway
     * @param  Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        try {
            if ($this->restoreHelper->isValid()) {
                if ($customerId = $this->customerSession->create()->getCustomerId()) {
                    $order = $this->getCustomerOrder($customerId);
                    if (is_array($order) && empty($order) === false) {
                        $this->recoverByLastOrder($customerId, $order);
                    }
                }
            }
        } catch (\Exception $e) {
            $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/recover.log');
            $logger = new \Zend\Log\Logger();
            $logger->addWriter($writer);
            $logger->info('RecoverCartStart');
        }
        
    }

    /**
     * Recover cart by last order
     * @param  array  $order
     * @return void
     */
    public function recoverByLastOrder(int $customerId, array $order)
    {
        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/recover.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
        $logger->info('RecoverCartStart');
        try {
            foreach (self::ORDER_RESTORE_CONDITIONS as $key => $value) {
                $logger->info('Status ==> '.$order[$key]);
                if(isset ($order[$key]) && $order[$key] == $value) {
                    $orderId = $order[OrderInterface::ENTITY_ID];
                    $orderDate = $order[OrderInterface::CREATED_AT];
                    if ($this->orderHelper->isOrderPaymentIsExpired($orderId, $orderDate)) {
                        $logger->info('Canceling previous order Start ==> ');
                        $this->cancelOrder($order[OrderInterface::ENTITY_ID]);
                        $logger->info('Recovering Start ==> ');
                        $this->restoreCart($customerId, $order[OrderInterface::QUOTE_ID]);
                        
                    }
                }
            }
            $logger->info('RecoverCartEnd-Success');
            return true;
        } catch (\Exception $e) {
            //
        }
        $logger->info('RecoverCartEnd-Failed');
        return false;
    }

    /**
     * Get customer order
     * @param  int $customerId
     * @return array|null
     */
    public function getCustomerOrder($customerId)
    {
        return $this->orderHelper->getLastOrderByCustomerId($customerId); 
    }

    /**
     * Cancel the order
     * @param  int $orderId
     * @return void
     */
    public function cancelOrder(int $orderId)
    {
        $this->orderHelper->doCancelationOrder($orderId);
    }

    /**
     * Restore the cart
     * @param  int $customerId
     * @param  int $quoteIds
     * @return void
     */
    public function restoreCart(int $customerId, int $quoteId)
    {
        $activeCart = $this->restoreHelper->hasActiveCart($customerId);
        if ($activeCart == false) {
            $quote = $this->restoreHelper->restoreQuote($quoteId);
        }
    }
}
