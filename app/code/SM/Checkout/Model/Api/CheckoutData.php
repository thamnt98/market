<?php

namespace SM\Checkout\Model\Api;

use Magento\Framework\Api\AbstractExtensibleObject;
use SM\Checkout\Api\Data\Checkout\CheckoutDataInterface;

class CheckoutData extends AbstractExtensibleObject implements CheckoutDataInterface
{
    /**
     * {@inheritdoc}
     */
    public function setShippingAddress($data)
    {
        return $this->setData(self::SHIPPING_ADDRESS, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function getShippingAddress()
    {
        return $this->_get(self::SHIPPING_ADDRESS);
    }

    /**
     * {@inheritdoc}
     */
    public function setSupportShipping($data)
    {
        return $this->setData(self::SUPPORT_SHIPPING, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function getSupportShipping()
    {
        return $this->_get(self::SUPPORT_SHIPPING);
    }

    /**
     * {@inheritdoc}
     */
    public function setItems($data)
    {
        return $this->setData(self::ITEMS, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function getItems()
    {
        return $this->_get(self::ITEMS);
    }

    /**
     * {@inheritdoc}
     */
    public function setItemsMessage($message)
    {
        return $this->setData(self::ITEMS_MESSAGE, $message);
    }

    /**
     * {@inheritdoc}
     */
    public function getItemsMessage()
    {
        return $this->_get(self::ITEMS_MESSAGE);
    }

    /**
     * {@inheritdoc}
     */
    public function setAdditionalInfo($data)
    {
        return $this->setData(self::ADDITIONAL_INFO, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function getAdditionalInfo()
    {
        return $this->_get(self::ADDITIONAL_INFO);
    }

    /**
     * {@inheritdoc}
     */
    public function setPreviewOrder($data)
    {
        return $this->setData(self::PREVIEW_ORDER, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function getPreviewOrder()
    {
        return $this->_get(self::PREVIEW_ORDER);
    }

    /**
     * {@inheritdoc}
     */
    public function setCheckoutTotal($data)
    {
        return $this->setData(self::CHECKOUT_TOTAL, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function getCheckoutTotal()
    {
        return $this->_get(self::CHECKOUT_TOTAL);
    }

    /**
     * {@inheritdoc}
     */
    public function setIsStoreFulFill($isStoreFulFill)
    {
        return $this->setData(self::IS_STORE_FULFILL, $isStoreFulFill);
    }

    /**
     * {@inheritdoc}
     */
    public function getIsStoreFulFill()
    {
        return $this->_get(self::IS_STORE_FULFILL);
    }

    /**
     * {@inheritdoc}
     */
    public function setIsSplitOrder($isSplitOrder)
    {
        return $this->setData(self::IS_SPLIT_ORDER, $isSplitOrder);
    }

    /**
     * {@inheritdoc}
     */
    public function getIsSplitOrder()
    {
        return $this->_get(self::IS_SPLIT_ORDER);
    }

    /**
     * {@inheritdoc}
     */
    public function setIsAddressComplete($isAddressComplete)
    {
        return $this->setData(self::IS_ADDRESS_COMPLETE, $isAddressComplete);
    }

    /**
     * {@inheritdoc}
     */
    public function getIsAddressComplete()
    {
        return $this->_get(self::IS_ADDRESS_COMPLETE);
    }

    /**
     * {@inheritdoc}
     */
    public function setIsErrorCheckout($isErrorCheckout)
    {
        return $this->setData(self::IS_ERROR_CHECKOUT, $isErrorCheckout);
    }

    /**
     * {@inheritdoc}
     */
    public function getIsErrorCheckout()
    {
        return $this->_get(self::IS_ERROR_CHECKOUT);
    }

    /**
     * @inheritDoc
     */
    public function getPaymentMethods()
    {
        return $this->_get(self::PAYMENT_METHODS);
    }

    /**
     * @inheritDoc
     */
    public function setPaymentMethods($data)
    {
        $this->setData(self::PAYMENT_METHODS, $data);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getVoucher()
    {
        return $this->_get(self::VOUCHER);
    }

    /**
     * @inheritDoc
     */
    public function setVoucher($voucher)
    {
        return $this->setData(self::VOUCHER, $voucher);
    }

    /**
     * @inheritDoc
     */
    public function getCurrencySymbol()
    {
        return $this->_get(self::CURRENCY_SYMBOL);
    }

    /**
     * @inheritDoc
     */
    public function setCurrencySymbol($currencySymbol)
    {
        return $this->setData(self::CURRENCY_SYMBOL, $currencySymbol);
    }

    /**
     * @inheritDoc
     */
    public function getDigitalCheckout()
    {
        return $this->_get(self::DIGITAL_CHECKOUT);
    }

    /**
     * @inheritDoc
     */
    public function setDigitalCheckout($digitalCheckout)
    {
        return $this->setData(self::DIGITAL_CHECKOUT, $digitalCheckout);
    }

    /**
     * @inheritDoc
     */
    public function getDigitalDetail()
    {
        return $this->_get(self::DIGITAL_DETAIL);
    }

    /**
     * @inheritDoc
     */
    public function setDigitalDetail($digitalDetail)
    {
        return $this->setData(self::DIGITAL_DETAIL, $digitalDetail);
    }

    /**
     * @inheritDoc
     */
    public function getUseStorePickUp()
    {
        return $this->_get(self::USE_STORE_PICK_UP);
    }

    /**
     * @inheritDoc
     */
    public function setUseStorePickUp($useStorePickUp)
    {
        return $this->setData(self::USE_STORE_PICK_UP, $useStorePickUp);
    }

    /**
     * @param string $key
     * @param null $value
     * @return $this
     */
    public function setData($key, $value = null)
    {
        if ($key === (array)$key) {
            $this->_data = $key;
        } else {
            $this->_data[$key] = $value;
        }
        return $this;
    }

    public function getBasketId()
    {
        return $this->_get(self::BASKET_ID);
    }

    public function setBasketId($value)
    {
        return $this->setData(self::BASKET_ID, $value);
    }

    public function getBasketValue()
    {
        return $this->_get(self::BASKET_VALUE);
    }

    public function setBasketValue($value)
    {
        return $this->setData(self::BASKET_VALUE, $value);
    }

    public function getBasketQty()
    {
        return $this->_get(self::BASKET_QTY);
    }

    public function setBasketQty($value)
    {
        return $this->setData(self::BASKET_QTY, $value);
    }

    /**
     * @inheritDoc
     */
    public function getTopicId()
    {
        return $this->_get(self::TOPIC_ID);
    }

    /**
     * @inheritDoc
     */
    public function setTopicId($value)
    {
        return $this->setData(self::TOPIC_ID, $value);
    }
}
