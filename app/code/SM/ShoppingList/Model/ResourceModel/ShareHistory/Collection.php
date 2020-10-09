<?php


namespace SM\ShoppingList\Model\ResourceModel\ShareHistory;


class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected function _construct()
    {
        $this->_init('SM\ShoppingList\Model\ShareHistory', 'SM\ShoppingList\Model\ResourceModel\ShareHistory');
    }

}
