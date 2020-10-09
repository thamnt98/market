<?php
namespace SM\MyVoucher\Model\ResourceModel\Voucher;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected $_idFieldName = 'id';
    protected $_eventPrefix = 'salesvoucher_customer_collection';
    protected $_eventObject = 'voucher_collection';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('SM\MyVoucher\Model\Voucher', 'SM\MyVoucher\Model\ResourceModel\Voucher');
    }

}