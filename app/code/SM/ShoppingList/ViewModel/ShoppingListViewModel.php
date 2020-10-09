<?php
/**
 * @category Magento
 * @package SM\ShoppingList\ViewModel
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @author Hung Viet Nguyen <hungnv6@smartosc.com>
 * @copyright   Copyright (c) SmartOSC. All rights reserved.
 * @url http://www.smartosc.com/
 */

namespace SM\ShoppingList\ViewModel;

use Magento\Catalog\Helper\Image;
use Magento\Catalog\Model\Product;
use Magento\Checkout\Model\Cart;
use Magento\Customer\Helper\Session\CurrentCustomer;
use Magento\Framework\Api\SearchCriteria;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\StoreManagerInterface;
use SM\ShoppingList\Api\Data\ShoppingListDataInterface;
use SM\ShoppingList\Model\ShoppingListRepository;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use SM\ShoppingList\Helper\Data;

abstract class ShoppingListViewModel implements ArgumentInterface
{
    /**
     * @var CurrentCustomer
     */
    protected $currentCustomer;
    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;
    /**
     * @var Image
     */
    protected $imageHelper;
    /**
     * @var ShoppingListRepository
     */
    protected $shoppingListRepository;
    /**
     * @var SearchCriteriaBuilder
     */
    protected $searchCriteriaBuilder;
    /**
     * @var Cart
     */
    protected $cart;

    /**
     * @var Data
     */
    protected $shoppingListHelper;

    /**
     * ShoppingListViewModel constructor.
     * @param CurrentCustomer $currentCustomer
     * @param StoreManagerInterface $storeManager
     * @param Image $imageHelper
     * @param ShoppingListRepository $shoppingListRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param Cart $cart
     * @param Data $shoppingListHelper
     */
    public function __construct(
        CurrentCustomer $currentCustomer,
        StoreManagerInterface $storeManager,
        Image $imageHelper,
        ShoppingListRepository $shoppingListRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        Cart $cart,
        Data $shoppingListHelper
    ) {
        $this->cart = $cart;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->shoppingListRepository = $shoppingListRepository;
        $this->imageHelper = $imageHelper;
        $this->currentCustomer = $currentCustomer;
        $this->storeManager = $storeManager;
        $this->shoppingListHelper = $shoppingListHelper;
    }

    /**
     * @return ShoppingListDataInterface[]
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
     * @param Product $product
     * @return string
     */
    public function getProductImage($product)
    {
        return $this->imageHelper->init($product, 'product_base_image')->getUrl();
    }

    /**
     * @return int
     * @throws NoSuchEntityException
     */
    public function getStoreId()
    {
        return $this->storeManager->getStore()->getId();
    }
}
