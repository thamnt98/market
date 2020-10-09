<?php


namespace SM\DigitalProduct\Model\Data\Transaction;

use SM\DigitalProduct\Api\Data\Transaction\MobileProductIdDataInterface;

/**
 * Class MobileProductIdData
 * @package SM\DigitalProduct\Model\Data\Transaction
 */
class MobileProductIdData extends ProductIdData implements MobileProductIdDataInterface
{

    /**
     * @inheritDoc
     */
    public function getFieldDenom()
    {
        return $this->getData(self::FIELD_DENOM);
    }

    /**
     * @inheritDoc
     */
    public function getFieldPaketData()
    {
        return $this->getData(self::FIELD_PAKET_DATA);
    }

    /**
     * @inheritDoc
     */
    public function setFieldDenom($value)
    {
        return $this->setData(self::FIELD_DENOM, $value);
    }

    /**
     * @inheritDoc
     */
    public function setFieldPaketData($value)
    {
        return $this->setData(self::FIELD_PAKET_DATA, $value);
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
    public function getProductId()
    {
        return $this->getData(self::PRODUCT_ID);
    }
}
