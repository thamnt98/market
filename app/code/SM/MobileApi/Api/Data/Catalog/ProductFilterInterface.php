<?php

namespace SM\MobileApi\Api\Data\Catalog;

/**
 * Interface for storing products filter information
 */
interface ProductFilterInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{
    const NAME           = 'name';
    const CODE           = 'code';
    const ITEMS          = 'items';
    const IS_MULTISELECT = 'is_multiselect';
    const MIN            = 'min';
    const MAX            = 'max';
    const FROM           = 'from';
    const TO             = 'to';

    /**
     * Get filter name
     *
     * @return string
     */
    public function getName();

    /**
     * @param string
     *
     * @return $this
     */
    public function setName($data);

    /**
     * Get filter code
     *
     * @return string
     */
    public function getCode();

    /**
     * @param $data string
     *
     * @return $this
     */
    public function setCode($data);

    /**
     * @return bool
     */
    public function getIsMultiselect();

    /**
     * @param bool $data
     *
     * @return $this
     */
    public function setIsMultiselect($data);

    /**
     * @return float
     */
    public function getMin();

    /**
     * @param float $data
     *
     * @return $this
     */
    public function setMin($data);

    /**
     * @return float
     */
    public function getMax();

    /**
     * @param float $data
     *
     * @return $this
     */
    public function setMax($data);

    /**
     * @return float
     */
    public function getFrom();

    /**
     * @param float $data
     *
     * @return $this
     */
    public function setFrom($data);

    /**
     * @return float
     */
    public function getTo();

    /**
     * @param float $data
     *
     * @return $this
     */
    public function setTo($data);

    /**
     * Get filter items
     *
     * @return \SM\MobileApi\Api\Data\Catalog\ProductFilterItemInterface[]
     */
    public function getItems();

    /**
     * @param $data \SM\MobileApi\Api\Data\Catalog\ProductFilterItemInterface[]
     *
     * @return $this
     */
    public function setItems($data);
}
