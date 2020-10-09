<?php
/**
 * Class PlaceReservationsForSalesEvent
 * @package SM\Inventory\Plugin\Api
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @author Son Nguyen <sonnn@smartosc.com>
 *
 * Copyright © 2020 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace SM\Inventory\Plugin\Api;

use SM\Inventory\Helper\ConfigurableStock;
use Magento\InventorySalesApi\Api\Data\ItemToSellInterfaceFactory;

class PlaceReservationsForSalesEvent
{
    /**
     * @var ItemToSellInterfaceFactory
     */
    private $itemsToSellFactory;

    /**
     * @var ConfigurableStock
     */
    private $configurableStock;

    /**
     * PlaceReservationsForSalesEvent constructor.
     * @param ItemToSellInterfaceFactory $itemsToSellFactory
     * @param ConfigurableStock $configurableStock
     */
    public function __construct(
        ItemToSellInterfaceFactory $itemsToSellFactory,
        \SM\Inventory\Helper\ConfigurableStock $configurableStock
    ) {
        $this->itemsToSellFactory = $itemsToSellFactory;
        $this->configurableStock = $configurableStock;
    }

    /**
     * @param \Magento\InventorySalesApi\Api\PlaceReservationsForSalesEventInterface $subject
     * @param \Magento\InventorySalesApi\Api\Data\ItemToSellInterface[] $items
     * @param \Magento\InventorySalesApi\Api\Data\SalesChannelInterface $salesChannel
     * @param \Magento\InventorySalesApi\Api\Data\SalesEventInterface $salesEvent
     * @return array
     */
    public function beforeExecute(
        \Magento\InventorySalesApi\Api\PlaceReservationsForSalesEventInterface $subject,
        array $items,
        \Magento\InventorySalesApi\Api\Data\SalesChannelInterface $salesChannel,
        \Magento\InventorySalesApi\Api\Data\SalesEventInterface $salesEvent
    ) {
        $itemsToSell = [];

        foreach ($items as $item) {
            if ($this->configurableStock->checkIsConfigurableStockSku($item->getSku(), $item->getQuantity())) {
                $itemsToSell[] = $this->itemsToSellFactory->create([
                    'sku' => $this->configurableStock->getBaseSku($item->getSku()),
                    'qty' => $this->configurableStock->getBaseQty()
                ]);
            }
        }

        $items = array_merge($items, $itemsToSell);
        return array($items, $salesChannel, $salesEvent);
    }
}
