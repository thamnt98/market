<?php

namespace SM\MobileApi\Model\Data\Catalog\Product;

/**
 * Class for storing attribute information
 */
class Option extends \Magento\Framework\Model\AbstractExtensibleModel implements \SM\MobileApi\Api\Data\Catalog\Product\OptionInterface
{
    public function getOptionId()
    {
        return $this->getData(self::OPTION_ID);
    }

    public function setOptionId($data)
    {
        return $this->setData(self::OPTION_ID, $data);
    }

    public function getTitle()
    {
        return $this->getData(self::TITLE);
    }

    public function setTitle($data)
    {
        return $this->setData(self::TITLE, $data);
    }

    public function getType()
    {
        return $this->getData(self::TYPE);
    }

    public function setType($data)
    {
        return $this->setData(self::TYPE, $data);
    }

    public function getIsRequire()
    {
        return $this->getData(self::IS_REQUIRE);
    }

    public function setIsRequire($data)
    {
        return $this->setData(self::IS_REQUIRE, $data);
    }

    public function getSortOrder()
    {
        return $this->getData(self::SORT_ORDER);
    }

    public function setSortOrder($data)
    {
        return $this->setData(self::SORT_ORDER, $data);
    }

    public function getAdditionalFields()
    {
        return $this->getData(self::ADDITIONAL_FIELDS);
    }

    public function setAdditionalFields($data)
    {
        return $this->setData(self::ADDITIONAL_FIELDS, $data);
    }
}
