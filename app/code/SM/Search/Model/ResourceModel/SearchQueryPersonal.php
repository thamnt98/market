<?php

declare(strict_types=1);

namespace SM\Search\Model\ResourceModel;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use SM\Search\Api\Entity\SearchQueryPersonalInterface;

class SearchQueryPersonal extends AbstractDb
{
    /**
     * Construct method
     */
    protected function _construct()
    {
        $this->_init('search_query_personal', 'entity_id');
    }

    /**
     * @param array $entityData
     * @throws LocalizedException
     */
    public function saveEntityData(array $entityData): void
    {
        $updateCols = [SearchQueryPersonalInterface::UPDATED_AT];
        $this->getConnection()->insertOnDuplicate($this->getMainTable(), $entityData, $updateCols);
    }

    /**
     * @param int $customerId
     * @param string $queryText
     * @throws LocalizedException
     */
    public function deleteOne(int $customerId, string $queryText): void
    {
        $this->getConnection()->delete($this->getMainTable(), [
            SearchQueryPersonalInterface::CUSTOMER_ID . ' = ?' => $customerId,
            SearchQueryPersonalInterface::QUERY_TEXT . ' = ?' => $queryText,
        ]);
    }

    /**
     * @param int $customerId
     * @throws LocalizedException
     */
    public function deleteAll(int $customerId): void
    {
        $this->getConnection()->delete($this->getMainTable(), [
            SearchQueryPersonalInterface::CUSTOMER_ID . ' = ?' => $customerId,
        ]);
    }
}
