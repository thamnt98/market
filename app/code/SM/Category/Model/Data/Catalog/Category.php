<?php

namespace SM\Category\Model\Data\Catalog;

/**
 * Class for storing Category information
 */
class Category extends \Magento\Framework\Model\AbstractExtensibleModel implements \SM\Category\Api\Data\Catalog\CategoryInterface
{
    public function getEntityId()
    {
        return $this->getData(self::ENTITY_ID);
    }

    public function setEntityId($entityId)
    {
        return $this->setData(self::ENTITY_ID, $entityId);
    }

    public function getAttributeSetId()
    {
        return $this->getData(self::ATTRIBUTE_SET_ID);
    }

    public function setAttributeSetId($attributeSetId)
    {
        return $this->setData(self::ATTRIBUTE_SET_ID, $attributeSetId);
    }

    public function getParentId()
    {
        return $this->getData(self::PARENT_ID);
    }

    public function setParentId($parentId)
    {
        return $this->setData(self::PARENT_ID, $parentId);
    }

    public function getCreatedAt()
    {
        return $this->getData(self::CREATE_AT);
    }

    public function setCreatedAt($createdAt)
    {
        return $this->setData(self::CREATE_AT, $createdAt);
    }

    public function getUpdatedAt()
    {
        return $this->getData(self::UPDATED_AT);
    }

    public function setUpdatedAt($updatedAt)
    {
        return $this->setData(self::UPDATED_AT, $updatedAt);
    }

    public function getPath()
    {
        return $this->getData(self::PATH);
    }

    public function setPath($path)
    {
        return $this->setData(self::PATH, $path);
    }

    public function getPosition()
    {
        return $this->getData(self::POSITION);
    }

    public function setPosition($position)
    {
        return $this->setData(self::POSITION, $position);
    }

    public function getLevel()
    {
        return $this->getData(self::LEVEL);
    }

    public function setLevel($level)
    {
        return $this->setData(self::LEVEL, $level);
    }

    public function getIsActive()
    {
        return $this->getData(self::IS_ACTIVE);
    }

    public function setIsActive($isActive)
    {
        return $this->setData(self::IS_ACTIVE, $isActive);
    }

    public function getName()
    {
        return $this->getData(self::NAME);
    }

    public function setName($name)
    {
        return $this->setData(self::NAME, $name);
    }

    public function getCategoryId()
    {
        return $this->getData(self::CATEGORY_ID);
    }

    public function setCategoryId($id)
    {
        return $this->setData(self::CATEGORY_ID, $id);
    }

    public function getImage()
    {
        return $this->getData(self::IMAGE);
    }

    public function setImage($image)
    {
        return $this->setData(self::IMAGE, $image);
    }

    public function getIsAnchor()
    {
        return $this->getData(self::IS_ANCHOR);
    }

    public function setIsAnchor($isAnchor)
    {
        return $this->setData(self::IS_ANCHOR, $isAnchor);
    }

    public function getColor()
    {
        return $this->getData(self::COLOR);
    }

    public function setColor($color)
    {
        return $this->setData(self::COLOR, $color);
    }

    /**
     * @inheritDoc
     */
    public function getIsDigital()
    {
        return $this->getData(self::IS_DIGITAL);
    }

    /**
     * @inheritDoc
     */
    public function setIsDigital($value)
    {
        return $this->setData(self::IS_DIGITAL, $value);
    }

    /**
     * @inheritDoc
     */
    public function getIsTobacco()
    {
        return $this->getData(self::IS_TOBACCO) ?? false;
    }

    /**
     * @inheritDoc
     */
    public function getIsAlcohol()
    {
        return $this->getData(self::IS_ALCOHOL) ?? false;
    }

    /**
     * @inheritDoc
     */
    public function setIsTobacco($value)
    {
        return $this->setData(self::IS_TOBACCO, $value);
    }

    /**
     * @inheritDoc
     */
    public function setIsAlcohol($value)
    {
        return $this->setData(self::IS_ALCOHOL, $value);
    }

    /**
     * @inheritDoc
     */
    public function getIsFresh()
    {
        return $this->getData(self::IS_FRESH);
    }

    /**
     * @inheritDoc
     */
    public function setIsFresh($value)
    {
        return $this->setData(self::IS_FRESH, $value);
    }
}
