<?php


namespace SM\Review\Model\ResourceModel;

/**
 * Class ReviewImage
 * @package SM\Review\Model\ResourceModel
 */
class ReviewImage extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init("review_image", "id");
    }
}
