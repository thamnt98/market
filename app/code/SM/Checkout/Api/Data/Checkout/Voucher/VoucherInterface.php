<?php

namespace SM\Checkout\Api\Data\Checkout\Voucher;

interface VoucherInterface
{
    const VOUCHER = 'voucher';
    const APPLY = 'apply';
    const AMOUNT = 'amount';
    const BASKET_ID = 'basket_id';
    const BASKET_VALUE = 'basket_value';
    const BASKET_QUANTITY = 'basket_quantity';
    const VOUCHER_ID = 'voucher_id';
    const VOUCHER_NAME = 'voucher_name';
    const VOUCHER_DESCRIPTION = 'voucher_description';
    const VOUCHER_VALIDATION = 'voucher_validation';
    const VOUCHER_STATUS = 'voucher_status';

    /**
     * @param string $voucher
     * @return $this
     */
    public function setVoucher($voucher);

    /**
     * @return string
     */
    public function getVoucher();

    /**
     * @param bool $apply
     * @return $this
     */
    public function setApply($apply);

    /**
     * @return bool
     */
    public function getApply();

    /**
     * @param string $amount
     * @return $this
     */
    public function setAmount($amount);

    /**
     * @return string
     */
    public function getAmount();

    /**
     * @return int
     */
    public function getBasketId();

    /**
     * @param int $value
     * @return $this
     */
    public function setBasketId($value);

    /**
     * @return string
     */
    public function getBasketValue();

    /**
     * @param string $value
     * @return $this
     */
    public function setBasketValue($value);

    /**
     * @return int
     */
    public function getBasketQuantity();

    /**
     * @param int $value
     * @return $this
     */
    public function setBasketQuantity($value);

    /**
     * @return string
     */
    public function getVoucherName();

    /**
     * @param string $value
     * @return $this
     */
    public function setVoucherName($value);

    /**
     * @return int
     */
    public function getVoucherId();

    /**
     * @param int $value
     * @return $this
     */
    public function setVoucherId($value);

    /**
     * @return string
     */
    public function getVoucherDescription();

    /**
     * @param string $value
     * @return $this
     */
    public function setVoucherDescription($value);

    /**
     * @return string
     */
    public function getVoucherValidation();

    /**
     * @param string $value
     * @return $this
     */
    public function setVoucherValidation($value);

    /**
     * @return string
     */
    public function getVoucherStatus();

    /**
     * @param string $value
     * @return $this
     */
    public function setVoucherStatus($value);
}
