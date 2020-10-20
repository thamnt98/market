<?php
/**
 * Created by PhpStorm.
 * User: thelightsp
 * Date: 5/25/20
 * Time: 5:16 PM
 */

namespace SM\Checkout\ViewModel;

use Magento\Framework\DataObject;
use Magento\Framework\View\Element\Block\ArgumentInterface;

/**
 * Class GetItemStock
 * @package SM\Checkout\ViewModel
 */
class CartItem extends DataObject implements ArgumentInterface
{
    /**
     * @var \Magento\CatalogInventory\Api\StockRegistryInterface
     */
    protected $stockRegistryInterface;

    /**
     * @var \SM\Catalog\Helper\Data
     */
    protected $catalogHelper;

    /**
     * @var \Amasty\Promo\Helper\Item
     */
    protected $freeGiftHelper;

    /**
     * @var \Magento\Quote\Model\ResourceModel\Quote\Item\CollectionFactory
     */
    private $quoteItemCollectionFactory;

    /**
     * CartItem constructor.
     *
     * @param \Amasty\Promo\Helper\Item                                       $freeGiftHelper
     * @param \Magento\CatalogInventory\Api\StockRegistryInterface            $stockRegistryInterface
     * @param \SM\Catalog\Helper\Data                                         $catalogHelper
     * @param \Magento\Quote\Model\ResourceModel\Quote\Item\CollectionFactory $quoteItemCollectionFactory
     * @param array                                                           $data
     */
    public function __construct(
        \Amasty\Promo\Helper\Item $freeGiftHelper,
        \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistryInterface,
        \SM\Catalog\Helper\Data $catalogHelper,
        \Magento\Quote\Model\ResourceModel\Quote\Item\CollectionFactory $quoteItemCollectionFactory,
        array $data = []
    ) {
        $this->stockRegistryInterface = $stockRegistryInterface;
        $this->catalogHelper = $catalogHelper;
        $this->quoteItemCollectionFactory = $quoteItemCollectionFactory;
        parent::__construct($data);
        $this->freeGiftHelper = $freeGiftHelper;
    }

    /**
     * @param $productId
     * @return float
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getItemStock($productId, $item = null)
    {
        $stockItem = $this->stockRegistryInterface->getStockItem($productId);
        switch ($stockItem->getTypeId()) {
            case \Magento\ConfigurableProduct\Model\Product\Type\Configurable::TYPE_CODE:
                return $this->getConfigItemStock($item);
            case \Magento\Bundle\Model\Product\Type::TYPE_CODE:
                return $this->getBundleItemStock($productId, $item);
            default:
                $stockQty = $this->stockRegistryInterface->getStockStatus($productId)->getQty();
                $minStockQty = $stockItem->getMinQty();
                return $stockQty - $minStockQty;
        }
    }

    public function getBundleItemStock($productId, $item)
    {
        $arrQty = [];
        $quoteItemCollection = $this->quoteItemCollectionFactory->create();
        $quoteItemCollection->getSelect()
            ->where('parent_item_id=?', $item->getItemId());
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
    protected function getConfigStock($item)
    {
        $minQty = $this->stockRegistryInterface->getStockItemBySku($item->getSku())->getMinQty();
        $stockQty = $this->stockRegistryInterface->getStockStatusBySku($item->getSku())->getQty();
        return $stockQty - $minQty;
    }

    /**
     * @param $product
     * @return int|null
     */
    public function getDiscountPercent($product)
    {
        switch ($product->getProduct()->getTypeId()) {
            case \Magento\ConfigurableProduct\Model\Product\Type\Configurable::TYPE_CODE:
                return $this->catalogHelper->getDiscountPercent($this->getDiscountPercentConfig($product));
            case \Magento\Bundle\Model\Product\Type::TYPE_CODE:
                return $this->getDiscountPercentBundle($product);
            default:
                return $this->catalogHelper->getDiscountPercent($product->getProduct());
        }
    }

    /**
     * @param $product
     * @return \Magento\Catalog\Model\Product
     */
    public function getDiscountPercentConfig($product)
    {
        $childItem = null;
        if (empty($product->getChildren())) {
            return $product->getProduct();
        } else {
            foreach ($product->getChildren() as $item) {
                $childItem = $item;
            }
            if ($childItem == null) {
                return $product->getProduct();
            } else {
                return $childItem->getProduct();
            }
        }
    }

    /**
     * @param $product
     * @return int|null
     */
    protected function getSumPriceChildrenBundle($product)
    {
        if (empty($product->getChildren())) {
            return null;
        } else {
            $sumPrice = 0;
            foreach ($product->getChildren() as $item) {
                if ($item->getProduct()->getTypeId() === \Magento\ConfigurableProduct\Model\Product\Type\Configurable::TYPE_CODE) {
                    $item = $this->getDiscountPercentConfig($item);
                    $sumPrice = $sumPrice + floatval($item->getPrice());
                } else {
                    $sumPrice = $sumPrice + floatval($item->getPrice());
                }
            }
            return $sumPrice;
        }
    }

    /**
     * @param $product
     * @return int|null
     */
    protected function getSumFinalPriceChildrenBundle($product)
    {
        if (empty($product->getChildren())) {
            return null;
        } else {
            $sumFinalPrice = 0;
            foreach ($product->getChildren() as $item) {
                if ($item->getProduct()->getTypeId() === \Magento\ConfigurableProduct\Model\Product\Type\Configurable::TYPE_CODE) {
                    $item = $this->getDiscountPercentConfig($item);
                    $sumFinalPrice = $sumFinalPrice + floatval($item->getFinalPrice());
                } else {
                    $sumFinalPrice = $sumFinalPrice + floatval($item->getProduct()->getFinalPrice());
                }
            }
            return $sumFinalPrice;
        }
    }

    /**
     * @param $product
     * @return int|null
     */
    protected function getDiscountPercentBundle($product)
    {
        $sumPrice = (int)$this->getSumPriceChildrenBundle($product);
        $sumFinalPrice = (int)$this->getSumFinalPriceChildrenBundle($product);
        if (is_null($sumPrice) ||
            is_null($sumFinalPrice) ||
            $sumPrice <= $sumFinalPrice
        ) {
            return null;
        } else {
            return round(
                ($sumPrice - $sumFinalPrice) * 100 / $sumPrice
            );
        }
    }

    /**
     * @param \Magento\Quote\Model\Quote\Item $item
     *
     * @return bool
     */
    public function isGiftItem($item)
    {
        if ($this->freeGiftHelper->isPromoItem($item)) {
            return true;
        }

        return false;
    }

    /**
     * @param \Magento\Quote\Model\Quote\Item $item
     * @param                                 $maxStock
     *
     * @return bool
     */
    public function isEnableQtyIncrease($item, $maxStock)
    {
        return $item->getQty() < 99 && $item->getQty() < $maxStock && !$this->isGiftItem($item);
    }

    /**
     * @param \Magento\Quote\Model\Quote\Item $item
     *
     * @return bool
     */
    public function isEnableQtyDecrease($item)
    {
        return $item->getQty() > 1 && !$this->isGiftItem($item);
    }
}
