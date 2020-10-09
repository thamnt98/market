<?php
/**
 * @category Trans
 * @package  Trans_IntegrationOrder
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 * @author   Muhammad Randy <muhammad.randy@ctcorpdigital.co.id>
 *
 * Copyright © 2019 CT Corp Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\IntegrationOrder\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Class StatusPaymentOms
 */
class StatusPaymentOms implements ObserverInterface
{
    /**
     * @var \Trans\IntegrationOrder\Logger\Logger
     */
    protected $logger;

    /**
     * @var \Trans\IntegrationOrder\Helper\Data
     */
    protected $dataHelper;

    /**
     * @var \Trans\IntegrationOrder\Api\PaymentStatusInterface
     */
    protected $paymentInterface;

    /**
     * @var \Trans\IntegrationOrder\Api\IntegrationOrderPaymentRepositoryInterface
     */
    protected $orderPaymentRepo;

    /**
     * @param \Trans\IntegrationOrder\Helper\Data $dataHelper
     * @param \Trans\IntegrationOrder\Api\IntegrationOrderPaymentRepositoryInterface; $orderPaymentRepo
     */
    public function __construct(
        \Trans\IntegrationOrder\Helper\Data $dataHelper,
        \Trans\IntegrationOrder\Api\PaymentStatusInterface $paymentInterface,
        \Trans\IntegrationOrder\Api\IntegrationOrderPaymentRepositoryInterface $orderPaymentRepo
    ) {
        $this->dataHelper       = $dataHelper;
        $this->orderPaymentRepo = $orderPaymentRepo;
        $this->paymentInterface = $paymentInterface;

        $this->logger = $dataHelper->getLogger();
    }

    /**
     * execute data status payment send to oms
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $this->logger->info('----------------------- Run Observer ' . __CLASS__ . ' -------------------------');
        try {
            $refNumber     = $observer->getEvent()->getReferenceNumber();
            $paymentStatus = $observer->getEvent()->getPaymentStatus();
            $postfield     = $this->paymentInterface->sendStatusPayment($refNumber, $paymentStatus);
        } catch (NoSuchEntityException $e) {
            $this->logger->info('error');
            $this->logger->info($e->getMessage());
        } catch (\Exception $e) {
            $this->logger->info('error');
            $this->logger->info($e->getMessage());
        }
        $this->logger->info('----------------------- end observera ' . __CLASS__ . ' -------------------------');
    }
}
