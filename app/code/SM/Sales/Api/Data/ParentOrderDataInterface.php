<?php
/**
 * @category Magento
 * @package SM\Sales\Api\Data
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @author Hung Viet Nguyen <hungnv6@smartosc.com>
 * @copyright   Copyright (c) SmartOSC. All rights reserved.
 * @url http://www.smartosc.com/
 */

namespace SM\Sales\Api\Data;

/**
 * Interface ParentOrderDataInterface
 * @package SM\Sales\Api\Data
 */
interface ParentOrderDataInterface
{
    const PARENT_ORDER_ID = "parent_order_id";
    const REFERENCE_NUMBER = "reference_number";
    const ORDER_DATE = "order_date";
    const TOTAL_PAYMENT = "total_payment";
    const TOTAL_REFUND = "total_refund";
    const GRAND_TOTAL = "grand_total";
    const SUB_ORDERS = "sub_orders";
    const PAYMENT_METHOD = 'payment_method';
    const TOTAL_SHIPPING_AMOUNT = "total_shipping_amount";
    const SUBTOTAL = "subtotal";
    const STATUS = "status";
    const INVOICE_NUMBER = "invoice_number";
    const PAYMENT_INFO = "payment_info";
    const IS_DIGITAL = "is_digital";
    const VOUCHER_DETAIL = "voucher_detail";
    const CONVERT_DATE  = "convert_date";
    const PDF_LINK  = "pdf_link";
    const DISCOUNT_AMOUNT = "discount_amount";
    const CANCEL_MESSAGE = "cancel_message";
    const CANCEL_MESSAGE_MB = "cancel_message_mobile";
    const TRANSACTION_ID = "transaction_ID";

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
     * @param string $value
     * @return $this
     */
    public function setStatus($value);

    /**
     * @return string
     */
    public function getStatus();

    /**
     * @return bool
     */
    public function getIsDigital();

    /**
     * @param bool $value
     * @return $this
     */
    public function setIsDigital($value);

    /**
     * @param string $value
     * @return $this
     */
    public function setInvoiceNumber($value);

    /**
     * @return string
     */
    public function getInvoiceNumber();

    /**
     * @param string $value
     * @return $this
     */
    public function setOrderDate($value);

    /**
     * @return string
     */
    public function getOrderDate();

    /**
     * @param int $value
     * @return $this
     */
    public function setSubTotal($value);

    /**
     * @return int
     */
    public function getSubTotal();

    /**
     * @param int $value
     * @return $this
     */
    public function setTotalShippingAmount($value);

    /**
     * @return int
     */
    public function getTotalShippingAmount();

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
     * @return int
     */
    public function getTotalPayment();

    /**
     * @param int $value
     * @return $this
     */
    public function setTotalPayment($value);

    /**
     * @param \SM\Sales\Api\Data\PaymentInfoDataInterface $value
     * @return $this
     */
    public function setPaymentInfo($value);

    /**
     * @return \SM\Sales\Api\Data\PaymentInfoDataInterface
     */
    public function getPaymentInfo();

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
     * @param \SM\Sales\Api\Data\SubOrderDataInterface[] $value
     * @return $this
     */
    public function setSubOrders($value);

    /**
     * @return \SM\Sales\Api\Data\SubOrderDataInterface[]
     */
    public function getSubOrders();

    /**
     * @return string
     */
    public function getConvertDate();

    /**
     * @param string $data
     * @return $this
     */
    public function setConvertDate($data);

    /**
     * @return string
     */
    public function getInvoiceLink();

    /**
     * @param string $link
     * @return $this
     */
    public function setInvoiceLink($link);

    /**
     * @return int|null
     */
    public function getDiscountAmount();

    /**
     * @param int $value
     * @return $this
     */
    public function setDiscountAmount($value);

    /**
     * @return string
     */
    public function getCancelMessageMobile();

    /**
     * @param string $value
     * @return $this
     */
    public function setCancelMessageMobile($value);

    /**
     * @return string
     */
    public function getCancelMessage();

    /**
     * @param string $value
     * @return $this
     */
    public function setCancelMessage($value);

    /**
     * @return string
     */
    public function getTransactionId();

    /**
     * @param string $value
     * @return $this
     */
    public function setTransactionId($value);

    /**
     * @param string $value
     * @return $this
     */
    public function setTotalRefund($value);

    /**
     * @return int
     */
    public function getTotalRefund();

    /**
     * @param int $value
     * @return $this
     */
    public function setGrandTotal($value);

    /**
     * @return int
     */
    public function getGrandTotal();

}
