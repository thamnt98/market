<?php

namespace SM\MobileApi\Api\Data\Catalog;

/**
 * Interface for storing products toolbar available orders information
 */
interface ProductToolbarOrderInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{
    const FIELD = 'field';
    const LABEL = 'label';

    /**
     * Get sort name
     *
     * @return string
     */
    public function getField();

    /**
     * @param string $data
     *
     * @return $this
     */
    public function setField($data);

    /**
     * Get sort direction
     *
     * @return string
     */
    public function getLabel();

    /**
     * @param string $data
     *
     * @return $this
     */
    public function setLabel($data);
}
