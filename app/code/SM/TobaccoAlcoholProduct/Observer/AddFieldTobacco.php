<?php
/**
 * SM\TobaccoAlcoholProduct\Observer
 *
 * @copyright Copyright © 2020 SmartOSC. All rights reserved.
 * @author    hungnv6@smartosc.com
 */

namespace SM\TobaccoAlcoholProduct\Observer;

use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

/**
 * Class AddFieldTobacco
 * @package SM\TobaccoAlcoholProduct\Observer
 */
class AddFieldTobacco implements ObserverInterface
{
    /**
     * @param Observer $observer
     * @return Collection
     */
    public function execute(Observer $observer)
    {
        /** @var Collection $productCollection */
        $productCollection = $observer->getEvent()->getCollection();
        $productCollection
            ->addAttributeToSelect([
                "is_tobacco",
                "is_alcohol"
            ]);
        return $productCollection;
    }
}
