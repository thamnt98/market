<?php


namespace SM\ShoppingList\Modifier;

use Magento\MultipleWishlist\Block\Link;

/**
 * Class ShoppingListLink
 * @package SM\ShoppingList\Modifier
 */
class ShoppingListLink extends Link
{
    /**
     * @return \Magento\Framework\Phrase
     */
    public function getLabel()
    {
        return (__("Shopping List"));
    }
}
