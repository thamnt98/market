<?php

/**
 * SMCommerce
 *
 * @category  SM
 * @package   SM_LayeredNavigation
 *
 * Date: April, 27 2020
 * Time: 11:02 AM
 * User: VooThanh DEV
 *
 * @author    SMCommerce Core Team <connect@smartosc.com>
 * @copyright Copyright SMCommerce (http://smartosc.com/)
 */

namespace SM\LayeredNavigation\Model\ResourceModel\Category;

class FilterList extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    const TABLE_NAME = 'category_filter_list';

    /**
     * Initialize resource
     */
    public function _construct()
    {
        $this->_init(self::TABLE_NAME, 'id');
    }

    /**
     * Get positions of associated to category filter list
     *
     * @param \Magento\Catalog\Api\Data\CategoryInterface|int $category
     *
     * @return array
     */
    public function getAttributesPosition($category)
    {
        if ($category instanceof \Magento\Catalog\Api\Data\CategoryInterface) {
            $category = $category->getId();
        }

        $select = $this->getConnection()
            ->select()
            ->from(
                self::TABLE_NAME,
                ['attribute_code', 'position']
            )->where("category_id = ?", $category);

        return $this->getConnection()->fetchPairs($select);
    }

    /**
     * @param \Magento\Catalog\Api\Data\CategoryInterface|int $category
     * @param array                                           $attributeCodes
     */
    public function removeCategoryItem($category, $attributeCodes)
    {
        if ($category instanceof \Magento\Catalog\Api\Data\CategoryInterface) {
            $category = $category->getId();
        }

        $this->getConnection()->delete(
            $this->getTable(self::TABLE_NAME),
            [
                'category_id = ?'       => $category,
                'attribute_code in (?)' => $attributeCodes
            ]
        );
    }
}
