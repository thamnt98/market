<?php

declare(strict_types=1);

namespace SM\Reports\Api\Entity;

interface ReportViewedProductSummaryInterface
{
    const PRODUCT_ID = 'product_id';
    const POPULARITY = 'popularity';
    const STORE_ID = 'store_id';
    const CUSTOMER_ID = 'customer_id';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    /**
     * @param int $productId
     * @return self
     */
    public function setProductId(int $productId): self;

    /**
     * @return int
     */
    public function getProductId(): int;

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
     * @param int $customerId
     * @return self
     */
    public function setCustomerId(int $customerId): self;

    /**
     * @return int
     */
    public function getCustomerId(): int;
}
