<?php

namespace SM\MobileApi\Api\Data\Catalog\Product;

/**
 * Interface for storing product's media data
 */
interface ProductMediaInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{
    const IMAGE = 'image';
    const SMALL_IMAGE = 'small_image';
    const THUMBNAIL = 'thumbnail';
    const GALLERY = 'gallery';
    const IMAGE_360 = 'image_360';

    /**
     * Get Image
     *
     * @return \SM\MobileApi\Api\Data\Catalog\Product\ImageInterface
     */
    public function getImage();

    /**
     * Set Image
     *
     * @param \SM\MobileApi\Api\Data\Catalog\Product\ImageInterface $data
     *
     * @return $this
     */
    public function setImage($data);

    /**
     * Get Small Image
     *
     * @return \SM\MobileApi\Api\Data\Catalog\Product\ImageInterface
     */
    public function getSmallImage();

    /**
     * Set Small Image
     *
     * @param \SM\MobileApi\Api\Data\Catalog\Product\ImageInterface $data
     *
     * @return $this
     */
    public function setSmallImage($data);

    /**
     * Get Thumbnail
     *
     * @return \SM\MobileApi\Api\Data\Catalog\Product\ImageInterface
     */
    public function getThumbnail();

    /**
     * Set Thumbnail
     *
     * @param \SM\MobileApi\Api\Data\Catalog\Product\ImageInterface $data
     *
     * @return $this
     */
    public function setThumbnail($data);

    /**
     * Get css description for mobile
     *
     * @return string
     */
    public function getImage360();

    /**
     * @param array $data
     *
     * @return $this
     */
    public function setImage360($data);
}
