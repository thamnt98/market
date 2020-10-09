<?php

namespace SM\Checkout\Api\Data\Checkout;

use SM\Checkout\Api\Data\Checkout\Estimate\AdditionalInfoInterface;
use SM\Checkout\Model\Api\CheckoutData;

interface CheckoutDataInterface
{
    const SHIPPING_ADDRESS = 'shipping_address';
    const SUPPORT_SHIPPING = 'support_shipping';
    const ITEMS = 'items';
    const ITEMS_MESSAGE = 'items_message';
    const PREVIEW_ORDER = 'preview_order';
    const CHECKOUT_TOTAL = 'checkout_total';
    const IS_STORE_FULFILL = 'is_store_fulfill';
    const IS_SPLIT_ORDER = 'is_split_order';
    const ADDITIONAL_INFO = 'additional_info';
    const IS_ADDRESS_COMPLETE = 'is_address_complete';
    const IS_ERROR_CHECKOUT = 'is_error_checkout';
    const PAYMENT_METHODS = 'payment_methods';
    const VOUCHER = 'voucher';
    const CURRENCY_SYMBOL = 'currency_symbol';
    const DIGITAL_CHECKOUT = 'digital_checkout';
    const DIGITAL_DETAIL = 'digital_detail';
    const USE_STORE_PICK_UP = 'use_store_pick_up';
    const BASKET_ID = 'basket_id';
    const BASKET_QTY = 'basket_qty';
    const BASKET_VALUE = 'basket_value';
    const TOPIC_ID = "topic_id";

    /**
     * @param \Magento\Customer\Api\Data\AddressInterface[] $data
     * @return $this
     */
    public function setShippingAddress($data);

    /**
     * @return \Magento\Customer\Api\Data\AddressInterface[]
     */
    public function getShippingAddress();

    /**
     * @param \SM\Checkout\Api\Data\Checkout\SupportShippingInterface $data
     * @return $this
     */
    public function setSupportShipping($data);

    /**
     * @return \SM\Checkout\Api\Data\Checkout\SupportShippingInterface
     */
    public function getSupportShipping();

    /**
     * @param \SM\Checkout\Api\Data\Checkout\QuoteItems\ItemInterface[] $data
     * @return $this
     */
    public function setItems($data);

    /**
     * @return \SM\Checkout\Api\Data\Checkout\QuoteItems\ItemInterface[]
     */
    public function getItems();

    /**
     * @param string $message
     * @return $this
     */
    public function setItemsMessage($message);

    /**
     * @return string
     */
    public function getItemsMessage();

    /**
     * @param \SM\Checkout\Api\Data\Checkout\Estimate\AdditionalInfoInterface $data
     * @return $this
     */
    public function setAdditionalInfo($data);

    /**
     * @return \SM\Checkout\Api\Data\Checkout\Estimate\AdditionalInfoInterface
     */
    public function getAdditionalInfo();

    /**
     * @param \SM\Checkout\Api\Data\Checkout\Estimate\PreviewOrderInterface[] $data
     * @return $this
     */
    public function setPreviewOrder($data);

    /**
     * @return \SM\Checkout\Api\Data\Checkout\Estimate\PreviewOrderInterface[]
     */
    public function getPreviewOrder();

    /**
     * @param \Magento\Quote\Api\Data\TotalsInterface $data
     * @return $this
     */
    public function setCheckoutTotal($data);

    /**
     * @return \Magento\Quote\Api\Data\TotalsInterface
     */
    public function getCheckoutTotal();

    /**
     * @param bool $isStoreFulFill
     * @return $this
     */
    public function setIsStoreFulFill($isStoreFulFill);

    /**
     * @return bool
     */
    public function getIsStoreFulFill();

    /**
     * @param bool $isSplitOrder
     * @return $this
     */
    public function setIsSplitOrder($isSplitOrder);

    /**
     * @return bool
     */
    public function getIsSplitOrder();

    /**
     * @param bool $isAddressComplete
     * @return $this
     */
    public function setIsAddressComplete($isAddressComplete);

    /**
     * @return bool
     */
    public function getIsAddressComplete();

    /**
     * @param bool $isErrorCheckout
     * @return $this
     */
    public function setIsErrorCheckout($isErrorCheckout);

    /**
     * @return bool
     */
    public function getIsErrorCheckout();

    /**
     * @return \SM\Checkout\Api\Data\Checkout\PaymentMethods\PaymentMethodInterface[]
     */
    public function getPaymentMethods();

    /**
     * @param \SM\Checkout\Api\Data\Checkout\PaymentMethods\PaymentMethodInterface[] $data
     * @return $this
     */
    public function setPaymentMethods($data);

    /**
     * @return \SM\Checkout\Api\Data\Checkout\Voucher\VoucherInterface[]
     */
    public function getVoucher();

    /**
     * @param \SM\Checkout\Api\Data\Checkout\Voucher\VoucherInterface[] $voucher
     * @return $this
     */
    public function setVoucher($voucher);

    /**
     * @return string
     */
    public function getCurrencySymbol();

    /**
     * @param string $currencySymbol
     * @return $this
     */
    public function setCurrencySymbol($currencySymbol);

    /**
     * @return bool
     */
    public function getDigitalCheckout();

    /**
     * @param bool $digitalCheckout
     * @return $this
     */
    public function setDigitalCheckout($digitalCheckout);

    /**
     * @return \SM\Checkout\Api\Data\CheckoutWeb\DigitalInterface[]
     */
    public function getDigitalDetail();

    /**
     * @param \SM\Checkout\Api\Data\CheckoutWeb\DigitalInterface[] $digitalCheckout
     * @return $this
     */
    public function setDigitalDetail($digitalCheckout);

    /**
     * @return bool
     */
    public function getUseStorePickUp();

    /**
     * @param bool $useStorePickUp
     * @return $this
     */
    public function setUseStorePickUp($useStorePickUp);



    /**
     * @return int
     */
    public function getBasketId();

    /**
     * @param int $value
     * @return $this
     */
    public function setBasketId($value);

    /**
     * @return float
     */
    public function getBasketValue();

    /**
     * @param float $value
     * @return $this
     */
    public function setBasketValue($value);

    /**
     * @return int
     */
    public function getBasketQty();

    /**
     * @param int $value
     * @return $this
     */
    public function setBasketQty($value);

    /**
     * @return int
     */
    public function getTopicId();

    /**
     * @param int $value
     * @return $this
     */
    public function setTopicId($value);
}
