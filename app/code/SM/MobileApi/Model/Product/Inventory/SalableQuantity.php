<?php

namespace SM\MobileApi\Model\Product\Inventory;

use Magento\Bundle\Api\ProductLinkManagementInterface;
use Magento\Bundle\Model\Product\Type as BundleProduct;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product\Type as SimpleProduct;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\GroupedProduct\Model\Product\Type\Grouped;
use Magento\InventorySalesApi\Api\GetProductSalableQtyInterface;
use Magento\InventorySalesApi\Model\StockByWebsiteIdResolverInterface;
use SM\Catalog\Controller\Product\View;

class SalableQuantity
{
    /**
     * @var ProductLinkManagementInterface
     */
    protected $productLink;

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
     * @var Validate
     */
    protected $inventoryValidate;

    /**
     * @param ProductLinkManagementInterface $productLinkManagement
     * @param StockByWebsiteIdResolverInterface $stockByWebsiteId
     * @param GetProductSalableQtyInterface $productSalableQty
     * @param ProductRepositoryInterface $productRepository
     * @param View $viewProductController
     * @param Validate $validate
     */
    public function __construct(
        ProductLinkManagementInterface $productLinkManagement,
        StockByWebsiteIdResolverInterface $stockByWebsiteId,
        GetProductSalableQtyInterface $productSalableQty,
        ProductRepositoryInterface $productRepository,
        View $viewProductController,
        Validate $validate
    ) {
        $this->productLink           = $productLinkManagement;
        $this->stockByWebsiteId      = $stockByWebsiteId;
        $this->getProductSalableQty  = $productSalableQty;
        $this->productRepository     = $productRepository;
        $this->viewProductController = $viewProductController;
        $this->inventoryValidate     = $validate;
    }

    /**
     * @param \Magento\Catalog\Model\Product|\Magento\Catalog\Api\Data\ProductInterface $product
     * @return int
     * @throws InputException
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function getSalableQuantity($product)
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
     * @throws InputException
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    protected function _getConfigurableProductStock($product)
    {
        $skus     = [];
        $ids      = [];
        $children = $product->getTypeInstance()->getUsedProducts($product);

        /** @var \Magento\Catalog\Api\Data\ProductInterface $child */
        foreach ($children as $child) {
            $skus[] = $child->getSku();
            $ids[]  = $child->getId();
        }

        $isShow = $this->inventoryValidate->validateProductCanDisplay($ids, false);

        return $isShow ? $this->_calculateStockBySkus($skus) : 0;
    }

    /**
     * @param \Magento\Catalog\Model\Product | \Magento\Catalog\Api\Data\ProductInterface $product
     * @return int
     * @throws InputException
     * @throws LocalizedException
     * @throws NoSuchEntityException
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

            if ($child->getTypeId() == SimpleProduct::TYPE_SIMPLE) {
                $saleableQuantity[] = $this->_getSimpleProductStock($child);
            }
        }

        $saleableQuantity = array_sum($saleableQuantity);

        return (int)$saleableQuantity;
    }

    /**
     * @param \Magento\Catalog\Model\Product | \Magento\Catalog\Api\Data\ProductInterface $product
     * @return int
     * @throws InputException
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    protected function _getSimpleProductStock($product)
    {
        $skus = [$product->getSku()];
        $ids  = [$product->getId()];

        $isShow = $this->inventoryValidate->validateProductCanDisplay($ids, false);
        return $isShow ? $this->_calculateStockBySkus($skus) : 0;
    }

    /**
     * @param \Magento\Catalog\Model\Product $product
     * @return int
     */
    protected function _getBundleProductStock($product)
    {
        $saleableQuantity = [];

        try {
            $children        = $this->productLink->getChildren($product->getSku());
            $childrenIds     = $product->getTypeInstance()->getChildrenIds($product->getId(), false);
            $childProductIds = $this->extractProductIds($childrenIds);

            //Check one of child product is out of stock or alcohol , tobacco product
            $isShow = $this->inventoryValidate->validateProductCanDisplay($childProductIds, true);

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
            return $isShow ? (int)$saleableQuantity : 0;
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
                /** @var \Magento\Catalog\Api\Data\ProductInterface $product */
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
}
