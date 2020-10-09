<?php
/**
 * Class ItemListInterface
 * @package SM\DigitalProduct\Api\Data\Order
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @author Son Nguyen <sonnn@smartosc.com>
 *
 * Copyright © 2020 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace SM\DigitalProduct\Api\Data\Order;

interface OrderItemInterface
{

    /**#@+
     * Constants for keys of data array. Identical to the name of the getter in snake case
     */
    /*
     * Item ID.
     */
    const ITEM_ID = 'item_id';
    /*
     * Order ID.
     */
    const ORDER_ID = 'order_id';

    /*
     * Store ID.
     */
    const STORE_ID = 'store_id';
    /*
     * Created-at timestamp.
     */
    const CREATED_AT = 'created_at';
    /*
     * Updated-at timestamp.
     */
    const UPDATED_AT = 'updated_at';
    /*
     * Product ID.
     */
    const PRODUCT_ID = 'product_id';
    /*
     * Product type.
     */
    const PRODUCT_TYPE = 'product_type';

    /*
     * Is-virtual flag.
     */
    const IS_VIRTUAL = 'is_virtual';
    /*
     * SKU.
     */
    const SKU = 'sku';
    /*
     * Name.
     */
    const NAME = 'name';

    /*
     * Price.
     */
    const PRICE = 'price';

    /*
     * Price.
     */
    const SERVICE_TYPE = 'service_type';

    /**
     * Gets the item ID for the order item.
     *
     * @return int|null Item ID.
     */
    public function getItemId();

    /**
     * Gets the name for the order item.
     *
     * @return string|null Name.
     */
    public function getName();

    /**
     * Gets the order ID for the order item.
     *
     * @return int|null Order ID.
     */
    public function getOrderId();

    /**
     * Gets the price for the order item.
     *
     * @return float|null Price.
     */
    public function getPrice();

    /**
     * Gets the product ID for the order item.
     *
     * @return int|null Product ID.
     */
    public function getProductId();

    /**
     * Gets the SKU for the order item.
     *
     * @return string SKU.
     */
    public function getSku();

    /**
     * Gets the Digital Service type the order item.
     *
     * @return string.
     */
    public function getServiceType();


    /**
     * Sets the item ID for the order item.
     *
     * @param int $id
     * @return $this
     */
    public function setItemId($id);

    /**
     * Sets the order ID for the order item.
     *
     * @param int $id
     * @return $this
     */
    public function setOrderId($id);

    /**
     * Sets the product ID for the order item.
     *
     * @param int $id
     * @return $this
     */
    public function setProductId($id);

    /**
     * Sets the SKU for the order item.
     *
     * @param string $sku
     * @return $this
     */
    public function setSku($sku);

    /**
     * Sets the Digital Service Type for the order item.
     *
     * @param string $value
     * @return $this
     */
    public function setServiceType($value);

    /**
     * Sets the name for the order item.
     *
     * @param string $name
     * @return $this
     */
    public function setName($name);

    /**
     * Sets the price for the order item.
     *
     * @param float $price
     * @return $this
     */
    public function setPrice($price);

    /**
     * Returns product option
     *
     * @return \Magento\Catalog\Api\Data\ProductOptionInterface
     */
    public function getProductOption();

    /**
     * Sets product option
     *
     * @param \Magento\Catalog\Api\Data\ProductOptionInterface $productOption
     * @return $this
     */
    public function setProductOption(\Magento\Catalog\Api\Data\ProductOptionInterface $productOption);


    /**
     * Gets the created-at timestamp for the order item.
     *
     * @return string|null Created-at timestamp.
     */
    public function getCreatedAt();

    /**
     * Sets the created-at timestamp for the order item.
     *
     * @param string $createdAt timestamp
     * @return \Magento\Sales\Api\Data\OrderItemInterface
     */
    public function setCreatedAt($createdAt);

    /**
     * Gets the updated-at timestamp for the order item.
     *
     * @return string|null Updated-at timestamp.
     */
    public function getUpdatedAt();

    /**
     * Sets the updated-at timestamp for the order item.
     *
     * @param string $timestamp
     * @return $this
     */
    public function setUpdatedAt($timestamp);
}
