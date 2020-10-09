<?php


namespace SM\ShoppingList\Modifier;

/**
 * Class ShoppingListLink
 * @package SM\ShoppingList\Modifier
 */
class ShoppingListLink extends \Magento\MultipleWishlist\Block\Link
{
    /**
     * @return \Magento\Framework\Phrase
     */
    public function getLabel()
    {
        return (__("Shopping List"));
    }

    /**
     * @return string
     */
    public function getHref()
    {
        return $this->getUrl('shoppinglist');
    }
}
