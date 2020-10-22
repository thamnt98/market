<?php

namespace Wizkunde\ConfigurableBundle\Plugin;

class QuoteItemPlugin
{
    protected $checkoutCart;

    /**
     * OptionQuantityValidatorPlugin constructor.
     * @param \Magento\Checkout\Model\Cart $cart
     */
    public function __construct(\Magento\Checkout\Model\Cart $cart)
    {
        $this->checkoutCart = $cart;
    }

    public function aroundSetProduct(\Magento\CatalogInventory\Model\Quote\Item $subject,
                                     \Closure $proceed,
                                     \Magento\Catalog\Model\Product $product
    ) {

        /**
         * This works since a configurable bundle does not allow multiple configurables to be selected
         * Otherwise we would need to iterate over all the configurable selections
         */

        if($product->getTypeId() == 'configurable') {
            foreach($this->checkoutCart->getQuote()->getAllItems() as $quoteItemData) {
                if($quoteItemData->getParentItemId() == $subject->getId()) {
                    $subject->setChildren(array($quoteItemData));
                }

                break;
            }
        }

        return $proceed($product);
    }
}
