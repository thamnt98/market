<?php

namespace SM\Checkout\Model\Api\Estimate\ItemAdditionalInfo;

class AdditionalInfo extends \Magento\Framework\Api\AbstractExtensibleObject implements \SM\Checkout\Api\Data\Checkout\Estimate\ItemAdditionalInfo\AdditionalInfoInterface
{
    const DELIVERY = 'delivery';
    const INSTALLATION = 'installation';

    /**
     * {@inheritdoc}
     */
    public function setDelivery($data)
    {
        return $this->setData(self::DELIVERY, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function getDelivery()
    {
        return $this->_get(self::DELIVERY);
    }

    /**
     * {@inheritdoc}
     */
    public function getInstallationInfo()
    {
        return $this->_get(self::INSTALLATION);
    }

    /**
     * {@inheritdoc}
     */
    public function setInstallationInfo($info)
    {
        return $this->setData(self::INSTALLATION, $info);
    }
}
