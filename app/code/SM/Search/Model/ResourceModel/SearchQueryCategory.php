<?php

declare(strict_types=1);

namespace SM\Search\Model\ResourceModel;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use SM\Search\Api\Entity\SearchQueryCategoryInterface;

class SearchQueryCategory extends AbstractDb
{
    /**
     * Construct method
     */
    protected function _construct()
    {
        $this->_init('search_query_category', 'entity_id');
    }

    /**
     * @param array $entityData
     * @throws LocalizedException
     */
    public function saveEntityData(array $entityData): void
    {
        $updateCols = [
            SearchQueryCategoryInterface::NUM_RESULTS,
            SearchQueryCategoryInterface::QUERY_ID,
            SearchQueryCategoryInterface::UPDATED_AT,
        ];
        if ($entityData[SearchQueryCategoryInterface::POPULARITY] == 1) {
            $updateCols[SearchQueryCategoryInterface::POPULARITY] = new \Zend_Db_Expr('`popularity` + 1');
        }
        $this->getConnection()->insertOnDuplicate($this->getMainTable(), $entityData, $updateCols);
    }
}
