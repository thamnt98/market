<?php
declare(strict_types=1);

namespace SM\DigitalProduct\Api\Data;

/**
 * Interface CategoryContentInterface
 * @package SM\DigitalProduct\Api\Data
 */
interface CategoryContentInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{
    const HOW_TO_BUY = 'how_to_buy';
    const CATEGORY_NAME = 'category_name';
    const INFORMATION = 'information';
    const TOOLTIP = "tooltip";
    const TYPE = "type";
    const STORE_ID = "store_id";
    const INFO = "info";
    const CATEGORY_ID = "category_id";
    const CATEGORY_STORE_ID = "category_store_id";
    const PRODUCTS = "product";
    const OPERATOR_IMAGE = "operator_image";
    const OPERATOR = "operator";

    /**
     * @return string
     */
    public function getTooltip();

    /**
     * @param string $value
     * @return \SM\DigitalProduct\Api\Data\CategoryContentInterface
     */
    public function setTooltip($value);

    /**
     * @return \SM\DigitalProduct\Api\Data\ProductInterface[] $value
     */
    public function getProducts();

    /**
     * @param \SM\DigitalProduct\Api\Data\ProductInterface[] $value
     * @return $this
     */
    public function setProducts($value);

    /**
     * Retrieve existing extension attributes object or create a new one.
     * @return \SM\DigitalProduct\Api\Data\CategoryContentExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object.
     * @param \SM\DigitalProduct\Api\Data\CategoryContentExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \SM\DigitalProduct\Api\Data\CategoryContentExtensionInterface $extensionAttributes
    );

    /**
     * Set category_name
     * @param string $categoryName
     * @return \SM\DigitalProduct\Api\Data\CategoryContentInterface
     */
    public function setCategoryName($categoryName);

    /**
     * Info using for payment page
     * @return string
     */
    public function getInfo();

    /**
     * @param string $value
     * @return $this
     */
    public function setInfo($value);

    /**
     * Get information
     * @return string
     */
    public function getInformation();

    /**
     * Set information
     * @param string $information
     * @return \SM\DigitalProduct\Api\Data\CategoryContentInterface
     */
    public function setInformation($information);

    /**
     * Get how_to_buy
     * @return string
     */
    public function getHowToBuy();

    /**
     * Set how_to_buy
     * @param string $howToBuy
     * @return \SM\DigitalProduct\Api\Data\CategoryContentInterface
     */
    public function setHowToBuy($howToBuy);

    /**
     * @return string
     */
    public function getOperatorImage();

    /**
     * @param string $value
     * @return $this
     */
    public function setOperatorImage($value);

    /**
     * @return string
     */
    public function getOperator();

    /**
     * @param string $value
     * @return $this
     */
    public function setOperator($value);
}
