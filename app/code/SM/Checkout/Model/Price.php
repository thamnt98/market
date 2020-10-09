<?php

namespace SM\Checkout\Model;

use Magento\Catalog\Model\Product;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable;

/**
 * Class MsiFullFill
 * @package SM\Checkout\Model
 */
class Price
{
    /**
     * @param \Magento\Catalog\Model\Product $product
     */
    public function getRegularPrice($product)
    {
        $productType = $product->getTypeId();
        if ($productType == 'configurable') {
            $regularPrice = $product->getPrice();
        } elseif ($productType == 'bundle') {
            $productTypeInstance = $product->getTypeInstance();
            $productOption = $productTypeInstance->getSelectionsCollection($productTypeInstance->getOptionsIds($product), $product)->getItems();
            $option = $product->getTypeInstance()->getOrderOptions($product);
            $selectedProduct = $this->getSelectedProduct($productOption, $option);
            $regularPrice = 0;
            foreach ($selectedProduct as $p) {
                $regularPrice += $p->getPrice();
            }
        } else {
            $regularPrice = $product->getPriceInfo()->getPrice('regular_price')->getValue();
        }
        return $regularPrice;
    }

    /**
     * @param $productOption
     * @param $option
     * @return array
     */
    public function getSelectedProduct($productOption, $option)
    {
        $products = [];
        $optionSelected = [];
        $infoBuyRequest = $option['info_buyRequest'];
        $bundleOption = $infoBuyRequest['bundle_option'];
        foreach ($productOption as $optionId => $productOpt) {
            foreach ($bundleOption as $opt) {
                if ($optionId == $opt) {
                    $optionSelected[] = $productOpt;
                    if ($productOpt->getTypeId() == Configurable::TYPE_CODE) {
                        if (!empty($infoBuyRequest['super_attribute'])) {
                            $supperAttributes = $infoBuyRequest['super_attribute'];
                            $attrOpt = 0;
                            foreach ($bundleOption as $k => $v) {
                                if ($v == $optionId) {
                                    $attrOpt = $k;
                                }
                            }
                            $attributes = $productOpt->getTypeInstance()->getConfigurableAttributesAsArray($productOpt);
                            $attributeId = head(array_keys($supperAttributes[$attrOpt][$optionId]));
                            $attributeSelected = head(array_values($supperAttributes[$attrOpt][$optionId]));
                            $attributeCode = $attributes[$attributeId]['attribute_code'];
                            $usedProduct = $productOpt->getTypeInstance()->getUsedProducts($productOpt);
                            /** @var Product $product */
                            foreach ($usedProduct as $product) {
                                if ($product->getData($attributeCode) == $attributeSelected) {
                                    $products[] = $product;
                                }
                            }
                        }

                    } else {
                        $products[] = $productOpt;
                    }
                }
            }
        }

        return $products;
    }
}
