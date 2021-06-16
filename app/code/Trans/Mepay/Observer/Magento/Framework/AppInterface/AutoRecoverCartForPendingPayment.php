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
use Trans\IntegrationOrder\Api\Data\IntegrationOrderInterface;
use Trans\Mepay\Model\CartRecovery\IsValid;
use Trans\Mepay\Helper\Restore as RestoreHelper;
use Trans\Mepay\Helper\Order as OrderHelper;

class AutoRecoverCartForPendingPayment implements ObserverInterface
{
    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @var \Trans\Mepay\Model\CartRecovery\IsValid
     */
    protected $isValid;

    /**
     * @var \Trans\Mepay\Helper\Restore
     */
    protected $restoreHelper;

    /**
     * @var \Trans\Mepay\Helper\Order
     */
    protected $orderHelper;

    /**
     * @var \Zend\Log\Logger()
     */
    protected $logger;

    /**
     * Constructor
     * @param CustomerSession $customerSession
     * @param RestoreHelper $restoreHelper
     * @param OrderHelper $orderHelper
     */
    public function __construct(
        CustomerSession $customerSession,
        IsValid $isValid,
        RestoreHelper $restoreHelper,
        OrderHelper $orderHelper
    ) {
        $this->customerSession = $customerSession;
        $this->isValid = $isValid;
        $this->restoreHelper = $restoreHelper;
        $this->orderHelper = $orderHelper;
        $this->setLogger();
    }

    /**
     * Execution gateway
     * @param  Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        try {
            if ($customerId = $this->customerSession->create()->getCustomerId()) {
                $this->restoreCustomerCartAndClosedPreviousOrder($customerId);
            }
        } catch (\Exception $e) {
            $this->logger->info('[RECOVER CART IS FAILED] ==>['.$e->getMessage().']');
        }
        
    }

    /**
     * Restore customer cart and closed previous created order
     * @param  int $customerId
     * @return boolean
     */
    public function restoreCustomerCartAndClosedPreviousOrder($customerId)
    {
        if($this->isValid->isRestoreConditionValid($customerId)) {
            $order = $this->isValid->getValidLatestOrder();
            $this->removeDoubleQuote($customerId);
            $this->restoreCart($order);
            $this->closeOrderByRefNum($order[IntegrationOrderInterface::REFERENCE_NUMBER]);
            return true;
        }
        return false;
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
     * Close the order
     * @param  int $orderId
     * @return void
     */
    public function closeOrder(int $orderId)
    {
        $this->orderHelper->setOrderClosed($orderId);
    }

    /**
     * Close order by their reference number
     * @param  string $refNum
     * @return void
     */
    public function closeOrderByRefNum(string $refNum)
    {
        $this->orderHelper->setOrderClosedByReffNumber($refNum);
    }


    /**
     * Restore the cart
     * @param  int $customerId
     * @param  int $quoteIds
     * @return void
     */
    public function restoreCart($order)
    {
        if ($this->restoreHelper->hasActiveCart() == false) {
            if (!$this->restoreHelper->restoreQuote()) {
                $this->restoreHelper->manualRestore($order[OrderInterface::QUOTE_ID]);
            }
        }
    }

    /**
     * Remove double quote happen on place order
     * @param  int $customerId
     * @return void
     */
    public function removeDoubleQuote($customerId)
    {
        $this->restoreHelper->removeDoubleQuote($customerId);
    }

    /**
     * Set logger property
     */
    public function setLogger()
    {
        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/recover.log');
        $this->logger = new \Zend\Log\Logger();
        $this->logger->addWriter($writer);
    }
}
