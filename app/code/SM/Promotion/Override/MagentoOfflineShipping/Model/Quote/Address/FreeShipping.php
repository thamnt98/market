<?php
/**
 * SMCommerce
 *
 * @category  SM
 * @package   SM_Promotion
 *
 * Date: July, 28 2020
 * Time: 2:30 PM
 * User: VooThanh DEV
 *
 * @author    SMCommerce Core Team <connect@smartosc.com>
 * @copyright Copyright SMCommerce (http://smartosc.com/)
 */

namespace SM\Promotion\Override\MagentoOfflineShipping\Model\Quote\Address;

class FreeShipping extends \Magento\OfflineShipping\Model\Quote\Address\FreeShipping
{
    /**
     * @param \Magento\Quote\Model\Quote                  $quote
     * @param \Magento\Quote\Api\Data\CartItemInterface[] $items
     *
     * @return bool
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function isFreeShipping(\Magento\Quote\Model\Quote $quote, $items)
    {
        /** @var \Magento\Quote\Model\Quote\Item\AbstractItem[] $items */
        if (!count($items)) {
            return false;
        }

        $result = false;
        $addressFreeShipping = true;
        $store = $this->storeManager->getStore($quote->getStoreId());
        $this->calculator->init($store->getWebsiteId(), $quote->getCustomerGroupId(), $quote->getCouponCode());

        foreach ($items as $item) {
            if ($item->getNoDiscount()) {
                $addressFreeShipping = false;
                $item->setFreeShipping(false);
                continue;
            }

            /** Child item discount we calculate for parent */
            if ($item->getParentItemId()) {
                continue;
            }

            $this->calculator->processFreeShipping($item);
            // at least one item matches to the rule and the rule mode is not a strict
            if ((bool)$item->getAddress()->getFreeShipping()) {
                $result = true;
                break;
            }

            $itemFreeShipping = (bool)$item->getFreeShipping();
            $addressFreeShipping = $addressFreeShipping && $itemFreeShipping;
            $result = $addressFreeShipping;
        }

        $this->applyToItems($items, $result);

        return $result;
    }

    /**
     * Sets free shipping availability to the quote items.
     *
     * @param array $items
     * @param bool $freeShipping
     */
    protected function applyToItems(array $items, bool $freeShipping)
    {
        /** @var \Magento\Quote\Model\Quote\Item\AbstractItem $item */
        foreach ($items as $item) {
            $item->getAddress()
                ->setFreeShipping((int)$freeShipping);
            $this->applyToChildren($item, $freeShipping);
        }
    }
}
