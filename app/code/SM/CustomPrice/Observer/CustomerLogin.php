<?php


namespace SM\CustomPrice\Observer;

use Magento\Customer\Model\Session;
use Magento\Framework\Event\ObserverInterface;

class CustomerLogin implements ObserverInterface
{
    /**
     * @var Session
     */
    protected $customerSession;

    public function __construct(Session $customerSession)
    {
        $this->customerSession = $customerSession;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /** @var \Magento\Customer\Model\Customer $customer */
        $customer = $observer->getEvent()->getCustomer();
        $lastTimeChangPwd = $customer->getDataModel()->getCustomAttribute('last_time_change_pwd');
        if (!empty($lastTimeChangPwd)) {
            $this->customerSession->setLastTimeChangePwdWhenLogged($lastTimeChangPwd->getValue());
        }
    }
}
