<?php
/**
 * SMCommerce
 *
 * @category  SM
 * @package   SM_Search
 *
 * Date: January, 15 2021
 * Time: 5:54 PM
 * User: VooThanh DEV
 *
 * @author    SMCommerce Core Team <connect@smartosc.com>
 * @copyright Copyright SMCommerce (http://smartosc.com/)
 */

namespace SM\Search\Api\Data\Product;

interface SuggestionInterface
{
    const ID   = 'id';
    const SKU  = 'sku';
    const NAME = 'name';

    /**
     * Get Product Id
     *
     * @return integer
     */
    public function getId();

    /**
     * Set Product Id
     *
     * @param $id
     *
     * @return $this
     */
    public function setId($id);

    /**
     * Get Product's SKU
     *
     * @return string
     */
    public function getSku();

    /**
     * Set Product's SKU
     *
     * @param $sku
     *
     * @return $this
     */
    public function setSku($sku);

    /**
     * Get Product's Name
     *
     * @return string
     */
    public function getName();

    /**
     * set Product's Name
     *
     * @param $name
     *
     * @return $this
     */
    public function setName($name);
}
