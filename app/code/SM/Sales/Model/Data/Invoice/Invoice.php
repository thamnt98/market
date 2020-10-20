<?php
/**
 * SM\Sales\Model\Data\Invoice
 *
 * @copyright Copyright © 2020 SmartOSC. All rights reserved.
 * @author    hungnv6@smartosc.com
 */

namespace SM\Sales\Model\Data\Invoice;

use Magento\Framework\DataObject;
use SM\Sales\Api\Data\Invoice\InvoiceInterface;

/**
 * Class Invoice
 * @package SM\Sales\Model\Data\Invoice
 */
class Invoice extends DataObject implements InvoiceInterface
{

    /**
     * @inheritDoc
     */
    public function getReferenceInvoiceNumber()
    {
        return $this->getData(self::REFERENCE_INVOICE_NUMBER);
    }

    /**
     * @inheritDoc
     */
    public function setReferenceInvoiceNumber($value)
    {
        return $this->setData(self::REFERENCE_INVOICE_NUMBER, $value);
    }

    /**
     * @inheritDoc
     */
    public function getCreatedAt()
    {
        return $this->getData(self::CREATED_AT);
    }

    /**
     * @inheritDoc
     */
    public function setCreatedAt($value)
    {
        return $this->setData(self::CREATED_AT, $value);
    }

    /**
     * @inheritDoc
     */
    public function getSubtotal()
    {
        return $this->getData(self::SUBTOTAL);
    }

    /**
     * @inheritDoc
     */
    public function setSubtotal($value)
    {
        return $this->setData(self::SUBTOTAL, $value);
    }

    /**
     * @inheritDoc
     */
    public function getServiceFee()
    {
        return $this->getData(self::SERVICE_FEE);
    }

    /**
     * @inheritDoc
     */
    public function setServiceFee($value)
    {
        return $this->setData(self::SERVICE_FEE, $value);
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
    public function getGrandTotal()
    {
        return $this->getData(self::GRAND_TOTAL);
    }

    /**
     * @inheritDoc
     */
    public function setGrandTotal($value)
    {
        return $this->setData(self::GRAND_TOTAL, $value);
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
    public function setPaymentInfo($value)
    {
        return $this->setData(self::PAYMENT_INFO, $value);
    }

    /**
     * @inheritDoc
     */
    public function getSubInvoices()
    {
        return $this->getData(self::SUB_INVOICES);
    }

    /**
     * @inheritDoc
     */
    public function setSubInvoices($value)
    {
        return $this->setData(self::SUB_INVOICES, $value);
    }

    /**
     * @inheritDoc
     */
    public function getShippingAmount()
    {
        return $this->getData(self::SHIPPING_AMOUNT);
    }

    /**
     * @inheritDoc
     */
    public function setShippingAmount($value)
    {
        return $this->setData(self::SHIPPING_AMOUNT, $value);
    }
}
