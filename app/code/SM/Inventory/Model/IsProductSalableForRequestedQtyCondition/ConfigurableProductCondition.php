<?php
/**
 * Class ConfigurableProductCondition
 * @package SM\Inventory\Model\IsProductSalableForRequestedQtyCondition
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @author Son Nguyen <sonnn@smartosc.com>
 *
 * Copyright © 2020 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace SM\Inventory\Model\IsProductSalableForRequestedQtyCondition;

use Magento\InventoryConfigurationApi\Api\GetStockItemConfigurationInterface;
use Magento\InventoryReservationsApi\Model\GetReservationsQuantityInterface;
use Magento\InventorySales\Model\IsProductSalableForRequestedQtyCondition\IsSalableWithReservationsCondition;
use Magento\InventorySalesApi\Api\Data\ProductSalableResultInterface;
use Magento\InventorySalesApi\Api\IsProductSalableForRequestedQtyInterface;
use Magento\InventorySalesApi\Model\GetStockItemDataInterface;
use Magento\InventorySalesApi\Api\Data\ProductSalableResultInterfaceFactory;
use Magento\InventorySalesApi\Api\Data\ProductSalabilityErrorInterfaceFactory;

class ConfigurableProductCondition extends IsSalableWithReservationsCondition implements IsProductSalableForRequestedQtyInterface
{
    /**
     * @var \SM\Inventory\Helper\ConfigurableStock
     */
    private $configurableStock;

    /**
     * @var ProductSalableResultInterfaceFactory
     */
    private $productSalableResultFactory;


    /**
     * ConfigurableProductCondition constructor.
     * @param GetStockItemDataInterface $getStockItemData
     * @param GetReservationsQuantityInterface $getReservationsQuantity
     * @param GetStockItemConfigurationInterface $getStockItemConfiguration
     * @param ProductSalabilityErrorInterfaceFactory $productSalabilityErrorFactory
     * @param ProductSalableResultInterfaceFactory $productSalableResultFactory
     * @param \SM\Inventory\Helper\ConfigurableStock $configurableStock
     */
    public function __construct(
        GetStockItemDataInterface $getStockItemData,
        GetReservationsQuantityInterface $getReservationsQuantity,
        GetStockItemConfigurationInterface $getStockItemConfiguration,
        ProductSalabilityErrorInterfaceFactory $productSalabilityErrorFactory,
        ProductSalableResultInterfaceFactory $productSalableResultFactory,
        \SM\Inventory\Helper\ConfigurableStock $configurableStock
    ) {
        $this->configurableStock = $configurableStock;
        parent::__construct(
            $getStockItemData,
            $getReservationsQuantity,
            $getStockItemConfiguration,
            $productSalabilityErrorFactory,
            $productSalableResultFactory
        );
        $this->productSalableResultFactory = $productSalableResultFactory;
    }

    /**
     * @inheritdoc
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function execute(string $sku, int $stockId, float $requestedQty): ProductSalableResultInterface
    {
        if ($this->configurableStock->checkIsConfigurableStockSku($sku, $requestedQty)) {
            $sku = $this->configurableStock->getBaseSku($sku);
            return parent::execute($sku, $stockId, $this->configurableStock->getBaseQty());
        }

        return $this->productSalableResultFactory->create(['errors' => []]);
    }
}
