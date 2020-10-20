<?php

namespace SM\Checkout\Model\Api\CheckoutWeb;

class AdditionalInfo extends \Magento\Framework\Api\AbstractExtensibleObject implements \SM\Checkout\Api\Data\CheckoutWeb\AdditionalInfoInterface
{
    const STORE_PICK_UP = 'store_pick_up';

    /**
     * {@inheritdoc}
     */
    public function setStorePickUp($storePickUp)
    {
        return $this->setData(self::STORE_PICK_UP, $storePickUp);
    }

    /**
     * {@inheritdoc}
     */
    public function getStorePickUp()
    {
        return $this->_get(self::STORE_PICK_UP);
    }
}
