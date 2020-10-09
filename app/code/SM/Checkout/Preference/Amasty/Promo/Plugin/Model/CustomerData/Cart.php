<?php

namespace SM\Checkout\Preference\Amasty\Promo\Plugin\Model\CustomerData;

class Cart extends \Amasty\Promo\Plugin\Model\CustomerData\Cart
{
    /**
     * Return customer quote items
     *
     * @param \Magento\Checkout\CustomerData\Cart $cart
     *
     * @return \Magento\Quote\Model\Quote\Item[]
     */
    protected function getAllQuoteItems(\Magento\Checkout\CustomerData\Cart $cart)
    {
        if ($cart->getCustomQuote()) {
            return $cart->getCustomQuote()->getAllVisibleItems();
        }

        return $this->getAllVisibleItems();
    }

    /**
     * Get array of all items what can be display directly
     *
     * @return \Magento\Quote\Model\Quote\Item[]
     */
    protected function getAllVisibleItems()
    {
        $items = [];
        foreach ($this->getQuote()->getItemsCollection() as $item) {
            if (!$item->isDeleted() && !$item->getParentItemId() && !$item->getParentItem()) {
                if ($item->getProduct()->getTypeId() == 'virtual') {
                    continue;
                }
                $items[] = $item;
            }
        }
        return $items;
    }
}
