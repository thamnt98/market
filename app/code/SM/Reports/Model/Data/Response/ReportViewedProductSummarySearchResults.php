<?php

declare(strict_types=1);

namespace SM\Reports\Model\Data\Response;

use Magento\Framework\Api\SearchResults;
use SM\Reports\Api\Data\Response\ReportViewedProductSummarySearchResultsInterface;
use SM\Reports\Api\Entity\ReportViewedProductSummaryInterface;

class ReportViewedProductSummarySearchResults extends SearchResults implements ReportViewedProductSummarySearchResultsInterface
{
    /**
     * @return ReportViewedProductSummaryInterface[]
     * @codeCoverageIgnore
     */
    public function getItems(): array
    {
        return $this->_get(self::KEY_ITEMS) === null ? [] : $this->_get(self::KEY_ITEMS);
    }

    /**
     * @param ReportViewedProductSummaryInterface[] $items
     * @return self
     * @codeCoverageIgnore
     */
    public function setItems(array $items): ReportViewedProductSummarySearchResultsInterface
    {
        return $this->setData(self::KEY_ITEMS, $items);
    }

    /**
     * @inheritDoc
     * @codeCoverageIgnore
     */
    public function getProducts(): array
    {
        return $this->_get(self::PRODUCTS) === null ? [] : $this->_get(self::PRODUCTS);
    }

    /**
     * @inheritDoc
     * @codeCoverageIgnore
     */
    public function setProducts(array $products): ReportViewedProductSummarySearchResultsInterface
    {
        return $this->setData(self::PRODUCTS, $products);
    }
}
