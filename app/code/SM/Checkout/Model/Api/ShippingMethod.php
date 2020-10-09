<?php

namespace SM\Checkout\Model\Api;

class ShippingMethod extends \Magento\Framework\Api\AbstractExtensibleObject implements \SM\Checkout\Api\Data\Checkout\ShippingMethodInterface
{
    const VALUE = 'value';
    const LABEL = 'label';
    const DISABLED = 'disabled';

    /**
     * {@inheritdoc}
     */
    public function setValue($value)
    {
        return $this->setData(self::VALUE, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getValue()
    {
        return $this->_get(self::VALUE);
    }

    /**
     * {@inheritdoc}
     */
    public function setLabel($label)
    {
        return $this->setData(self::LABEL, $label);
    }

    /**
     * {@inheritdoc}
     */
    public function getLabel()
    {
        return $this->_get(self::LABEL);
    }

    /**
     * {@inheritdoc}
     */
    public function setDisabled($disabled)
    {
        return $this->setData(self::DISABLED, $disabled);
    }

    /**
     * {@inheritdoc}
     */
    public function getDisabled()
    {
        return $this->_get(self::DISABLED);
    }
}
