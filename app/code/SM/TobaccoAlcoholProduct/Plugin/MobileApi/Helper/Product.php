<?php
/**
 * SM\TobaccoAlcoholProduct\Plugin\MobileApi\Helper
 *
 * @copyright Copyright © 2020 SmartOSC. All rights reserved.
 * @author    hungnv6@smartosc.com
 */

namespace SM\TobaccoAlcoholProduct\Plugin\MobileApi\Helper;

use Magento\Catalog\Model\ProductRepository;
use SM\MobileApi\Api\Data\Product\ListItemInterface;
use SM\MobileApi\Api\Data\Product\ProductDetailsInterface;

/**
 * Class Product
 * @package SM\TobaccoAlcoholProduct\Plugin\MobileApi\Helper
 */
class Product
{
    /**
     * @param \SM\MobileApi\Helper\Product $subject
     * @param ProductDetailsInterface $result
     * @param \Magento\Catalog\Model\Product $product
     * @return ProductDetailsInterface
     */
    public function afterGetProductDetailsToResponseV2($subject, $result, $product) {
        if ($result) {
            /** @var \Magento\Catalog\Model\Product $product */
            if ($product->getCustomAttribute(ProductDetailsInterface::IS_TOBACCO)) {
                $result->setIsTobacco($product->getCustomAttribute(ProductDetailsInterface::IS_TOBACCO)->getValue() ?? false);
            }

            if ($product->getCustomAttribute(ProductDetailsInterface::IS_ALCOHOL)) {
                $result->setIsAlcohol($product->getCustomAttribute(ProductDetailsInterface::IS_ALCOHOL)->getValue() ?? false);
            }
        }
        return $result;
    }

    /**
     * @param \SM\MobileApi\Helper\Product $subject
     * @param ListItemInterface $result
     * @param \Magento\Catalog\Model\Product $product
     * @return ListItemInterface
     */
    public function afterGetProductListToResponseV2($subject, $result, $product) {
        if ($result) {
            if ($product->getData(ProductDetailsInterface::IS_TOBACCO)) {
                $result->setIsTobacco($product->getData(ProductDetailsInterface::IS_TOBACCO) ?? false);
            }

            if ($product->getData(ProductDetailsInterface::IS_ALCOHOL)) {
                $result->setIsAlcohol($product->getData(ProductDetailsInterface::IS_ALCOHOL) ?? false);
            }
        }
        return $result;
    }
}
