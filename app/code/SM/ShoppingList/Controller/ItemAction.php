<?php
/**
 * @category Magento
 * @package SM\ShoppingList\Controller\Action
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @author Hung Viet Nguyen <hungnv6@smartosc.com>
 * @copyright   Copyright (c) SmartOSC. All rights reserved.
 * @url http://www.smartosc.com/
 */

namespace SM\ShoppingList\Controller;

use Magento\Customer\Helper\Session\CurrentCustomer;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\MultipleWishlist\Helper\Data;
use SM\ShoppingList\Model\ShoppingListItemRepository;
use SM\ShoppingList\Helper\Data as ShoppingListHelper;

/**
 * Class ItemAction
 * @package SM\ShoppingList\Controller
 */
abstract class ItemAction extends Action
{
    /**
     * @var ShoppingListItemRepository
     */
    protected $shoppingListItemRepository;
    /**
     * @var Data
     */
    protected $wishlistData;
    /**
     * @var JsonFactory
     */
    protected $jsonFactory;
    /**
     * @var CurrentCustomer
     */
    protected $currentCustomer;
    /**
     * @var ShoppingListHelper
     */
    protected $shoppinglistHelper;

    /**
     * ItemAction constructor.
     * @param Data $wishlistData
     * @param ShoppingListItemRepository $shoppingListItemRepository
     * @param JsonFactory $jsonFactory
     * @param Context $context
     * @param CurrentCustomer $currentCustomer
     * @param ShoppingListHelper $shoppinglistHelper
     */
    public function __construct(
        Data $wishlistData,
        ShoppingListItemRepository $shoppingListItemRepository,
        JsonFactory $jsonFactory,
        Context $context,
        CurrentCustomer $currentCustomer,
        ShoppingListHelper $shoppinglistHelper
    ) {
        $this->currentCustomer = $currentCustomer;
        $this->jsonFactory = $jsonFactory;
        $this->shoppingListItemRepository = $shoppingListItemRepository;
        $this->wishlistData = $wishlistData;
        $this->shoppinglistHelper = $shoppinglistHelper;
        parent::__construct($context);
    }
}
