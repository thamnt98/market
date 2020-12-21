<?php
/**
 * SM\FreshProductApi\Plugin\Magento\Catalog\Model\ResourceModel\Product
 *
 * @copyright Copyright © 2020 SmartOSC. All rights reserved.
 * @author    hungnv6@smartosc.com
 */

namespace SM\FreshProductApi\Plugin\Magento\Catalog\Model\ResourceModel\Product;

use SM\FreshProductApi\Api\Data\FreshProductInterface;

class Collection
{
    public function beforeLoad(\Magento\Catalog\Model\ResourceModel\Product\Collection $subject)
    {
        $subject->addAttributeToSelect([
            'is_fresh',
            FreshProductInterface::OWN_COURIER,
            FreshProductInterface::BASE_PRICE_IN_KG,
            FreshProductInterface::PROMO_PRICE_IN_KG,
            FreshProductInterface::IS_DECIMAL,
            FreshProductInterface::WEIGHT,
            FreshProductInterface::SOLD_IN,
            FreshProductInterface::PRICE_IN_KG,
        ]);
    }
}
