<?php

namespace Wizkunde\ConfigurableBundle\Model\Indexer\Category\Product;

class Rows extends \Magento\Catalog\Model\Indexer\Category\Product\Action\Rows
{
    /**
     * Retrieve select for reindex products of non anchor categories
     *
     * @param \Magento\Store\Model\Store $store
     * @return \Magento\Framework\DB\Select
     */
    protected function getNonAnchorCategoriesSelect(\Magento\Store\Model\Store $store)
    {
        if (!isset($this->nonAnchorSelects[$store->getId()])) {
            $statusAttributeId = $this->config->getAttribute(
                \Magento\Catalog\Model\Product::ENTITY,
                'status'
            )->getId();
            $visibilityAttributeId = $this->config->getAttribute(
                \Magento\Catalog\Model\Product::ENTITY,
                'visibility'
            )->getId();

            $rootPath = $this->getPathFromCategoryId($store->getRootCategoryId());

            $metadata = $this->getMetadataPool()->getMetadata(\Magento\Catalog\Api\Data\ProductInterface::class);
            $linkField = $metadata->getLinkField();
            $select = $this->connection->select()->from(
                ['cc' => $this->getTable('catalog_category_entity')],
                []
            )->joinInner(
                ['ccp' => $this->getTable('catalog_category_product')],
                'ccp.category_id = cc.entity_id',
                []
            )->joinInner(
                ['cpw' => $this->getTable('catalog_product_website')],
                'cpw.product_id = ccp.product_id',
                []
            )->joinInner(
                ['cpe' => $this->getTable('catalog_product_entity')],
                'ccp.product_id = cpe.entity_id',
                []
            )->joinInner(
                ['cpsd' => $this->getTable('catalog_product_entity_int')],
                'cpsd.' . $linkField . ' = cpe.' . $linkField . ' AND cpsd.store_id = 0' .
                ' AND cpsd.attribute_id = ' .
                $statusAttributeId,
                []
            )->joinLeft(
                ['cpss' => $this->getTable('catalog_product_entity_int')],
                'cpss.' . $linkField . ' = cpe.' . $linkField . ' AND cpss.attribute_id = cpsd.attribute_id' .
                ' AND cpss.store_id = ' .
                $store->getId(),
                []
            )->joinInner(
                ['cpvd' => $this->getTable('catalog_product_entity_int')],
                'cpvd.' . $linkField . ' = cpe.' . $linkField . ' AND cpvd.store_id = 0' .
                ' AND cpvd.attribute_id = ' .
                $visibilityAttributeId,
                []
            )->joinLeft(
                ['cpvs' => $this->getTable('catalog_product_entity_int')],
                'cpvs.' . $linkField . ' = cpe.' . $linkField . ' AND cpvs.attribute_id = cpvd.attribute_id' .
                ' AND cpvs.store_id = ' .
                $store->getId(),
                []
            )->where(
                'cc.path LIKE ' . $this->connection->quote($rootPath . '/%')
            )->where(
                'cpw.website_id = ?',
                $store->getWebsiteId()
            )->where(
                $this->connection->getIfNullSql('cpss.value', 'cpsd.value') . ' = ?',
                \Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED
            )->where(
                $this->connection->getIfNullSql('cpvs.value', 'cpvd.value') . ' IN (?)',
                [
                    \Magento\Catalog\Model\Product\Visibility::VISIBILITY_IN_CATALOG,
                    \Magento\Catalog\Model\Product\Visibility::VISIBILITY_IN_SEARCH,
                    \Magento\Catalog\Model\Product\Visibility::VISIBILITY_BOTH,
                    \Magento\Catalog\Model\Product\Visibility::VISIBILITY_NOT_VISIBLE,
                ]
            )->columns(
                [
                    'category_id' => 'cc.entity_id',
                    'product_id' => 'ccp.product_id',
                    'position' => 'ccp.position',
                    'is_parent' => new \Zend_Db_Expr('1'),
                    'store_id' => new \Zend_Db_Expr($store->getId()),
                    'visibility' => new \Zend_Db_Expr(
                        $this->connection->getIfNullSql('cpvs.value', 'cpvd.value')
                    ),
                ]
            );

            $this->nonAnchorSelects[$store->getId()] = $select;
        }

        return $this->nonAnchorSelects[$store->getId()];
    }

