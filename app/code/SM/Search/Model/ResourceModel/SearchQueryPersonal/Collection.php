<?php

declare(strict_types=1);

namespace SM\Search\Model\ResourceModel\SearchQueryPersonal;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use SM\Search\Model\Entity\SearchQueryPersonal;
use SM\Search\Model\ResourceModel\SearchQueryPersonal as ResourceModel;

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
        $this->_init(SearchQueryPersonal::class, ResourceModel::class);
    }
}
