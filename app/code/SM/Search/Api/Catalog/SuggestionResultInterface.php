<?php
/**
 * SMCommerce
 *
 * @category  SM
 * @package   SM_Search
 *
 * Date: January, 15 2021
 * Time: 6:52 PM
 * User: VooThanh DEV
 *
 * @author    SMCommerce Core Team <connect@smartosc.com>
 * @copyright Copyright SMCommerce (http://smartosc.com/)
 */

namespace SM\Search\Api\Catalog;

interface SuggestionResultInterface
{
    const KEY_PRODUCTS = 'products';
    const KEY_TOTAL    = 'total';

    /**
     * Get products
     *
     * @return \SM\Search\Api\Data\Product\SuggestionInterface[]
     */
    public function getProducts();

    /**
     * @param \SM\Search\Api\Data\Product\SuggestionInterface[] $data
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
