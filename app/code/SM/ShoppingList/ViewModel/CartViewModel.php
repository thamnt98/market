<?php

namespace SM\ShoppingList\ViewModel;

use Magento\Catalog\Model\Product;

/**
 * Class CartViewModel
 * @package SM\ShoppingList\ViewModel
 */
class CartViewModel extends ShoppingListViewModel
{
    /**
     * @return array
     */
    public function getItemIds()
    {
        $item_ids = [];
        $itemCollection = $this->cart->getQuote()->getAllItems();
        /** @var \Magento\Quote\Model\Quote\Item $item */
        foreach ($itemCollection as $item) {
            $item_ids[] = $item->getId();
        }
        return $item_ids;
    }

    /**
     * @param Product $product
     * @return string
     */
    public function getProductImage($product)
    {
        return $this->imageHelper->init($product, 'cart_page_product_thumbnail')->getUrl();
    }
}
