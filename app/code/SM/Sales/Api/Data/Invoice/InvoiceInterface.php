<?php

namespace SM\Sales\Api\Data\Invoice;

/**
 * Interface InvoiceInterface
 * @package SM\Sales\Api\Data\Invoice
 */
interface InvoiceInterface
{
    const REFERENCE_INVOICE_NUMBER = 'invoice_number';
    const CREATED_AT = 'created_at';
    const SUBTOTAL = "subtotal";
    const SERVICE_FEE = "service_fee";
    const SHIPPING_AMOUNT = "shipping_amount";
    const DISCOUNT_AMOUNT = "discount_amount";
    const GRAND_TOTAL = "grand_total";
    const PAYMENT_INFO = 'payment_info';
    const SUB_INVOICES = 'sub_invoices';

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
    public function getCreatedAt();

    /**
     * @param string $value
     * @return $this
     */
    public function setCreatedAt($value);

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
    public function getShippingAmount();

    /**
     * @param int $value
     * @return $this
     */
    public function setShippingAmount($value);

    /**
     * @return int
     */
    public function getServiceFee();

    /**
     * @param int $value
     * @return $this
     */
    public function setServiceFee($value);

    /**
     * @return int
     */
    public function getDiscountAmount();

    /**
     * @param int $value
     * @return $this
     */
    public function setDiscountAmount($value);

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
     * @return \SM\Sales\Api\Data\PaymentInfoDataInterface
     */
    public function getPaymentInfo();

    /**
     * @param \SM\Sales\Api\Data\PaymentInfoDataInterface $value
     * @return $this
     */
    public function setPaymentInfo($value);

    /**
     * @return \SM\Sales\Api\Data\Invoice\SubInvoiceInterface[]
     */
    public function getSubInvoices();

    /**
     * @param \SM\Sales\Api\Data\Invoice\SubInvoiceInterface[] $value
     * @return $this
     */
    public function setSubInvoices($value);
}
