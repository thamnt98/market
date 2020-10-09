<?php
/**
 * SM\TobaccoAlcoholProduct\Plugin\Catalog\Model\ResourceModel\Product
 *
 * @copyright Copyright © 2020 SmartOSC. All rights reserved.
 * @author    hungnv6@smartosc.com
 */

namespace SM\TobaccoAlcoholProduct\Plugin\Catalog\Model\ResourceModel\Product;

/**
 * Class Collection
 * @package SM\TobaccoAlcoholProduct\Plugin\Catalog\Model\ResourceModel\Product
 */
class Collection
{
    /**
     * @param \Magento\Catalog\Model\ResourceModel\Product\Collection $subject
     */
    public function beforeLoad($subject) {
        $subject->addAttributeToSelect([
            "is_alcohol",
            "is_tobacco"
        ]);
    }
}
