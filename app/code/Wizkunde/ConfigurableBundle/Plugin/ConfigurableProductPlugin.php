<?php

namespace Wizkunde\ConfigurableBundle\Plugin;

use Magento\ConfigurableProduct\Model\Product\Type\Configurable;

class ConfigurableProductPlugin
{
    /**
     * @param $product
     * @param \Closure $proceed
     * @param $key
     * @return bool
     */
    public function aroundHasData(
        $product,
        \Closure $proceed,
        $key
    ) {
        // Workaround to prevent the validator in M2.3.1 failing over this
        if($product->getTypeId() == Configurable::TYPE_CODE) {
            if($product->getData('category_ids') === null) {
                $product->addData(array('category_ids' => ''));
            }
        }

        return $proceed($key);
    }
}
