<?php

namespace SM\Checkout\Model\Api\CheckoutWeb;

class SourceStore extends \Magento\Framework\Api\AbstractExtensibleObject implements \SM\Checkout\Api\Data\CheckoutWeb\SourceStoreInterface
{
    const SOURCE_CODE = 'source_code';
    const DISTANCE = 'distance';

    /**
     * {@inheritdoc}
     */
    public function setSourceCode($sourceCode)
    {
        return $this->setData(self::SOURCE_CODE, $sourceCode);
    }

    /**
     * {@inheritdoc}
     */
    public function getSourceCode()
    {
        return $this->_get(self::SOURCE_CODE);
    }

    /**
     * {@inheritdoc}
     */
    public function setDistance($distance)
    {
        return $this->setData(self::DISTANCE, $distance);
    }

    /**
     * {@inheritdoc}
     */
    public function getDistance()
    {
        return $this->_get(self::DISTANCE);
    }
}
