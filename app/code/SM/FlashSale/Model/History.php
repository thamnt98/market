<?php
namespace SM\FlashSale\Model;
class History extends \Magento\Framework\Model\AbstractModel implements \Magento\Framework\DataObject\IdentityInterface
{
    const CACHE_TAG = 'flashsale_customer_history';

    protected $_cacheTag = 'flashsale_customer_history';

    protected $_eventPrefix = 'flashsale_customer_history';

    protected function _construct()
    {
        $this->_init('SM\FlashSale\Model\ResourceModel\History');
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