<?php

namespace SM\Checkout\Api;

/**
 * Interface MultiShippingMobileInterface
 * @package SM\Checkout\Api
 */
interface MultiShippingMobileInterface
{
    /**
     * @param int $customerId
     * @param int $cartId
     * @return \SM\Checkout\Api\Data\Checkout\CheckoutDataInterface
     */
    public function initCheckout($customerId, $cartId);

    /**
     * @param int $customerId
     * @param int $cartId
     * @return \SM\Checkout\Api\Data\Checkout\SearchStoreResponseInterface
     */
    public function getStorePickUpSourceFullFill($customerId, $cartId);

    /**
     * @param int $cartId
     * @param float $lat
     * @param float $lng
     * @param mixed $storePickupItems
     * @param string $currentStoreCode
     * @return \SM\Checkout\Api\Data\Checkout\SearchStoreResponseInterface
     */
    public function searchStore($cartId, $lat, $lng, $storePickupItems, $currentStoreCode);

    /**
     * @param \Magento\Customer\Api\Data\AddressInterface[] $shippingAddress
     * @param \SM\Checkout\Api\Data\Checkout\QuoteItems\ItemInterface[] $items
     * @param \SM\Checkout\Api\Data\Checkout\Estimate\AdditionalInfoInterface $additionalInfo
     * @param int $customerId
     * @param int $cartId
     * @return \SM\Checkout\Api\Data\Checkout\CheckoutDataInterface
     */
    public function saveShippingItems($shippingAddress, $items, $additionalInfo, $customerId, $cartId);

    /**
     * @param \Magento\Customer\Api\Data\AddressInterface[] $shippingAddress
     * @param \SM\Checkout\Api\Data\Checkout\QuoteItems\ItemInterface[] $items
     * @param \SM\Checkout\Api\Data\Checkout\Estimate\AdditionalInfoInterface $additionalInfo
     * @param bool $isStoreFulFill
     * @param bool $isSplitOrder
     * @param bool $isAddressComplete
     * @param bool $isErrorCheckout
     * @param \SM\Checkout\Api\Data\Checkout\Voucher\VoucherInterface[] $voucher
     * @param bool $showEachItems
     * @param bool $disablePickUp
     * @param int $customerId
     * @param int $cartId
     * @return \SM\Checkout\Api\Data\Checkout\CheckoutDataInterface
     */
    public function previewOrder($shippingAddress, $items, $additionalInfo, $isStoreFulFill, $isSplitOrder, $isAddressComplete, $isErrorCheckout, $voucher, $showEachItems, $disablePickUp, $customerId, $cartId);

    /**
     * @return \SM\Checkout\Api\Data\Checkout\ConfigInterface
     */
    public function getDateTimeConfig();

    /**
     * @param \Magento\Customer\Api\Data\AddressInterface[] $shippingAddress
     * @param \SM\Checkout\Api\Data\Checkout\QuoteItems\ItemInterface[] $items
     * @param \SM\Checkout\Api\Data\Checkout\Estimate\AdditionalInfoInterface $additionalInfo
     * @param bool $isStoreFulFill
     * @param bool $isSplitOrder
     * @param bool $isAddressComplete
     * @param bool $isErrorCheckout
     * @param \SM\Checkout\Api\Data\Checkout\Voucher\VoucherInterface[] $voucher
     * @param string $currencySymbol
     * @param bool $digitalCheckout
     * @param \SM\Checkout\Api\Data\CheckoutWeb\DigitalInterface[] $digitalDetail
     * @param bool $showEachItems
     * @param bool $disablePickUp
     * @param int $customerId
     * @param int $cartId
     * @return \SM\Checkout\Api\Data\Checkout\CheckoutDataInterface
     */
    public function applyVoucher($shippingAddress, $items, $additionalInfo, $isStoreFulFill, $isSplitOrder, $isAddressComplete, $isErrorCheckout, $voucher, $currencySymbol, $digitalCheckout, $digitalDetail, $showEachItems, $disablePickUp, $customerId, $cartId);

    /**
     * @param string $paymentMethod
     * @param int $term
     * @param int $customerId
     * @param int $cartId
     * @return \SM\Checkout\Api\Data\Checkout\CheckoutDataInterface
     */
    public function saveMobilePayment($paymentMethod, $term = null, $customerId, $cartId);
}
