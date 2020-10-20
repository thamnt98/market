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
 * Interface SubOrderDataInterface
 * @package SM\Sales\Api\Data
 */
interface SubOrderDataInterface
{
    const SUB_ORDER_ID = "sub_order_id";
    const SHIPPING_METHOD = "shipping_method";
    const SHIPPING_METHOD_CODE = "shipping_method_code";
    const STATUS = "status";
    const ITEMS = "items";
    const ID = "id";
    const DELIVERY_ADDRESS = "delivery_address";
    const TRACKING_NUMBER = "tracking_number";
    const TRACKING_LINK = "tracking_link";
    const DELIVERY_FEE = "delivery_fee";
    const SUBTOTAL = "subtotal";
    const TOTAL_PAYMENT = "total_payment";
    const CREATED_AT = "created_at";
    const STATUS_HISTORY = "status_history";
    const STATUS_LABEL = "status_label";
    const STORE_INFO = 'store_info';
    const INVOICE_LINK = 'invoice_link';
    const STATUS_HISTORY_DETAILS = 'status_history_details';
    const SHIPPING_SERVICE_TYPE = 'shipping_service_type';
    const SHIPPING_DRIVER = 'shipping_driver';
    const SHIPPING_PLATE_NUMBER = 'shipping_plate_number';
    const DISCOUNT_AMOUNT = "discount_amount";
    const CANCEL_TYPE = "cancel_type";

    /**
     * @return int
     */
    public function getId();

    /**
     * @param int $value
     * @return $this
     */
    public function setId($value);

    /**
     * @return string
     */
    public function getSubOrderId();

    /**
     * @param string $value
     * @return $this
     */
    public function setSubOrderId($value);

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
    public function setStatusLabel($value);

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
     * @param int $value
     * @return $this
     */
    public function setTotalPayment($value);

    /**
     * @return int
     */
    public function getTotalPayment();

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
     * @param int $value
     * @return $this
     */
    public function setDeliveryFee($value);

    /**
     * @return int
     */
    public function getDeliveryFee();

    /**
     * @return \SM\Sales\Api\Data\DeliveryAddressDataInterface|null
     */
    public function getDeliveryAddress();

    /**
     * @param string $value
     * @return $this
     */
    public function setDeliveryAddress($value);

    /**
     * @return \SM\MobileApi\Api\Data\Catalog\Product\StoreInfoInterface|null
     */
    public function getStoreInfo();

    /**
     * @param \SM\MobileApi\Api\Data\Catalog\Product\StoreInfoInterface $store
     * @return $this
     */
    public function setStoreInfo($store);

    /**
     * @return string
     */
    public function getTrackingNumber();

    /**
     * @param string $value
     * @return $this
     */
    public function setTrackingNumber($value);

    /**
     * @return string
     */
    public function getTrackingLink();

    /**
     * @param string $value
     * @return $this
     */
    public function setTrackingLink($value);

    /**
     * @return string
     */
    public function getShippingMethod();

    /**
     * @param string $value
     * @return $this
     */
    public function setShippingMethod($value);

    /**
     * @return string
     */
    public function getShippingMethodCode();

    /**
     * @param string $value
     * @return $this
     */
    public function setShippingMethodCode($value);

    /**
     * @return \SM\Sales\Api\Data\DetailItemDataInterface[]
     */
    public function getItems();

    /**
     * @param \SM\Sales\Api\Data\DetailItemDataInterface[] $value
     * @return $this
     */
    public function setItems($value);

    /**
     * @return \SM\Sales\Api\Data\StatusHistoryDataInterface[]
     */
    public function getStatusHistory();

    /**
     * @param \SM\Sales\Api\Data\StatusHistoryDataInterface[] $value
     * @return $this
     */
    public function setStatusHistory($value);

    /**
     * @return string
     */
    public function getInvoiceLink();

    /**
     * @param string $data
     * @return $this
     */
    public function setInvoiceLink($data);

    /**
     * @return \SM\Sales\Api\Data\StatusHistoryDataInterface[]
     */
    public function getStatusHistoryDetails();

    /**
     * @param \SM\Sales\Api\Data\StatusHistoryDataInterface[] $value
     * @return $this
     */
    public function setStatusHistoryDetails($value);

    /**
     * @return string
     */
    public function getShippingServiceType();

    /**
     * @param string $value
     * @return $this
     */
    public function setShippingServiceType($value);

    /**
     * @return string
     */
    public function getShippingDriver();

    /**
     * @param string $value
     * @return $this
     */
    public function setShippingDriver($value);

    /**
     * @return string
     */
    public function getShippingPlateNumber();

    /**
     * @param string $value
     * @return $this
     */
    public function setShippingPlateNumber($value);

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
     * @return int
     */
    public function getCancelType();

    /**
     * @param int $value
     * @return $this
     */
    public function setCancelType($value);
}
