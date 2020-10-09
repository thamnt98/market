<?php


namespace SM\Customer\Model;

use SM\Customer\Api\CustomerInterface;
use SM\GTM\Helper\Data;

class Customer implements CustomerInterface
{
    /**
     * @var Data
     */
    protected $gtmDataHelper;

    public function __construct(Data $gtmDataHelper)
    {
        $this->gtmDataHelper = $gtmDataHelper;
    }

    public function getInfo($customerId)
    {
        return $this->gtmDataHelper->getGtmCustomerInfo($customerId);
    }
}
