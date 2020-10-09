<?php

declare(strict_types=1);

namespace SM\Reports\Model\ResourceModel\ReportViewedProductSummary;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use SM\Reports\Model\Entity\ReportViewedProductSummary;
use SM\Reports\Model\ResourceModel\ReportViewedProductSummary as ResourceModel;

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
        $this->_init(ReportViewedProductSummary::class, ResourceModel::class);
    }
}
