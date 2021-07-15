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
namespace Trans\Mepay\Model\CartRecovery;

use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Model\Order;
use Trans\Mepay\Helper\Restore;
use Trans\Mepay\Helper\Order as OrderHelper;

class IsValid
{
    /**
     * @var array
     */
    const ORDER_RESTORE_CONDITIONS = [
        OrderInterface::STATUS => Order::STATE_PENDING_PAYMENT
    ];

    /**
     * @var \Trans\Mepay\Helper\Restore
     */
    protected $restoreHelper;

    /**
     * @var \Trans\Mepay\Helper\Order
     */
    protected $orderHelper;

    /**
     * @var null | \Magento\Sales\Api\Data\OrderInterface
     */
    protected $order;

    /**
     * Constructor
     * @param Restore $restoreHelper
     * @param Order   $orderHelper
     */
    public function __construct(
        Restore $restoreHelper,
        OrderHelper $orderHelper
    ){
        $this->order = null;
        $this->restoreHelper = $restoreHelper;
        $this->orderHelper = $orderHelper;
    }

    /**
     * Check condition for restore
     * @param  int  $customerId [description]
     * @return boolean
     */
    public function isRestoreConditionValid($customerId)
    {
        if ($this->restoreHelper->isValid() == false)
            return false;
        $this->order = $this->getCustomerOrder($customerId);
        if (is_array($this->order) == false)
            return false;
        if (empty($this->order))
            return false;
        if ($this->isOrderCanRestore($this->order) == false)
            return false;
        if ($this->isOrderExpired($this->order) == false)
            return false;
        return true;
    }

    /**
     * Get valid latest order
     * @return [type] [description]
     */
    public function getValidLatestOrder()
    {
        return $this->order;
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
     * Is order fulfill condition to be restored
     * @param  array  $order
     * @return boolean
     */
    public function isOrderCanRestore($order)
    {
        foreach (self::ORDER_RESTORE_CONDITIONS as $key => $value) {
            if(isset ($order[$key]) && $order[$key] == $value) {
                return true;
            }
        }
        return false;
    }

    /**
     * Is order expired
     * @param  array  $order
     * @return boolean
     */
    public function isOrderExpired($order)
    {
        return $this->orderHelper->isOrderPaymentIsExpired(
            $order[OrderInterface::ENTITY_ID], 
            $order[OrderInterface::CREATED_AT]
        );
    }
}
