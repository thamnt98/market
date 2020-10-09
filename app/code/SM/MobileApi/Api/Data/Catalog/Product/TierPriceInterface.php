<?php

namespace SM\MobileApi\Api\Data\Catalog\Product;

/**
 * Interface for storing product's tier-price infomation
 */
interface TierPriceInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{
    const PRICE = 'price';
    const WEBSITE = 'website';
    const QTY = 'qty';
    const CUSTOMER_GROUP_ID = 'customer_group_id';
    const SAVE_PERCENT = 'save_percent';

    /**
     * Get Price
     *
     * @return float
     */
    public function getPrice();

    /**
     * Set Price
     *
     * @param $price
     *
     * @return $this
     */
    public function setPrice($price);

    /**
     * Get Website
     *
     * @return integer
     */
    public function getWebsite();

    /**
     * Set Website
     *
     * @param $id
     *
     * @return $this
     */
    public function setWebsite($id);

    /**
     * Get Qty
     *
     * @return integer
     */
    public function getQty();

    /**
     * Set Qty
     *
     * @param $qty
     *
     * @return $this
     */
    public function setQty($qty);

    /**
     * Get Customer group Id
     *
     * @return integer
     */
    public function getCustomerGroupId();

    /**
     * Set Customer group Id
     *
     * @param $id
     *
     * @return $this
     */
    public function setCustomerGroupId($id);

    /**
     * Get Save percent
     *
     * @return float
     */
    public function getSavePercent();

    /**
     * Get Save percent
     *
     * @param $percent
     *
     * @return $this
     */
    public function setSavePercent($percent);
}
