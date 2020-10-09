<?php


namespace SM\GTM\Model\ResourceModel;


class Basket extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{

    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init('basket','basket_id');
    }
}
