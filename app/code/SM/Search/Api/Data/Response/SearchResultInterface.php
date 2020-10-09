<?php

declare(strict_types=1);

namespace SM\Search\Api\Data\Response;

use Magento\Framework\Api\Search\SearchResultInterface as BaseSearchResultInterface;

interface SearchResultInterface extends BaseSearchResultInterface
{
    const PRODUCTS = 'products';

    /**
     * Place here just to make items above products in response
     * @inheritDoc
     */
    public function getItems();

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
