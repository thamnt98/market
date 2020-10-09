<?php


namespace SM\CustomPrice\Helper;


use SM\CustomPrice\Model\ResourceModel\District;

class Customer
{
    /**
     * @var District
     */
    protected $district;

    public function __construct(
        District $district,
        \Magento\Quote\Model\QuoteFactory $quoteFactory
    )
    {
        $this->district = $district;
        $this->quoteFactory = $quoteFactory;
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
            $customerData = $customer->getDataModel();
            $omniStoreCode=$this->district->getOmniStoreCodeByDistrictId($district_id);
            $customerData->setCustomAttribute('district',$district_id);
            $customerData->setCustomAttribute('city',$city);
            $customerData->setCustomAttribute(\SM\CustomPrice\Model\Customer::OMNI_STORE_ID, ($omniStoreCode??$customer->getDefaultOmniStoreCode()));
            $customer->updateData($customerData);
            $customer->setData('ignore_validation_flag',true);
            $customer->save();
            $this->truncateCart($customer->getId());
        } catch (\Exception $exception) {
            throw new \Exception($exception->getMessage());
        }
    }

    public function truncateCart($customerId)
    {
        $quote=$this->quoteFactory->create()->loadByCustomer($customerId);
        $quote->removeAllItems();
        $quote->save();
    }
}
