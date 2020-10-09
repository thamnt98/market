<?php
/**
 * @category  SM
 * @package   SM_Catalog
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 *
 * @author    Logan Ta <phuongtt2@smartosc.com>
 *
 * @copyright 2020 Copyright (c) SmartOSC. All rights reserved.
 * @url http://www.smartosc.com/
 */

declare(strict_types=1);

namespace SM\Catalog\Block\Product\ProductList\Item\AddTo;

use Magento\Catalog\Model\Product\Attribute\Source\Status;

/**
 * Class Iteminfo
 * @package SM\Catalog\Block\Product\ProductList\Item\AddTo
 */
class Iteminfo extends \Magento\Catalog\Block\Product\ProductList\Item\Block
{
    const PRODUCT_CONFIGURABLE = 'configurable';
    const PRODUCT_BUNDLE = 'bundle';
    const PRODUCT_GROUPED = 'grouped';
    const PRODUCT_SIMPLE = 'simple';

    /**
     * @var \SM\Catalog\Helper\Data
     */
    protected $helper;

    /**
     * Iteminfo constructor.
     *
     * @param \SM\Catalog\Helper\Data                $helper
     * @param \Magento\Catalog\Block\Product\Context $context
     * @param array                                  $data
     */
    public function __construct(
        \SM\Catalog\Helper\Data $helper,
        \Magento\Catalog\Block\Product\Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->helper = $helper;
    }

    /**
     * @return \Magento\Catalog\Api\Data\ProductInterface|\Magento\Catalog\Model\Product
     */
    public function getProductItem()
    {
        return $this->getProduct();
    }

    /**
     * @param $product
     * @return float|null
     */
    public function getDiscountPercent($product)
    {
        return $this->helper->getDiscountPercent($product);
    }

    /**
     * @param $product
     * @return bool
     */
    public function isShowBadgeDiscount($product)
    {
        if ($product->getTypeId() != self::PRODUCT_GROUPED && $product->getTypeId() != self::PRODUCT_BUNDLE) {
            return true;
        }

        return false;
    }

    /**
     * @param $product
     * @return null
     */
    public function getFirstItemOfConfigProduct($product)
    {
        $_firstSimple = $product->getTypeInstance();
        $usedProduct = $_firstSimple->getUsedProducts($product);
        $firstChild = NULL;
        $productChildChoose = NULL;
        $specialPriceArr = [];
        //get product first child default and price array to compare
        foreach ($usedProduct as $child) {
            if ($firstChild == NULL) {
                $firstChild = $child;
            }
            //push array child item has special price
            if ($child->getFinalPrice() != NULL) {
                $specialPriceArr [] = floatval($child->getFinalPrice());
            }
        }

        if (empty($specialPriceArr)) {
            return $firstChild;
        } else {
            $minValue = min($specialPriceArr);
            //get product child compare
            foreach ($usedProduct as $child) {
                if ($child->getFinalPrice() != NULL && floatval($child->getFinalPrice()) == $minValue) {
                    $productChildChoose = $child;
                }
            }
        }

        if ($productChildChoose != NULL) {
            return $productChildChoose;
        } else {
            return $firstChild;
        }
    }

    /**
     * @param $product
     * @return bool
     */
    public function isConfigProduct($product)
    {
        return $product->getTypeId() == self::PRODUCT_CONFIGURABLE;
    }
}
