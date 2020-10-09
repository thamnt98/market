<?php


namespace SM\MobileApi\Api\Data\Catalog\Product\Configurable;


interface AttributeOptionInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{
    const ID = 'id';
    const LABEL = 'label';
    const PRODUCTS = 'products';
    const HEX_COLOR = 'hex_color';
    const IMAGE = 'image';

    /**
     * Get Option ID
     *
     * @return int
     */
    public function getId();

    /**
     * @param int $data
     * @return $this
     */
    public function setId($data);

    /**
     * Get Option label
     *
     * @return string
     */
    public function getLabel();

    /**
     * @param string $data
     * @return $this
     */
    public function setLabel($data);

    /**
     * @return string
     */
    public function getHexColorCode();

    /**
     * @param string $color
     * @return $this
     */
    public function setHexColorCode($color);

    /**
     * @return string
     */
    public function getImage();

    /**
     * @param string $image
     * @return $this
     */
    public function setImage($image);

    /**
     * Get products
     *
     * @return \SM\MobileApi\Api\Data\Catalog\Product\Configurable\ProductInterface[]
     */
    public function getProducts();

    /**
     * @param \SM\MobileApi\Api\Data\Catalog\Product\Configurable\ProductInterface[] $data
     * @return $this
     */
    public function setProducts($data);
}
