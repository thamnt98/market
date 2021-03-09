<?php

namespace SM\CustomPrice\Helper;

use SM\CustomPrice\Model\ResourceModel\District;

class Customer
{
    /**
     * @var District
     */
    protected $district;
    /**
     * @var \Magento\Quote\Model\QuoteFactory
     */
    protected $quoteFactory;

    /**
     * @var \Magento\Framework\App\Request\DataPersistorInterface
     */
    protected $dataPersistor;

    /**
     * Customer constructor.
     * @param District $district
     * @param \Magento\Quote\Model\QuoteFactory $quoteFactory
     * @param \Magento\Framework\App\Request\DataPersistorInterface $dataPersistor
     */
    public function __construct(
        District $district,
        \Magento\Quote\Model\QuoteFactory $quoteFactory,
        \Magento\Framework\App\Request\DataPersistorInterface $dataPersistor
    ) {
        $this->district = $district;
        $this->quoteFactory = $quoteFactory;
        $this->dataPersistor = $dataPersistor;
    }

    /**
     * Update District and omni store id for customer by billing district Id
     *
     * @param \Magento\Customer\Model\Customer $customer
     * @param null $district_id
     * @param null $city
     * @throws \Exception
     */
    public function updateDistrictAndOmniStoreForCustomer(\Magento\Customer\Model\Customer $customer, $district_id = null, $city = null)
    {
        try {
            $customerData  = $customer->getDataModel();
            $omniStoreCode = $this->district->getOmniStoreCodeByDistrictId($district_id);
            $customerData->setCustomAttribute('district', $district_id);
            $customerData->setCustomAttribute('city', $city);
            $customerData->setCustomAttribute(\SM\CustomPrice\Model\Customer::OMNI_STORE_ID, ($omniStoreCode ?? $customer->getDefaultOmniStoreCode()));
            $customer->updateData($customerData);
            $customer->setData('ignore_validation_flag', true);
            $customer->save();
            $this->updateCart($customer->getId());
            $this->dataPersistor->set('update_omni_code', true);
        } catch (\Exception $exception) {
            throw new \Exception($exception->getMessage());
        }
    }

    public function updateCart($customerId)
    {
        $quote = $this->quoteFactory->create()->loadByCustomer($customerId);
        $quote->getShippingAddress()->setCollectShippingRates(false);
        $quote->collectTotals();
    }
}
