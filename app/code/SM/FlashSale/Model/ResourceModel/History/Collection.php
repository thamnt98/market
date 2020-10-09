<?php
namespace SM\FlashSale\Model\ResourceModel\History;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected $_idFieldName = 'id';
    protected $_eventPrefix = 'flashsale_customer_history_collection';
    protected $_eventObject = 'history_collection';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('SM\FlashSale\Model\History', 'SM\FlashSale\Model\ResourceModel\History');
    }

}