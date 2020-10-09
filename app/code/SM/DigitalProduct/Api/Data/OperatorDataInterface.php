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
 * Interface OperatorDataInterface
 * @package SM\DigitalProduct\Api\Data
 */
interface OperatorDataInterface
{
    const ID = "id";
    const BRAND_ID = "brand_id";
    const OPERATOR_NAME = "operator_name";
    const SERVICE_NAME = "service_name";
    const PREFIX_NUMBER = "prefix_number";
    const OPERATOR_ICON = "operator_icon";
    const PRODUCTS = "products";

    /**
     * @return int
     */
    public function getId();

    /**
     * @return int
     */
    public function getBrandId();

    /**
     * @return string
     */
    public function getOperatorName();

    /**
     * @return string
     */
    public function getServiceName();

    /**
     * @return string
     */
    public function getPrefixNumber();

    /**
     * @param int $value
     * @return $this
     */
    public function setId($value);

    /**
     * @param int $value
     * @return $this
     */
    public function setBrandId($value);

    /**
     * @param string $value
     * @return $this
     */
    public function setOperatorName($value);

    /**
     * @param string $value
     * @return $this
     */
    public function setServiceName($value);

    /**
     * @param string $value
     * @return $this
     */
    public function setPrefixNumber($value);

    /**
     * @return string
     */
    public function getOperatorIcon();

    /**
     * @param string $value
     * @return $this
     */
    public function setOperatorIcon($value);

    /**
     * @param \SM\DigitalProduct\Api\Data\ProductInterface[] $value
     * @return mixed
     */
    public function setProducts($value);

    /**
     * @return \SM\DigitalProduct\Api\Data\ProductInterface[]
     */
    public function getProducts();
}
