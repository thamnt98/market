<?php

namespace SM\Customer\Observer;

use Magento\Customer\Model\Customer;
use Magento\Framework\Event\ObserverInterface;

class ChangeCredential implements ObserverInterface
{
    /**
     * @var \SM\Customer\Helper\Customer
     */
    protected $customerHelper;

    public function __construct(\SM\Customer\Helper\Customer $customerHelper)
    {
        $this->customerHelper = $customerHelper;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /** @var \Magento\Customer\Model\Data\Customer $customer */
        $customer = $observer->getEvent()->getCustomer();

        $customerOrig = $customer->getOrigData();
        if (!$customerOrig) {
            return;
        }
        $emailOrig = $customerOrig['email'];
        $phoneOrig = $customerOrig['telephone'];
        $passwordHashOrig = $customerOrig['password_hash'];

        $email    = $customer->getEmail();
        $phone    = $customer->getTelephone();
        $passwordHash = $customer->getPasswordHash();

        if ($email != $emailOrig ||
            $phoneOrig != $phone ||
            $passwordHashOrig != $passwordHash
        ) {
            $this->customerHelper->logout($customer->getId());
        }
    }
}
