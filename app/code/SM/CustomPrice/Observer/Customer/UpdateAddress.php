<?php


namespace SM\CustomPrice\Observer\Customer;

use Magento\Customer\Model\Address;
use SM\CustomPrice\Model\Customer;

class UpdateAddress implements \Magento\Framework\Event\ObserverInterface
{

    /**
     * @var \SM\CustomPrice\Model\Customer
     */
    protected $customer;
    /**
     * @var \SM\CustomPrice\Helper\Customer
     */
    protected $customerHelper;

    public function __construct(
        \SM\CustomPrice\Model\Customer $customer,
        \SM\CustomPrice\Helper\Customer $customerHelper
    )
    {
        $this->customer = $customer;
        $this->customerHelper = $customerHelper;
    }

    /**
     * Address after save event handler
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /** @var $customerAddress Address */
        $customerAddress = $observer->getCustomerAddress();
        /** @var \SM\CustomPrice\Model\Customer $customer */
        $customer      = $customerAddress->getCustomer();
        $customerModel = $this->customer->load($customer->getId());
        $newDistrict   = $customerAddress->getDistrict();
        $oldDistrict   = $customerAddress->getOrigData('district');
        $newCity   = $customerAddress->getCity();
            /*
            * if already billing address -> check it is main address
            * and if it is new account
            */
        if (($customer->getDefaultBillingAddress() instanceof \Magento\Customer\Model\Address\AbstractAddress
             && $customer->getDefaultBillingAddress()->getId() == $customerAddress->getId()
             &&$newDistrict!=$oldDistrict)
            || !$customerModel->getDataModel()->getCustomAttribute(Customer::OMNI_STORE_ID)) {
            $this->customerHelper->updateDistrictAndOmniStoreForCustomer($customerModel, $newDistrict, $newCity);
        }

    }
}
