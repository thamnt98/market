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
namespace Trans\Mepay\Model\Config;

use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Api\Data\OrderInterface;
use Trans\Mepay\Model\Config\Provider\Mepay;

class Expire
{
    /**
     * @var int
     */
    const EXPIRATION_TIME = 0;

    /**
     * @var string
     */
    const ORDER_MISSING_MESSAGE = 'Please, set the order please';

    /**
     * @var \Magento\Sales\Api\OrderRepositoryInterface
     */
    protected $orderRepo;

    /**
     * @var \Magento\Sales\Api\Data\OrderInterface
     */
    protected $order;

    /**
     * @var \Magento\Sales\Api\Data\OrderPaymentInterface
     */
    protected $payment;

    /**
     * Constructor
     * @param OrderInterface $orderRepo [description]
     */
    public function __construct(
        OrderRepositoryInterface $orderRepo
    ) {
        $this->orderRepo = $orderRepo;
        $this->order = null;
        $this->payment = null;
    }

    /**
     * Is order valid
     * @param  int  $orderId
     * @param  bool $isThrow
     * @return bool
     * @throw \Exception
     */
    public function isValid(int $orderId, bool $isThrow = false)
    {
        $result = false;
        try {
            $this->setOrder($orderId);
            $this->setPayment();
            $result = $this->isMethodValid($this->payment);
        } catch (\Exception $e) {
            $this->logger->log($e->getMessage());
            if ($isThrow)
                throw $e;
        }
        return $result;
    }

    /**
     * Is payment expired
     * @param  int  $orderId
     * @param  bool $isThrow
     * @return bool|null
     * @throw \Exception
     */
    public function isExpired(int $orderId, bool $isThrow = true)
    {
        try {
            $this->isValid($orderId, $isThrow);
            return $this->_isExpired();
        } catch (\Exception $e) {
            if ($isThrow)
                throw $e;
        }
        /** will return null if there an error and throw is disabled  */
        return null;
    }

    /**
     * Is payment method valid
     * @param  \Magento\Sales\Api\Data\OrderPaymentInterface $payment
     * @return boolean
     */
    public function isMethodValid($payment)
    {
        try {
            if ($payment->getMethod())
                return true;
        } catch (\Exception $e) {
            throw $e;
        }
        return false;
    }

    /**
     * Set order property
     * @param int $orderId
     * @throw \Exception
     */
    protected function setOrder(int $orderId)
    {
        try {
            $this->order = $this->orderRepo->get($orderId);
        } catch (\Exception $e) {
            throw $e;
        }
        
    }

    /**
     * Set payment property
     * @throw \Exception
     */
    protected function setPayment()
    {
        try {
            $this->payment = $this->order->getPayment();
            return true;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Protected is payment expired
     * @return bool
     * @throw \Exception
     */
    protected function _isExpired()
    {
        try {
            return ($this->calcInterval() > self::EXPIRATION_TIME)? true : false;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Calculate interval expiration on seconds
     * @return int
     * @throw \Exception
     */
    protected function calcInterval()
    {
        try {
            $dateStart = new \DateTime($this->payment->getCreateAt());
            $dateEnd = new \DateTime(date("Y-m-d H:i:s"));
            $diff = $dateEnd->diff($dateStart);
            return ($diff->format('%r%a') * 24 * 60 * 60) + ($diff->h * 60 * 60) + ($diff->i * 60) + $diff->s;
        } catch (\Exception $e) {
            throw $e;
        }
    }
}
