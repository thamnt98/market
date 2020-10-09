<?php

namespace SM\Sales\Api\Data;

/**
 * Interface DigitalOrderDataInterface
 * @package SM\Sales\Api\Data
 */
interface DigitalOrderDataInterface
{
    const PARENT_ORDER_ID = "parent_order_id";
    const REFERENCE_NUMBER = "reference_number";
    const REFERENCE_ORDER_ID = "reference_order_id";
    const REFERENCE_INVOICE_NUMBER = "reference_invoice_number";
    const PRODUCT_NAME = "product_name";
    const SKU = "sku";
    const CREATED_AT = "created_at";
    const UPDATED_AT = "updated_at";
    const PRICE = "price";
    const SUBTOTAL = "subtotal";
    const GRAND_TOTAL = "grand_total";
    const STATUS = "status";
    const STATUS_LABEL = "status_label";
    const BUY_REQUEST = "buy_request";
    const PAYMENT_METHOD = 'payment_method';
    const PAYMENT_INFO = "payment_info";
    const PRODUCT_OPTION = "product_option";
    const VOUCHER_DETAIL = "voucher_detail";
    const INVOICE_LINK = "invoice_link";

    /**
     * @return int
     */
    public function getParentOrderId();

    /**
     * @param int $value
     * @return $this
     */
    public function setParentOrderId($value);

    /**
     * @return string
     */
    public function getReferenceNumber();

    /**
     * @param string $value
     * @return $this
     */
    public function setReferenceNumber($value);

    /**
     * @return string
     */
    public function getReferenceOrderId();

    /**
     * @param string $value
     * @return $this
     */
    public function setReferenceOrderId($value);

    /**
     * @return string
     */
    public function getReferenceInvoiceNumber();

    /**
     * @param string $value
     * @return $this
     */
    public function setReferenceInvoiceNumber($value);

    /**
     * @return string
     */
    public function getInvoiceLink();

    /**
     * @param string $value
     * @return $this
     */
    public function setInvoiceLink($value);

    /**
     * @return string
     */
    public function getProductName();

    /**
     * @param string $value
     * @return $this
     */
    public function setProductName($value);

    /**
     * @return string
     */
    public function getSku();

    /**
     * @param string $value
     * @return $this
     */
    public function setSku($value);

    /**
     * @return string
     */
    public function getCreatedAt();

    /**
     * @param string $value
     * @return $this
     */
    public function setCreatedAt($value);

    /**
     * @return string
     */
    public function getUpdatedAt();

    /**
     * @param string $value
     * @return $this
     */
    public function setUpdatedAt($value);

    /**
     * @return int
     */
    public function getPrice();

    /**
     * @param int $value
     * @return $this
     */
    public function setPrice($value);

    /**
     * @return int
     */
    public function getSubtotal();

    /**
     * @param int $value
     * @return $this
     */
    public function setSubtotal($value);

    /**
     * @return int
     */
    public function getGrandTotal();

    /**
     * @param int $value
     * @return $this
     */
    public function setGrandTotal($value);

    /**
     * @return string
     */
    public function getStatus();

    /**
     * @param string $value
     * @return $this
     */
    public function setStatus($value);

    /**
     * @return string
     */
    public function getStatusLabel();

    /**
     * @param string $value
     * @return $this
     */
    public function setBuyRequest($value);

    /**
     * @param string $value
     * @return $this
     */
    public function setStatusLabel($value);

    /**
     * @return string
     */
    public function getPaymentMethod();

    /**
     * @param string $value
     * @return $this
     */
    public function setPaymentMethod($value);

    /**
     * @return \SM\Sales\Api\Data\PaymentInfoDataInterface
     */
    public function getPaymentInfo();

    /**
     * @param \SM\Sales\Api\Data\PaymentInfoDataInterface $value
     * @return $this
     */
    public function setPaymentInfo($value);

    /**
     * @param \Amasty\Rules\Api\Data\DiscountBreakdownLineInterface[] $value
     * @return $this
     */
    public function setVoucherDetail($value);

    /**
     * @return \Amasty\Rules\Api\Data\DiscountBreakdownLineInterface[]
     */
    public function getVoucherDetail();

    /**
     * @return \Magento\Catalog\Api\Data\ProductOptionInterface|null
     */
    public function getProductOption();

    /**
     * @param \Magento\Catalog\Api\Data\ProductOptionInterface $value
     * @return $this
     */
    public function setProductOption($value);
}
