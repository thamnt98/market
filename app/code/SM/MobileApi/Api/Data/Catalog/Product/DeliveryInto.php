<?php


namespace SM\MobileApi\Api\Data\Catalog\Product;


interface DeliveryInto
{
    const VALUE = 'value';
    const LABEL = 'label';

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
     * @return string
     */
    public function getLabel();

    /**
     * @param string $label
     * @return $this
     */
    public function setLabel($label);
}
