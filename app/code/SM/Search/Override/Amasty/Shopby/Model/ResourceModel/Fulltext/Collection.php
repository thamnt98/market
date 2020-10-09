<?php


namespace SM\Search\Override\Amasty\Shopby\Model\ResourceModel\Fulltext;


class Collection extends \Amasty\Shopby\Model\ResourceModel\Fulltext\Collection
{

    protected function _renderFiltersBefore()
    {
        $this->addFieldToSelect('search_result.index_price');
        parent::_renderFiltersBefore();
    }
}
