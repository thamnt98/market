<?php


namespace SM\Review\Model\ResourceModel\ReviewImage;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

/**
 * Class Collection
 * @package SM\Review\Model\ResourceModel\ReviewImage
 */
class Collection extends AbstractCollection
{
    protected function _construct()
    {
        parent::_construct();
        $this->_init('SM\Review\Model\ReviewImage', 'SM\Review\Model\ResourceModel\ReviewImage');
    }
}
