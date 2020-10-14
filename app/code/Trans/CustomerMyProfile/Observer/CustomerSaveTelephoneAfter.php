<?php
/**
 * @category    Trans
 * @package     Trans_CustomerMyProfile
 * @license     http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 *
 * @author      Nam Nguyen <namnd2@smartosc.com>
 *
 * @copyright   Copyright (c) SmartOSC. All rights reserved.
 * @url http://www.smartosc.com/
 */

namespace Trans\CustomerMyProfile\Observer;

use Magento\Framework\Event\ObserverInterface;
use SM\Customer\Model\Email\Sender as EmailSender;

class CustomerSaveTelephoneAfter implements ObserverInterface
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
            $this->emailSender->sendChangeTelephoneEmail($customer);
        } catch (\Exception $e) {
            //$this->messageManager->addError($e->getMessage());
        }
    }
}
