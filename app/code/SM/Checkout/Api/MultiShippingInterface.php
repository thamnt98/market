<?php

namespace SM\Checkout\Api;

/**
 * Interface MultiShippingInterface
 * @package SM\Checkout\Api
 */
interface MultiShippingInterface
{
    /**
     * @param \SM\Checkout\Api\Data\CheckoutWeb\EstimateItemInterface[] $items
     * @param \SM\Checkout\Api\Data\CheckoutWeb\AdditionalInfoInterface $additionalInfo
     * @param string $type
     * @param string $address
     * @return \SM\Checkout\Api\Data\CheckoutWeb\EstimateShippingInterface
     */
    public function saveShippingItems($items, $additionalInfo, $type, $address);

    /**
     * @param int $customerId
     * @return string
     */
    public function placeOrder($customerId);

    /**
     * @param int $cartId
     * @param string $paymentMethod
     * @param integer $term
     * @throws \Magento\Framework\Webapi\Exception
     * @return boolean
     */
    public function saveMobilePayment($cartId, $paymentMethod,$term=null);

    /**
     * @param int    $customerId
     * @param string $paymentMethod
     * @param int    $serviceFee
     *
     * @return string
     */
    public function savePayment($customerId, $paymentMethod, $serviceFee = 0);

    /**
     * @param \SM\Checkout\Api\Data\CheckoutWeb\EstimateItemInterface[] $items
     * @param \SM\Checkout\Api\Data\CheckoutWeb\PreviewOrder\Request\StoreDateTimeInterface $storeDateTime
     * @param \SM\Checkout\Api\Data\CheckoutWeb\PreviewOrder\Request\DeliveryDateTimeInterface[] $deliveryDateTime
     * @param bool $isSplitOrder
     * @return \SM\Checkout\Api\Data\CheckoutWeb\PreviewOrderInterface
     */
    public function previewOrder($items, $storeDateTime, $deliveryDateTime, $isSplitOrder);

    /**
     * respond orders ids "10001,10001-1,10001-2,10001-3,10001-4....";
     * @param int $cartId
     * @return \SM\Checkout\Api\Data\Checkout\PlaceOrderInterface
     */
    public function placeOrderMobile($cartId);

    /**
     * @param float $lat
     * @param float $lng
     * @param mixed $storePickupItems
     * @param string $currentStoreCode
     * @return \SM\Checkout\Api\Data\CheckoutWeb\SearchStoreInterface
     */
    public function searchStore($lat, $lng, $storePickupItems, $currentStoreCode);

    /**
     * @return \SM\Checkout\Api\Data\CheckoutWeb\PreviewOrderInterface
     */
    public function resetPaymentFail();

    /**
     * @return \SM\Checkout\Api\Data\CheckoutWeb\DigitalInterface[]
     */
    public function digitalDetail();
}
