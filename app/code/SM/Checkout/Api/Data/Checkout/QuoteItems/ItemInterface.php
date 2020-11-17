<?php

namespace SM\Checkout\Api\Data\Checkout\QuoteItems;

interface ItemInterface
{
    /**
     * @param int $itemId
     * @return $this
     */
    public function setItemId($itemId);

    /**
     * @return int
     */
    public function getItemId();

    /**
     * @return string
     */
    public function getSku();

    /**
     * @param string $sku
     * @return $this
     */
    public function setSku($sku);

    /**
     * @param string $name
     * @return $this
     */
    public function setName($name);

    /**
     * @return string
     */
    public function getName();

    /**
     * @param string $url
     * @return $this
     */
    public function setUrl($url);

    /**
     * @return string
     */
    public function getUrl();

    /**
     * @param float $weight
     * @return $this
     */
    public function setWeight($weight);

    /**
     * @return float
     */
    public function getWeight();

    /**
     * @param string $weightUnit
     * @return $this
     */
    public function setWeightUnit($weightUnit);

    /**
     * @return string
     */
    public function getWeightUnit();

    /**
     * @param int $qty
     * @return $this
     */
    public function setQty($qty);

    /**
     * @return int
     */
    public function getQty();

    /**
     * @param string $thumbnail
     * @return $this
     */
    public function setThumbnail($thumbnail);

    /**
     * @return string
     */
    public function getThumbnail();

    /**
     * @param float $rowTotal
     * @return $this
     */
    public function setRowTotal($rowTotal);

    /**
     * @return float
     */
    public function getRowTotal();

    /**
     * @param float $baseRowTotalByLocation
     * @return $this
     */
    public function setBaseRowTotalByLocation($baseRowTotalByLocation);

    /**
     * @return float
     */
    public function getBaseRowTotalByLocation();

    /**
     * @param string $symbol
     * @return $this
     */
    public function setCurrencySymbol($symbol);

    /**
     * @return string
     */
    public function getCurrencySymbol();

    /**
     * @param string $productType
     * @return $this
     */
    public function setProductType($productType);

    /**
     * @return string
     */
    public function getProductType();

    /**
     * @param \SM\Checkout\Api\Data\Checkout\QuoteItems\ProductOptions\ProductOptionsInterface[] $productOption
     * @return $this
     */
    public function setProductOption($productOption);

    /**
     * @return \SM\Checkout\Api\Data\Checkout\QuoteItems\ProductOptions\ProductOptionsInterface[]
     */
    public function getProductOption();

    /**
     * @param \SM\Checkout\Api\Data\Checkout\ShippingMethodInterface[] $data
     * @return $this
     */
    public function setShippingMethod($data);

    /**
     * @return \SM\Checkout\Api\Data\Checkout\ShippingMethodInterface[]
     */
    public function getShippingMethod();

    /**
     * @param int $addressId
     * @return $this
     */
    public function setShippingAddressId($addressId);

    /**
     * @return int
     */
    public function getShippingAddressId();

    /**
     * @param string $methodCode
     * @return $this
     */
    public function setShippingMethodSelected($methodCode);

    /**
     * @return string
     */
    public function getShippingMethodSelected();

    /**
     * @param \SM\Checkout\Api\Data\Checkout\Estimate\ItemAdditionalInfo\AdditionalInfoInterface $additionalInfo
     * @return $this
     */
    public function setAdditionalInfo($additionalInfo);

    /**
     * @return \SM\Checkout\Api\Data\Checkout\Estimate\ItemAdditionalInfo\AdditionalInfoInterface
     */
    public function getAdditionalInfo();

    /**
     * @param bool $disable
     * @return $this
     */
    public function setDisable($disable);

    /**
     * @return bool
     */
    public function getDisable();

    /**
     * @param string $message
     * @return $this
     */
    public function setMessage($message);

    /**
     * @return string
     */
    public function getMessage();

    /**
     * @return \SM\MobileApi\Api\Data\GTM\GTMCartInterface
     */
    public function getGtmData();

    /**
     * @param \SM\MobileApi\Api\Data\GTM\GTMCartInterface $value
     * @return $this
     */
    public function setGtmData($value);

    /**
     * @param \SM\FreshProductApi\Api\Data\FreshProductInterface $value
     * @return $this
     */
    public function setFreshProduct($value);

    /**
     * @return \SM\FreshProductApi\Api\Data\FreshProductInterface
     */
    public function getFreshProduct();

    /**
     * @param bool $value
     * @return $this
     */
    public function setDisableStorePickUp($value);

    /**
     * @return bool
     */
    public function getDisableStorePickUp();
}
