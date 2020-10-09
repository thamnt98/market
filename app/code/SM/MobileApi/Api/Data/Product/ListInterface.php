<?php

namespace SM\MobileApi\Api\Data\Product;

/**
 * @api
 * Interface ListInterface
 * @package SM\MobileApi\Api\Data\Product
 */
interface ListInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{
    const FILTERS = 'filters';
    const PRODUCTS = 'products';
    const TOOLBAR_INFO = 'toolbar_info';
    const CATEGORY_ID = 'category_id';
    const EVENT_END_TIME = 'event_end_time';
    const EVENT_END_TIME_CONVERTED = 'event_end_time_converted';

    const FLASH_IMAGE = 'flash_sale_image';
    const IS_TOBACCO = "is_tobacco";
    const IS_ALCOHOL = "is_alcohol";

    const IS_FRESH = "is_fresh";
    /**
     * Get filter data
     *
     * @return \SM\MobileApi\Api\Data\Catalog\ProductFilterInterface[]
     */
    public function getFilters();

    /**
     * @param \SM\MobileApi\Api\Data\Catalog\ProductFilterInterface[]
     *
     * @return $this
     */
    public function setFilters($data);

    /**
     * Get toolbar information
     *
     * @return \SM\MobileApi\Api\Data\Catalog\ProductToolbarInterface
     */
    public function getToolbarInfo();

    /**
     * @param \SM\MobileApi\Api\Data\Catalog\ProductToolbarInterface $data
     *
     * @return $this
     */
    public function setToolbarInfo($data);

    /**
     * Get product collection
     *
     * @return \SM\MobileApi\Api\Data\Product\ListItemInterface[]
     */
    public function getProducts();

    /**
     * @param \SM\MobileApi\Api\Data\Product\ListItemInterface[] $data
     *
     * @return $this
     */
    public function setProducts($data);

    /**
     * @return int
     */
    public function getCategoryId();

    /**
     * @param int $id
     * @return $this
     */
    public function setCategoryId($id);

    /**
     * @return string
     */
    public function getEndTime();

    /**
     * @param string $datetime
     * @return $this
     */
    public function setEndTime($datetime);

    /**
     * @return string
     */
    public function getEndTimeConverted();

    /**
     * @param string $datetime
     * @return $this
     */
    public function setEndTimeConverted($datetime);

    /**
     * @return string
     */
    public function getFlashImage();

    /**
     * @param string $flashImage
     * @return $this
     */
    public function setFlashImage($flashImage);

    /**
     * @return bool
     */
    public function getIsTobacco();

    /**
     * @param bool $value
     * @return $this
     */
    public function setIsTobacco($value);

    /**
     * @return bool
     */
    public function getIsAlcohol();

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
