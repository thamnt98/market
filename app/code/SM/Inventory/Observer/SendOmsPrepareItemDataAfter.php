<?php

namespace SM\Inventory\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class SendOmsPrepareItemDataAfter implements ObserverInterface
{
    /**
     * @var \SM\Inventory\Helper\ConfigurableStock
     */
    private $configurableStock;

    /**
     * Split constructor.
     * @param \SM\Inventory\Helper\ConfigurableStock $configurableStock
     */
    public function __construct(
        \SM\Inventory\Helper\ConfigurableStock $configurableStock
    ) {
        $this->configurableStock = $configurableStock;
    }

    /**
     * @inheritDoc
     */
    public function execute(Observer $observer)
    {
        $itemData = $observer->getData('itemData');
        $itemData['sku_basic'] = $this->configurableStock->getSkuBasic($itemData['sku']);
    }
}
