<?php
/**
 * SM\FreshProductApi\Plugin\Magento\Quote\Model\Quote
 *
 * @copyright Copyright © 2020 SmartOSC. All rights reserved.
 * @author    hungnv6@smartosc.com
 */

namespace SM\FreshProductApi\Plugin\Magento\Quote\Model\Quote;

use SM\FreshProductApi\Api\Data\FreshProductInterface;
use SM\FreshProductApi\Helper\Fresh;

/**
 * Class Item
 * @package SM\FreshProductApi\Plugin\Magento\Quote\Model\Quote
 */
class Item
{
    /**
     * @var Fresh
     */
    protected $fresh;

    /**
     * Item constructor.
     * @param Fresh $fresh
     */
    public function __construct(
        Fresh $fresh
    ) {
        $this->fresh = $fresh;
    }

    /**
     * @param \Magento\Quote\Model\Quote\Item $subject
     * @param \Magento\Quote\Api\Data\CartItemExtensionInterface $extension
     * @return \Magento\Quote\Api\Data\CartItemExtensionInterface
     */
    public function afterGetExtensionAttributes(
        \Magento\Quote\Model\Quote\Item $subject,
        \Magento\Quote\Api\Data\CartItemExtensionInterface $extension
    ) {
        if ($extension && !$extension->getFreshProduct()) {
            /** @var FreshProductInterface $freshProductData */
            $freshProductData = $this->fresh->populateObject($subject->getProduct());
            $extension->setFreshProduct($freshProductData);
        }
        return $extension;
    }
}
