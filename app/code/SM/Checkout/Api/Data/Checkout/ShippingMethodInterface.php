<?php

namespace SM\Checkout\Api\Data\Checkout;

interface ShippingMethodInterface
{
    /**
     * @param string $value
     * @return $this
     */
    public function setValue($value);

    /**
     * @return string
     */
    public function getValue();

    /**
     * @param string $label
     * @return $this
     */
    public function setLabel($label);

    /**
     * @return string
     */
    public function getLabel();

    /**
     * @param bool $disabled
     * @return $this
     */
    public function setDisabled($disabled);

    /**
     * @return bool
     */
    public function getDisabled();
}
