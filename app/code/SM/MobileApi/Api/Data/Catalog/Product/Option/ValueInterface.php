<?php

namespace SM\MobileApi\Api\Data\Catalog\Product\Option;

/**
 * Interface for storing attribute infomation
 */
interface ValueInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{
    const VALUE_ID = 'value_id';
    const TITLE = 'title';
    const PRICE = 'price';
    const PRICE_TYPE = 'price_type';
    const SKU = 'sku';
    const SORT_ORDER = 'sort_order';

    /**
     * Get value ID
     *
     * @return int
     */
    public function getValueId();

    /**
     * @param int $data
     *
     * @return $this
     */
    public function setValueId($data);

    /**
     * Get value title
     *
     * @return string
     */
    public function getTitle();

    /**
     * @param string $data
     *
     * @return $this
     */
    public function setTitle($data);

    /**
     * Get value price
     *
     * @return float
     */
    public function getPrice();

    /**
     * @param float $data
     *
     * @return $this
     */
    public function setPrice($data);

    /**
     * Get value price type
     *
     * @return string
     */
    public function getPriceType();

    /**
     * @param string $data
     *
     * @return $this
     */
    public function setPriceType($data);

    /**
     * Get value SKU
     *
     * @return string
     */
    public function getSku();

    /**
     * @param string $data
     *
     * @return $this
     */
    public function setSku($data);

    /**
     * Get value position
     *
     * @return int
     */
    public function getSortOrder();

    /**
     * @param int $data
     *
     * @return $this
     */
    public function setSortOrder($data);
}
