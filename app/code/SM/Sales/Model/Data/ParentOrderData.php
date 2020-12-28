<?php
/**
 * @category Magento
 * @package SM\Sales\Model\Data
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @author Hung Viet Nguyen <hungnv6@smartosc.com>
 * @copyright   Copyright (c) SmartOSC. All rights reserved.
 * @url http://www.smartosc.com/
 */

namespace SM\Sales\Model\Data;

use Magento\Framework\DataObject;
use SM\Sales\Api\Data\ParentOrderDataInterface;

/**
 * Class ParentOrderData
 * @package SM\Sales\Model\Data
 */
class ParentOrderData extends DataObject implements ParentOrderDataInterface
{

    /**
     * @inheritDoc
     */
    public function getReferenceNumber()
    {
        return $this->getData(self::REFERENCE_NUMBER);
    }

    /**
     * @inheritDoc
     */
    public function getOrderDate()
    {
        return $this->getData(self::ORDER_DATE);
    }

    /**
     * @inheritDoc
     */
    public function getTotalPayment()
    {
        return $this->getData(self::TOTAL_PAYMENT);
    }

    /**
     * @inheritDoc
     */
    public function getSubOrders()
    {
        return $this->getData(self::SUB_ORDERS);
    }

    /**
     * @inheritDoc
     */
    public function setReferenceNumber($value)
    {
        return $this->setData(self::REFERENCE_NUMBER, $value);
    }

    /**
     * @inheritDoc
     */
    public function setOrderDate($value)
    {
        return $this->setData(self::ORDER_DATE, $value);
    }

    /**
     * @inheritDoc
     */
    public function setTotalPayment($value)
    {
        return $this->setData(self::TOTAL_PAYMENT, $value);
    }

    /**
     * @inheritDoc
     */
    public function setSubOrders($value)
    {
        return $this->setData(self::SUB_ORDERS, $value);
    }

    /**
     * @inheritDoc
     */
    public function getParentOrderId()
    {
        return $this->getData(self::PARENT_ORDER_ID);
    }

    /**
     * @inheritDoc
     */
    public function setParentOrderId($value)
    {
        return $this->setData(self::PARENT_ORDER_ID, $value);
    }

    /**
     * @inheritDoc
     */
    public function getPaymentMethod()
    {
        return $this->getData(self::PAYMENT_METHOD);
    }

    /**
     * @inheritDoc
     */
    public function setPaymentMethod($value)
    {
        return $this->setData(self::PAYMENT_METHOD, $value);
    }

    /**
     * @inheritDoc
     */
    public function setTotalShippingAmount($value)
    {
        return $this->setData(self::TOTAL_SHIPPING_AMOUNT, $value);
    }

    /**
     * @inheritDoc
     */
    public function getTotalShippingAmount()
    {
        return $this->getData(self::TOTAL_SHIPPING_AMOUNT);
    }

    /**
     * @inheritDoc
     */
    public function setSubTotal($value)
    {
        return $this->setData(self::SUBTOTAL, $value);
    }

    /**
     * @inheritDoc
     */
    public function getSubTotal()
    {
        return $this->getData(self::SUBTOTAL);
    }

    /**
     * @inheritDoc
     */
    public function setStatus($value)
    {
        return $this->setData(self::STATUS, $value);
    }

    /**
     * @inheritDoc
     */
    public function getStatus()
    {
        return $this->getData(self::STATUS);
    }

    /**
     * @inheritDoc
     */
    public function setInvoiceNumber($value)
    {
        return $this->setData(self::INVOICE_NUMBER, $value);
    }

    /**
     * @inheritDoc
     */
    public function getInvoiceNumber()
    {
        return $this->getData(self::INVOICE_NUMBER);
    }

    /**
     * @inheritDoc
     */
    public function setPaymentInfo($value)
    {
        return $this->setData(self::PAYMENT_INFO, $value);
    }

    /**
     * @inheritDoc
     */
    public function getPaymentInfo()
    {
        return $this->getData(self::PAYMENT_INFO);
    }

    /**
     * @inheritDoc
     */
    public function getIsDigital()
    {
        return $this->getData(self::IS_DIGITAL);
    }

    /**
     * @inheritDoc
     */
    public function setIsDigital($value)
    {
        return $this->setData(self::IS_DIGITAL, $value);
    }

    /**
     * @inheritDoc
     */
    public function setVoucherDetail($value)
    {
        return $this->setData(self::VOUCHER_DETAIL, $value);
    }

    /**
     * @inheritDoc
     */
    public function getVoucherDetail()
    {
        return $this->getData(self::VOUCHER_DETAIL);
    }

    /**
     * @inheritDoc
     */
    public function getConvertDate()
    {
        return $this->getData(self::CONVERT_DATE);
    }

    /**
     * @inheritDoc
     */
    public function setConvertDate($data)
    {
        return $this->setData(self::CONVERT_DATE, $data);
    }

    /**
     * @inheritDoc
     */
    public function getInvoiceLink()
    {
        return $this->getData(self::PDF_LINK);
    }

    /**
     * @inheritDoc
     */
    public function setInvoiceLink($link)
    {
        return $this->setData(self::PDF_LINK, $link);
    }

    /**
     * @inheritDoc
     */
    public function getDiscountAmount()
    {
        return $this->getData(self::DISCOUNT_AMOUNT);
    }

    /**
     * @inheritDoc
     */
    public function setDiscountAmount($value)
    {
        return $this->setData(self::DISCOUNT_AMOUNT, $value);
    }

    /**
     * @inheritDoc
     */
    public function getCancelMessageMobile()
    {
        return $this->getData(self::CANCEL_MESSAGE_MB);
    }

    /**
     * @inheritDoc
     */
    public function setCancelMessageMobile($value)
    {
        return $this->setData(self::CANCEL_MESSAGE_MB, $value);
    }

    /**
     * @inheritDoc
     */
    public function getCancelMessage()
    {
        return $this->getData(self::CANCEL_MESSAGE);
    }

    /**
     * @inheritDoc
     */
    public function setCancelMessage($value)
    {
        return $this->setData(self::CANCEL_MESSAGE, $value);
    }

    /**
     * @inheritDoc
     */
    public function getTransactionId()
    {
        return $this->getData(self::TRANSACTION_ID);
    }

    /**
     * @inheritDoc
     */
    public function setTransactionId($value)
    {
        return $this->setData(self::TRANSACTION_ID, $value);
    }

    public function setTotalRefund($value)
    {
        return $this->setData(self::TOTAL_REFUND, $value);
    }

    public function getTotalRefund()
    {
        return $this->getData(self::TOTAL_REFUND);
    }

    public function setGrandTotal($value)
    {
        return $this->setData(self::GRAND_TOTAL, $value);
    }

    public function getGrandTotal()
    {
        return $this->getData(self::GRAND_TOTAL);
    }
}
