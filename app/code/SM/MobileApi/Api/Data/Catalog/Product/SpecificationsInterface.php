<?php

namespace SM\MobileApi\Api\Data\Catalog\Product;

interface SpecificationsInterface
{
    const VALUE = 'value';
    const LABEL = 'label';
    const CODE = "code";

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
     * @return string
     */
    public function getCode();

    /**
     * @param string $value
     * @return $this
     */
    public function setCode($value);
}
