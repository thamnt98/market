<?php


namespace SM\GTM\Model\ResourceModel\Basket;


class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected $_idFieldName = 'basket_id';

    protected function _construct()
    {
        $this->_init('SM\GTM\Model\Basket','SM\GTM\Model\ResourceModel\Basket');
    }
}
