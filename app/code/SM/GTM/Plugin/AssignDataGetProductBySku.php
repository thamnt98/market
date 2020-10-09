<?php
/**
 * SM\GTM\Plugin
 *
 * @copyright Copyright © 2020 SmartOSC. All rights reserved.
 * @author    hungnv6@smartosc.com
 */

namespace SM\GTM\Plugin;

/**
 * Class AssignDataGetProductBySku
 * @package SM\GTM\Plugin
 */
class AssignDataGetProductBySku
{
    /**
     * @var \SM\GTM\Block\Product\ListProduct
     */
    protected $productGtm;

    /**
     * @var \SM\MobileApi\Model\Data\GTM\GTMFactory
     */
    protected $gtmDataFactory;

    protected $adjustPrice;

    protected $productListV2Factory;
    /**
     * AssignDataGetProductBySku constructor.
     * @param \SM\GTM\Block\Product\ListProduct $productGtm
     * @param \SM\MobileApi\Model\Data\GTM\GTMFactory $gtmDataFactory
     */
    public function __construct(
        \SM\GTM\Block\Product\ListProduct $productGtm,
        \SM\MobileApi\Model\Data\GTM\GTMFactory $gtmDataFactory,
        \SM\MobileApi\Model\Product\Price\AdjustPrice $adjustPrice,
        \SM\MobileApi\Model\Data\Product\ListItemFactory $productListV2Factory
    ) {
        $this->gtmDataFactory = $gtmDataFactory;
        $this->productGtm = $productGtm;
        $this->adjustPrice = $adjustPrice;
        $this->productListV2Factory = $productListV2Factory;
    }

    /**
     * @param \SM\MobileApi\Helper\Product $subject
     * @param \SM\MobileApi\Api\Data\Product\ProductDetailsInterface $result
     * @param \Magento\Catalog\Model\Product $product
     * @return \SM\MobileApi\Api\Data\Product\ProductDetailsInterface
     */
    public function afterGetProductDetailsToResponseV2(
        \SM\MobileApi\Helper\Product $subject,
        \SM\MobileApi\Api\Data\Product\ProductDetailsInterface $result,
        \Magento\Catalog\Model\Product $product
    ) {
        if ($result) {
            $data = $this->productGtm->getGtm($product);
            $data = \Zend_Json_Decoder::decode($data);
            /** @var \SM\MobileApi\Api\Data\GTM\GTMInterface $gtmData */
            $gtmData = $this->gtmDataFactory->create();
            $gtmData
                ->setProductName($data['name'])
                ->setProductId($data['id'])
                ->setProductPrice($data['price'])
                ->setProductBrand($data['brand'])
                ->setProductCategory($data['category'])
                ->setProductSize($data['product_size'])
                ->setProductVolume($data['product_volume'])
                ->setProductWeight($data['product_weight'])
                ->setProductVariant($data['variant'])
                ->setDiscountPrice($data['salePrice'])
                ->setProductList($data['list'])
                ->setProductRating($data['rating'])
                ->setInitialPrice($data['initialPrice'])
                ->setDiscountRate($data['discountRate'])
                ->setProductType($product->getTypeId());
            $productInfo = $this->productListV2Factory->create();

            $this->adjustPrice->execute($productInfo, $product);
            if ($product->getTypeId() == "bundle") {
                $gtmData->setProductPrice($productInfo->getFinalPrice());
                $gtmData->setDiscountPrice($productInfo->getPrice() - $productInfo->getFinalPrice());
                $gtmData->setInitialPrice($productInfo->getPrice());
                $discount = $productInfo->getPrice() - $productInfo->getFinalPrice();

                if ($discount != 0) {
                    $discount = round(($discount * 100) / $productInfo->getPrice()) . '%';
                }

                $gtmData->setDiscountRate($discount);
            } else {
                $gtmData->setDiscountPrice($productInfo->getPrice() - $productInfo->getFinalPrice());
            }

            if ($data['salePrice'] && $data['salePrice'] > 0) {
                $gtmData->setProductOnSale(__('Yes'));
            } else {
                $gtmData->setProductOnSale(__('Not on sale'));
            }
            if ($productInfo->getFinalPrice() < $productInfo->getPrice()) {
                $gtmData->setProductOnSale(__('Yes'));
            } else {
                $gtmData->setProductOnSale(__('Not on sale'));
            }
            $result->setGtmData($gtmData);
            return $result;
        }

        return $result;
    }
}
