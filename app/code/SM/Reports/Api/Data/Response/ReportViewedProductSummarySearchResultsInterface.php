<?php

declare(strict_types=1);

namespace SM\Reports\Api\Data\Response;

use Magento\Framework\Api\SearchResultsInterface;

interface ReportViewedProductSummarySearchResultsInterface extends SearchResultsInterface
{
    const PRODUCTS = 'products';

    /**
     * @return \SM\Reports\Api\Entity\ReportViewedProductSummaryInterface[]
     */
    public function getItems(): array;

    /**
     * @param \SM\Reports\Api\Entity\ReportViewedProductSummaryInterface[] $items
     * @return self
     */
    public function setItems(array $items): self;

    /**
     * @return \Magento\Catalog\Api\Data\ProductInterface[]
     */
    public function getProducts(): array;

    /**
     * @param \Magento\Catalog\Api\Data\ProductInterface[] $products
     * @return self
     */
    public function setProducts(array $products): self;
}
