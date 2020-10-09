<?php

namespace SM\Review\Model\ResourceModel;

class ReviewEdit extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{

    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init("sm_review_edit", "entity_id");
    }
}
