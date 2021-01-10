<?php


namespace SM\Sales\Api\Data\Invoice;

/**
 * Interface SubInvoiceInterface
 * @package SM\Sales\Api\Data\Invoice
 */
interface SubInvoiceInterface
{
    const REFERENCE_ORDER_ID = "reference_order_id";
    const SUBTOTAL = "subtotal";
    const SHIPPING_AMOUNT = "shipping_amount";
    const GRAND_TOTAL = "grand_total";
    const ADDITIONAL_FEE = "additional_fee";
    const DISCOUNT_AMOUNT = "discount_amount";
    const ITEM_AMOUNT = "item_amount";
    const DELIVERY_ADDRESS = "delivery_address";
    const STORE_INFO = "store_info";
    const ITEMS = "items";
    const SHIPPING_METHOD = "shipping_method";

    /**
     * @return string
     */
    public function getReferenceOrderId();

    /**
     * @return int
     */
    public function getItemAmount();

    /**
     * @return int
     */
    public function getSubtotal();

    /**
     * @return int
     */
    public function getShippingAmount();

    /**
     * @return int
     */
    public function getAdditionalFee();

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
     * @return \SM\Sales\Api\Data\DeliveryAddressDataInterface
     */
    public function getDeliveryAddress();

    /**
     * @param \SM\Sales\Api\Data\DeliveryAddressDataInterface $value
     * @return $this
     */
    public function setDeliveryAddress($value);

    /**
     * @return \SM\MobileApi\Model\Data\Catalog\Product\StoreInfo
     */
    public function getStoreInfo();

    /**
     * @param \SM\MobileApi\Model\Data\Catalog\Product\StoreInfo $value
     * @return $this
     */
    public function setStoreInfo($value);

    /**
     * @return \SM\Sales\Api\Data\Invoice\SubInvoiceItemInterface[]
     */
    public function getItems();

    /**
     * @param string $value
     * @return $this
     */
    public function setReferenceOrderId($value);

    /**
     * @param int $value
     * @return $this
     */
    public function setItemAmount($value);

    /**
     * @param int $value
     * @return $this
     */
    public function setSubtotal($value);

    /**
     * @param int $value
     * @return $this
     */
    public function setShippingAmount($value);

    /**
     * @param int $value
     * @return $this
     */
    public function setAdditionalFee($value);

    /**
     * @param int $value
     * @return $this
     */
    public function setGrandTotal($value);

    /**
     * @param \SM\Sales\Api\Data\Invoice\SubInvoiceItemInterface[] $value
     * @return $this
     */
    public function setItems($value);

    /**
     * @param string $shippingMethod
     * @return string
     */
    public function setShippingMethod($shippingMethod);

    /**
     * @return string
     */
    public function getShippingMethod();
}
