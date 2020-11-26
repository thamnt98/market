<?php

/**
 * @category SM
 * @package SM_Checkout
 * @license     http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @author      Chinhvd <chinhvd@smartosc.com>
 * @copyright   Copyright (c) SmartOSC. All rights reserved.
 * @url http://www.smartosc.com/
 */

namespace SM\Checkout\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\LocalizedException;
use SM\Checkout\Helper\Email;
use SM\Email\Model\Email\Sender as EmailSender;
use Psr\Log\LoggerInterface;

class PaymentExpireSendEmail implements ObserverInterface
{
    /**
     * @var EmailSender
     */
    protected $emailSender;

    /**
     * @var Email
     */
    protected $emailHelper;

    /**
     *
     * @var LoggerInterface
     */
    protected $logger;

    public function __construct(
        EmailSender $emailSender,
        \SM\Checkout\Helper\Email $emailHelper,
        LoggerInterface $logger
    ) {
        $this->emailHelper = $emailHelper;
        $this->emailSender = $emailSender;
        $this->logger      = $logger;
    }

    public function execute(Observer $observer)
    {
        try {
            $order = $observer->getEvent()->getOrder();
            if ($order) {
                $templateId = $this->emailHelper->getExpiredTemplateId();
                if (!$templateId) {
                    throw new LocalizedException(__('Template does not exist'));
                }

                $sender = $this->emailHelper->getExpiredSender();
                $templateVars = [
                    'order' => $order
                ];
                $email = $order->getCustomerEmail();
                $name = $order->getCustomerName();
                $this->emailSender->send(
                    $templateId,
                    $sender,
                    $email,
                    $name,
                    $templateVars,
                    (int) $order->getStoreId()
                );
            }
        } catch (\Exception $e) {
            $this->logger->debug($e->getMessage());
        }
    }
}
