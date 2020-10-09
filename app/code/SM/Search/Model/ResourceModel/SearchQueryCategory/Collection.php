<?php

declare(strict_types=1);

namespace SM\Search\Model\ResourceModel\SearchQueryCategory;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use SM\Search\Model\Entity\SearchQueryCategory;
use SM\Search\Model\ResourceModel\SearchQueryCategory as ResourceModel;

class Collection extends AbstractCollection
{
    protected $_idFieldName = 'entity_id';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(SearchQueryCategory::class, ResourceModel::class);
    }
}
