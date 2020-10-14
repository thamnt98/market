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

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use SM\ShoppingList\Model\ResourceModel\Wishlist\CollectionFactory as ShoppingListCollectionFactory;
use Magento\Wishlist\Model\ResourceModel\Item\CollectionFactory as ShoppingListItemCollectionFactory;
use Magento\Customer\Helper\Session\CurrentCustomer;
use Magento\Framework\Api\SearchCriteria;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Store\Model\StoreManagerInterface;
use SM\ShoppingList\Api\Data\ShoppingListDataInterface;
use SM\ShoppingList\Model\ShoppingListRepository;
use Magento\Framework\Exception\NoSuchEntityException;

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
     * @var ShoppingListRepository
     */
    protected $shoppingListRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    protected $searchCriteriaBuilder;

    public function __construct(
        Context $context,
        ShoppingListCollectionFactory $shoppingListCollectionFactory,
        ShoppingListItemCollectionFactory $shoppingListItemCollectionFactory,
        CurrentCustomer $currentCustomer,
        StoreManagerInterface $storeManager,
        ShoppingListRepository $shoppingListRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder
    ) {
        $this->shoppingListCollectionFactory = $shoppingListCollectionFactory;
        $this->shoppingListItemCollectionFactory = $shoppingListItemCollectionFactory;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->shoppingListRepository = $shoppingListRepository;
        $this->currentCustomer = $currentCustomer;
        $this->storeManager = $storeManager;

        parent::__construct($context);
    }

    /**
     * @param $wishlistId
     * @return mixed
     */
    public function getShoppingListById($wishlistId)
    {
        $collection = $this->shoppingListCollectionFactory->create();
        $collection->addFieldToFilter('wishlist_id', $wishlistId);

        return $collection;
    }

    /**
     * @param $wishlistId
     * @param $productId
     * @return null
     */
    public function isProductAddedToList($wishlistId, $productId)
    {
        $shoppingListItemId = NULL;
        $collection = $this->shoppingListItemCollectionFactory->create()
            ->addFieldToFilter('wishlist_id', $wishlistId)
            ->addFieldToFilter('product_id', $productId);

        if($collection){
            foreach ($collection->getData() as $item) {
                $shoppingListItemId = $item['wishlist_item_id'];
            }
        }

        return $shoppingListItemId;
    }

    /**
     * @return array|ShoppingListDataInterface[]
     */
    public function getShoppingLists()
    {
        $this->searchCriteriaBuilder->addFilter("customer_id", $this->currentCustomer->getCustomerId());
        /** @var SearchCriteria $searchCriteria */
        $searchCriteria = $this->searchCriteriaBuilder->create();
        try {
            $searchResults = $this->shoppingListRepository->getList(
                $searchCriteria,
                $this->currentCustomer->getCustomerId()
            );
            return $searchResults->getItems();
        } catch (NoSuchEntityException $e) {
            return [];
        }
    }

    /**
     * @return bool
     */
    public function getMyFavoritesListDefaultId()
    {
        $currentCustomerId = $this->currentCustomer->getCustomerId();
        $myFavorite = $this->shoppingListCollectionFactory->create()
            ->addFieldToFilter('customer_id', $currentCustomerId);

        foreach ($myFavorite as $favorite) {
            if (is_null($favorite->getData('name'))) {
                return $favorite->getData('wishlist_id');
            }
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
        $itemId = NULL;
        $myFavoriteListId = $this->getMyFavoritesListDefaultId();
        $shoppingListItemId = $this->isProductAddedToList($myFavoriteListId, $productId);
        if ($shoppingListItemId != null) {
            return $shoppingListItemId;
        }

        return $itemId;
    }


    public function isActiveShoppingList()
    {
        return false;
    }
}
