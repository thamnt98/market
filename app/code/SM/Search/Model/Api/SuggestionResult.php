<?php
/**
 * SMCommerce
 *
 * @category  SM
 * @package   SM_Search
 *
 * Date: January, 15 2021
 * Time: 6:54 PM
 * User: VooThanh DEV
 *
 * @author    SMCommerce Core Team <connect@smartosc.com>
 * @copyright Copyright SMCommerce (http://smartosc.com/)
 */

namespace SM\Search\Model\Api;

class SuggestionResult extends \Magento\Framework\DataObject implements
    \SM\Search\Api\Catalog\SuggestionResultInterface
{
    /**
     * @return \SM\Search\Api\Data\Product\SuggestionInterface[]
     */
    public function getProducts()
    {
        return $this->getData(self::KEY_PRODUCTS);
    }

    /**
     * @param \SM\Search\Api\Data\Product\SuggestionInterface[] $data
     *
     * @return SuggestionResult
     */
    public function setProducts($data)
    {
        return $this->setData(self::KEY_PRODUCTS, $data);
    }

    /**
     * @return int
     */
    public function getTotal()
    {
        return $this->getData(self::KEY_TOTAL);
    }

    /**
     * @param int $total
     *
     * @return SuggestionResult
     */
    public function setTotal($total)
    {
        return $this->setData(self::KEY_TOTAL, $total);
    }
}
