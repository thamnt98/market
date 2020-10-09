<?php

namespace SM\InspireMe\Api\Data;

interface RelatedProductResultInterface
{
    const TITLE = 'title';
    const PRODUCTS = 'products';

    /**
     * @return string
     */
    public function getTitle();

    /**
     * @param string $title
     * @return $this
     */
    public function setTitle($title);

    /**
     * @return \SM\MobileApi\Api\Data\Product\ListItemInterface[]
     */
    public function getProducts();

    /**
     * @param \SM\MobileApi\Api\Data\Product\ListItemInterface[] $products
     * @return $this
     */
    public function setProducts($products);
}
