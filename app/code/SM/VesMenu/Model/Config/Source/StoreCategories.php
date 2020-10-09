<?php
/**
 * @category  SM
 * @package   SM_VesMenu
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 *
 * @author    Logan Ta <phuongtt2@smartosc.com>
 *
 * @copyright 2020 Copyright (c) SmartOSC. All rights reserved.
 * @url http://www.smartosc.com/
 */

declare(strict_types=1);

namespace SM\VesMenu\Model\Config\Source;

use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory as CategoryCollectionFactory;
use Magento\Framework\DB\Helper as DbHelper;
use Magento\Catalog\Model\Category as CategoryModel;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\App\CacheInterface;
use Magento\Catalog\Model\Locator\LocatorInterface;
use Ves\Megamenu\Model\Config\Source\StoreCategories as StoreCategoriesCore;

class StoreCategories extends StoreCategoriesCore
{
    const TREE_ROOT_ID = 2;

    /**
     * @param int $root_id
     * @return array|mixed
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getCategoryList($root_id = 0)
    {
        $categoriesTrees = $this->getCategoriesTree();
        if ($categoriesTrees) {
            foreach ($categoriesTrees as $category) {
                $this->generatCategory($category);
            }
        }
        $list = $this->list;
        foreach ($list as $k => &$category) {
            if ($category['level'] == 0) {
                unset($list[$k]);
            }
            $category['level'] -= 1;

            $categoryLabel = '';

            if ($category['level'] != 0) {
                $categoryLabel = '| ';
            }

            $categoryLabel .= $this->_getSpaces($category['level']) . '(ID:' . $category['value'] . ') ' . $category['label'];
            $category['label'] = $categoryLabel;
        }

        return $list;
    }

    /**
     * @param null $filter
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getCategoriesTree($filter = NULL)
    {
        if (isset($this->categoriesTrees[$filter])) {
            return $this->categoriesTrees[$filter];
        }

        // @var $collection \Magento\Catalog\Model\ResourceModel\Category\Collection
        $collection = $this->getCategoryCollection();

        if (!empty($filter)) {
            $collection->addAttributeToFilter('entity_id', ['in' => $this->getCategoryIdsByName(NULL, $filter)]);
        }
        $collection->addAttributeToSelect(['name', 'is_active', 'parent_id']);

        $categoryById = [
            self::TREE_ROOT_ID => [
                'value' => self::TREE_ROOT_ID,
                'children' => NULL,
            ],
        ];

        foreach ($collection as $category) {
            if ($category->getName()) {
                foreach ([$category->getId(), $category->getParentId()] as $categoryId) {
                    if (!isset($categoryById[$categoryId])) {
                        $categoryById[$categoryId] = [
                            'value' => $categoryId,
                            'category' => $categoryId,
                            'link_type' => 'category_link'
                        ];
                    }
                }
                $categoryById[$category->getId()]['category'] = $category->getId();
                $categoryById[$category->getId()]['link_type'] = "category_link";
                $categoryById[$category->getId()]['is_active'] = $category->getIsActive();
                $categoryById[$category->getId()]['label'] = str_replace("'", " ", $category->getName());
                $categoryById[$category->getId()]['name'] = str_replace("'", " ", $category->getName());
                $categoryById[$category->getParentId()]['children'][] = &$categoryById[$category->getId()];
                $categoryById[$category->getId()]['level'] = $category->getLevel();
            }
        }

        $this->categoriesTrees[$filter] = $categoryById[self::TREE_ROOT_ID]['children'];

        return $this->categoriesTrees[$filter];

    }
}
