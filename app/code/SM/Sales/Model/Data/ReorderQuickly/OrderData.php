<?php


namespace SM\Sales\Model\Data\ReorderQuickly;

use Magento\Framework\DataObject;
use SM\Sales\Api\Data\ReorderQuickly\OrderDataInterface;

/**
 * Class OrderData
 * @package SM\Sales\Model\Data\ReorderQuickly
 */
class OrderData extends DataObject implements OrderDataInterface
{

    /**
     * @inheritDoc
     */
    public function getEntityId()
    {
        return $this->getData(self::ENTITY_ID);
    }

    /**
     * @inheritDoc
     */
    public function setEntityId($value)
    {
        return $this->setData(self::ENTITY_ID, $value);
    }

    /**
     * @inheritDoc
     */
    public function getCreatedAt()
    {
        return $this->getData(self::CREATED_AT);
    }

    /**
     * @inheritDoc
     */
    public function setCreatedAt($value)
    {
        return $this->setData(self::CREATED_AT, $value);
    }

    /**
     * @inheritDoc
     */
    public function getGrandTotal()
    {
        return $this->getData(self::GRAND_TOTAL);
    }

    /**
     * @inheritDoc
     */
    public function setGrandTotal($value)
    {
        return $this->setData(self::GRAND_TOTAL, $value);
    }

    /**
     * @inheritDoc
     */
    public function getItemImages()
    {
        return $this->getData(self::ITEM_IMAGES);
    }

    /**
     * @inheritDoc
     */
    public function setItemImages($value)
    {
        return $this->setData(self::ITEM_IMAGES, $value);
    }

    /**
     * @inheritDoc
     */
    public function getTransactionId()
    {
        return $this->getData(self::TRANSACTION_ID);
    }

    /**
     * @inheritDoc
     */
    public function setTransactionId($value)
    {
        return $this->setData(self::TRANSACTION_ID, $value);
    }

    /**
     * @inheritDoc
     */
    public function getItemLeft()
    {
        return $this->getData(self::ITEM_LEFT);
    }

    /**
     * @inheritDoc
     */
    public function setItemLeft($value)
    {
        return $this->setData(self::ITEM_LEFT, $value);
    }
}
