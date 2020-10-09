<?php

namespace SM\ShoppingList\ViewModel;

use Magento\Catalog\Helper\Image;
use Magento\Catalog\Model\Product;
use Magento\Customer\Helper\Session\CurrentCustomer;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Wishlist\Helper\Data;
use Magento\Wishlist\Model\ResourceModel\Wishlist\CollectionFactory as ShoppingListCollectionFactory;

/**
 * Class PDPViewModel
 * @package SM\ShoppingList\ViewModel
 */
class PDPViewModel extends ShoppingListViewModel
{
    /**
     * @param $productId
     * @return bool
     */
    public function isAddedShoppingList($productId)
    {
        return $this->shoppingListHelper->isAddedShoppingList($productId);
    }

    /**
     * @param $productId
     * @return array|null
     */
    public function getAddedItemIdInList($productId)
    {
        return $this->shoppingListHelper->getAddedItemIdInList($productId);
    }
}
