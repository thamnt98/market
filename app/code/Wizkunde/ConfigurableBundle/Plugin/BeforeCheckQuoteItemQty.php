<?php

namespace Wizkunde\ConfigurableBundle\Plugin;

use Magento\CatalogInventory\Api\Data\StockItemInterface;

/**
 * Class BeforeCheckQuoteItemQty
 * @package Wizkunde\ConfigurableBundle\Plugin
 */
class BeforeCheckQuoteItemQty
{
    /**
     * @param \Magento\CatalogInventory\Model\StockStateProvider $provider
     * @param StockItemInterface $stockItem
     * @param $qty
     * @param $summaryQty
     * @param $origQty
     * @return array
     */
    public function beforeCheckQuoteItemQty(
        \Magento\CatalogInventory\Model\StockStateProvider $provider,
        StockItemInterface $stockItem,
        $qty,
        $summaryQty,
        $origQty
    ) {
    
        if ($stockItem->getIsChildItem() == 1) {
            $stockItem->setMinSaleQty(0);
            $stockItem->setUseConfigMinSaleQty(0);
        }

        return [$stockItem, $qty, $summaryQty, $origQty];
    }
}
