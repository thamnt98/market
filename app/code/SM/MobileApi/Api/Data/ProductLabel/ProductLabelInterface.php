<?php

namespace SM\MobileApi\Api\Data\ProductLabel;

interface ProductLabelInterface
{
    /**#@+
     * Constants defined for keys of data array
     */
    const STORES = 'stores';

    const PRODUCT_TEXT = 'product_text';
    const PRODUCT_TEXT_COLOR = 'product_text_color';
    const PRODUCT_BACKGROUND_COLOR = 'product_background_color';

    const CATEGORY_TEXT = 'category_text';
    const CATEGORY_TEXT_COLOR = 'category_text_color';
    const CATEGORY_BACKGROUND_COLOR = 'category_background_color';
    /**#@-*/

    /**
     * @return string
     */
    public function getStores();

    /**
     * @param string $stores
     *
     * @return \SM\MobileApi\Api\Data\ProductLabel\ProductLabelInterface
     */
    public function setStores($stores);

    /**
     * @return string
     */
    public function getProductText();

    /**
     * @param string $prodTxt
     *
     * @return \SM\MobileApi\Api\Data\ProductLabel\ProductLabelInterface
     */
    public function setProductText($prodTxt);

    /**
     * @return string
     */
    public function getProductTextColor();

    /**
     * @param string $prodStyle
     *
     * @return \SM\MobileApi\Api\Data\ProductLabel\ProductLabelInterface
     */
    public function setProductTextColor($prodStyle);

    /**
     * @return string
     */
    public function getProductBackGround();

    /**
     * @param string $prodTextStyle
     *
     * @return \SM\MobileApi\Api\Data\ProductLabel\ProductLabelInterface
     */
    public function setProductBackGround($prodTextStyle);

    /**
     * @return string
     */
    public function getCategoryText();

    /**
     * @param string $value
     * @return $this
     */
    public function setCategoryText($value);

    /**
     * @return string
     */
    public function getCategoryBackGround();

    /**
     * @param string $value
     * @return $this
     */
    public function setCategoryBackGround($value);

    /**
     * @return string
     */
    public function getCategoryTextColor();

    /**
     * @param string $value
     * @return $this
     */
    public function setCategoryTextColor($value);
}
