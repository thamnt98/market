<?php

/**
 * @category SM
 * @package SM_Brand
 * @license     http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @author      Chinhvd <chinhvd@smartosc.com>
 * @copyright   Copyright (c) SmartOSC. All rights reserved.
 * @url http://www.smartosc.com/
 */

namespace SM\Brand\Model\ResourceModel\Category;


class BrandList extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    const TABLE_NAME = 'category_brand_list';

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
    public function getBrandPosition($category)
    {
        if ($category instanceof \Magento\Catalog\Api\Data\CategoryInterface) {
            $category = $category->getId();
        }

        $select = $this->getConnection()
            ->select()
            ->from(
                self::TABLE_NAME,
                ['option_setting_id', 'position']
            )->where("category_id = ?", $category);

        return $this->getConnection()->fetchPairs($select);
    }

    /**
     * @param \Magento\Catalog\Api\Data\CategoryInterface|int $category
     * @param array                                           $optionIds
     */
    public function removeCategoryItem($category, $optionIds)
    {
        if ($category instanceof \Magento\Catalog\Api\Data\CategoryInterface) {
            $category = $category->getId();
        }

        $this->getConnection()->delete(
            $this->getTable(self::TABLE_NAME),
            [
                'category_id = ?'       => $category,
                'option_setting_id in (?)' => $optionIds
            ]
        );
    }
}
