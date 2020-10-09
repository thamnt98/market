<?php

namespace SM\MobileApi\Model\Quote\Item;

use Magento\Bundle\Api\ProductLinkManagementInterface;
use Magento\Bundle\Model\Product\Type as BundleProduct;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\GroupedProduct\Model\Product\Type\Grouped;
use Magento\InventorySalesApi\Api\GetProductSalableQtyInterface;
use Magento\InventorySalesApi\Model\StockByWebsiteIdResolverInterface;
use SM\MobileApi\Helper\Product\Common;

/**
 * Class Stock
 * @package SM\MobileApi\Model\Quote\Item
 */
class Stock
{
    /**
     * @var StockByWebsiteIdResolverInterface
     */
    protected $stockByWebsiteId;

    /**
     * @var GetProductSalableQtyInterface
     */
    protected $getProductSalableQty;

    /**
     * @var ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * @param StockByWebsiteIdResolverInterface $stockByWebsiteId
     * @param GetProductSalableQtyInterface $productSalableQty
     * @param ProductRepositoryInterface $productRepository
     */
    public function __construct(
        StockByWebsiteIdResolverInterface $stockByWebsiteId,
        GetProductSalableQtyInterface $productSalableQty,
        ProductRepositoryInterface $productRepository
    ) {
        $this->stockByWebsiteId = $stockByWebsiteId;
        $this->getProductSalableQty = $productSalableQty;
        $this->productRepository = $productRepository;
    }

    /**
     * Get stock in shopping cart
     * @param \Magento\Catalog\Model\Product $product
     * @return int
     */
    public function getStock($product)
    {
        /** @var \Magento\Quote\Model\Quote\Item $product */
        $productTypeId = $product->getProduct()->getTypeId();

        switch ($productTypeId) {
            case BundleProduct::TYPE_CODE:
                return $this->_getBundleProductStock($product);
            default:
                return $this->_calculateStockBySku($product->getSku());
        }
    }


    /**
     * @param \Magento\Quote\Model\Quote\Item $product
     * @return int
     */
    protected function _getBundleProductStock($product)
    {
        $itemsCollection = $product->getQuote()->getItemsCollection()->getItems();
        $childBundleSkus = [];

        //Get child items product of bundle product
        foreach ($itemsCollection as $item) {
            /** @var \Magento\Quote\Model\Quote\Item $item */
            if ($item->getParentItemId() == $product->getItemId()) {
                $childBundleSkus[] = ['sku' => $item->getSku(), 'qty' => $item->getQty()];
            }
        }

        //Get saleable quantity of child items
        $saleableQuantity = [];
        foreach ($childBundleSkus as $childBundleSku) {
            $stock              = (int)$this->_calculateStockBySku($childBundleSku['sku']);
            $saleableQuantity[] = $stock / (int)$childBundleSku['qty'];
        }

        if (empty($saleableQuantity)) {
            return 0;
        }

        return min($saleableQuantity);
    }

    /**
     * @param string $sku
     * @return int
     */
    protected function _calculateStockBySku($sku)
    {
        try {
            $product           = $this->productRepository->get($sku);
            $websiteId         = $product->getStore()->getWebsiteId();
            $stockId           = (int)$this->stockByWebsiteId->execute($websiteId)->getStockId();
            $productSalableQty = $this->getProductSalableQty->execute($sku, $stockId);
            $stock             = $productSalableQty;
        } catch (NoSuchEntityException $exception) {
            return 0;
        } catch (InputException $e) {
            return 0;
        } catch (LocalizedException $e) {
            return 0;
        }

        return $stock;
    }
}
