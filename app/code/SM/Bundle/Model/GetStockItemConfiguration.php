<?php

namespace SM\Bundle\Model;

use Magento\InventoryApi\Model\IsProductAssignedToStockInterface;
use Magento\InventoryCatalogApi\Api\DefaultStockProviderInterface;
use Magento\InventoryConfiguration\Model\GetLegacyStockItem;
use Magento\InventoryConfiguration\Model\StockItemConfigurationFactory;
use Magento\InventoryConfigurationApi\Model\IsSourceItemManagementAllowedForSkuInterface;
use Magento\InventoryConfigurationApi\Api\Data\StockItemConfigurationInterface;
use Magento\InventoryConfigurationApi\Exception\SkuIsNotAssignedToStockException;

class GetStockItemConfiguration extends \Magento\InventoryConfiguration\Model\GetStockItemConfiguration
{
    /**
     * @var GetLegacyStockItem
     */
    private $getLegacyStockItem;
    /**
     * @var StockItemConfigurationFactory
     */
    private $stockItemConfigurationFactory;
    /**
     * @var IsProductAssignedToStockInterface
     */
    private $isProductAssignedToStock;
    /**
     * @var DefaultStockProviderInterface
     */
    private $defaultStockProvider;
    /**
     * @var IsSourceItemManagementAllowedForSkuInterface
     */
    private $isSourceItemManagementAllowedForSku;
    /**
     * @var \Magento\InventoryCatalogApi\Model\GetProductTypesBySkusInterface
     */
    private $getProductTypesBySkus;


    public function __construct(
        GetLegacyStockItem $getLegacyStockItem,
        StockItemConfigurationFactory $stockItemConfigurationFactory,
        IsProductAssignedToStockInterface $isProductAssignedToStock,
        DefaultStockProviderInterface $defaultStockProvider,
        IsSourceItemManagementAllowedForSkuInterface $isSourceItemManagementAllowedForSku,
        \Magento\InventoryCatalogApi\Model\GetProductTypesBySkusInterface $getProductTypesBySkus

    ) {
        $this->getLegacyStockItem = $getLegacyStockItem;
        $this->stockItemConfigurationFactory = $stockItemConfigurationFactory;
        $this->isProductAssignedToStock = $isProductAssignedToStock;
        $this->defaultStockProvider = $defaultStockProvider;
        $this->isSourceItemManagementAllowedForSku = $isSourceItemManagementAllowedForSku;
        $this->getProductTypesBySkus = $getProductTypesBySkus;
        parent::__construct($getLegacyStockItem, $stockItemConfigurationFactory, $isProductAssignedToStock,
            $defaultStockProvider, $isSourceItemManagementAllowedForSku);
    }

    /**
     * @inheritdoc
     */
    public function execute(string $sku, int $stockId): StockItemConfigurationInterface
    {
        $productType = $this->getProductTypesBySkus->execute([$sku]);
        $typeId = '';
        if (isset($productType[$sku])) {
            $typeId = $productType[$sku];
        }
        if ($typeId == 'configurable') {
            return $this->stockItemConfigurationFactory->create(
                [
                    'stockItem' => $this->getLegacyStockItem->execute($sku)
                ]
            );
        } else {
            if ($this->defaultStockProvider->getId() !== $stockId
                && true === $this->isSourceItemManagementAllowedForSku->execute($sku)
                && false === $this->isProductAssignedToStock->execute($sku, $stockId)) {
                throw new SkuIsNotAssignedToStockException(
                    __('The requested sku is not assigned to given stock.')
                );
            }
        }


        return $this->stockItemConfigurationFactory->create(
            [
                'stockItem' => $this->getLegacyStockItem->execute($sku)
            ]
        );
    }
}
