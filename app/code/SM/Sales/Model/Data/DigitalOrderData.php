<?php


namespace SM\Sales\Model\Data;

use Magento\Framework\DataObject;
use SM\Sales\Api\Data\DigitalOrderDataInterface;

/**
 * Class DigitalOrderData
 * @package SM\Sales\Model\Data
 */
class DigitalOrderData extends DataObject implements DigitalOrderDataInterface
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
    public function setReferenceNumber($value)
    {
        return $this->setData(self::REFERENCE_NUMBER, $value);
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
    public function getStatus()
    {
        return $this->getData(self::STATUS);
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
    public function getStatusLabel()
    {
        return $this->getData(self::STATUS_LABEL);
    }

    /**
     * @inheritDoc
     */
    public function setStatusLabel($value)
    {
        return $this->setData(self::STATUS_LABEL, $value);
    }

    /**
     * @inheritDoc
     */
    public function getProductOption()
    {
        return $this->getData(self::PRODUCT_OPTION);
    }

    /**
     * @inheritDoc
     */
    public function setProductOption($value)
    {
        return $this->setData(self::PRODUCT_OPTION, $value);
    }

    /**
     * @inheritDoc
     */
    public function getReferenceOrderId()
    {
        return $this->getData(self::REFERENCE_ORDER_ID);
    }

    /**
     * @inheritDoc
     */
    public function setReferenceOrderId($value)
    {
        return $this->setData(self::REFERENCE_ORDER_ID, $value);
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
    public function getBuyRequest()
    {
        return $this->getData(self::BUY_REQUEST);
    }

    /**
     * @inheritDoc
     */
    public function setBuyRequest($value)
    {
        return $this->setData(self::BUY_REQUEST, $value);
    }

    /**
     * @inheritDoc
     */
    public function getProductName()
    {
        return $this->getData(self::PRODUCT_NAME);
    }

    /**
     * @inheritDoc
     */
    public function setProductName($value)
    {
        return $this->setData(self::PRODUCT_NAME, $value);
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
    public function getSku()
    {
        return $this->getData(self::SKU);
    }

    /**
     * @inheritDoc
     */
    public function setSku($value)
    {
        return $this->setData(self::SKU, $value);
    }

    /**
     * @inheritDoc
     */
    public function getPrice()
    {
        return $this->getData(self::PRICE);
    }

    /**
     * @inheritDoc
     */
    public function setPrice($value)
    {
        return $this->setData(self::PRICE, $value);
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
    public function getUpdatedAt()
    {
        return $this->getData(self::UPDATED_AT);
    }

    /**
     * @inheritDoc
     */
    public function setUpdatedAt($value)
    {
        return $this->setData(self::UPDATED_AT, $value);
    }

    /**
     * @inheritDoc
     */
    public function getInvoiceLink()
    {
        return $this->getData(self::INVOICE_LINK);
    }

    /**
     * @inheritDoc
     */
    public function setInvoiceLink($value)
    {
        return $this->setData(self::INVOICE_LINK, $value);
    }
}
