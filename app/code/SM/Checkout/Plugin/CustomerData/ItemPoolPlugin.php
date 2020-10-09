<?php

namespace SM\Checkout\Plugin\CustomerData;

use Magento\Checkout\CustomerData\ItemPool;

class ItemPoolPlugin
{

    /**
     * @var \Magento\CatalogInventory\Api\StockRegistryInterface
     */
    protected $stockRegistryInterface;
    /**
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    private $productRepository;
    /**
     * @var \Magento\Quote\Model\ResourceModel\Quote\Item\CollectionFactory
     */
    private $quoteItemCollectionFactory;
    /**
     * @var \Magento\Framework\Pricing\PriceCurrencyInterface
     */
    private $priceCurrency;

    protected $bundlePrice = 0;

    /**
     * CartItem constructor.
     * @param \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistryInterface
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
     * @param \Magento\Quote\Model\ResourceModel\Quote\Item\CollectionFactory $quoteItemCollectionFactory
     * @param \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency
     */
    public function __construct(
        \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistryInterface,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\Quote\Model\ResourceModel\Quote\Item\CollectionFactory $quoteItemCollectionFactory,
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency
    ) {
        $this->stockRegistryInterface = $stockRegistryInterface;
        $this->productRepository = $productRepository;
        $this->quoteItemCollectionFactory = $quoteItemCollectionFactory;
        $this->priceCurrency = $priceCurrency;
    }

    public function afterGetItemData(ItemPool $subject, $result)
    {
        switch ($result['product_type']) {
            case \Magento\ConfigurableProduct\Model\Product\Type\Configurable::TYPE_CODE:
                $productStock =  $this->getConfigItemStock($result);
                break;
            case \Magento\Bundle\Model\Product\Type::TYPE_CODE:
                $productStock = $this->getBundleItemStock($result, $result['product_id']);
                $result['product_price_value'] = $this->bundlePrice;
                break;
            default:
                $productStock = $this->getItemStock($result['product_id']);
        }

        $result['row_total'] = $this->priceCurrency->convertAndFormat($result['product_price_value'] * $result['qty']);
        $result['product_stock'] = $productStock;
        return $result;
    }

    /**
     * @param $productId
     * @return float
     */
    public function getItemStock($productId)
    {
        $stockItem = $this->stockRegistryInterface->getStockItem($productId);
        $stockQty = $this->stockRegistryInterface->getStockStatus($productId)->getQty();
        $minStockQty = $stockItem->getMinQty();

        return $stockQty - $minStockQty;
    }

    public function getBundleItemStock($result, $productId)
    {
        $arrQty = [];
        $quoteItemCollection = $this->quoteItemCollectionFactory->create();
        $quoteItemCollection->getSelect()
            ->where('parent_item_id=?', $result['item_id']);
        $this->bundlePrice = 0;
        foreach ($quoteItemCollection as $item) {
            $minQty = $this->stockRegistryInterface->getStockItemBySku($item->getSku())->getMinQty();
            $stockQty = $this->stockRegistryInterface->getStockStatusBySku($item->getSku())->getQty();
            $arrQty[] = $stockQty - $minQty;
            $this->bundlePrice += $item->getPrice()*$item->getQty();
        }

        return min($arrQty);
    }

    public function getConfigItemStock($result)
    {
        $qty = 0;
        $quoteItemCollection = $this->quoteItemCollectionFactory->create();
        $quoteItemCollection->getSelect()
            ->where('parent_item_id=?', $result['item_id']);
        if (!empty($quoteItemCollection)) {
            foreach ($quoteItemCollection as $item) {
                $qty = $this->getConfigStock($item->getSku());
            }
        } else {
            $qty = $this->getConfigStock($result['product_sku']);
        }
        return $qty;
    }


    protected function getConfigStock($item)
    {
        $minQty = $this->stockRegistryInterface->getStockItemBySku($item)->getMinQty();
        $stockQty = $this->stockRegistryInterface->getStockStatusBySku($item)->getQty();
        return $stockQty - $minQty;
    }
}
