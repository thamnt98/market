<?php


namespace SM\DigitalProduct\Model\Data;

use Magento\Framework\DataObject;
use SM\DigitalProduct\Api\Data\SubCategoryDataInterface;

/**
 * Class SubCategoryData
 * @package SM\DigitalProduct\Model\Data
 */
class SubCategoryData extends DataObject implements SubCategoryDataInterface
{
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
    public function setType($value)
    {
        return $this->setData(self::TYPE, $value);
    }

    /**
     * @inheritDoc
     */
    public function getCategoryName()
    {
        return $this->getData(self::CATEGORY_NAME);
    }

    /**
     * @inheritDoc
     */
    public function setCategoryName($value)
    {
        return $this->setData(self::CATEGORY_NAME, $value);
    }

    /**
     * @inheritDoc
     */
    public function getTooltip()
    {
        return $this->getData(self::TOOLTIP);
    }

    /**
     * @inheritDoc
     */
    public function setTooltip($value)
    {
        return $this->setData(self::TOOLTIP, $value);
    }

    /**
     * @inheritDoc
     */
    public function getInfo()
    {
        return $this->getData(self::INFO);
    }

    /**
     * @inheritDoc
     */
    public function setInfo($value)
    {
        return $this->setData(self::INFO, $value);
    }

    /**
     * @inheritDoc
     */
    public function getHowToBuy()
    {
        return $this->getData(self::HOW_TO_BUY);
    }

    /**
     * @inheritDoc
     */
    public function setHowToBuy($value)
    {
        return $this->setData(self::HOW_TO_BUY, $value);
    }
}
