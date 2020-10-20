<?php

namespace SM\Checkout\Model\Api;

class ProductOptions extends \Magento\Framework\Api\AbstractExtensibleObject implements \SM\Checkout\Api\Data\Checkout\QuoteItems\ProductOptions\ProductOptionsInterface
{
    const LABEL = 'label';
    const VALUE = 'value';

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
}
