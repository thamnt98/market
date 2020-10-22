<?php

namespace Wizkunde\ConfigurableBundle\Block\Checkout\Cart\Item;

class Renderer extends \Magento\Bundle\Block\Checkout\Cart\Item\Renderer
{
    /**
     * Overloaded method for getting list of bundle options
     * Caches result in quote item, because it can be used in cart 'recent view' and on same page in cart checkout
     *
     * @return array
     */
    public function getOptionList()
    {
        return $this->_bundleProductConfiguration->getOptions($this->getItem());
    }
}
