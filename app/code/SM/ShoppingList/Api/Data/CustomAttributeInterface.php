<?php

namespace SM\ShoppingList\Api\Data;

/**
 * Interface CustomAttributeInterface
 * @package SM\ShoppingList\Api\Data
 */
interface CustomAttributeInterface
{
    /**
     * @return string
     */
    public function getName();

    /**
     * @param string $name Name of the attribute
     *
     * @return $this
     */
    public function setName($name);

    /**
     * @return string
     */
    public function getValue();

    /**
     * @param string $value Value of the attribute
     *
     * @return $this
     */
    public function setValue($value);
}
