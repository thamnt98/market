<?php

namespace SM\MobileApi\Api\Data\Store\Setting;

/**
 * Interface ProductAttributeInterface
 * @package SM\MobileApi\Api\Data\Store
 */
interface ProductAttributeInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{
    const VALUE = 'value';
    const LABEL = 'label';

    /**
     * Get attribute label
     *
     * @return string
     */
    public function getLabel();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setLabel($value);

    /**
     * @return string
     */
    public function getValue();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setValue($value);
}
