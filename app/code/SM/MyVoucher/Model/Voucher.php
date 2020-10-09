<?php
namespace SM\MyVoucher\Model;
class Voucher extends \Magento\Framework\Model\AbstractModel implements \Magento\Framework\DataObject\IdentityInterface
{
    const CACHE_TAG = 'salesvoucher_customer';

    protected $_cacheTag = 'salesvoucher_customer';

    protected $_eventPrefix = 'salesvoucher_customer';

    protected function _construct()
    {
        $this->_init('SM\MyVoucher\Model\ResourceModel\Voucher');
    }

    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    public function getDefaultValues()
    {
        $values = [];

        return $values;
    }
}