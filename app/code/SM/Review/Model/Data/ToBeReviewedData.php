<?php

namespace SM\Review\Model\Data;

use Magento\Framework\Api\AbstractExtensibleObject;
use SM\Review\Api\Data\ToBeReviewedInterface;

/**
 * Class ToBeReviewedData
 * @package SM\Review\Model\Data
 */
class ToBeReviewedData extends AbstractExtensibleObject implements ToBeReviewedInterface
{
    /**
     * {@inheritdoc}
     */
    public function setReferenceNumber($value)
    {
        return $this->setData(self::REFERENCE_NUMBER, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getReferenceNumber()
    {
        return $this->_get(self::REFERENCE_NUMBER);
    }

    /**
     * {@inheritdoc}
     */
    public function setTimeCreated($value)
    {
        return $this->setData(self::TIME_CREATED, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getTimeCreated()
    {
        return $this->_get(self::TIME_CREATED);
    }

    /**
     * {@inheritdoc}
     */
    public function setProducts($value)
    {
        return $this->setData(self::PRODUCTS, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getProducts()
    {
        return $this->_get(self::PRODUCTS);
    }

    /**
     * @inheritDoc
     */
    public function setOrderId($value)
    {
        return $this->setData(self::ORDER_ID, $value);
    }

    /**
     * @inheritDoc
     */
    public function getOrderId()
    {
        return $this->_get(self::ORDER_ID);
    }
}
