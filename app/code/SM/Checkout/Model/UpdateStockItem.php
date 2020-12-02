<?php
/**
 * Created by PhpStorm.
 * User: thelightsp
 * Date: 5/13/20
 * Time: 5:11 PM
 */

namespace SM\Checkout\Model;

/**
 * Class Split
 * @package SM\Checkout\Model
 */
class UpdateStockItem
{
    /**
     * @var \Magento\Quote\Api\CartRepositoryInterface
     */
    protected $quoteRepository;

    /**
     * @var \Magento\CatalogInventory\Api\StockRegistryInterface
     */
    protected $stockRegistryInterface;

    /**
     * @var \Magento\Quote\Model\ResourceModel\Quote\Item\CollectionFactory
     */
    private $quoteItemCollectionFactory;

    /**
     * UpdateStockItem constructor.
     * @param \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistryInterface
     * @param \Magento\Quote\Api\CartRepositoryInterface $quoteRepository
     * @param \Magento\Quote\Model\ResourceModel\Quote\Item\CollectionFactory $quoteItemCollectionFactory
     */
    public function __construct(
        \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistryInterface,
        \Magento\Quote\Api\CartRepositoryInterface $quoteRepository,
        \Magento\Quote\Model\ResourceModel\Quote\Item\CollectionFactory $quoteItemCollectionFactory
    ) {
        $this->stockRegistryInterface = $stockRegistryInterface;
        $this->quoteRepository = $quoteRepository;
        $this->quoteItemCollectionFactory = $quoteItemCollectionFactory;
    }

    /**
     * @param $quote
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function updateStock($quote)
    {
        if (($quote->getAllVisibleItems()) > 0) {
            $hasRemove = false;
            $hasUpdate = false;

            foreach ($quote->getAllVisibleItems() as $item) {
                $isOutOfStock = $this->stockRegistryInterface->getProductStockStatus($item->getProduct()->getId());

                if (!$isOutOfStock) {
                    $item->delete();
                    $hasRemove = true;
                }


                switch ($item->getProduct()->getTypeId()) {
                    case \Magento\ConfigurableProduct\Model\Product\Type\Configurable::TYPE_CODE:
                        $maxqty = $this->getConfigItemStock($item);
                        break;
                    case \Magento\Bundle\Model\Product\Type::TYPE_CODE:
                        $maxqty = $this->getBundleItemStock($item);
                        break;
                    default:
                        $maxqty = $this->getItemStock($item->getProduct()->getId());
                }

                if ($item->getQty() >= $maxqty) {
                    $item->setQty($maxqty);
                    $hasUpdate = true;
                }
            }

            if ($hasRemove || $hasUpdate) {
                $quote->setTotalsCollectedFlag(false)->collectTotals();
                $this->quoteRepository->save($quote);
            }
        }
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

    public function getBundleItemStock($quoteItem)
    {
        $arrQty = [];
        $quoteItemCollection = $this->quoteItemCollectionFactory->create();
        $quoteItemCollection->getSelect()
            ->where('parent_item_id=?', $quoteItem->getItemId());
        foreach ($quoteItemCollection as $item) {
            $minQty = $this->stockRegistryInterface->getStockItemBySku($item->getSku())->getMinQty();
            $stockQty = $this->stockRegistryInterface->getStockStatusBySku($item->getSku())->getQty();
            $arrQty[] = $stockQty - $minQty;
        }

        return min($arrQty);
    }

    /**
     * @param $item
     * @return float
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getConfigItemStock($item)
    {
        $qty = 0;
        $quoteItemCollection = $this->quoteItemCollectionFactory->create();
        $quoteItemCollection->getSelect()
            ->where('parent_item_id=?', $item->getItemId());
        if (!empty($quoteItemCollection)) {
            foreach ($quoteItemCollection as $item) {
                $qty = $this->getConfigStock($item);
            }
        } else {
            $qty = $this->getConfigStock($item);
        }
        return $qty;
    }

    /**
     * @param $item
     * @return float
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getConfigStock($item)
    {
        $minQty = $this->stockRegistryInterface->getStockItemBySku($item->getSku())->getMinQty();
        $stockQty = $this->stockRegistryInterface->getStockStatusBySku($item->getSku())->getQty();
        return $stockQty - $minQty;
    }

    /**
     * @param $productId
     * @return int
     */
    public function isOutStock($productId)
    {
        return $this->stockRegistryInterface->getProductStockStatus($productId);
    }
}
