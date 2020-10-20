<?php

namespace SM\Checkout\Preference\Quote\Model\Quote;

use Magento\Framework\App\ObjectManager;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class Address
 * @package SM\Checkout\Preference\Quote\Model\Quote
 */
class Address extends \Magento\Quote\Model\Quote\Address
{
    /**
     * Request shipping rates for entire address or specified address item
     *
     * Returns true if current selected shipping method code corresponds to one of the found rates
     *
     * @param \Magento\Quote\Model\Quote\Item\AbstractItem $item
     * @return bool
     */
    public function requestShippingRates(\Magento\Quote\Model\Quote\Item\AbstractItem $item = null)
    {
        $storeManager = ObjectManager::getInstance()->get(StoreManagerInterface::class);
        /** @var $request \Magento\Quote\Model\Quote\Address\RateRequest */
        $request = $this->_rateRequestFactory->create();
        $request->setAllItems($item ? [$item] : $this->getAllItems());
        $request->setDestCountryId($this->getCountryId());
        $request->setDestRegionId($this->getRegionId());
        $request->setDestRegionCode($this->getRegionCode());
        $request->setDestStreet($this->getStreetFull());
        $request->setDestCity($this->getCity());
        $request->setDestPostcode($this->getPostcode());
        $request->setPackageValue($item ? $item->getBaseRowTotal() : $this->getBaseSubtotal());
        $packageWithDiscount = $item ? $item->getBaseRowTotal() -
            $item->getBaseDiscountAmount() : $this->getBaseSubtotalWithDiscount();
        $request->setPackageValueWithDiscount($packageWithDiscount);
        $request->setPackageWeight($item ? $item->getRowWeight() : $this->getWeight());
        $request->setPackageQty($item ? $item->getQty() : $this->getItemQty());

        /**
         * Need for shipping methods that use insurance based on price of physical products
         */
        $packagePhysicalValue = $item ? $item->getBaseRowTotal() : $this->getBaseSubtotal() -
            $this->getBaseVirtualAmount();
        $request->setPackagePhysicalValue($packagePhysicalValue);

        $request->setFreeMethodWeight($item ? 0 : $this->getFreeMethodWeight());

        /**
         * Store and website identifiers specified from StoreManager
         */
        if ($this->getQuote()->getStoreId()) {
            $storeId = $this->getQuote()->getStoreId();
            $request->setStoreId($storeId);
            $request->setWebsiteId($storeManager->getStore($storeId)->getWebsiteId());
        } else {
            $request->setStoreId($storeManager->getStore()->getId());
            $request->setWebsiteId($storeManager->getWebsite()->getId());
        }
        $request->setFreeShipping($this->getFreeShipping());
        /**
         * Currencies need to convert in free shipping
         */
        $request->setBaseCurrency($storeManager->getStore()->getBaseCurrency());
        $request->setPackageCurrency($storeManager->getStore()->getCurrentCurrency());
        $request->setLimitCarrier($this->getLimitCarrier());
        $baseSubtotalInclTax = $this->getBaseSubtotalTotalInclTax();
        $request->setBaseSubtotalInclTax($baseSubtotalInclTax);

        $request->setCustomerAddressId($this->getData('customer_address_id'));
        $request->setPreShippingMethod($this->getData('pre_shipping_method'));
        $request->setQuote($this->getQuote());

        $result = $this->_rateCollector->create()->collectRates($request)->getResult();

        $found = false;
        if ($result) {
            $shippingRates = $result->getAllRates();

            foreach ($shippingRates as $shippingRate) {
                $rate = $this->_addressRateFactory->create()->importShippingRate($shippingRate);
                if (!$item) {
                    $this->addShippingRate($rate);
                }

                if ($this->getShippingMethod() == $rate->getCode()) {
                    if ($item) {
                        $item->setBaseShippingAmount($rate->getPrice());
                    } else {

                        /** @var \Magento\Store\Api\Data\StoreInterface */
                        $store = $storeManager->getStore();
                        $amountPrice = $store->getBaseCurrency()
                            ->convert($rate->getPrice(), $store->getCurrentCurrencyCode());
                        $this->setBaseShippingAmount($rate->getPrice());
                        $this->setShippingAmount($amountPrice);
                    }

                    $found = true;
                }
            }
        }

        return $found;
    }

    /**
     * @override
     *
     * Get all available address items
     *
     * @return \Magento\Quote\Model\Quote\Address\Item[]
     */
    public function getAllItems()
    {
        $key = 'cached_items_all';
        if (!$this->hasData($key)) {
            $quoteItems = $this->getQuote()->getItemsCollection();
            $addressItems = $this->getItemsCollection();

            $items = [];
            if ($this->getQuote()->getIsMultiShipping() && $addressItems->count() > 0) {
                foreach ($addressItems as $aItem) {
                    if ($aItem->isDeleted()) {
                        continue;
                    }

                    if (!$aItem->getQuoteItemImported()) {
                        $qItem = $this->getQuote()->getItemById($aItem->getQuoteItemId());
                        if ($qItem) {
                            $aItem->importQuoteItem($qItem);
                        }
                    }

                    if ($aItem->getQuoteItem() && $this->quoteItemActive($aItem->getQuoteItem())) {
                        $items[] = $aItem;
                    } else {
                        continue;
                    }
                }
            } else {
                /*
                 * For virtual quote we assign items only to billing address, otherwise - only to shipping address
                 */
                $addressType = $this->getAddressType();
                $canAddItems = $this->getQuote()->isVirtual()
                    ? $addressType == self::TYPE_BILLING
                    : $addressType == self::TYPE_SHIPPING;

                if ($canAddItems) {
                    foreach ($quoteItems as $qItem) {
                        if (!$this->quoteItemActive($qItem)) {
                            continue;
                        }

                        $items[] = $qItem;
                    }
                }
            }

            // Cache calculated lists
            if ($this->getId()) {
                $this->setData('cached_items_all', $items);
            } else {
                return $items;
            }
        }

        $items = $this->getData($key);

        return $items;
    }

    /**
     * @param \Magento\Quote\Model\Quote\Item $quoteItem
     *
     * @return bool
     */
    public function quoteItemActive($quoteItem)
    {
        if ($quoteItem->getQuote()->getIsVirtual()) {
            if ($quoteItem->getIsVirtual() && !$quoteItem->isDeleted()) {
                return true;
            }
        } elseif (!$quoteItem->getIsVirtual() && !$quoteItem->isDeleted() && $quoteItem->getIsActive()) {
            return true;
        }

        return false;
    }
}
