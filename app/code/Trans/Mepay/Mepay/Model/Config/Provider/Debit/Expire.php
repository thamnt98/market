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
namespace Trans\Mepay\Model\Config\Provider\Debit;

use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Api\Data\OrderInterface;
use Trans\Mepay\Model\Config\Provider\Debit;
use Trans\Mepay\Model\Config\Expire as ExpireParent;
use Trans\Mepay\Helper\Payment\Transaction;

class Expire extends ExpireParent
{
    /**
     * @var \Trans\Mepay\Helper\Payment\Transaction
     */
    protected $transactionHelper;

    /**
     * Constructor
     * @param OrderInterface $orderRepo [description]
     */
    public function __construct(
        OrderRepositoryInterface $orderRepo,
        Transaction $transactionHelper
    ) {
        $this->transactionHelper = $transactionHelper;
        parent::__construct($orderRepo);
    }

    /**
     * Is payment method valid
     * @param  \Magento\Sales\Api\Data\OrderPaymentInterface $payment
     * @return boolean
     */
    public function isMethodValid($payment)
    {
        try {
            if ($payment->getMethod() == Debit::CODE)
                return true;
        } catch (\Exception $e) {
            throw $e;
        }
        return false;
    }

    /**
     * Protected is payment expired
     * @return bool
     * @throw \Exception
     */
    protected function _isExpired()
    {
        try {
            if ($this->transactionHelper->getPgResponse($this->order->getId()))
                return ($this->calcInterval() > self::EXPIRATION_TIME)? true : false;
        } catch (\Exception $e) {
            throw $e;
        }
        return true;
    }
}
