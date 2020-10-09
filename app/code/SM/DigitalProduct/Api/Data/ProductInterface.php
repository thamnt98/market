<?php
/**
 * Class ProductInterface
 * @package SM\DigitalProduct\Api\Data
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @author Son Nguyen <sonnn@smartosc.com>
 *
 * Copyright © 2020 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace SM\DigitalProduct\Api\Data;

use Magento\Framework\Api\ExtensibleDataInterface;

interface ProductInterface extends ExtensibleDataInterface
{
    /**#@+
     * Constants defined for keys of  data array
     */
    const DENOME = 'denom';
    const PRODUCT_ID_VENDOR = 'product_id_vendor';
    const DESCRIPTION = 'description';
    const SPECIAL_PRICE = 'special_price';


    /**
     * Product id
     *
     * @return int|null
     */
    public function getId();

    /**
     * Set product id
     *
     * @param int $id
     * @return \Magento\Catalog\Api\Data\ProductInterface
     */
    public function setId($id);

    /**
     * Product sku
     *
     * @return string
     */
    public function getSku();

    /**
     * Set product sku
     *
     * @param string $sku
     * @return $this
     */
    public function setSku($sku);

    /**
     * Product name
     *
     * @return string|null
     */
    public function getName();

    /**
     * Set product name
     *
     * @param string $name
     * @return $this
     */
    public function setName($name);

    /**
     * Product price
     *
     * @return float
     */
    public function getPrice();

    /**
     * Set product price
     *
     * @param float $price
     * @return \Magento\Catalog\Api\Data\ProductInterface
     */
    public function setPrice($price);

    /**
     * Product price
     *
     * @return float
     */
    public function getSpecialPrice();

    /**
     * Set product price
     *
     * @param float $price
     * @return \Magento\Catalog\Api\Data\ProductInterface
     */
    public function setSpecialPrice($price);

    /**
     * Product Denom
     *
     * @return int
     */
    public function getDenom();

    /**
     * Set product Denom
     *
     * @param int $denom
     * @return $this
     */
    public function setDenom($denom);

    /**
     * Product Denom
     *
     * @return int
     */
    public function getProductIdVendor();

    /**
     * Set product Denom
     *
     * @param int $value
     * @return $this
     */
    public function setProductIdVendor(int $value);

    /**
     * @return string
     */
    public function getDescription();

    /**
     * Set product Denom
     *
     * @param string $value
     * @return $this
     */
    public function setDescription($value);
}
