<?php

namespace SM\MobileApi\Model\Product;

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
use SM\Catalog\Controller\Product\View;
use SM\MobileApi\Helper\Product\Common;

/**
 * Class Stock
 * @package SM\MobileApi\Model\Product
 */
class Stock
{
    /**
     * @var ProductLinkManagementInterface
     */
    protected $productLink;

    /**
     * @var Common
     */
    protected $commonHelper;

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
     * @var View
     */
    protected $viewProductController;

    /**
     * @param ProductLinkManagementInterface $productLinkManagement
     * @param Common $commonHelper
     * @param StockByWebsiteIdResolverInterface $stockByWebsiteId
     * @param GetProductSalableQtyInterface $productSalableQty
     * @param ProductRepositoryInterface $productRepository
     * @param View $viewProductController
     */
    public function __construct(
        ProductLinkManagementInterface $productLinkManagement,
        Common $commonHelper,
        StockByWebsiteIdResolverInterface $stockByWebsiteId,
        GetProductSalableQtyInterface $productSalableQty,
        ProductRepositoryInterface $productRepository,
        View $viewProductController
    ) {
        $this->productLink = $productLinkManagement;
        $this->commonHelper = $commonHelper;
        $this->stockByWebsiteId = $stockByWebsiteId;
        $this->getProductSalableQty = $productSalableQty;
        $this->productRepository = $productRepository;
        $this->viewProductController = $viewProductController;
    }

    /**
     * Get stock product in category listing page
     * @param \Magento\Catalog\Model\Product $product
     * @return int
     */
    public function getStock($product)
    {
        $productTypeId = $product->getTypeId();

        switch ($productTypeId) {
            case Configurable::TYPE_CODE:
                return $this->_getConfigurableProductStock($product);
            case BundleProduct::TYPE_CODE:
                return $this->_getBundleProductStock($product);
            case Grouped::TYPE_CODE:
                return $this->_getGroupProductStock($product);
            default:
                return $this->_getSimpleProductStock($product);
        }
    }

    /**
     * @param \Magento\Catalog\Model\Product | \Magento\Catalog\Api\Data\ProductInterface $product
     * @return int
     */
    protected function _getConfigurableProductStock($product)
    {
        $children = $product->getTypeInstance()->getUsedProducts($product);
        $skus     = $this->_getSkusChildren($children);

        return $this->_calculateStockBySkus($skus);
    }

    /**
     * @param \Magento\Catalog\Model\Product | \Magento\Catalog\Api\Data\ProductInterface $product
     * @return int
     */
    protected function _getGroupProductStock($product)
    {
        $saleableQuantity = [];
        $children = $product->getTypeInstance()->getAssociatedProducts($product);
        foreach ($children as $child) {
            /** @var \Magento\Catalog\Model\Product $child */
            if ($child->getTypeId() == Configurable::TYPE_CODE) {
                $saleableQuantity[] = $this->_getConfigurableProductStock($child);
            }

            if ($child->getTypeId() == 'simple') {
                $saleableQuantity[] = $this->_getSimpleProductStock($child);
            }
        }

        $saleableQuantity = array_sum($saleableQuantity);

        return (int)$saleableQuantity;
    }

    /**
     * @param \Magento\Catalog\Model\Product | \Magento\Catalog\Api\Data\ProductInterface $product
     * @return int
     */
    protected function _getSimpleProductStock($product)
    {
        $sku  = $product->getSku();
        $skus = [$sku];
        return $this->_calculateStockBySkus($skus);
    }

    /**
     * @param \Magento\Catalog\Model\Product $product
     * @return int
     */
    protected function _getBundleProductStock($product)
    {
        $saleableQuantity = [];

        try {
            $children = $this->productLink->getChildren($product->getSku());
            $this->viewProductController->checkShowProduct($product->getEntityId());

            foreach ($children as $child) {
                /** @var \Magento\Bundle\Model\Link $child */
                $product = $this->productRepository->getById($child->getEntityId());
                if ($product->getTypeId() == Configurable::TYPE_CODE) {
                    $saleableQuantity[] = $this->_getConfigurableProductStock($product) / $child->getQty();
                }

                if ($product->getTypeId() == 'simple') {
                    $saleableQuantity[] = $this->_getSimpleProductStock($product) / $child->getQty();
                }
            }

            $saleableQuantity = min($saleableQuantity);
            return (int)$saleableQuantity;
        } catch (\Exception $exception) {
            return 0;
        }
    }

    /**
     * @param array $skus
     * @return int
     */
    protected function _calculateStockBySkus($skus)
    {
        if (!is_array($skus)) {
            return 0;
        }

        $stock = [];
        try {
            foreach ($skus as $sku) {
                $product = $this->productRepository->get($sku);
                $websiteId = $product->getStore()->getWebsiteId();
                $stockId = (int)$this->stockByWebsiteId->execute($websiteId)->getStockId();
                $productSalableQty = $this->getProductSalableQty->execute($sku, $stockId);
                $stock[] = $productSalableQty;
            }
        } catch (NoSuchEntityException $exception) {
            return 0;
        } catch (InputException $e) {
            return 0;
        } catch (LocalizedException $e) {
            return 0;
        }

        return array_sum($stock);
    }

    /**
     * @param $children
     * @return array|int
     */
    protected function _getSkusChildren($children)
    {
        $skus = [];
        foreach ($children as $child) {
            $skus[] = $child->getSku();
        }

        if (empty($skus)) {
            return 0;
        }

        return $skus;
    }

    /**
     * Check product is salable or not, don't rely on $product->getIsSalable()
     *
     * @param \Magento\Catalog\Model\Product $product
     *
     * @return bool
     * @throws NoSuchEntityException
     */
    public function isProductSalable($product)
    {
        if ($product) {
            if ($this->commonHelper->isProductAllowedBackOrder($product)) {
                //if product is allowed back order => always return true
                return true;
            } else {
                if ($this->commonHelper->isProductEnabled($product) && $this->getStock($product) > 0) {
                    return true;
                } else {
                    return false;
                }
            }
        }

        return false;
    }
}
