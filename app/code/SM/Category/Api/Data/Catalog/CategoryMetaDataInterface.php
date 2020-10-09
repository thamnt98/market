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
}
