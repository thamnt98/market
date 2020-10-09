<?php

namespace SM\MobileApi\Api\Data\Catalog\Product;

/**
 * Interface for storing attribute infomation
 */
interface AttributeInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{
    const LABEL = 'label';
    const VALUE = 'value';
    const CODE = 'code';

    /**
     * Get Label
     *
     * @return string
     */
    public function getLabel();

    /**
     * Set Label
     *
     * @param $label
     *
     * @return $this
     */
    public function setLabel($label);

    /**
     * Get Value
     *
     * @return string
     */
    public function getValue();

    /**
     * Set Value
     *
     * @param $value
     *
     * @return $this
     */
    public function setValue($value);

    /**
     * Set Code
     *
     * @return string
     */
    public function getCode();

    /**
     * Set Code
     *
     * @param $code
     *
     * @return $this
     */
    public function setCode($code);
}
