<?php
/**
 * SM\ShoppingList\Model
 *
 * @copyright Copyright © 2021 SmartOSC. All rights reserved.
 * @author    hungnv6@smartosc.com
 */

namespace SM\ShoppingList\Model;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Registry;
use Magento\Framework\Webapi\Exception as WebapiException;
use SM\ShoppingList\Api\ItemManagementInterface;
use SM\ShoppingList\Helper\Data;

/**
 * Class ItemManagement
 * @package SM\ShoppingList\Model
 */
class ItemManagement implements ItemManagementInterface
{
    /**
     * @var Registry
     */
    protected $registry;

    /**
     * @var Data
     */
    protected $shoppingListHelper;

    /**
     * @var ShoppingListItemRepository
     */
    protected $shoppingListItemRepository;

    /**
     * @var \Magento\MultipleWishlist\Helper\Data
     */
    protected $wishlistData;

    /**
     * ItemManagement constructor.
     * @param Registry $registry
     * @param Data $shoppingListHelper
     * @param ShoppingListItemRepository $shoppingListItemRepository
     * @param \Magento\MultipleWishlist\Helper\Data $wishlistData
     */
    public function __construct(
        Registry $registry,
        Data $shoppingListHelper,
        ShoppingListItemRepository $shoppingListItemRepository,
        \Magento\MultipleWishlist\Helper\Data $wishlistData
    ) {
        $this->wishlistData = $wishlistData;
        $this->registry = $registry;
        $this->shoppingListHelper = $shoppingListHelper;
        $this->shoppingListItemRepository = $shoppingListItemRepository;
    }

    /**
     * @inheritDoc
     * @throws WebapiException
     */
    public function addItem($customerId, $productId, $shoppingListIds = [])
    {
        if (empty($shoppingListIds)) {
            $this->registry->register('customer_id', $customerId);
            $defaultId = $this->wishlistData->getDefaultWishlist($customerId)->getId();
            if ($defaultId == false) {
                throw new WebapiException(
                    __("Internal Error: User does not have default list"),
                    0,
                    WebapiException::HTTP_INTERNAL_ERROR
                );
            } else {
                $shoppingListIds = [$defaultId];
            }
        }

        return $this->shoppingListItemRepository->add($shoppingListIds, $productId);
    }
}
