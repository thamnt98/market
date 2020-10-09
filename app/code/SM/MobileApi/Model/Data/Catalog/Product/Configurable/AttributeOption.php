<?php

namespace SM\MobileApi\Model\Data\Catalog\Product\Configurable;

use Magento\Framework\Model\AbstractExtensibleModel;
use SM\MobileApi\Api\Data\Catalog\Product\Configurable\AttributeOptionInterface;

/**
 * Class AttributeOption
 * @package SM\MobileApi\Model\Data\Catalog\Product\Configurable
 */
class AttributeOption extends AbstractExtensibleModel implements AttributeOptionInterface
{
    public function getId()
    {
        return $this->getData(self::ID);
    }

    public function setId($data)
    {
        return $this->setData(self::ID, $data);
    }

    public function getLabel()
    {
        return $this->getData(self::LABEL);
    }

    public function setLabel($data)
    {
        return $this->setData(self::LABEL, $data);
    }

    public function getHexColorCode()
    {
        return $this->getData(self::HEX_COLOR);
    }

    public function setHexColorCode($color)
    {
        return $this->setData(self::HEX_COLOR, $color);
    }

    public function getImage()
    {
        return $this->getData(self::IMAGE);
    }

    public function setImage($image)
    {
        return $this->setData(self::IMAGE, $image);
    }

    public function getProducts()
    {
        return $this->getData(self::PRODUCTS);
    }

    public function setProducts($data)
    {
        return $this->setData(self::PRODUCTS, $data);
    }
}
