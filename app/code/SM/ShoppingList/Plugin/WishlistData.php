<?php
/**
 * @category Magento
 * @package SM\ShoppingList\Plugin
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @author Hung Viet Nguyen <hungnv6@smartosc.com>
 * @copyright   Copyright (c) SmartOSC. All rights reserved.
 * @url http://www.smartosc.com/
 */

namespace SM\ShoppingList\Plugin;

use Magento\Customer\Helper\Session\CurrentCustomer;
use Magento\MultipleWishlist\Helper\Data;
use Magento\Wishlist\Model\ResourceModel\Item\CollectionFactory as ItemCollectionFactory;

/**
 * Class WishlistData
 * @package SM\ShoppingList\Plugin
 */
class WishlistData
{
    /**
     * @var ItemCollectionFactory
     */
    protected $itemCollectionFactory;
    /**
     * @var CurrentCustomer
     */
    protected $currentCustomer;

    /**
     * WishlistData constructor.
     * @param ItemCollectionFactory $itemCollectionFactory
     * @param CurrentCustomer $currentCustomer
     */
    public function __construct(
        ItemCollectionFactory $itemCollectionFactory,
        CurrentCustomer $currentCustomer
    ) {
        $this->currentCustomer = $currentCustomer;
        $this->itemCollectionFactory = $itemCollectionFactory;
    }

    /**
     * @param Data $subject
     * @param bool $result
     * @return bool
     */
    public function afterIsMultipleEnabled(Data $subject, $result)
    {
        return true;
    }

    /**
     * @param Data $subject
     * @param $result
     * @return int
     */
    public function afterGetItemCount(Data $subject, $result)
    {
        if ($this->currentCustomer->getCustomerId()) {
            $collection = $this->itemCollectionFactory->create();
            $collection->getSelect()->joinLeft(
                "wishlist",
                "main_table.wishlist_id = wishlist.wishlist_id",
                []
            );
            $collection->addFieldToFilter("wishlist.customer_id", $this->currentCustomer->getCustomerId());
            return $collection->getSize();
        }
        return 0;
    }
}
