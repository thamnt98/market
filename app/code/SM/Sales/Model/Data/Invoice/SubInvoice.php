<?php
/**
 * SM\Sales\Model\Data\Invoice
 *
 * @copyright Copyright © 2020 SmartOSC. All rights reserved.
 * @author    hungnv6@smartosc.com
 */

namespace SM\Sales\Model\Data\Invoice;

use Magento\Framework\DataObject;
use SM\Sales\Api\Data\Invoice\SubInvoiceInterface;

/**
 * Class SubInvoice
 * @package SM\Sales\Model\Data\Invoice
 */
class SubInvoice extends DataObject implements SubInvoiceInterface
{

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
    public function getItemAmount()
    {
        return $this->getData(self::ITEM_AMOUNT);
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
    public function getShippingAmount()
    {
        return $this->getData(self::SHIPPING_AMOUNT);
    }

    /**
     * @inheritDoc
     */
    public function getAdditionalFee()
    {
        return $this->getData(self::ADDITIONAL_FEE);
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
    public function getItems()
    {
        return $this->getData(self::ITEMS);
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
    public function setItemAmount($value)
    {
        return $this->setData(self::ITEM_AMOUNT, $value);
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
    public function setShippingAmount($value)
    {
        return $this->setData(self::SHIPPING_AMOUNT, $value);
    }

    /**
     * @inheritDoc
     */
    public function setAdditionalFee($value)
    {
        return $this->setData(self::ADDITIONAL_FEE, $value);
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
    public function setItems($value)
    {
        return $this->setData(self::ITEMS, $value);
    }

    /**
     * @inheritDoc
     */
    public function getDeliveryAddress()
    {
        return $this->getData(self::DELIVERY_ADDRESS);
    }

    /**
     * @inheritDoc
     */
    public function setDeliveryAddress($value)
    {
        return $this->setData(self::DELIVERY_ADDRESS, $value);
    }

    /**
     * @inheritDoc
     */
    public function getStoreInfo()
    {
        return $this->getData(self::STORE_INFO);
    }

    /**
     * @inheritDoc
     */
    public function setStoreInfo($value)
    {
        return $this->setData(self::STORE_INFO, $value);
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
}
