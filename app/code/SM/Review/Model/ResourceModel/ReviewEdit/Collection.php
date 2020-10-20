<?php

namespace SM\Review\Model\ResourceModel\ReviewEdit;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use SM\Review\Model\ReviewEdit;

class Collection extends AbstractCollection
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('SM\Review\Model\ReviewEdit', 'SM\Review\Model\ResourceModel\ReviewEdit');
    }

    /**
     * @param int $reviewId
     * @return ReviewEdit|null
     */
    public function loadByReviewId($reviewId)
    {
        $this->addFieldToFilter("review_id", ["eq" => $reviewId]);
        if ($this->getSize()) {
            /** @var ReviewEdit $reviewEditModel */
            $reviewEditModel = $this->getLastItem();
            return $reviewEditModel;
        }
        return null;
    }
}
