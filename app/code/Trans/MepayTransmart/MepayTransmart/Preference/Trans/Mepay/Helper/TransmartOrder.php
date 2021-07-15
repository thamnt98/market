<?php
/**
 * @category Trans
 * @package  Trans_MepayTransmart
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   Anan Fauzi <anan.fauzi@transdigital.co.id>
 *
 * Copyright © 2020 PT CT Corp Digital. All rights reserved.
 * http://www.ctcorpora.com
 */
namespace Trans\MepayTransmart\Preference\Trans\Mepay\Helper;

use Magento\Framework\App\Helper\Context;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Sales\Model\ResourceModel\Order as OrderResource;
use Magento\Sales\Api\OrderManagementInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Framework\Event\ManagerInterface;
use Trans\Sprint\Helper\Config as SprintConfig;
use Trans\MepayTransmart\Model\Config\Payment\Sprint as SprintPayment;
use Trans\Mepay\Logger\LoggerWrite;
use Trans\Mepay\Helper\Order as MepayOrderHelper;
use Trans\Mepay\Helper\Data;

class TransmartOrder extends MepayOrderHelper
{
    protected $sprintConfig;
    protected $sprintPayment;
    /**
     * Constructor
     * @param Context $context
     * @param OrderResource $orderResource
     * @param OrderManagementInterface $orderManagement
     * @param OrderRepositoryInterface $orderRepo
     * @param ManagerInterface $eventManager
     * @param LoggerWrite $logger
     */
    public function __construct(
        Context $context,
        OrderResource $orderResource,
        OrderManagementInterface $orderManagement,
        OrderRepositoryInterface $orderRepo,
        ManagerInterface $eventManager,
        LoggerWrite $logger,
        SprintConfig $sprintConfig,
        SprintPayment $sprintPayment
    ) {
        $this->sprintConfig = $sprintConfig;
        $this->sprintPayment = $sprintPayment;
        parent::__construct(
            $context,
            $orderResource,
            $orderManagement,
            $orderRepo,
            $eventManager,
            $logger
        );
    }

    /**
     * Is payment expired
     * @param  int $orderId
     * @param  string $createdAt
     * @return boolean
     */
    public function isOrderPaymentIsExpired(int $orderId, string $createdAt)
    {
        $instance = null;
        $payment = $this->getPaymentData($orderId);
        if ($payment['method'] == \Trans\Mepay\Model\Config\Provider\Cc::CODE_CC)
            $instance = Data::getClassInstance('Trans\Mepay\Model\Config\Provider\Cc\Expire');
        if ($payment['method'] == \Trans\Mepay\Model\Config\Provider\CcDebit::CODE)
            $instance = Data::getClassInstance('Trans\Mepay\Model\Config\Provider\CcDebit\Expire');
        if ($payment['method'] == \Trans\Mepay\Model\Config\Provider\Debit::CODE)
            $instance = Data::getClassInstance('Trans\Mepay\Model\Config\Provider\Debit\Expire');
        if ($payment['method'] == \Trans\Mepay\Model\Config\Provider\Qris::CODE_QRIS)
            $instance = Data::getClassInstance('Trans\Mepay\Model\Config\Provider\Qris\Expire');
        if ($payment['method'] == \Trans\Mepay\Model\Config\Provider\Va::CODE_VA)
            $instance = Data::getClassInstance('Trans\Mepay\Model\Config\Provider\Va\Expire');
        if ($payment['method'] == \Trans\Mepay\Model\Config\Provider\AllbankCc::CODE)
            $instance = Data::getClassInstance('Trans\Mepay\Model\Config\Provider\AllbankCc\Expire');
        if ($payment['method'] == \Trans\Mepay\Model\Config\Provider\AllbankDebit::CODE)
            $instance = Data::getClassInstance('Trans\Mepay\Model\Config\Provider\AllbankDebit\Expire');

         if ($this->sprintConfig->getPaymentChannel($payment['method']) && Data::isMegaMethod($payment['method']) == false){
            return $this->sprintPayment->isExpired($orderId);
        }

        if ($instance)
            return $instance->isExpired($orderId);
        throw new \Exception("Error Processing Request", 1);
    }
}