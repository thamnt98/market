<?php


namespace SM\DigitalProduct\Api\Data\Transaction;

/**
 * Interface MobileProductIdDataInterface
 * @package SM\DigitalProduct\Api\Data\Transaction
 */
interface MobileProductIdDataInterface extends ProductIdDataInterface
{
    const FIELD_DENOM = "field_denom";
    const FIELD_PAKET_DATA = "field_paket_data";

    /**
     * @return string
     */
    public function getFieldDenom();

    /**
     * @return string
     */
    public function getFieldPaketData();

    /**
     * @param string $value
     * @return $this
     */
    public function setFieldDenom($value);

    /**
     * @param string $value
     * @return $this
     */
    public function setFieldPaketData($value);
}
