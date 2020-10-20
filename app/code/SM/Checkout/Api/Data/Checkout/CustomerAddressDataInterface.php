<?php

namespace SM\Checkout\Api\Data\Checkout;

interface CustomerAddressDataInterface
{
    /**
     * @param bool $error
     * @return $this
     */
    public function setError($error);

    /**
     * @return bool
     */
    public function getError();

    /**
     * @param \Magento\Customer\Api\Data\AddressInterface[] $shippingAddressList
     * @return $this
     */
    public function setShippingAddressList($shippingAddressList);

    /**
     * @return \Magento\Customer\Api\Data\AddressInterface[]
     */
    public function getShippingAddressList();
}
