<?php

namespace SM\MobileApi\Api\Data\Catalog;

/**
 * Interface for storing products filter item information
 */
interface ProductFilterItemInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{
    const COUNT = 'count';
    const LABEL = 'label';
    const VALUE = 'value';

    /**
     * Get products count
     *
     * @return int
     */
    public function getCount();

    /**
     * @param int
     * @return $this
     */
    public function setCount($data);

    /**
     * Get filter item label
     *
     * @return string
     */
    public function getLabel();

    /**
     * @param $data string
     * @return $this
     */
    public function setLabel($data);

    /**
     * Get filter item value
     *
     * @return string
     */
    public function getValue();

    /**
     * @param $data string
     * @return $this
     */
    public function setValue($data);
}
