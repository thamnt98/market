<?php

namespace Wizkunde\ConfigurableBundle\Plugin;

class AroundConfigurableGetFinalPrice
{
    /**
     * Make sure the custom options values are calculated on top of the normal values
     *
     * @param \Magento\ConfigurableProduct\Model\Product\Type\Configurable\Price $priceRenderer
     * @param \Closure $proceed
     * @param $qty
     * @param $product
     * @return mixed
     */
    public function aroundGetFinalPrice(
        \Magento\ConfigurableProduct\Model\Product\Type\Configurable\Price $priceRenderer,
        \Closure $proceed,
        $qty,
        $product
    )
    {

        $finalPrice = $proceed($qty, $product);

        if ($product->getCustomOption('simple_product') && $product->getCustomOption('simple_product')->getProduct()) {
            $subProduct = $product->getCustomOption('simple_product')->getProduct();
            $subProduct->setCustomOptions($product->getCustomOptions());
            $finalPrice = $this->_applyOptionsPrice($subProduct, $qty, $subProduct->getPrice());

            $finalPrice = max(0, $finalPrice);
            $product->setFinalPrice($finalPrice);
        }


        return $finalPrice;
    }

     /**
     * Apply options price
     *
     * @param Product $product
     * @param int $qty
     * @param float $finalPrice
     * @return float
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    protected function _applyOptionsPrice($product, $qty, $finalPrice)
    {
        $optionIds = $product->getCustomOption('option_ids');
        if ($optionIds) {
            $basePrice = $finalPrice;
            foreach (explode(',', $optionIds->getValue()) as $optionId) {
                if ($option = $product->getOptionById($optionId)) {
                    $confItemOption = $product->getCustomOption('option_' . $option->getId());

                    $group = $option->groupFactory($option->getType())
                        ->setOption($option)
                        ->setConfigurationItemOption($confItemOption);
                    $finalPrice += $group->getOptionPrice($confItemOption->getValue(), $basePrice);
                }
            }
        }

        return $finalPrice;
    }
}