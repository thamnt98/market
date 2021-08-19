<?php
/**
 * @category    SM
 * @package     SM_ShoppingList
 * @license     http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 *
 * @author      Logan Ta <phuongtt2@smartosc.com>
 *
 * @copyright   Copyright (c) SmartOSC. All rights reserved.
 * @url http://www.smartosc.com/
 */

namespace SM\ShoppingList\Helper;

use Magento\Catalog\Helper\Image;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Type;
use Magento\Catalog\Model\ProductRepository;
use Magento\Customer\Helper\Session\CurrentCustomer;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\App\Area;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Registry;
use Magento\Review\Model\ReviewFactory;
use Magento\Store\Model\App\Emulation;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Wishlist\Model\Item;
use SM\ShoppingList\Model\ResourceModel\Wishlist\CollectionFactory as ShoppingListCollectionFactory;
use Magento\Wishlist\Model\ResourceModel\Item\CollectionFactory as ShoppingListItemCollectionFactory;
use SM\ShoppingList\Api\Data\ShoppingListItemDataInterface;

/**
 * Class Data
 * @package SM\ShoppingList\Helper
 */
class Data extends AbstractHelper
{
    /**
     * @var ShoppingListCollectionFactory
     */
    protected $shoppingListCollectionFactory;

    /**
     * @var ShoppingListItemCollectionFactory
     */
    protected $shoppingListItemCollectionFactory;

    /**
     * @var CurrentCustomer
     */
    protected $currentCustomer;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var SearchCriteriaBuilder
     */
    protected $searchCriteriaBuilder;

    /**
     * @var Registry
     */
    protected $registry;

    /**
     * @var \Magento\MultipleWishlist\Helper\Data
     */
    protected $wishlistData;

    protected $favoriteIds = null;

    /**
     * Data constructor.
     * @param Context $context
     * @param ShoppingListCollectionFactory $shoppingListCollectionFactory
     * @param ShoppingListItemCollectionFactory $shoppingListItemCollectionFactory
     * @param CurrentCustomer $currentCustomer
     * @param StoreManagerInterface $storeManager
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param Registry $registry
     */
    public function __construct(
        Context $context,
        ShoppingListCollectionFactory $shoppingListCollectionFactory,
        ShoppingListItemCollectionFactory $shoppingListItemCollectionFactory,
        CurrentCustomer $currentCustomer,
        StoreManagerInterface $storeManager,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        Registry $registry,
        \Magento\MultipleWishlist\Helper\Data $wishlistData
    ) {
        $this->wishlistData = $wishlistData;
        $this->registry = $registry;
        $this->shoppingListCollectionFactory = $shoppingListCollectionFactory;
        $this->shoppingListItemCollectionFactory = $shoppingListItemCollectionFactory;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->currentCustomer = $currentCustomer;
        $this->storeManager = $storeManager;

        parent::__construct($context);
    }

    /**
     * @param $wishlistId
     * @param $productId
     * @return null
     */
    public function isProductAddedToList($wishlistId, $productId)
    {
        $shoppingListItemId = null;
        $collection = $this->shoppingListItemCollectionFactory->create()
            ->addFieldToFilter('wishlist_id', $wishlistId)
            ->addFieldToFilter('product_id', $productId);

        if ($collection) {
            foreach ($collection->getData() as $item) {
                $shoppingListItemId = $item['wishlist_item_id'];
            }
        }

        return $shoppingListItemId;
    }

    /**
     * @return bool
     */
    public function getMyFavoritesListDefaultId()
    {
        $currentCustomerId = $this->currentCustomer->getCustomerId();

        $defaultId = $this->wishlistData->getDefaultWishlist($currentCustomerId)->getId();
        if ($defaultId) {
            return $defaultId;
        }

        return false;
    }

    /**
     * @param $productId
     * @return bool
     */
    public function isAddedShoppingList($productId)
    {
        $myFavoriteListId = $this->getMyFavoritesListDefaultId();
        $shoppingListItemId = $this->isProductAddedToList($myFavoriteListId, $productId);
        if ($shoppingListItemId != null) {
            return true;
        }

        return false;
    }

    /**
     * @param $productId
     * @return array|null
     */
    public function getAddedItemIdInList($productId)
    {
        $itemId = null;
        $myFavoriteListId = $this->getMyFavoritesListDefaultId();
        $shoppingListItemId = $this->isProductAddedToList($myFavoriteListId, $productId);
        if ($shoppingListItemId != null) {
            return $shoppingListItemId;
        }

        return $itemId;
    }

    public function isAddedToFavorites($productId)
    {
        $myFavoriteListId = $this->getMyFavoritesListDefaultId();
        if (is_null($this->favoriteIds)) {
            $this->favoriteIds = [];
            $collection = $this->shoppingListItemCollectionFactory->create()
                ->addFieldToFilter('wishlist_id', $myFavoriteListId);
            /** @var Item $item */
            foreach ($collection as $item) {
                $this->favoriteIds[$item->getProductId()] = $item->getId();
            }
        }

        return in_array($productId, array_keys($this->favoriteIds));
    }

    public function getFavoriteIds()
    {
        if (is_null($this->favoriteIds)) {
            return [];
        };

        return $this->favoriteIds;
    }

    public function isActiveShoppingList()
    {
        return (bool)$this->scopeConfig->getValue(
            "wishlist/general/active",
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return int
     */
    public function getLimitShoppingListNumber()
    {
        return $this->scopeConfig->getValue(
            'wishlist/general/multiple_wishlist_number',
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return string
     */
    public function getDefaultShoppingListName()
    {
        return __("My Favorites");
    }

}
