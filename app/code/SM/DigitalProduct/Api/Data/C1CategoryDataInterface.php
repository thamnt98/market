<?php
/**
 * @category Magento
 * @package SM\DigitalProduct\Api\Data
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @author Hung Viet Nguyen <hungnv6@smartosc.com>
 * @copyright   Copyright (c) SmartOSC. All rights reserved.
 * @url http://www.smartosc.com/
 */

namespace SM\DigitalProduct\Api\Data;

/**
 * Interface C1Category
 * @package SM\DigitalProduct\Api\Data
 */
interface C1CategoryDataInterface
{
    const PRODUCTS = "products";
    const MAGENTO_CATEGORY_ID = "magento_category_id";
    const TYPE = "type";

    /**
     * @return int
     */
    public function getMagentoCategoryId();

    /**
     * @param int $value
     * @return $this
     */
    public function setMagentoCategoryId($value);

    /**
     * @return string
     */
    public function getType();

    /**
     * @param string $value
     * @return $this
     */
    public function setType($value);

    /**
     * @return \Magento\Catalog\Api\Data\ProductInterface[]
     */
    public function getProducts();

    /**
     * @param \Magento\Catalog\Api\Data\ProductInterface[] $value
     * @return $this
     */
    public function setProducts($value);
}
