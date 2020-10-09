<?php

namespace SM\FlashSale\Model;

/**
 * Class FlashSaleDate
 * @package SM\FlashSale\Model
 */
class FlashSaleDate extends \Magento\Framework\Model\AbstractModel implements \SM\FlashSale\Api\Data\FlashSaleDateInterface
{
    /**
     * @return string
     */
    public function getDateStart()
    {
        return $this->getData(self::DATE_START);
    }

    /**
     * @param string $value
     * @return $this
     */
    public function setDateStart($value)
    {
        return $this->setData(self::DATE_START, $value);
    }

    /**
     * @return string
     */
    public function getDateEnd()
    {
        return $this->getData(self::DATE_END);
    }

    /**
     * @param string $value
     * @return $this
     */
    public function setDateEnd($value)
    {
        return $this->setData(self::DATE_END, $value);
    }

    /**
     * @return string
     */
    public function getDateStartConverted()
    {
        // TODO: Implement getDateStartConverted() method.
        return $this->getData(self::DATE_START_CONVERTED);
    }

    /**
     * @param string $value
     * @return $this
     */
    public function setDateStartConverted($value)
    {
        // TODO: Implement setDateStartConverted() method.
        return $this->setData(self::DATE_START_CONVERTED,$value);
    }

    /**
     * @return string
     */
    public function getDateEndConverted()
    {
        // TODO: Implement getDateEndConverted() method.
        return $this->getData(self::DATE_END_CONVERTED);
    }

    /**
     * @param string $value
     * @return $this
     */
    public function setDateEndConverted($value)
    {
        // TODO: Implement setDateEndConverted() method.
        return $this->setData(self::DATE_END_CONVERTED,$value);
    }
}
