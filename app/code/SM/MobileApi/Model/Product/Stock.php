<?php

namespace SM\MobileApi\Model\Product;

use Magento\Framework\Exception\NoSuchEntityException;
use SM\MobileApi\Helper\Product\Common;
use SM\MobileApi\Model\Product\Inventory\SalableQuantity;

/**
 * Class Stock
 * @package SM\MobileApi\Model\Product
 */
class Stock
{
    /**
     * @var SalableQuantity
     */
    protected $saleableQuantity;

    /**
     * @var Common
     */
    protected $commonHelper;

    /**
     * Stock constructor.
     * @param Common $commonHelper
     * @param SalableQuantity $salableQuantity
     */
    public function __construct(
        Common $commonHelper,
        SalableQuantity $salableQuantity
    ) {
        $this->commonHelper = $commonHelper;
        $this->saleableQuantity = $salableQuantity;
    }

    /**
     * Get stock product in category listing page
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return int
     * @throws NoSuchEntityException
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getStock($product)
    {
        return $this->saleableQuantity->getSalableQuantity($product);
    }

    /**
     * Check product is salable or not, don't rely on $product->getIsSalable()
     *
     * @param \Magento\Catalog\Model\Product $product
     *
     * @return bool
     * @throws NoSuchEntityException
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\LocalizedException
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
