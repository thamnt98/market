<?php

namespace SM\ShoppingList\Controller;

use Magento\Customer\Helper\Session\CurrentCustomer;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\View\Result\PageFactory;
use Magento\Wishlist\Controller\Shared\WishlistProvider;
use SM\ShoppingList\Api\Data\ShoppingListDataInterfaceFactory;
use SM\ShoppingList\Model\ShoppingListRepository;

/**
 * Class ListAction
 * @package SM\ShoppingList\Controller
 */
abstract class ListAction extends Action
{
    /**
     * @var ShoppingListRepository
     */
    protected $shoppingListRepository;
    /**
     * @var CurrentCustomer
     */
    protected $currentCustomer;
    /**
     * @var JsonFactory
     */
    protected $jsonFactory;
    /**
     * @var ShoppingListDataInterfaceFactory
     */
    protected $listDataFactory;
    /**
     * @var WishlistProvider
     */
    protected $wishlistProvider;
    /**
     * @var Session
     */
    protected $customerSession;

    /**
     * ListAction constructor.
     * @param Context $context
     * @param ShoppingListRepository $shoppingListRepository
     * @param CurrentCustomer $currentCustomer
     * @param JsonFactory $jsonFactory
     * @param ShoppingListDataInterfaceFactory $listDataFactory
     * @param WishlistProvider $wishlistProvider
     * @param Session $customerSession
     */
    public function __construct(
        Context $context,
        ShoppingListRepository $shoppingListRepository,
        CurrentCustomer $currentCustomer,
        JsonFactory $jsonFactory,
        ShoppingListDataInterfaceFactory $listDataFactory,
        WishlistProvider $wishlistProvider,
        Session $customerSession
    ) {
        $this->customerSession = $customerSession;
        $this->wishlistProvider = $wishlistProvider;
        $this->listDataFactory = $listDataFactory;
        $this->jsonFactory = $jsonFactory;
        $this->currentCustomer = $currentCustomer;
        $this->shoppingListRepository = $shoppingListRepository;
        parent::__construct($context);
    }
}
