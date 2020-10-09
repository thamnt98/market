<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace SM\Label\Block\Cart\Additional;

use Magento\Checkout\Block\Cart\Additional\Info as InfoDefault;

/**
 * @api
 * @since 100.0.2
 */
class Info extends InfoDefault
{
    /**
     * @var \Magento\Quote\Model\Quote\Item\AbstractItem
     */
    protected $_item;

    /**
     * @param \Magento\Quote\Model\Quote\Item\AbstractItem $item
     * @return \Magento\Checkout\Block\Cart\Additional\Info
     * @codeCoverageIgnore
     */
    public function setItem(\Magento\Quote\Model\Quote\Item\AbstractItem $item)
    {
        $this->_item = $item;
        return $this;
    }

    /**
     * @return \Magento\Quote\Model\Quote\Item\AbstractItem
     * @codeCoverageIgnore
     */
    public function getItem()
    {
        return $this->_item;
    }

    /**
     * @return mixed
     */
    public function getLabel()
    {
        $buyRequest = $this->_item->getBuyRequest();
        $label = $buyRequest['product-cat-label-'. $this->_item->getProductId()];

        return $label;
    }
}
