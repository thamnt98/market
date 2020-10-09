<?php

namespace SM\DigitalProduct\Api\Data\Transaction;

/**
 * Interface ProductIdDataInterface
 * @package SM\DigitalProduct\Api\Data\Transaction
 */
interface ProductIdDataInterface
{
    const PRODUCT_ID = "product_id";
    const TYPE = "type";
    const LABEL = "label";
    const OPERATOR = "operator";
    const NOMINAL = "nominal";
    const PRICE = "price";
    const ENABLED = "enabled";

    /**
     * @return string
     */
    public function getProductId();

    /**
     * @return string
     */
    public function getType();

    /**
     * @return string
     */
    public function getLabel();

    /**
     * @return string
     */
    public function getOperator();

    /**
     * @return string
     */
    public function getNominal();

    /**
     * @return int
     */
    public function getPrice();

    /**
     * @return string
     */
    public function getEnabled();

    /**
     * @param string $value
     * @return $this
     */
    public function setProductId($value);

    /**
     * @param string $value
     * @return $this
     */
    public function setType($value);

    /**
     * @param string $value
     * @return $this
     */
    public function setLabel($value);

    /**
     * @param string $value
     * @return $this
     */
    public function setOperator($value);

    /**
     * @param string $value
     * @return $this
     */
    public function setNominal($value);

    /**
     * @param int $value
     * @return $this
     */
    public function setPrice($value);

    /**
     * @param string $value
     * @return $this
     */
    public function setEnabled($value);
}
