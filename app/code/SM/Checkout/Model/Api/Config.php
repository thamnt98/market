<?php

namespace SM\Checkout\Model\Api;

class Config extends \Magento\Framework\Api\AbstractExtensibleObject implements \SM\Checkout\Api\Data\Checkout\ConfigInterface
{
    const STORE_PICK_UP_DATE_TIME = 'store_pick_up_date_time';
    const DELIVERY_DATE_TIME = 'delivery_date_time';

    /**
     * {@inheritdoc}
     */
    public function setStorePickUpDateTime($data)
    {
        return $this->setData(self::STORE_PICK_UP_DATE_TIME, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function getStorePickUpDateTime()
    {
        return $this->_get(self::STORE_PICK_UP_DATE_TIME);
    }

    /**
     * {@inheritdoc}
     */
    public function setDeliveryDateTime($data)
    {
        return $this->setData(self::DELIVERY_DATE_TIME, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function getDeliveryDateTime()
    {
        return $this->_get(self::DELIVERY_DATE_TIME);
    }
}
