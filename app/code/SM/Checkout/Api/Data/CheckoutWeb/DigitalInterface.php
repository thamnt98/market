<?php

namespace SM\Checkout\Api\Data\CheckoutWeb;

interface DigitalInterface
{
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
     * @param string $value
     * @return $this
     */
    public function setValue($value);

    /**
     * @return string
     */
    public function getValue();
}
