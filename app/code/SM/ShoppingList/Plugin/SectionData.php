<?php
/**
 * SM\ShoppingList\Plugin
 *
 * @copyright Copyright © 2021 SmartOSC. All rights reserved.
 * @author    hungnv6@smartosc.com
 */

namespace SM\ShoppingList\Plugin;

use Magento\Framework\Registry;

/**
 * Class SectionData
 * @package SM\ShoppingList\Plugin
 */
class SectionData
{
    const REGISTRY_KEY_ADDED_IDS = "shoppinglist_added_ids";
    const SECTION_KEY_ADDED_IDS = "added_ids";

    /**
     * @var Registry
     */
    protected $registry;

    /**
     * SectionData constructor.
     * @param Registry $registry
     */
    public function __construct(
        Registry $registry
    ) {
        $this->registry = $registry;
    }

    /**
     * @param \Magento\Wishlist\CustomerData\Wishlist $subject
     * @param array $result
     * @return array
     */
    public function afterGetSectionData($subject, $result)
    {
        $addedProductIds = $this->registry->registry(SectionData::REGISTRY_KEY_ADDED_IDS);
        $result[SectionData::SECTION_KEY_ADDED_IDS] = $addedProductIds;

        return $result;
    }
}
