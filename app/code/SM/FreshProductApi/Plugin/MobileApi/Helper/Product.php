<?php
/**
 * SM\FreshProductApi\Plugin\MobileApi\Helper
 *
 * @copyright Copyright © 2020 SmartOSC. All rights reserved.
 * @author    hungnv6@smartosc.com
 */

namespace SM\FreshProductApi\Plugin\MobileApi\Helper;

use SM\FreshProductApi\Helper\Fresh;

/**
 * Class Product
 * @package SM\FreshProductApi\Plugin\MobileApi\Helper
 */
class Product
{
    /**
     * @var Fresh
     */
    protected $fresh;

    /**
     * Product constructor.
     * @param Fresh $fresh
     */
    public function __construct(Fresh $fresh)
    {
        $this->fresh = $fresh;
    }

    /**
     * @param \SM\MobileApi\Helper\Product $subject
     * @param \SM\MobileApi\Api\Data\Product\ListItemInterface $result
     * @param \Magento\Catalog\Model\Product $product
     * @return \SM\MobileApi\Api\Data\Product\ListItemInterface
     */
    public function afterGetProductListToResponseV2($subject, $result, $product) {
        if ($result) {
            $freshProductData = $this->fresh->populateObject($product);
            $result->setFreshProduct($freshProductData);
        }
        return $result;
    }

    /**
     * @param \SM\MobileApi\Helper\Product $subject
     * @param \SM\MobileApi\Api\Data\Product\ProductDetailsInterface $result
     * @param \Magento\Catalog\Model\Product $product
     * @return \SM\MobileApi\Api\Data\Product\ProductDetailsInterface
     */
    public function afterGetProductDetailsToResponseV2($subject, $result, $product) {
        if ($result) {
            $freshProductData = $this->fresh->populateObject($product);
            $result->setFreshProduct($freshProductData);
        }
        return $result;
    }
}
