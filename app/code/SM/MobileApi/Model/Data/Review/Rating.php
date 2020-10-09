<?php

namespace SM\MobileApi\Model\Data\Review;

/**
 * Class Rating
 * @package SM\MobileApi\Model\Data\Review
 */
class Rating extends \Magento\Framework\Model\AbstractExtensibleModel implements \SM\MobileApi\Api\Data\Review\RatingInterface
{
    public function getTitle()
    {
        return $this->getData(self::TITLE);
    }

    public function setTitle($value)
    {
        return $this->setData(self::TITLE, $value);
    }

    public function getCode()
    {
        return $this->getData(self::CODE);
    }

    public function setCode($value)
    {
        return $this->setData(self::CODE, $value);
    }

    public function getType()
    {
        return $this->getData(self::TYPE);
    }

    public function setType($value)
    {
        return $this->setData(self::TYPE, $value);
    }

    public function getPercent()
    {
        return $this->getData(self::PERCENT);
    }

    public function setPercent($value)
    {
        return $this->setData(self::PERCENT, $value);
    }

    public function getValues()
    {
        return $this->getData(self::VALUES);
    }

    public function setValues($value)
    {
        return $this->setData(self::VALUES, $value);
    }

    public function getSelected()
    {
        return $this->getData(self::SELECTED);
    }

    public function setSelected($value)
    {
        return $this->setData(self::SELECTED, $value);
    }
}
