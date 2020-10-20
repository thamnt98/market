<?php


namespace SM\Sales\Model\Data;

use Magento\Framework\DataObject;
use SM\Sales\Api\Data\StatusHistoryDataInterface;

/**
 * Class StatusHistoryData
 * @package SM\Sales\Model\Data
 */
class StatusHistoryData extends DataObject implements StatusHistoryDataInterface
{

    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->getData(self::STATUS);
    }

    /**
     * @return string
     */
    public function getLabel()
    {
        return $this->getData(self::LABEL);
    }

    /**
     * @return string
     */
    public function getCreatedAt()
    {
        return $this->getData(self::CREATED_AT);
    }

    /**
     * @param string $value
     * @return StatusHistoryData
     */
    public function setStatus($value)
    {
        return $this->setData(self::STATUS, $value);
    }

    /**
     * @param string $value
     * @return StatusHistoryData
     */
    public function setLabel($value)
    {
        return $this->setData(self::LABEL, $value);
    }

    /**
     * @param string $value
     * @return StatusHistoryData
     */
    public function setCreatedAt($value)
    {
        return $this->setData(self::CREATED_AT, $value);
    }

    /**
     * @inheritDoc
     */
    public function getOrderUpdate()
    {
        return $this->getData(self::ORDER_UPDATE);
    }

    /**
     * @inheritDoc
     */
    public function setOrderUpdate($value)
    {
        return $this->setData(self::ORDER_UPDATE, $value);
    }

    /**
     * @inheritDoc
     */
    public function getIcon()
    {
        return $this->getData(self::ICON);
    }

    /**
     * @inheritDoc
     */
    public function setIcon($value)
    {
        return $this->setData(self::ICON, $value);
    }

    /**
     * @inheritDoc
     */
    public function getIconClass()
    {
        return $this->getData(self::ICON_CLASS);
    }

    /**
     * @inheritDoc
     */
    public function getIsActive()
    {
        return $this->getData(self::IS_ACTIVE);
    }

    /**
     * @inheritDoc
     */
    public function setIconClass($value)
    {
        return $this->setData(self::ICON_CLASS, $value);
    }

    /**
     * @inheritDoc
     */
    public function setIsActive($value)
    {
        return $this->setData(self::IS_ACTIVE, $value);
    }

    /**
     * Set raw date from database
     * For web only, so don't need to be declared in interface
     */
    public function setRawFormatDate($value)
    {
        return $this->setData(self::RAW_FORMAT_DATE, $value);
    }

    /**
     * Get raw date from database
     * For web only, so don't need to be declared in interface
     */
    public function getRawFormatDate()
    {
        return $this->getData(self::RAW_FORMAT_DATE);
    }
}
