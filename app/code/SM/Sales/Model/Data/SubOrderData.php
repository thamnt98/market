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
use SM\Sales\Api\Data\SubOrderDataInterface;

/**
 * Class SubOrderData
 * @package SM\Sales\Model\Data
 */
class SubOrderData extends DataObject implements SubOrderDataInterface
{
    /**
     * @inheritDoc
     */
    public function getSubOrderId()
    {
        return $this->getData(self::SUB_ORDER_ID);
    }

    /**
     * @inheritDoc
     */
    public function getShippingMethod()
    {
        return $this->getData(self::SHIPPING_METHOD);
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
    public function setSubOrderId($value)
    {
        return $this->setData(self::SUB_ORDER_ID, $value);
    }

    /**
     * @inheritDoc
     */
    public function setShippingMethod($value)
    {
        return $this->setData(self::SHIPPING_METHOD, $value);
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
    public function getItems()
    {
        return $this->getData(self::ITEMS);
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
    public function getId()
    {
        return $this->getData("id");
    }

    /**
     * @inheritDoc
     */
    public function setId($value)
    {
        return $this->setData(self::ID, $value);
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
    public function getTrackingNumber()
    {
        return $this->getData(self::TRACKING_NUMBER);
    }

    /**
     * @inheritDoc
     */
    public function setTrackingNumber($value)
    {
        return $this->setData(self::TRACKING_NUMBER, $value);
    }

    /**
     * @inheritDoc
     */
    public function setDeliveryFee($value)
    {
        return $this->setData(self::DELIVERY_FEE, $value);
    }

    /**
     * @inheritDoc
     */
    public function getDeliveryFee()
    {
        return $this->getData(self::DELIVERY_FEE);
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
    public function setTotalPayment($value)
    {
        return $this->setData(self::TOTAL_PAYMENT, $value);
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
    public function getStatusHistory()
    {
        return $this->getData(self::STATUS_HISTORY);
    }

    /**
     * @inheritDoc
     */
    public function setStatusHistory($value)
    {
        return $this->setData(self::STATUS_HISTORY, $value);
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
    public function getShippingMethodCode()
    {
        return $this->getData(self::SHIPPING_METHOD_CODE);
    }

    /**
     * @inheritDoc
     */
    public function setShippingMethodCode($value)
    {
        return $this->setData(self::SHIPPING_METHOD_CODE, $value);
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
    public function setStoreInfo($store)
    {
        return $this->setData(self::STORE_INFO, $store);
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
    public function setInvoiceLink($data)
    {
        return $this->setData(self::INVOICE_LINK, $data);
    }

    /**
     * @inheritDoc
     */
    public function getStatusHistoryDetails()
    {
        return $this->getData(self::STATUS_HISTORY_DETAILS);
    }

    /**
     * @inheritDoc
     */
    public function setStatusHistoryDetails($value)
    {
        return $this->setData(self::STATUS_HISTORY_DETAILS, $value);
    }

    /**
     * @inheritDoc
     */
    public function getShippingServiceType()
    {
        return $this->getData(self::SHIPPING_SERVICE_TYPE);
    }

    /**
     * @inheritDoc
     */
    public function setShippingServiceType($value)
    {
        return $this->setData(self::SHIPPING_SERVICE_TYPE, $value);
    }

    /**
     * @inheritDoc
     */
    public function getShippingDriver()
    {
        return $this->getData(self::SHIPPING_DRIVER);
    }

    /**
     * @inheritDoc
     */
    public function setShippingDriver($value)
    {
        return $this->setData(self::SHIPPING_DRIVER, $value);
    }

    /**
     * @inheritDoc
     */
    public function getShippingPlateNumber()
    {
        return $this->getData(self::SHIPPING_PLATE_NUMBER);
    }

    /**
     * @inheritDoc
     */
    public function setShippingPlateNumber($value)
    {
        return $this->setData(self::SHIPPING_PLATE_NUMBER, $value);
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
    public function getCancelType()
    {
        return $this->getData(self::CANCEL_TYPE);
    }

    /**
     * @inheritDoc
     */
    public function setCancelType($value)
    {
        return $this->setData(self::CANCEL_TYPE, $value);
    }

    /**
     * @inheritDoc
     */
    public function getTrackingLink()
    {
        return $this->getData(self::TRACKING_LINK);
    }

    /**
     * @inheritDoc
     */
    public function setTrackingLink($value)
    {
        return $this->setData(self::TRACKING_LINK, $value);
    }

    public function setCreditmemoId($value)
    {
        return $this->setData(self::CREDITMEMO_ID, $value);
    }

    public function getCreditmemoId()
    {
        return $this->getData(self::CREDITMEMO_ID);
    }

    public function setHasCreditmemo($value)
    {
        return $this->setData(self::HAS_CREDITMEMO, $value);
    }

    public function hasCreditmemo()
    {
        return $this->getData(self::HAS_CREDITMEMO);
    }
}
