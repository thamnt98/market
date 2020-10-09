<?php


namespace SM\ShoppingList\Model\ResourceModel\Wishlist;


class Collection extends \Magento\Wishlist\Model\ResourceModel\Wishlist\Collection
{

    public function getSelectItems()
    {
        $this->getSelect()
            ->joinLeft(
                "wishlist_item",
                'main_table.wishlist_id = wishlist_item.wishlist_id'

            );

        return $this;
    }
}
