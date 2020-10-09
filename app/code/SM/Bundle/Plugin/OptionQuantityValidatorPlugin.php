<?php

namespace SM\Bundle\Plugin;

class OptionQuantityValidatorPlugin extends \Wizkunde\ConfigurableBundle\Plugin\OptionQuantityValidatorPlugin
{
    public function aroundInitialize(
        \Magento\CatalogInventory\Model\Quote\Item\QuantityValidator\Initializer\Option $subject,
        \Closure $proceed,
        \Magento\Quote\Model\Quote\Item\Option $option,
        \Magento\Quote\Model\Quote\Item $quoteItem,
        $qty
    ) {

        /**
         * This works since a configurable bundle does not allow multiple configurables to be selected
         * Otherwise we would need to iterate over all the configurable selections
         */

        if ($option->getProduct()->getTypeId() == 'configurable') {
            foreach ($quoteItem->getQuote()->getAllItems() as $quoteItemData) {
                if ($quoteItemData->getParentItemId() == $quoteItem->getId()) {
                    $simpleProduct = $option->getProduct()->getCustomOption('simple_product')->getValue();

                    $quoteItem = $quoteItemData;

                    foreach ($option->getProduct()->getTypeInstance()->getUsedProducts($option->getProduct()) as $childProduct) {
                        if ($childProduct->getId() == $simpleProduct) {
                            $option->setProduct($childProduct);
                            break;
                        }
                    }

                }

                break;
            }
        }

        return $proceed($option, $quoteItem, $qty);
    }
}
