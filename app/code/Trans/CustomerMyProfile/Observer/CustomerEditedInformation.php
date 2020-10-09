<?php
/**
 * @project    ct-corp-transmart
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @author     Duc Nguyen Hoang <ducnh2@smartosc.com>
 * @copyright  Copyright © 2020 SmartOSC. All rights reserved.
 * @url        http://www.smartosc.com
 */

namespace Trans\CustomerMyProfile\Observer;

use Magento\Framework\Event\Observer;
use Trans\CustomerMyProfile\Helper\Data;

/**
 * Class CustomerEditedInformation
 * @package Trans\CustomerMyProfile\Observer
 */
class CustomerEditedInformation implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * @var \SM\Customer\Model\Email\Sender
     */
    protected $emailSender;

    /**
     * @var \Magento\Customer\Api\CustomerRepositoryInterface
     */
    protected $customerRepository;

    /**
     * CustomerEditedInformation constructor.
     * @param \SM\Customer\Model\Email\Sender $emailSender
     * @param \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository
     */
    public function __construct(
        \SM\Customer\Model\Email\Sender $emailSender,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository
    ) {
        $this->emailSender = $emailSender;
        $this->customerRepository = $customerRepository;
    }

    /**
     * @inheritDoc
     */
    public function execute(Observer $observer)
    {
        if ($observer->getData('type')) {
            if ($observer->getData('type') === Data::CHANGE_TELEPHONE ||
                $observer->getData('type') === Data::CHANGE_EMAIL ||
                $observer->getData('type') === Data::CHANGE_PASSWORD
            ) {
                return;
            }
        }

        try {
            $email = $observer->getData('email');
            $customer = $this->customerRepository->get($email);
            $this->emailSender->sendChangePersonalInformation($customer);
        } catch (\Exception $e) {
        }
    }
}
