<?php

declare(strict_types=1);

namespace SM\Reports\Model\ResourceModel;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use SM\Search\Api\Entity\SearchQueryCategoryInterface;

class ReportViewedProductSummary extends AbstractDb
{
    /**
     * Construct method
     */
    protected function _construct()
    {
        $this->_init('report_viewed_product_summary', 'entity_id');
    }

    /**
     * @param array $entityData
     * @throws LocalizedException
     */
    public function saveEntityData(array $entityData): void
    {
        $updateCols = [
            SearchQueryCategoryInterface::POPULARITY => new \Zend_Db_Expr('`popularity` + 1'),
            SearchQueryCategoryInterface::UPDATED_AT,
        ];
        $this->getConnection()->insertOnDuplicate($this->getMainTable(), $entityData, $updateCols);
    }
}
