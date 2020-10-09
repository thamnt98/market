<?php

namespace SM\Customer\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use SM\GTM\Helper\Data;

/**
 * Class CustomerInfo
 * @package SM\Customer\Observer
 */
class CustomerInfo implements ObserverInterface
{
    /**
     * @var Data
     */
    protected $helperGtm;

    /**
     * CustomerInfo constructor.
     * @param Data $helperGtm
     */
    public function __construct(
        Data $helperGtm
    ) {
        $this->helperGtm = $helperGtm;
    }

    /**
     * @param Observer $observer
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function execute(Observer $observer)
    {
        $customer = $observer->getEvent()->getData('customer');
        $city = $observer->getEvent()->getData('city');
        $district = $observer->getEvent()->getData('district');
        if ($city && $district) {
            $storeInfo = $this->helperGtm->setCustomerStore($city, $district);
            if ($storeInfo) {
                $customer->setCustomAttribute('store_name_gtm', $storeInfo['store_name']);
                $customer->setCustomAttribute('store_id_gtm', $storeInfo['store_id']);
            }
        }
    }
}