    /**
     * Create anchor select
     *
     * @param \Magento\Store\Model\Store $store
     * @return \Magento\Framework\DB\Select
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function createAnchorSelect(\Magento\Store\Model\Store $store)
    {
        $isAnchorAttributeId = $this->config->getAttribute(
            \Magento\Catalog\Model\Category::ENTITY,
            'is_anchor'
        )->getId();
        $statusAttributeId = $this->config->getAttribute(\Magento\Catalog\Model\Product::ENTITY, 'status')->getId();
        $visibilityAttributeId = $this->config->getAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'visibility'
        )->getId();
        $rootCatIds = explode('/', $this->getPathFromCategoryId($store->getRootCategoryId()));
        array_pop($rootCatIds);

        $temporaryTreeTable = $this->makeTempCategoryTreeIndex();

        $productMetadata = $this->getMetadataPool()->getMetadata(\Magento\Catalog\Api\Data\ProductInterface::class);
        $categoryMetadata = $this->getMetadataPool()->getMetadata(\Magento\Catalog\Api\Data\CategoryInterface::class);
        $productLinkField = $productMetadata->getLinkField();
        $categoryLinkField = $categoryMetadata->getLinkField();

        return $this->connection->select()->from(
            ['cc' => $this->getTable('catalog_category_entity')],
            []
        )->joinInner(
            ['cc2' => $temporaryTreeTable],
            'cc2.parent_id = cc.entity_id AND cc.entity_id NOT IN (' . implode(
                ',',
                $rootCatIds
            ) . ')',
            []
        )->joinInner(
            ['ccp' => $this->getTable('catalog_category_product')],
            'ccp.category_id = cc2.child_id',
            []
        )->joinInner(
            ['cpe' => $this->getTable('catalog_product_entity')],
            'ccp.product_id = cpe.entity_id',
            []
        )->joinInner(
            ['cpw' => $this->getTable('catalog_product_website')],
            'cpw.product_id = ccp.product_id',
            []
        )->joinInner(
            ['cpsd' => $this->getTable('catalog_product_entity_int')],
            'cpsd.' . $productLinkField . ' = cpe.' . $productLinkField . ' AND cpsd.store_id = 0'
            . ' AND cpsd.attribute_id = ' . $statusAttributeId,
            []
        )->joinLeft(
            ['cpss' => $this->getTable('catalog_product_entity_int')],
            'cpss.' . $productLinkField . ' = cpe.' . $productLinkField . ' AND cpss.attribute_id = cpsd.attribute_id' .
            ' AND cpss.store_id = ' .
            $store->getId(),
            []
        )->joinInner(
            ['cpvd' => $this->getTable('catalog_product_entity_int')],
            'cpvd.' . $productLinkField . ' = cpe. ' . $productLinkField . ' AND cpvd.store_id = 0' .
            ' AND cpvd.attribute_id = ' .
            $visibilityAttributeId,
            []
        )->joinLeft(
            ['cpvs' => $this->getTable('catalog_product_entity_int')],
            'cpvs.' . $productLinkField . ' = cpe.' . $productLinkField .
            ' AND cpvs.attribute_id = cpvd.attribute_id ' . 'AND cpvs.store_id = ' .
            $store->getId(),
            []
        )->joinInner(
            ['ccad' => $this->getTable('catalog_category_entity_int')],
            'ccad.' . $categoryLinkField . ' = cc.' . $categoryLinkField . ' AND ccad.store_id = 0' .
            ' AND ccad.attribute_id = ' . $isAnchorAttributeId,
            []
        )->joinLeft(
            ['ccas' => $this->getTable('catalog_category_entity_int')],
            'ccas.' . $categoryLinkField . ' = cc.' . $categoryLinkField
            . ' AND ccas.attribute_id = ccad.attribute_id AND ccas.store_id = ' .
            $store->getId(),
            []
        )->where(
            'cpw.website_id = ?',
            $store->getWebsiteId()
        )->where(
            $this->connection->getIfNullSql('cpss.value', 'cpsd.value') . ' = ?',
            \Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED
        )->where(
            $this->connection->getIfNullSql('cpvs.value', 'cpvd.value') . ' IN (?)',
            [
                \Magento\Catalog\Model\Product\Visibility::VISIBILITY_IN_CATALOG,
                \Magento\Catalog\Model\Product\Visibility::VISIBILITY_IN_SEARCH,
                \Magento\Catalog\Model\Product\Visibility::VISIBILITY_BOTH,
                \Magento\Catalog\Model\Product\Visibility::VISIBILITY_NOT_VISIBLE
            ]
        )->where(
            $this->connection->getIfNullSql('ccas.value', 'ccad.value') . ' = ?',
            1
        )->columns(
            [
                'category_id' => 'cc.entity_id',
                'product_id' => 'ccp.product_id',
                'position' => new \Zend_Db_Expr('ccp.position + 10000'),
                'is_parent' => new \Zend_Db_Expr('0'),
                'store_id' => new \Zend_Db_Expr($store->getId()),
                'visibility' => new \Zend_Db_Expr($this->connection->getIfNullSql('cpvs.value', 'cpvd.value')),
            ]
        );
    }

    /**
     * Get select for all products
     *
     * @param \Magento\Store\Model\Store $store
     * @return \Magento\Framework\DB\Select
     */
    protected function getAllProducts(\Magento\Store\Model\Store $store)
    {
        if (!isset($this->productsSelects[$store->getId()])) {
            $statusAttributeId = $this->config->getAttribute(
                \Magento\Catalog\Model\Product::ENTITY,
                'status'
            )->getId();
            $visibilityAttributeId = $this->config->getAttribute(
                \Magento\Catalog\Model\Product::ENTITY,
                'visibility'
            )->getId();

            $metadata = $this->getMetadataPool()->getMetadata(\Magento\Catalog\Api\Data\ProductInterface::class);
            $linkField = $metadata->getLinkField();

            $select = $this->connection->select()->from(
                ['cp' => $this->getTable('catalog_product_entity')],
                []
            )->joinInner(
                ['cpw' => $this->getTable('catalog_product_website')],
                'cpw.product_id = cp.entity_id',
                []
            )->joinInner(
                ['cpsd' => $this->getTable('catalog_product_entity_int')],
                'cpsd.' . $linkField . ' = cp.' . $linkField . ' AND cpsd.store_id = 0' .
                ' AND cpsd.attribute_id = ' .
                $statusAttributeId,
                []
            )->joinLeft(
                ['cpss' => $this->getTable('catalog_product_entity_int')],
                'cpss.' . $linkField . ' = cp.' . $linkField . ' AND cpss.attribute_id = cpsd.attribute_id' .
                ' AND cpss.store_id = ' .
                $store->getId(),
                []
            )->joinInner(
                ['cpvd' => $this->getTable('catalog_product_entity_int')],
                'cpvd.' . $linkField . ' = cp.' . $linkField . ' AND cpvd.store_id = 0' .
                ' AND cpvd.attribute_id = ' .
                $visibilityAttributeId,
                []
            )->joinLeft(
                ['cpvs' => $this->getTable('catalog_product_entity_int')],
                'cpvs.' . $linkField . ' = cp.' . $linkField . ' AND cpvs.attribute_id = cpvd.attribute_id ' .
                ' AND cpvs.store_id = ' .
                $store->getId(),
                []
            )->joinLeft(
                ['ccp' => $this->getTable('catalog_category_product')],
                'ccp.product_id = cp.entity_id',
                []
            )->where(
                'cpw.website_id = ?',
                $store->getWebsiteId()
            )->where(
                $this->connection->getIfNullSql('cpss.value', 'cpsd.value') . ' = ?',
                \Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED
            )->where(
                $this->connection->getIfNullSql('cpvs.value', 'cpvd.value') . ' IN (?)',
                [
                    \Magento\Catalog\Model\Product\Visibility::VISIBILITY_IN_CATALOG,
                    \Magento\Catalog\Model\Product\Visibility::VISIBILITY_IN_SEARCH,
                    \Magento\Catalog\Model\Product\Visibility::VISIBILITY_BOTH,
                    \Magento\Catalog\Model\Product\Visibility::VISIBILITY_NOT_VISIBLE
                ]
            )->group(
                'cp.entity_id'
            )->columns(
                [
                    'category_id' => new \Zend_Db_Expr($store->getRootCategoryId()),
                    'product_id' => 'cp.entity_id',
                    'position' => new \Zend_Db_Expr(
                        $this->connection->getCheckSql('ccp.product_id IS NOT NULL', 'ccp.position', '0')
                    ),
                    'is_parent' => new \Zend_Db_Expr(
                        $this->connection->getCheckSql('ccp.product_id IS NOT NULL', '1', '0')
                    ),
                    'store_id' => new \Zend_Db_Expr($store->getId()),
                    'visibility' => new \Zend_Db_Expr(
                        $this->connection->getIfNullSql('cpvs.value', 'cpvd.value')
                    ),
                ]
            );

            $this->productsSelects[$store->getId()] = $select;
        }

        return $this->productsSelects[$store->getId()];
    }

    /**
     * @return \Magento\Framework\EntityManager\MetadataPool
     */
    private function getMetadataPool()
    {
        if (null === $this->metadataPool) {
            $this->metadataPool = \Magento\Framework\App\ObjectManager::getInstance()
                ->get('Magento\Framework\EntityManager\MetadataPool');
        }
        return $this->metadataPool;
    }
}
