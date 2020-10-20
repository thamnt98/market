<?php
/**
 * Created by PhpStorm.
 * User: thelightsp
 * Date: 3/31/20
 * Time: 5:48 PM
 */

namespace SM\Checkout\Helper;

use Magento\Store\Model\ScopeInterface;

class DeliveryType extends \Magento\Framework\App\Helper\AbstractHelper
{
    const TYPE_DELIVERY = 'custom_delivery';
    const TYPE_PICKUP   = 'store_pickup';
    const LABEL_DELIVERY = 'Delivery';
    const LABEL_PICKUP   = 'Pick Up in Store';
    const VALUE_DELIVERY = '0';
    const VALUE_PICKUP   = '1';

    /**
     * @return array
     */
    public function getDeliveryType()
    {
        $data = [];

        if ($this->canShowDelivery()) {
            $data[] = $this->getDeliveryData();
        }
        if ($this->canShowPickupInStore()) {
            $data[] = $this->getPickupData();
        }
        return $data;
    }

    /**
     * @return bool
     */
    public function canShowDelivery()
    {
        return true;
    }

    /**
     * @param false $storeId
     * @return int
     */
    public function canShowPickupInStore($storeId = null)
    {
        return $this->scopeConfig->getValue(
            'carriers/store_pickup/active',
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * @return array
     */
    public function getDeliveryData()
    {
        return [
            'code'  => self::TYPE_DELIVERY,
            'label' => __(self::LABEL_DELIVERY),
            'value' => self::VALUE_DELIVERY,
            'data'  => []
        ];
    }

    /**
     * @return array
     */
    public function getPickupData()
    {
        return [
            'code'  => self::TYPE_PICKUP,
            'label' => __(self::LABEL_PICKUP),
            'value' => self::VALUE_PICKUP,
            'data'  => []
        ];
    }
}
