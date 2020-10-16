<?php

namespace SM\MobileApi\Model\Product\Inventory;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\InventorySalesApi\Api\GetProductSalableQtyInterface;
use Magento\InventorySalesApi\Model\StockByWebsiteIdResolverInterface;

class Validate
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
     * @param array $childProductIds
     * @param bool $checkStock
     * @return bool
     * @throws InputException
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function validateProductCanDisplay($childProductIds, $checkStock = false)
    {
        $isStock = true;

        foreach ($childProductIds as $id) {
            $product           = $this->productRepository->getById($id);
            $isTobacco         = (boolean)$product->getIsTobacco();
            $isAlcohol         = (boolean)$product->getIsAlcohol();

            //Check product out of stock then return false
            if ($checkStock) {
                $websiteId         = $product->getStore()->getWebsiteId();
                $stockId           = (int)$this->stockByWebsiteId->execute($websiteId)->getStockId();
                $productSalableQty = $this->getProductSalableQty->execute($product->getSku(), $stockId);

                if ($productSalableQty <= 0) {
                    $isStock = false;
                    break;
                }
            }

            //Check product is alcohol, tobacco then return false
            if ($isAlcohol || $isTobacco) {
                $isStock = false;
                break;
            }
        }

        return $isStock;
    }
}
