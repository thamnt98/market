<?php

namespace SM\Category\Api\Data\Catalog;

/**
 * Interface for storing categories information
 */
interface CategoryInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{
    const ENTITY_ID = "entity_id";
    const ENTITY_TYPE_ID = "entity_type_id";
    const ATTRIBUTE_SET_ID = "attribute_set_id";
    const PARENT_ID = "parent_id";
    const CREATE_AT = "created_at";
    const UPDATED_AT = "updated_at";
    const PATH = "path";
    const POSITION = "position";
    const LEVEL = "level";
    const IS_ACTIVE = "is_active";
    const NAME = "name";
    const IMAGE = "image";
    const IS_ANCHOR = "is_anchor";
    const CATEGORY_ID = "category_id";
    const COLOR = "color";

    const PRODUCTS = "products";
    const IS_DIGITAL = "is_digital";
    const IS_TOBACCO = "is_tobacco";
    const IS_ALCOHOL = "is_alcohol";

    const IS_FRESH = "is_fresh";
    /**
     * Get entity_id
     *
     * @return integer
     */
    public function getEntityId();

    /**
     * Set entity_id
     *
     * @param int $entityId
     *
     * @return $this
     */
    public function setEntityId($entityId);

    /**
     * Get attribute_set_id
     *
     * @return integer
     */
    public function getAttributeSetId();

    /**
     * Set attribute_set_id
     *
     * @param int $attributeSetId
     *
     * @return $this
     */
    public function setAttributeSetId($attributeSetId);

    /**
     * Get parent id
     *
     * @return integer
     */
    public function getParentId();

    /**
     * Set parent id
     *
     * @param int $parentId
     *
     * @return $this
     */
    public function setParentId($parentId);

    /**
     * Get created at
     *
     * @return string
     */
    public function getCreatedAt();

    /**
     * Set crested at
     *
     * @param string $createdAt
     *
     * @return $this
     */
    public function setCreatedAt($createdAt);

    /**
     * Get updated at
     *
     * @return string
     */
    public function getUpdatedAt();

    /**
     * Get updated at
     *
     * @param string $updatedAt
     *
     * @return $this
     */
    public function setUpdatedAt($updatedAt);

    /**
     * Get path
     *
     * @return string
     */
    public function getPath();

    /**
     * Set path
     *
     * @param string $path
     *
     * @return $this
     */
    public function setPath($path);

    /**
     * Get position
     *
     * @return integer
     */
    public function getPosition();

    /**
     * Set position
     *
     * @param int $position
     *
     * @return $this
     */
    public function setPosition($position);

    /**
     * Get Level
     *
     * @return integer
     */
    public function getLevel();

    /**
     * Set Level
     *
     * @param int $level
     *
     * @return $this
     */
    public function setLevel($level);

    /**
     * Is Active
     *
     * @return boolean
     */
    public function getIsActive();

    /**
     * Set is active
     *
     * @param bool $isActive
     *
     * @return $this
     */
    public function setIsActive($isActive);

    /**
     * Get Name
     *
     * @return string
     */
    public function getName();

    /**
     * Set Name
     *
     * @param string $name
     *
     * @return $this
     */
    public function setName($name);

    /**
     * Get Category Id
     *
     * @return integer
     */
    public function getCategoryId();

    /**
     * Set Category Id
     *
     * @param int $id
     *
     * @return $this
     */
    public function setCategoryId($id);

    /**
     * Get Image
     *
     * @return string
     */
    public function getImage();

    /**
     * Set Image
     *
     * @param string $image
     *
     * @return $this
     */
    public function setImage($image);

    /**
     * Is anchor
     *
     * @return boolean
     */
    public function getIsAnchor();

    /**
     * Set is anchor
     *
     * @param bool $isAnchor
     *
     * @return $this
     */
    public function setIsAnchor($isAnchor);

    /**
     * Get Color
     *
     * @return \SM\Category\Api\Data\Catalog\CategoryColorInterface
     */
    public function getColor();

    /**
     * Set Children
     *
     * @param \SM\Category\Api\Data\Catalog\CategoryColorInterface $color
     *
     * @return $this
     */
    public function setColor($color);

    /**
     * Get Color
     *
     * @return bool
     */
    public function getIsDigital();

    /**
     * Set Children
     *
     * @param bool $value
     *
     * @return $this
     */
    public function setIsDigital($value);

    /**
     * @return bool
     */
    public function getIsTobacco();

    /**
     * @return bool
     */
    public function getIsAlcohol();

    /**
     * @param bool $value
     * @return $this
     */
    public function setIsTobacco($value);

    /**
     * @param bool $value
     * @return $this
     */
    public function setIsAlcohol($value);

    /**
     * @return bool
     */
    public function getIsFresh();

    /**
     * @param bool $value
     * @return $this
     */
    public function setIsFresh($value);
}
