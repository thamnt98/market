<?php


namespace SM\ShoppingList\Plugin;

/**
 * Class Link
 * @package SM\ShoppingList\Plugin
 */
class Link extends \Magento\Wishlist\Block\Link
{
    /**
     * @return string
     */
    public function afterGetHref()
    {
        return $this->getUrl("shoppinglist");
    }
}
