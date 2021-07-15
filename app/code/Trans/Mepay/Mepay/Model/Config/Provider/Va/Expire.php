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
namespace Trans\Mepay\Model\Config\Provider\Va;

use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Api\Data\OrderInterface;
use Trans\Mepay\Model\Config\Provider\Va;
use Trans\Mepay\Model\Config\Expire as ExpireParent;
use Trans\Mepay\Helper\Payment\Transaction;
use Trans\Mepay\Model\Config\Provider\Va\Expire\Extractor;
use Trans\Mepay\Api\Data\TransactionStatusDataInterface;

class Expire extends ExpireParent
{
    /**
     * @var \Trans\Mepay\Helper\Payment\Transaction
     */
    protected $transactionHelper;

    /**
     * @var \Trans\Mepay\Model\Config\Provider\Va\Expire\Extractor
     */
    protected $extractor;

    /**
     * @var string
     */
    protected $expireDate;

    /**
     * Constructor
     * @param OrderInterface $orderRepo [description]
     */
    public function __construct(
        OrderRepositoryInterface $orderRepo,
        Transaction $transactionHelper,
        Extractor $extractor
    ) {
        $this->transactionHelper = $transactionHelper;
        $this->extractor = $extractor;
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
            if ($payment->getMethod() == Va::CODE_VA)
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
            if ($this->transactionHelper->getPgResponse($this->order->getId())) {
                $pgData = $this->extractor->extract($this->order->getId());
                if ($pgData[TransactionStatusDataInterface::VA_NUMBER]) {
                    $this->expireDate = $pgData[TransactionStatusDataInterface::EXPIRE_TIME];
                    $txExpireDate = str_replace('T',' ', $this->expireDate);
                    $txExpireDate = substr($txExpireDate, 0, strpos($txExpireDate, "."));
                    $dateNow = new \DateTime(date("Y-m-d H:i:s"));
                    $dateEnd = new \DateTime($txExpireDate);
                    return ($dateNow > $dateEnd)? true : false;
                }
            }
            //return ($this->calcInterval() > self::EXPIRATION_TIME)? true : false;
        } catch (\Exception $e) {
            throw $e;
        }
        return true;
    }

}