<?php

namespace SM\ShoppingList\Model\ResourceModel;

class ShareHistory extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    protected function _construct()
    {
        $this->_init('shoppinglist_share_history', 'entity_id');
    }
}
