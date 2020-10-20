<?php

namespace SM\Checkout\Model\Api;

use SM\Checkout\Api\Data\Checkout\CustomerAddressDataInterface;

class CustomerAddressData extends \Magento\Framework\Api\AbstractExtensibleObject implements \SM\Checkout\Api\Data\Checkout\CustomerAddressDataInterface
{
    const ERROR = 'error';
    const SHIPPING_ADDRESS_LIST = 'shipping_address_list';

    /**
     * {@inheritdoc}
     */
    public function setError($error)
    {
        return $this->setData(self::ERROR, $error);
    }

    /**
     * {@inheritdoc}
     */
    public function getError()
    {
        return $this->_get(self::ERROR);
    }

    /**
     * {@inheritdoc}
     */
    public function setShippingAddressList($shippingAddressList)
    {
        return $this->setData(self::SHIPPING_ADDRESS_LIST, $shippingAddressList);
    }

    /**
     * {@inheritdoc}
     */
    public function getShippingAddressList()
    {
        return $this->_get(self::SHIPPING_ADDRESS_LIST);
    }
}
