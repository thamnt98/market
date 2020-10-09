<?php


namespace SM\Customer\Api;


interface CustomerInterface
{
    /**
     * @param int $customerId
     * @return \Magento\Customer\Api\Data\CustomerInterface
     */
    public function getInfo($customerId);
}
