<?php

namespace SM\Checkout\Plugin\Checkout\Block;

/**
 * Class CartPlugin
 * @package SM\Checkout\Plugin\Checkout\Block
 */
class CartPlugin
{
    /**
     * @param \Magento\Checkout\Block\Cart $subject
     * @param $result
     * @return mixed
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterGetItems(
        \Magento\Checkout\Block\Cart $subject,
        $result
    ) {
        return $subject->getQuote()->getAllVisibleItemsInCart();
    }
}
