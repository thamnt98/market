<?php

namespace SM\MobileApi\Api\Data\Product;

interface SearchInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{
    const PRODUCTS = 'products';
    const SEARCH_TYPE = 'search_type';
    const SUGGEST_KEYWORD = 'suggest_keyword';
    const TOTAL = 'total';
    const MAXIMUM_PRODUCTS_RESPONSE = 100;

    /**
     * @return string
     */
    public function getSearchType();

    /**
     * @param string $type
     * @return $this
     */
    public function setSearchType($type);

    /**
     * @return string
     */
    public function getSuggestKeyword();

    /**
     * @param string $keyword
     * @return $this
     */
    public function setSuggestKeyword($keyword);
    /**
     * Get product collection
     *
     * @return \SM\MobileApi\Api\Data\Product\ListItemInterface[]
     */
    public function getProducts();

    /**
     * @param \SM\MobileApi\Api\Data\Product\ListItemInterface[] $data
     *
     * @return $this
     */
    public function setProducts($data);

    /**
     * @return integer
     */
    public function getTotal();

    /**
     * @param $total
     * @return $this
     */
    public function setTotal($total);
}
