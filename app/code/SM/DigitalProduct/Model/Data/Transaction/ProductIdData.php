<?php

namespace SM\DigitalProduct\Model\Data\Transaction;

use Magento\Framework\DataObject;
use SM\DigitalProduct\Api\Data\Transaction\ProductIdDataInterface;

/**
 * Class ProductIdData
 * @package SM\DigitalProduct\Model\Data\Transaction
 */
class ProductIdData extends DataObject implements ProductIdDataInterface
{

    /**
     * @inheritDoc
     */
    public function getProductId()
    {
        return $this->getData(self::PRODUCT_ID);
    }

    /**
     * @inheritDoc
     */
    public function getType()
    {
        return $this->getData(self::TYPE);
    }

    /**
     * @inheritDoc
     */
    public function getLabel()
    {
        return $this->getData(self::LABEL);
    }

    /**
     * @inheritDoc
     */
    public function getOperator()
    {
        return $this->getData(self::OPERATOR);
    }

    /**
     * @inheritDoc
     */
    public function getNominal()
    {
        return $this->getData(self::NOMINAL);
    }

    /**
     * @inheritDoc
     */
    public function getPrice()
    {
        return $this->getData(self::PRICE);
    }

    /**
     * @inheritDoc
     */
    public function getEnabled()
    {
        return $this->getData(self::ENABLED);
    }

    /**
     * @inheritDoc
     */
    public function setProductId($value)
    {
        return $this->setData(self::PRODUCT_ID, $value);
    }

    /**
     * @inheritDoc
     */
    public function setType($value)
    {
        return $this->setData(self::TYPE, $value);
    }

    /**
     * @inheritDoc
     */
    public function setLabel($value)
    {
        return $this->setData(self::LABEL, $value);
    }

    /**
     * @inheritDoc
     */
    public function setOperator($value)
    {
        return $this->setData(self::OPERATOR, $value);
    }

    /**
     * @inheritDoc
     */
    public function setNominal($value)
    {
        return $this->setData(self::NOMINAL, $value);
    }

    /**
     * @inheritDoc
     */
    public function setPrice($value)
    {
        return $this->setData(self::PRICE, $value);
    }

    /**
     * @inheritDoc
     */
    public function setEnabled($value)
    {
        return $this->setData(self::ENABLED, $value);
    }
}
