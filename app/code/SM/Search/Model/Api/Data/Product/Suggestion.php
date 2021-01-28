<?php
/**
 * SMCommerce
 *
 * @category  SM
 * @package   SM_Search
 *
 * Date: January, 15 2021
 * Time: 5:58 PM
 * User: VooThanh DEV
 *
 * @author    SMCommerce Core Team <connect@smartosc.com>
 * @copyright Copyright SMCommerce (http://smartosc.com/)
 */

namespace SM\Search\Model\Api\Data\Product;

class Suggestion extends \Magento\Framework\DataObject implements \SM\Search\Api\Data\Product\SuggestionInterface
{
    /**
     * @return int
     */
    public function getId()
    {
        return (int) $this->getData(self::ID);
    }

    /**
     * @param int $id
     *
     * @return Suggestion
     */
    public function setId($id)
    {
        return $this->setData(self::ID, $id);
    }

    /**
     * @return string
     */
    public function getSku()
    {
        return $this->getData(self::SKU);
    }

    /**
     * @param string $sku
     *
     * @return Suggestion
     */
    public function setSku($sku)
    {
        return $this->setData(self::SKU, $sku);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->getData(self::NAME);
    }

    /**
     * @param string $name
     *
     * @return Suggestion
     */
    public function setName($name)
    {
        return $this->setData(self::NAME, $name);
    }
}
