<?php

namespace SM\Checkout\Api\Data\CartItem;

/**
 * Interface OptionListInterface
 * @package SM\Checkout\Api\Data\CartItem
 */
interface OptionListInterface
{
    const PRODUCT_NAME = 'product_name';
    const LABEL = 'label';
    const VALUE = 'value';
    const OPTION_ID = 'option_id';
    const OPTION_VALUE = 'option_value';
    const OPTION_COLOR_TEXT = 'option_color_text';

    /**
     * @return string
     */
    public function getLabel();

    /**
     * @param string $label
     * @return $this
     */
    public function setLabel($label);

    /**
     * @return string
     */
    public function getValue();

    /**
     * @param string $value
     * @return $this
     */
    public function setValue($value);

    /**
     * @return int
     */
    public function getOptionId();

    /**
     * @param int $optionId
     * @return $this
     */
    public function setOptionId($optionId);

    /**
     * @return int
     */
    public function getOptionValue();

    /**
     * @param int $optionValue
     * @return $this
     */
    public function setOptionValue($optionValue);

    /**
     * @return string
     */
    public function getOptionColorText();

    /**
     * @param string $colorText
     * @return $this
     */
    public function setOptionColorText($colorText);

    /**
     * @return string
     */
    public function getProductName();

    /**
     * @param string $data
     * @return $this
     */
    public function setProductName($data);
}
