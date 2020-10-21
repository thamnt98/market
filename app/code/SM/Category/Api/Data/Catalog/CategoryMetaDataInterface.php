<?php

namespace SM\Category\Api\Data\Catalog;

/**
 * Interface CategoryMetaDataInterface
 * @package SM\Category\Api\Data\Catalog
 */
interface CategoryMetaDataInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{
    const ENTITY_ID = 'entity_id';
    const GALLERY = 'gallery';
    const COLOR = 'color';

    const IS_TOBACCO = "is_tobacco";
    const IS_ALCOHOL = "is_alcohol";
    const IS_FRESH = "is_fresh";

    /**
     * @return int
     */
    public function getEntityId();

    /**
     * @param int $data
     * @return $this
     */
    public function setEntityId($data);

    /**
     * Get Gallery
     * @return \SM\HeroBanner\Api\Data\BannerInterface[]
     */
    public function getGallery();

    /**
     * @param \SM\HeroBanner\Api\Data\BannerInterface[] $data
     * @return $this
     */
    public function setGallery($data);

    /**
     * Get Color
     * @return \SM\Category\Api\Data\Catalog\CategoryColorInterface
     */
    public function getColor();

    /**
     * @param \SM\Category\Api\Data\Catalog\CategoryColorInterface $data
     * @return $this
     */
    public function setColor($data);

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
