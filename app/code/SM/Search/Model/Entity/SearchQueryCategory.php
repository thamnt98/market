<?php

declare(strict_types=1);

namespace SM\Search\Model\Entity;

use Magento\Framework\Model\AbstractModel;
use SM\Search\Api\Entity\SearchQueryCategoryInterface;
use SM\Search\Model\ResourceModel\SearchQueryCategory as ResourceModel;

class SearchQueryCategory extends AbstractModel implements SearchQueryCategoryInterface
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
    public function setQueryText(string $queryText): SearchQueryCategoryInterface
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
    public function setNumResults(int $numResults): SearchQueryCategoryInterface
    {
        return $this->setData(self::NUM_RESULTS, $numResults);
    }

    /**
     * {@inheritdoc}
     */
    public function getNumResults(): int
    {
        return (int) $this->getData(self::NUM_RESULTS);
    }

    /**
     * {@inheritdoc}
     */
    public function setPopularity(int $popularity): SearchQueryCategoryInterface
    {
        return $this->setData(self::POPULARITY, $popularity);
    }

    /**
     * {@inheritdoc}
     */
    public function getPopularity(): int
    {
        return (int) $this->getData(self::POPULARITY);
    }

    /**
     * {@inheritdoc}
     */
    public function setStoreId(int $storeId): SearchQueryCategoryInterface
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
    public function setCategoryId(int $categoryId): SearchQueryCategoryInterface
    {
        return $this->setData(self::CATEGORY_ID, $categoryId);
    }

    /**
     * {@inheritdoc}
     */
    public function getCategoryId(): int
    {
        return (int) $this->getData(self::CATEGORY_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setQueryId(int $queryId): SearchQueryCategoryInterface
    {
        return $this->setData(self::QUERY_ID, $queryId);
    }

    /**
     * {@inheritdoc}
     */
    public function getQueryId(): int
    {
        return (int) $this->getData(self::QUERY_ID);
    }
}
