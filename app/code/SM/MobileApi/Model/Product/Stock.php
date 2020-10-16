<?php

namespace SM\MobileApi\Model\Product;

use Magento\Bundle\Api\ProductLinkManagementInterface;
use Magento\Bundle\Model\Product\Type as BundleProduct;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Webapi\Exception;
use Magento\GroupedProduct\Model\Product\Type\Grouped;
use Magento\InventorySalesApi\Api\GetProductSalableQtyInterface;
use Magento\InventorySalesApi\Model\StockByWebsiteIdResolverInterface;
use SM\Catalog\Controller\Product\View;
use SM\MobileApi\Helper\Product\Common;
use Magento\Catalog\Model\Product\Type as SimpleProduct;

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
            $children     = $this->productLink->getChildren($product->getSku());
            $typeInstance = $product->getTypeInstance();
            $childrenIds  = $typeInstance->getChildrenIds($product->getId(), false);
            $isShowBundle = $this->verifyBundleProductCanDisplay($childrenIds);

            //Check one of child product is out of stock or alcohol , tobacco product
            if (!$isShowBundle) {
                throw new Exception(__('Product is out of stock'), 0, Exception::HTTP_NOT_FOUND);
            }

            //Check children product has same source inventory
            //If not, will throw exception then return 0
            $this->viewProductController->checkShowProduct($product->getEntityId());

            foreach ($children as $child) {
                /** @var \Magento\Bundle\Model\Link $child */
                $product = $this->productRepository->getById($child->getEntityId());
                if ($product->getTypeId() == Configurable::TYPE_CODE) {
                    $saleableQuantity[] = $this->_getConfigurableProductStock($product) / $child->getQty();
                }

                if ($product->getTypeId() == SimpleProduct::TYPE_SIMPLE) {
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

    /**
     * @param array $childProductIds
     * @return bool
     * @throws InputException
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    protected function checkChildProductBundleIsOutOfStock($childProductIds)
    {
        $isStock = true;

        foreach ($childProductIds as $id) {
            $product           = $this->productRepository->getById($id);
            $websiteId         = $product->getStore()->getWebsiteId();
            $stockId           = (int)$this->stockByWebsiteId->execute($websiteId)->getStockId();
            $productSalableQty = $this->getProductSalableQty->execute($product->getSku(), $stockId);
            $isTobacco         = (boolean)$product->getIsTobacco();
            $isAlcohol         = (boolean)$product->getIsAlcohol();

            //If child product out of stock or is alcohol, tobacco then return false
            if ($productSalableQty <= 0 || $isAlcohol || $isTobacco) {
                $isStock = false;
                break;
            }
        }

        return $isStock;
    }

    /**
     * @param $childrenIds
     * @return array
     */
    protected function extractProductIds($childrenIds)
    {
        $ids = [];
        foreach ($childrenIds as $childrenId) {
            foreach ($childrenId as $id) {
                $ids[] = $id;
            }
        }

        return $ids;
    }

    /**
     * Check bundle product has child product out of stock
     * @param $childrenIds
     * @return bool
     * @throws InputException
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    protected function verifyBundleProductCanDisplay($childrenIds)
    {
        $ids = $this->extractProductIds($childrenIds);
        return $this->checkChildProductBundleIsOutOfStock($ids);
    }
}
