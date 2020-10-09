<?php
/**
 * Class SourceItemsSave
 * @package SM\Inventory\Plugin\Api
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @author Son Nguyen <sonnn@smartosc.com>
 *
 * Copyright © 2020 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace SM\Inventory\Plugin\Api;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\InventoryApi\Api\Data\SourceItemInterface;
use Magento\InventoryApi\Api\SourceItemsSaveInterface;
use Magento\InventorySourceDeductionApi\Model\GetSourceItemBySourceCodeAndSku;
use Magento\Setup\Exception;
use SM\Inventory\Helper\ConfigurableStock;

class SourceItemsSave
{
    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
     */
    private $productCollectionFactory;

    /**
     * @var \Magento\InventoryApi\Api\Data\SourceItemInterfaceFactory
     */
    private $sourceItemFactory;

    /**
     * @var \Magento\Inventory\Model\SourceItem
     */
    private $item;

    /**
     * @var \Magento\Framework\DataObject[]
     */
    private $otherVariants;

    /**
     * @var \SM\Inventory\Helper\ConfigurableStock
     */
    private $configurableStock;

    /**
     * @var string|null
     */
    private $currentSku;

    /**
     * @var GetSourceItemBySourceCodeAndSku
     */
    private $getSourceItemBySourceCodeAndSku;

    /**
     * SourceItemsSave constructor.
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory
     * @param \Magento\InventoryApi\Api\Data\SourceItemInterfaceFactory $sourceItemFactory
     * @param \SM\Inventory\Helper\ConfigurableStock $configurableStock
     * @param GetSourceItemBySourceCodeAndSku $getSourceItemBySourceCodeAndSku
     */
    public function __construct(
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Magento\InventoryApi\Api\Data\SourceItemInterfaceFactory $sourceItemFactory,
        \SM\Inventory\Helper\ConfigurableStock $configurableStock,
        GetSourceItemBySourceCodeAndSku $getSourceItemBySourceCodeAndSku
    ) {
        $this->productCollectionFactory = $productCollectionFactory;
        $this->sourceItemFactory = $sourceItemFactory;
        $this->configurableStock = $configurableStock;
        $this->getSourceItemBySourceCodeAndSku = $getSourceItemBySourceCodeAndSku;
    }

    /**
     * @param SourceItemsSaveInterface $subject
     * @param SourceItemInterface[] $sourceItems
     * @return \Magento\InventoryApi\Api\Data\SourceItemInterface[]
     * @throws LocalizedException
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function beforeExecute($subject, array $sourceItems): array
    {
        foreach ($sourceItems as $item) {
            $this->currentSku = $item->getSku();
            $this->item = $item;
            if ($this->configurableStock->checkIsConfigurableBaseSku($this->currentSku)) {
                $sourceItems = array_merge($sourceItems, $this->getVariantsItemsStock());
            } elseif ($this->configurableStock->checkIsConfigurableStockSku($this->currentSku, $item->getQuantity())) {
                $sourceItems = array_merge($sourceItems, $this->getBaseItemStock());
            }
        }

        return array($sourceItems);
    }

    /**
     * @return \Magento\Framework\DataObject[]
     */
    private function getOtherVariantsProducts()
    {
        if (empty($this->otherVariants)) {
            /**
             * @var \Magento\Catalog\Model\ResourceModel\Product\Collection $productCollection
             */
            $productCollection = $this->productCollectionFactory->create();
            $productCollection->addAttributeToFilter('type_id', ConfigurableStock::SIMPLE_PRODUCT_TYPE)
                ->addAttributeToFilter(SourceItemInterface::SKU, ['like' => "{$this->getBaseSkuPreFix()}%"])
                ->addAttributeToFilter(SourceItemInterface::SKU, ['neq' => $this->currentSku]);

            $this->otherVariants = $productCollection->getItems();
        }

        return $this->otherVariants;
    }

    /**
     * @return string
     */
    private function getBaseSkuPreFix()
    {
        $position = strlen($this->currentSku) - 3;
        return substr($this->currentSku, 0, $position);
    }

    /**
     * @return array
     */
    private function getVariantsItemsStock()
    {
        $items = [];
        if ($products = $this->getOtherVariantsProducts()) {
            foreach ($products as $product) {
                try {
                    $data = $this->item->getData();
                    $data['source_item_id'] = null;
                    $data[SourceItemInterface::SKU] = $product->getSku();
                    $quantity = $this->calculateItemQuantity($data);
                    $data[SourceItemInterface::QUANTITY] = $quantity;
                    if ($quantity <= 0) {
                        $data[SourceItemInterface::STATUS] = SourceItemInterface::STATUS_OUT_OF_STOCK;
                    }
                    $items[] = $this->sourceItemFactory->create()->setData($data);
                } catch (\Exception $e) {
                    continue;
                }

            }
        }

        return $items;
    }

    /**
     * @param $data
     * @return int
     * @throws \Exception
     */
    private function calculateItemQuantity($data)
    {
        $sku = explode($this->getBaseSkuPreFix(), $data[SourceItemInterface::SKU]);
        if (!isset($sku[1]) || !is_numeric($sku[1] || $sku[1] == 0)) {
            throw new \Exception(__('Can\t get quantity from this sku %1', $sku));
        }
        return intval($data[SourceItemInterface::QUANTITY] / (int)$sku[1]);
    }

    /**
     * @return mixed
     * @throws LocalizedException
     */
    private function getBaseItemStock()
    {
        $sourceItems = [];
        $data = $this->item->getData();
        $qtyIsChange = $this->item->getOrigData(SourceItemInterface::QUANTITY) - $data[SourceItemInterface::QUANTITY];
        $itemSku = $this->configurableStock->getBaseSku($data[SourceItemInterface::SKU]);

        if ($itemSku) {
            if ($qtyIsChange > 0) {
                $sourceItems[] = $this->getSubtractItemQty($data, $itemSku);
            } elseif ($qtyIsChange < 0) {
                $sourceItems[] = $this->getAdditionalItemQty($data, $itemSku);
            }
        }

        return $sourceItems;
    }

    /**
     * @param $data
     * @param $itemSku
     * @return SourceItemInterface
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    private function getSubtractItemQty($data, $itemSku)
    {
        $sourceItem = $this->getSourceItemBySourceCodeAndSku->execute(
            $data[SourceItemInterface::SOURCE_CODE],
            $itemSku
        );
        $deductQty = $sourceItem->getQuantity() - $this->configurableStock->getBaseQty();
        if ($deductQty >= 0) {
            $sourceItem->setQuantity($sourceItem->getQuantity() - $this->configurableStock->getBaseQty());
            if ($sourceItem->getQuantity() <= 0) {
                $sourceItem->setStatus(SourceItemInterface::STATUS_OUT_OF_STOCK);
            }
            $sourceItems[] = $sourceItem;
        } else {
            throw new LocalizedException(
                __('Not all of your products are available in the requested quantity.')
            );
        }

        return $sourceItem;
    }

    /**
     * @param $data
     * @param string $itemSku
     * @return SourceItemInterface
     * @throws NoSuchEntityException
     */
    private function getAdditionalItemQty($data, $itemSku)
    {
        $sourceItem = $this->getSourceItemBySourceCodeAndSku->execute(
            $data[SourceItemInterface::SOURCE_CODE],
            $itemSku
        );
        $sourceItem->setQuantity($sourceItem->getQuantity() + $this->configurableStock->getBaseQty());
        $sourceItem->setStatus(SourceItemInterface::STATUS_IN_STOCK);

        return $sourceItem;
    }
}
