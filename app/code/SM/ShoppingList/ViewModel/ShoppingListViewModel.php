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
use Magento\Framework\Api\SearchCriteriaBuilder;
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
     * @var \Magento\MultipleWishlist\Helper\Data
     */
    protected $wishlistData;

    /**
     * ShoppingListViewModel constructor.
     * @param CurrentCustomer $currentCustomer
     * @param Image $imageHelper
     * @param ShoppingListRepository $shoppingListRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param Cart $cart
     * @param Data $shoppingListHelper
     * @param \Magento\MultipleWishlist\Helper\Data $wishlistData
     */
    public function __construct(
        CurrentCustomer $currentCustomer,
        Image $imageHelper,
        ShoppingListRepository $shoppingListRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        Cart $cart,
        Data $shoppingListHelper,
        \Magento\MultipleWishlist\Helper\Data $wishlistData
    ) {
        $this->wishlistData = $wishlistData;
        $this->cart = $cart;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->shoppingListRepository = $shoppingListRepository;
        $this->imageHelper = $imageHelper;
        $this->currentCustomer = $currentCustomer;
        $this->shoppingListHelper = $shoppingListHelper;
    }

    public function getShoppingLists()
    {
        $customerId = $this->currentCustomer->getCustomerId();
        return $this->wishlistData->getCustomerWishlists($customerId)->getItems();
    }

    /**
     * @param Product $product
     * @return string
     */
    public function getProductImage($product)
    {
        return $this->imageHelper->init($product, 'product_base_image')->getUrl();
    }
}
