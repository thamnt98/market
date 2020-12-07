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
    protected $items;

    /**
     * @param \Magento\Checkout\Block\Cart $subject
     * @param callable                     $proceed
     *
     * @return \Magento\Quote\Model\Quote\Item[]
     */
    public function aroundGetItems(\Magento\Checkout\Block\Cart $subject, callable $proceed)
    {
        if (!$this->items) {
            $this->initItems($subject);
        }

        return $this->items;
    }

    /**
     * @param \Magento\Checkout\Block\Cart $subject
     * @param callable                     $proceed
     *
     * @return int
     */
    public function aroundGetItemsCount(\Magento\Checkout\Block\Cart $subject, callable $proceed)
    {
        if (!$this->items) {
            $this->initItems($subject);
        }

        return count($this->items);
    }

    protected function initItems(\Magento\Checkout\Block\Cart $cart)
    {
        if ($cart->getCustomItems()) {
            $this->items = $cart->getCustomItems() ?: [];
        } else {
            $this->items = [];
            /** @var \Magento\Quote\Model\Quote\Item $item */
            foreach ($cart->getQuote()->getItemsCollection() as $key => $item) {
                if ($item->getParentItemId() ||
                    $item->isDeleted() ||
                    ($cart->getQuote()->getIsVirtual() && !$item->getIsVirtual()) ||
                    (!$cart->getQuote()->getIsVirtual() && $item->getIsVirtual())
                ) {
                    continue;
                }

                $this->items[] = $item;
            }
        }
    }

}
