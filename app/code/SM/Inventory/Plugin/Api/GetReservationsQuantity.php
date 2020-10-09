<?php
/**
 * Class GetReservationsQuantity
 * @package SM\Inventory\Plugin\Api
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @author Son Nguyen <sonnn@smartosc.com>
 *
 * Copyright © 2020 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */


namespace SM\Inventory\Plugin\Api;

use Magento\InventoryReservationsApi\Model\ReservationInterface;
use SM\Inventory\Helper\ConfigurableStock;

class GetReservationsQuantity
{
    /**
     * @var \SM\Inventory\Helper\ConfigurableStock
     */
    private $configurableStock;

    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    private $resource;

    /**
     * GetReservationsQuantity constructor.
     * @param \Magento\Framework\App\ResourceConnection $resource
     * @param ConfigurableStock $configurableStock
     */
    public function __construct(
        \Magento\Framework\App\ResourceConnection $resource,
        \SM\Inventory\Helper\ConfigurableStock $configurableStock
    ) {
        $this->configurableStock = $configurableStock;
        $this->resource = $resource;
    }

    /**
     * @param \Magento\InventoryReservationsApi\Model\GetReservationsQuantityInterface $subject
     * @param $result
     * @param string $sku
     * @param int $stockId
     * @return float
     */
    public function afterExecute(
        \Magento\InventoryReservationsApi\Model\GetReservationsQuantityInterface $subject,
        $result,
        string $sku,
        int $stockId
    ): float {
        if ($this->configurableStock->checkIsConfigurableStockSku($sku, 1)) {
            $baseSkuReservationsQuantity = $this->getBaseSkuReservationsQuantity(
                $this->configurableStock->getBaseSku($sku),
                $stockId
            );
            $result = ceil(
                -($baseSkuReservationsQuantity / $this->configurableStock->getConfigurableSkuSuffix())
            );

            return -$result;
        }

        return $result;
    }

    /**
     * @param $sku
     * @param $stockId
     * @return float
     */
    private function getBaseSkuReservationsQuantity($sku, $stockId)
    {
        $connection = $this->resource->getConnection();
        $reservationTable = $this->resource->getTableName('inventory_reservation');

        $select = $connection->select()
            ->from($reservationTable, [ReservationInterface::QUANTITY => 'SUM(' . ReservationInterface::QUANTITY . ')'])
            ->where(ReservationInterface::SKU . ' = ?', $sku)
            ->where(ReservationInterface::STOCK_ID . ' = ?', $stockId)
            ->limit(1);

        $reservationQty = $connection->fetchOne($select);
        if (false === $reservationQty) {
            $reservationQty = 0;
        }
        return (float)$reservationQty;
    }

    /**
     * @param string $sku
     * @return float
     */
    private function getItemQtyFromSku(string $sku)
    {
        $sku = explode(ConfigurableStock::BASE_CONFIGURABLE_SKU_SUB_FIX, $sku);

        if (count($sku) == 2) {
            return $sku[1];
        }

        return 0;
    }
}
