<?php
/**
 * Created by PhpStorm.
 * User: thelightsp
 * Date: 3/25/20
 * Time: 9:47 AM
 */

namespace SM\Checkout\Plugin\Magento\Checkout\Block;

class Cart
{
    /**
     * @param \Magento\Checkout\Block\Cart|\Magento\Checkout\Block\Cart\Grid $subject
     * @return mixed
     */
    public function aroundGetItems($subject)
    {
        if ($subject->getCustomItems()) {
            return $subject->getCustomItems();
        }
        return $this->getActiveItems($subject);
    }

    /**
     * @param \Magento\Checkout\Block\Cart|\Magento\Checkout\Block\Cart\Grid $subject
     * @return array
     */
    public function getActiveItems($subject)
    {
        $items = [];
        /** @var \Magento\Quote\Model\Quote\Item $item */
        foreach ($subject->getQuote()->getItemsCollection() as $item) {
            if ($item->getParentItemId()) {
                continue;
            }

            if ($subject->getQuote()->getIsVirtual()) {
                if ($item->getIsVirtual() && !$item->isDeleted()) {
                    $items[] = $item;
                }
            } elseif (!$item->getIsVirtual() && !$item->getParentItemId() && !$item->isDeleted()) {
                $items[] = $item;
            }
        }

        return $items;
    }
}
