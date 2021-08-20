<?php

namespace SM\ShoppingList\Model\ResourceModel\Item;

use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SortOrder;
use Magento\Wishlist\Model\ResourceModel\Item\Collection as ItemCollection;

/**
 * Class Collection
 * @package SM\ShoppingList\Model\ResourceModel\Item
 */
class Collection extends ItemCollection
{
    protected function _assignProducts()
    {

        $this->_productIds = array_map(
            function ($item) {
                return $item["product_id"];
            }, $this->getData());
        return parent::_assignProducts();
    }
}
