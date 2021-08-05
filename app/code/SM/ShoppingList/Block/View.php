<?php

namespace SM\ShoppingList\Block;

use Exception;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Model\ProductRepository;
use Magento\Customer\Helper\Session\CurrentCustomer;
use Magento\Customer\Model\Session;
use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\Search\FilterGroupBuilder;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\SortOrderBuilder;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Element\Template;
use Magento\MultipleWishlist\Helper\Data;
use Magento\Wishlist\Block\Customer\Wishlist\Items;
use SM\Customer\Plugin\Magento\Framework\App\Action\AbstractAction;
use SM\ShoppingList\Api\Data\ShoppingListDataInterface;
use SM\ShoppingList\Api\Data\ShoppingListItemDataInterface;
use SM\ShoppingList\Model\ShoppingListItemRepository;
use SM\ShoppingList\Model\ShoppingListRepository;

/**
 * Class View
 * @package SM\ShoppingList\Block
 */
class View extends Template
{
    /**
     * @var ShoppingListItemDataInterface[]
     */
    protected $items;
    /**
     * @var ShoppingListRepository
     */
    protected $shoppingListRepository;
    /**
     * @var Data
     */
    protected $wishlistData;
    /**
     * @var
     */
    protected $shoppingList;
    /**
     * @var CurrentCustomer
     */
    protected $currentCustomer;
    /**
     * @var ShoppingListItemRepository
     */
    protected $itemRepository;
    /**
     * @var FilterBuilder
     */
    protected $filterBuilder;
    /**
     * @var SearchCriteriaBuilder
     */
    protected $searchCriteriaBuilder;
    /**
     * @var FilterGroupBuilder
     */
    protected $filterGroupBuilder;
    /**
     * @var SortOrderBuilder
     */
    protected $sortOrderBuilder;
    /**
     * @var ProductRepository
     */
    protected $productRepository;

    /**
     * @var Session
     */
    protected $customerSession;

    /**
     * @var \Magento\Framework\App\Http\Context
     */
    protected $httpContext;

    /**
     * View constructor.
     * @param ShoppingListRepository $shoppingListRepository
     * @param Template\Context $context
     * @param CurrentCustomer $currentCustomer
     * @param Data $wishlistData
     * @param ShoppingListItemRepository $itemRepository
     * @param FilterBuilder $filterBuilder
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param FilterGroupBuilder $filterGroupBuilder
     * @param SortOrderBuilder $sortOrderBuilder
     * @param ProductRepository $productRepository
     * @param Session $customerSession
     * @param \Magento\Framework\App\Http\Context $httpContext
     * @param array $data
     */
    public function __construct(
        ShoppingListRepository $shoppingListRepository,
        Template\Context $context,
        CurrentCustomer $currentCustomer,
        Data $wishlistData,
        ShoppingListItemRepository $itemRepository,
        FilterBuilder $filterBuilder,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        FilterGroupBuilder $filterGroupBuilder,
        SortOrderBuilder $sortOrderBuilder,
        ProductRepository $productRepository,
        Session $customerSession,
        \Magento\Framework\App\Http\Context $httpContext,
        array $data = []
    ) {
        $this->httpContext = $httpContext;
        $this->customerSession = $customerSession;
        $this->productRepository = $productRepository;
        $this->shoppingListRepository = $shoppingListRepository;
        $this->wishlistData = $wishlistData;
        $this->currentCustomer = $currentCustomer;
        $this->itemRepository = $itemRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->filterGroupBuilder = $filterGroupBuilder;
        $this->filterBuilder = $filterBuilder;
        $this->sortOrderBuilder = $sortOrderBuilder;
        parent::__construct($context, $data);
    }

    /**
     * @return Items|void
     */
    protected function _prepareLayout()
    {
        if ($navigationBlock = $this->getLayout()->getBlock('customer_account_navigation')) {
            $navigationBlock->setActive('shoppinglist');
        }

        $shoppingListId = $this->wishlistData->getDefaultWishlist()->getId();
        if ($this->getRequest()->getParam("id")) {
            $shoppingListId = $this->getRequest()->getParam("id");
        }

        $this->initShoppingList($shoppingListId);
    }

    /**
     * @param int $shoppingListId
     * @throws NoSuchEntityException
     */
    private function initShoppingList($shoppingListId) {
        $shoppingList = $this->shoppingListRepository->getById($shoppingListId);

        if ($shoppingList->getWishlistId()) {
            $this->setShoppingList($shoppingList);
            $this->setItems($shoppingList->getItems());
        } else {
            $this->setShoppingList(0);
        }
    }

    /**
     * @return ShoppingListItemDataInterface[]
     */
    public function getItems()
    {
        return $this->items;
    }

    /**
     * @param ShoppingListItemDataInterface[] $items
     * @return View
     */
    public function setItems($items)
    {
        $this->items = $items;
        return $this;
    }

    /**
     * @return bool
     */
    public function isDefaultList()
    {
        if (!$this->getRequest()->getParam("id")) {
            return true;
        } else {
            if ($this->getRequest()->getParam("id") == $this->wishlistData->getDefaultWishlist()->getId()) {
                return true;
            }
        }
        return false;
    }

    /**
     * @return ShoppingListDataInterface
     */
    public function getShoppingList()
    {
        return $this->shoppingList;
    }

    /**
     * @param $shoppingList
     */
    public function setShoppingList($shoppingList)
    {
        $this->shoppingList = $shoppingList;
    }

    /**
     * @return string
     */
    public function getCurrentUrl()
    {
        return strtok($this->getUrl('*/*/*', ['_current' => true, '_use_rewrite' => true]), "?");
    }

    /**
     * @param ShoppingListItemDataInterface $item
     * @return bool|ProductInterface
     */
    public function getProduct(ShoppingListItemDataInterface $item)
    {
        try {
            return $this->productRepository->getById($item->getProductId());
        } catch (Exception $exception) {
            return false;
        }
    }

    /**
     * @return ShoppingListDataInterface[]
     */
    public function getShoppingLists()
    {
        return [];
    }
}
