<?php

declare(strict_types=1);

namespace SM\Search\Api\Entity;

interface SearchQueryPersonalInterface
{
    const QUERY_TEXT = 'query_text';
    const STORE_ID = 'store_id';
    const CUSTOMER_ID = 'customer_id';
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
     * @param int $storeId
     * @return self
     */
    public function setStoreId(int $storeId): self;

    /**
     * @return int
     */
    public function getStoreId(): int;

    /**
     * @param int $customerId
     * @return self
     */
    public function setCustomerId(int $customerId): self;

    /**
     * @return int
     */
    public function getCustomerId(): int;
}
