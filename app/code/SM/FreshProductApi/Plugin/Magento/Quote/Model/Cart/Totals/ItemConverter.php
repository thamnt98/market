<?php
/**
 * SM\FreshProductApi\Plugin\Magento\Quote\Model\Cart\Totals
 *
 * @copyright Copyright © 2020 SmartOSC. All rights reserved.
 * @author    hungnv6@smartosc.com
 */

namespace SM\FreshProductApi\Plugin\Magento\Quote\Model\Cart\Totals;

use SM\FreshProductApi\Api\Data\FreshProductInterface;
use SM\FreshProductApi\Helper\Fresh;

/**
 * Class ItemConverter
 * @package SM\FreshProductApi\Plugin\Magento\Quote\Model\Cart\Totals
 */
class ItemConverter
{
    /**
     * @var Fresh
     */
    protected $fresh;

    /**
     * ItemConverter constructor.
     * @param Fresh $fresh
     */
    public function __construct(Fresh $fresh)
    {
        $this->fresh = $fresh;
    }

    /**
     * @param \Magento\Quote\Model\Cart\Totals\ItemConverter $subject
     * @param \Magento\Quote\Api\Data\TotalsItemInterface $result
     * @param \Magento\Quote\Model\Quote\Item $item
     * @return \Magento\Quote\Api\Data\TotalsItemInterface
     */
    public function afterModelToDataObject(
        \Magento\Quote\Model\Cart\Totals\ItemConverter $subject,
        \Magento\Quote\Api\Data\TotalsItemInterface $result,
        \Magento\Quote\Model\Quote\Item $item
    ) {
        $extension = $result->getExtensionAttributes();
        if ($extension && !$extension->getFreshProduct()) {
            /** @var FreshProductInterface $freshProductData */
            $freshProductData = $this->fresh->populateObject($item->getProduct());
            $extension->setFreshProduct($freshProductData);
            $result->setExtensionAttributes($extension);
        }
        return $result;
    }
}
