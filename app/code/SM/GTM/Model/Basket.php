<?php


namespace SM\GTM\Model;


class Basket extends \Magento\Framework\Model\AbstractModel implements \Magento\Framework\DataObject\IdentityInterface
{
    const CACHE_TAG = 'basket_tag';

    protected function _construct()
    {
        $this->_init('SM\GTM\Model\ResourceModel\Basket');
    }

    /**
     * @inheritDoc
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }
}
