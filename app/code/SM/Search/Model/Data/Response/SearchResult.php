<?php

declare(strict_types=1);

namespace SM\Search\Model\Data\Response;

use Magento\Framework\Api\Search\SearchResult as BaseSearchResult;
use SM\Search\Api\Data\Response\SearchResultInterface;

class SearchResult extends BaseSearchResult implements SearchResultInterface
{
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
    public function setProducts(array $products): SearchResultInterface
    {
        return $this->setData(self::PRODUCTS, $products);
    }
}
