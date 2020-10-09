<?php

declare(strict_types=1);

namespace SM\Search\Model\Entity;

use Magento\Framework\Model\AbstractModel;
use SM\Search\Api\Entity\SearchQueryPersonalInterface;
use SM\Search\Model\ResourceModel\SearchQueryPersonal as ResourceModel;

class SearchQueryPersonal extends AbstractModel implements SearchQueryPersonalInterface
{
    /**
     * Construct method
     */
    protected function _construct()
    {
        $this->_init(ResourceModel::class);
    }

    /**
     * {@inheritdoc}
     */
    public function setQueryText(string $queryText): SearchQueryPersonalInterface
    {
        return $this->setData(self::QUERY_TEXT, $queryText);
    }

    /**
     * {@inheritdoc}
     */
    public function getQueryText(): string
    {
        return $this->getData(self::QUERY_TEXT);
    }

    /**
     * {@inheritdoc}
     */
    public function setStoreId(int $storeId): SearchQueryPersonalInterface
    {
        return $this->setData(self::STORE_ID, $storeId);
    }

    /**
     * {@inheritdoc}
     */
    public function getStoreId(): int
    {
        return (int) $this->getData(self::STORE_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setCustomerId(int $customerId): SearchQueryPersonalInterface
    {
        return $this->setData(self::CUSTOMER_ID, $customerId);
    }

    /**
     * {@inheritdoc}
     */
    public function getCustomerId(): int
    {
        return (int) $this->getData(self::CUSTOMER_ID);
    }
}
