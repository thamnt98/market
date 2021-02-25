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

        $categoryIds = $this->getAllCategoryRowIds($product);
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
                ->from(['ccev' => $nameAttr->getBackendTable()], 'value')
                ->joinInner(['cce' => 'catalog_category_entity'], 'cce.row_id = ccev.row_id', [])
                ->where('ccev.attribute_id = ?', $nameAttr->getId())
                ->where('cce.parent_id NOT IN (0, 1)')
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

    /**
     * @param Product $product
     *
     * @return array
     */
    public function getAllCategoryRowIds($product)
    {
        $categoryIds = $product->getData(Config::CATEGORY_IDS_ATTRIBUTE_CODE);
        if (empty($categoryIds)) {
            return [];
        }

        $select = $this->connection
            ->select()
            ->from('catalog_category_entity', [])
            ->where('entity_id in (' . implode(',', $categoryIds) . ')')
            ->columns([
                "group_concat(replace(path, '/', ',')) as path"
            ]);

        $result = [];
        foreach ($this->connection->fetchCol($select) as $path) {
            foreach (array_unique(explode(',', $path)) as $id) {
                if ($id != 0 && $id != 1) {
                    $result[] = $id;
                }
            }

        }

        return $result;
    }
}
