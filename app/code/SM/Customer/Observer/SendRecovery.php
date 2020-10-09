<?php
/**
 * @category    SM
 * @package     SM_Customer
 * @license     http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 *
 * @author      Nam Nguyen <namnd2@smartosc.com>
 *
 * @copyright   Copyright (c) SmartOSC. All rights reserved.
 * @url http://www.smartosc.com/
 */

namespace SM\Customer\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\LocalizedException;
use SM\Customer\Model\Email\Sender as EmailSender;

class SendRecovery implements ObserverInterface
{
    /**
     * @var EmailSender
     */
    protected $emailSender;

    /**
     * CustomerSaveEmailAfter constructor.
     * @param EmailSender $emailSender
     */
    public function __construct(
        EmailSender $emailSender
    ) {
        $this->emailSender = $emailSender;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        try {
            $customer = $observer->getData('customer');
            $type = $observer->getData('type');
            $this->emailSender->sendRecoveryEmail($customer, $type);
        } catch (\Exception $e) {
            throw new LocalizedException(__($e->getMessage()));
        }
    }
}
