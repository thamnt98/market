<?php


namespace SM\MobileApi\Model\Data\Catalog\Product\Configurable;

use Magento\Framework\Model\AbstractExtensibleModel;
use SM\MobileApi\Api\Data\Catalog\Product\Configurable\AttributeInterface;

/**
 * Class Attribute
 * @package SM\MobileApi\Model\Data\Catalog\Product\Configurable
 */
class Attribute extends AbstractExtensibleModel implements AttributeInterface
{
    public function getId()
    {
        return $this->getData(self::ID);
    }

    public function setId($data)
    {
        return $this->setData(self::ID, $data);
    }

    public function getCode()
    {
        return $this->getData(self::CODE);
    }

    public function setCode($data)
    {
        return $this->setData(self::CODE, $data);
    }

    public function getLabel()
    {
        return $this->getData(self::LABEL);
    }

    public function setLabel($data)
    {
        return $this->setData(self::LABEL, $data);
    }

    public function getOptions()
    {
        return $this->getData(self::OPTIONS);
    }

    public function setOptions($data)
    {
        return $this->setData(self::OPTIONS, $data);
    }

    public function getInputType()
    {
        return $this->getData(self::INPUT_TYPE);
    }

    public function setInputType($type)
    {
        return $this->setData(self::INPUT_TYPE, $type);
    }
}
