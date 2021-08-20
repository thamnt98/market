<?php

namespace SM\ShoppingList\Plugin;

/**
 * Class ShoppingListData
 * @package SM\ShoppingList\Plugin
 */
class ShoppingListData
{
    /**
     * @return \Magento\Framework\Phrase
     */
    public function afterGetDefaultWishlistName()
    {
        return __('My Favorites');
    }
}
