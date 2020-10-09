<?php

namespace SM\GTM\Controller\Gtm;

use Magento\Customer\Model\Customer;
use Magento\Customer\Model\ResourceModel\CustomerFactory;
use Magento\Customer\Model\SessionFactory as CustomerSessionFactory;
use Magento\Framework\App\Action\Context;

class ChangeStore extends \Magento\Framework\App\Action\Action
{
    /**
     * @var CustomerSessionFactory
     */
    private $customerSession;
    /**
     * @var CustomerFactory
     */
    private $customerFactory;
    /**
     * @var Customer
     */
    private $customer;

    public function __construct(
        Context $context,
        CustomerSessionFactory $customerSession,
        CustomerFactory $customerFactory,
        Customer $customer
    ) {
        parent::__construct($context);
        $this->customerSession = $customerSession;
        $this->customerFactory = $customerFactory;
        $this->customer = $customer;
    }

    /**
     * @inheritDoc
     */
    public function execute()
    {
        $storeInfo = $this->getRequest()->getParam('storeInfo');
        if (array_key_exists('store_name', $storeInfo) && array_key_exists('store_ID', $storeInfo)) {
            $storeName = $storeInfo['store_name'];
            $storeID = $storeInfo['store_ID'];

            $customerId = $this->customerSession->create()->getCustomerId();
            $customer = $this->customer->load($customerId);
            $customerData = $customer->getDataModel();

            $customerData->setCustomAttribute('store_name_gtm', $storeName);
            $customer->updateData($customerData);
            $customerResource = $this->customerFactory->create();
            $customerResource->saveAttribute($customer, 'store_name_gtm');

            $customerData->setCustomAttribute('store_id_gtm', $storeID);
            $customer->updateData($customerData);
            $customerResource = $this->customerFactory->create();
            $customerResource->saveAttribute($customer, 'store_id_gtm');
        }
    }
}
