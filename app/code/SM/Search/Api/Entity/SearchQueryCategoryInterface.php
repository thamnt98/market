<?php

declare(strict_types=1);

namespace SM\Search\Api\Entity;

interface SearchQueryCategoryInterface
{
    const QUERY_TEXT = 'query_text';
    const NUM_RESULTS = 'num_results';
    const POPULARITY = 'popularity';
    const STORE_ID = 'store_id';
    const QUERY_ID = 'query_id';
    const CATEGORY_ID = 'category_id';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    /**
     * @param string $queryText
     * @return self
     */
    public function setQueryText(string $queryText): self;

    /**
     * @return string
     */
    public function getQueryText(): string;

    /**
     * @param int $numResults
     * @return self
     */
    public function setNumResults(int $numResults): self;

    /**
     * @return int
     */
    public function getNumResults(): int;

    /**
     * @param int $popularity
     * @return self
     */
    public function setPopularity(int $popularity): self;

    /**
     * @return int
     */
    public function getPopularity(): int;

    /**
     * @param int $storeId
     * @return self
     */
    public function setStoreId(int $storeId): self;

    /**
     * @return int
     */
    public function getStoreId(): int;

    /**
     * @param int $categoryId
     * @return self
     */
    public function setCategoryId(int $categoryId): self;

    /**
     * @return int
     */
    public function getCategoryId(): int;

    /**
     * @param int $queryId
     * @return self
     */
    public function setQueryId(int $queryId): self;

    /**
     * @return int
     */
    public function getQueryId(): int;
}
