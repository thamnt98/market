<?php

namespace SM\Checkout\Model\Api\Estimate;

class AdditionalInfo extends \Magento\Framework\Api\AbstractExtensibleObject implements \SM\Checkout\Api\Data\Checkout\Estimate\AdditionalInfoInterface
{
    const STORE_PICK_UP = 'store_pick_up';


    /**
     * {@inheritdoc}
     */
    public function setStorePickUp($data)
    {
        return $this->setData(self::STORE_PICK_UP, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function getStorePickUp()
    {
        return $this->_get(self::STORE_PICK_UP);
    }
}
