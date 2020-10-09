<?php
/**
 * Class SelectBuilder
 * @package SM\InventoryBundleProductIndexer\Indexer
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @author Son Nguyen <sonnn@smartosc.com>
 *
 * Copyright © 2020 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace SM\InventoryBundleProductIndexer\Indexer;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\DB\Select;
use Magento\Framework\EntityManager\MetadataPool;
use Magento\InventoryIndexer\Indexer\IndexStructure;
use Magento\InventoryIndexer\Indexer\InventoryIndexer;
use Magento\InventoryMultiDimensionalIndexerApi\Model\Alias;
use Magento\InventoryMultiDimensionalIndexerApi\Model\IndexNameBuilder;
use Magento\InventoryMultiDimensionalIndexerApi\Model\IndexNameResolverInterface;

class SelectBuilder
{
    /**
     * @var ResourceConnection
     */
    private $resourceConnection;

    /**
     * @var IndexNameBuilder
     */
    private $indexNameBuilder;

    /**
     * @var IndexNameResolverInterface
     */
    private $indexNameResolver;

    /**
     * @var MetadataPool
     */
    private $metadataPool;
    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
     */
    private $prodCollectionFactory;
    /**
     * @var \Magento\InventorySalesAdminUi\Model\ResourceModel\GetAssignedStockIdsBySku
     */
    private $assignedStockIdsBySku;
    /**
     * @var \Magento\InventorySalesApi\Model\GetStockItemDataInterface
     */
    private $stockItemData;
    /**
     * @var \Magento\CatalogInventory\Api\StockRegistryInterface
     */
    private $stockRegistry;
    /**
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    private $productRepository;
    /**
     * @var \Magento\InventoryApi\Api\GetSourceItemsBySkuInterface
     */
    private $sourceItemsBySku;

    /**
     * @param ResourceConnection $resourceConnection
     * @param IndexNameBuilder $indexNameBuilder
     * @param IndexNameResolverInterface $indexNameResolver
     * @param MetadataPool $metadataPool
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $prodCollectionFactory
     * @param \Magento\InventorySalesAdminUi\Model\ResourceModel\GetAssignedStockIdsBySku $assignedStockIdsBySku
     * @param \Magento\InventorySalesApi\Model\GetStockItemDataInterface $stockItemData
     * @param \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
     * @param \Magento\InventoryApi\Api\GetSourceItemsBySkuInterface $sourceItemsBySku
     */
    public function __construct(
        ResourceConnection $resourceConnection,
        IndexNameBuilder $indexNameBuilder,
        IndexNameResolverInterface $indexNameResolver,
        MetadataPool $metadataPool,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $prodCollectionFactory,
        \Magento\InventorySalesAdminUi\Model\ResourceModel\GetAssignedStockIdsBySku $assignedStockIdsBySku,
        \Magento\InventorySalesApi\Model\GetStockItemDataInterface $stockItemData,
        \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\InventoryApi\Api\GetSourceItemsBySkuInterface $sourceItemsBySku
    ) {
        $this->resourceConnection = $resourceConnection;
        $this->indexNameBuilder = $indexNameBuilder;
        $this->indexNameResolver = $indexNameResolver;
        $this->metadataPool = $metadataPool;
        $this->prodCollectionFactory = $prodCollectionFactory;
        $this->assignedStockIdsBySku = $assignedStockIdsBySku;
        $this->stockItemData = $stockItemData;
        $this->stockRegistry = $stockRegistry;
        $this->productRepository = $productRepository;
        $this->sourceItemsBySku = $sourceItemsBySku;
    }

    /**
     * Prepare select.
     *
     * @param int $stockId
     * @return Select
     * @throws \Exception
     */
    public function execute(int $stockId): Select
    {
        $connection = $this->resourceConnection->getConnection();

        $indexName = $this->indexNameBuilder
            ->setIndexId(InventoryIndexer::INDEXER_ID)
            ->addDimension('stock_', (string)$stockId)
            ->setAlias(Alias::ALIAS_MAIN)
            ->build();

        $indexTableName = $this->indexNameResolver->resolveName($indexName);
        //Insert Stock For Bundle
        $data = [];
        /** @var \Magento\Catalog\Model\ResourceModel\Product\Collection $productCollection */
        $productCollection = $this->prodCollectionFactory->create();
        $productCollection->addAttributeToSelect('*')->addAttributeToFilter('type_id', ['eq' => 'bundle']);
        foreach ($productCollection as $product) {
            $select = $connection->select()
                ->from(
                    ['stock' => $indexTableName]
                )->where('sku = ?', explode(',', $product->getSku()));

            if ($connection->fetchOne($select)) {
                if (!$this->checkShowProduct($product)) {
                    $connection->delete($indexTableName, ['sku=?' => $product->getSku()]);
                }
                continue;
            }
            if (!$this->checkShowProduct($product)) {
                continue;
            }
            $options = $product->getTypeInstance(true)->getSelectionsCollection(
                $product->getTypeInstance(true)->getOptionsIds($product),
                $product
            )->getItems();
            $stock = [];
            $skus[] = $product->getSku();
            foreach ($options as $option) {
                $stockIds = $this->assignedStockIdsBySku->execute($option->getSku());
                if (count($stockIds)) {
                    foreach ($stockIds as $stockId) {
                        $stockData = $this->stockItemData->execute($option->getSku(), $stockId);
                        $stockDataArr = [];
                        if (is_array($stockData) && array_key_exists('quantity', $stockData)) {
                            $stockDataArr[] = (int)$this->stockItemData->execute($option->getSku(), $stockId)['quantity'];
                        } else {
                            $stockDataArr[] = 0;
                        }
                    }
                    $stock[] = max($stockDataArr);
                } else {
                    $items = $option->getTypeInstance()->getUsedProducts($option);
                    foreach ($items as $item) {
                        $stockIds = $this->assignedStockIdsBySku->execute($item->getSku());
                        if (count($stockIds)) {
                            foreach ($stockIds as $stockId) {
                                $stockData = $this->stockItemData->execute($item->getSku(), $stockId);
                                $stockDataArr = [];
                                if (is_array($stockData) && array_key_exists('quantity', $stockData)) {
                                    $stockDataArr[] = (int)$this->stockItemData->execute($item->getSku(), $stockId)['quantity'];
                                } else {
                                    $stockDataArr[] = 0;
                                }
                            }
                            $stock[] = max($stockDataArr);
                        }
                    }

                }
            }
            if (!empty($stock) && min($stock) > 0) {
                $data[] = [
                    $product->getSku(),
                    min($stock),
                    1
                ];
            }
        }
        $columns = [
            'sku',
            'quantity',
            'is_salable'
        ];
        if(!empty($data)) {
            $connection->insertArray($indexTableName, $columns, $data);
        }
        $metadata = $this->metadataPool->getMetadata(ProductInterface::class);
        $linkField = $metadata->getLinkField();

        return $connection->select()
            ->from(
                ['stock' => $indexTableName],
                [
                    IndexStructure::SKU => 'stock.sku',
                    IndexStructure::QUANTITY => 'SUM(stock.quantity)',
                    IndexStructure::IS_SALABLE => 'MAX(stock.is_salable)',
                ]
            )->joinInner(
                ['product_entity' => $this->resourceConnection->getTableName('catalog_product_entity')],
                'product_entity.sku = stock.sku',
                []
            )->joinInner(
                ['parent_link' => $this->resourceConnection->getTableName('catalog_product_bundle_selection')],
                'parent_link.product_id = product_entity.entity_id',
                []
            )->joinInner(
                ['parent_product_entity' => $this->resourceConnection->getTableName('catalog_product_entity')],
                'parent_product_entity.' . $linkField . ' = parent_link.parent_product_id AND parent_product_entity.sku in (select sku from ' . $indexTableName . ')',
                []
            )
            ->group(['stock.sku']);
    }

    public function checkShowProduct($product)
    {
        if ($product->getTypeId() == 'bundle') {
            $options = $product->getTypeInstance(true)->getSelectionsCollection(
                $product->getTypeInstance(true)->getOptionsIds($product),
                $product
            )->getItems();
            $stockArr = [];
            $count = 0;
            foreach ($options as $option) {
                $productOp = $this->productRepository->get($option->getSku());
                if ($productOp->getStatus() != '1') {
                    return false;
                }
                if ($option->getTypeId() == 'configurable') {
                    if (is_array($productOp->getQuantityAndStockStatus()) && !$productOp->getQuantityAndStockStatus()['is_in_stock']) {
                        return false;
                    }
                    $listChildProds = $productOp->getExtensionAttributes()->getConfigurableProductLinks();
                    $childSource = [];
                    $j = 0;
                    foreach ($listChildProds as $child) {
                        $emptyStock = false;
                        $product = $this->productRepository->getById($child);
                        $sources = $this->sourceItemsBySku->execute($product->getSku());

                        foreach ($sources as $source) {
                            if ($source->getStatus() > 0 && $source->getQuantity() > 0) {
                                $childSource[$j][] = $source->getSourceCode();
                            } else {
                                $emptyStock = true;
                            }
                        }
                        if ($emptyStock && !isset($childSource[$j])) {
                            return false;
                        }
                        $j++;
                    }
                    $stocksChild = [];
                    for ($i = 0; $i < sizeof($childSource) - 1; $i++) {
                        if ($i == 0) {
                            $stocksChild = array_intersect($childSource[$i], $childSource[$i + 1]);
                        } else {
                            $stocksChild = array_intersect($childSource[$i], $childSource[$i + 1], $stocksChild);
                        }
                    }
                    if (empty($stocksChild)) {
                        return false;
                    } else {
                        $stockArr[$count] = $stocksChild;
                    }

                } else {
                    $sources = $this->sourceItemsBySku->execute($option->getSku());
                    $emptyStock = false;
                    foreach ($sources as $source) {
                        if ($source->getStatus() > 0 && $source->getQuantity() > 0) {
                            $stockArr[$count][] = $source->getSourceCode();
                        } else {
                            $emptyStock = true;
                        }
                    }
                    if ($emptyStock && empty($stockArr[$count])) {
                        return false;
                    }
                }

                $count++;
            }
            $stocks = [];
            if (empty($stockArr)) {
                return false;
            } else {
                for ($i = 0; $i < sizeof($stockArr) - 1; $i++) {
                    if ($i == 0) {
                        $stocks = array_intersect($stockArr[$i], $stockArr[$i + 1]);
                    } else {
                        $stocks = array_intersect($stockArr[$i], $stockArr[$i + 1], $stocks);
                    }
                }
            }
            if (empty($stocks)) {
                return false;
            }
        }
        return true;
    }
}
