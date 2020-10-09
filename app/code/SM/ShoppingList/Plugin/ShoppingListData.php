<?php

namespace SM\ShoppingList\Plugin;

use SM\ShoppingList\Helper\Data;

/**
 * Class ShoppingListData
 * @package SM\ShoppingList\Plugin
 */
class ShoppingListData
{
    /**
     * @var Data
     */
    protected $shoppingListHelper;

    /**
     * ShoppingListData constructor.
     * @param Data $shoppingListHelper
     */
    public function __construct(Data $shoppingListHelper)
    {
        $this->shoppingListHelper = $shoppingListHelper;
    }

    /**
     * Retrieve default empty comment message
     *
     * @return \Magento\Framework\Phrase
     */
    public function afterGetDefaultWishlistName()
    {
        return __('My Favorites');
    }

    /**
     * @return bool
     */
    public function afterIsAllow()
    {
        return $this->shoppingListHelper->isActiveShoppingList();
    }
}
