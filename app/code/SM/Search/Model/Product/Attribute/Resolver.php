<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace SM\Search\Model\Product\Attribute;

use Magento\Catalog\Model\Product;
use SM\Category\Model\Repository\CategoryRepository;
use SM\Search\Helper\Config;

class Resolver
{
    /**
     * @var CategoryRepository
     */
    protected $categoryRepository;

    /**
     * @var \Magento\Eav\Model\Config
     */
    protected $eavConfig;

    /**
     * @var \Magento\Framework\DB\Adapter\AdapterInterface
     */
    protected $connection;

    /**
     * Resolver constructor.
     *
     * @param CategoryRepository                        $categoryRepository
     * @param \Magento\Eav\Model\Config                 $eavConfig
     * @param \Magento\Framework\App\ResourceConnection $resource
     */
    public function __construct(
        CategoryRepository $categoryRepository,
        \Magento\Eav\Model\Config $eavConfig,
        \Magento\Framework\App\ResourceConnection $resource
    ) {
        $this->categoryRepository = $categoryRepository;
        $this->eavConfig = $eavConfig;
        $this->connection = $resource->getConnection();
    }

    /**
     * @param Product $product
     * @return string
     */
    public function resolveCategoryNamesAttribute(Product $product): string
    {
        $names = [];

        $categoryIds = $product->getData(Config::CATEGORY_IDS_ATTRIBUTE_CODE);
        if (empty($categoryIds)) {
            return '';
        }

        try {
            $nameAttr = $this->eavConfig->getAttribute(
                \Magento\Catalog\Model\Category::ENTITY,
                'name'
            );

            $select = $this->connection
                ->select()
                ->from(['v' => $nameAttr->getBackendTable()], 'value')
                ->joinInner(['cce' => 'catalog_category_entity'], 'cce.row_id = v.row_id', [])
                ->where('attribute_id = ?', $nameAttr->getId())
                ->where('cce.entity_id in (' . implode(',', $categoryIds) . ')');

            $names = array_unique($this->connection->fetchCol($select));
        } catch (\Exception $e) {
            $categories = $this->categoryRepository->getCategoriesByIds($categoryIds);
            foreach ($categories as $category) {
                $names[] = $category->getName();
            }
        }

        return implode(' | ', $names);
    }
}
