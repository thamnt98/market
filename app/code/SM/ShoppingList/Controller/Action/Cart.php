<?php
/**
 * @category Magento
 * @package SM\ShoppingList\Controller\Action
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @author Hung Viet Nguyen <hungnv6@smartosc.com>
 * @copyright   Copyright (c) SmartOSC. All rights reserved.
 * @url http://www.smartosc.com/
 */

namespace SM\ShoppingList\Controller\Action;

use Exception;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;
use SM\ShoppingList\Api\Data\ShoppingListItemDataInterface;
use SM\ShoppingList\Controller\ItemAction;

/**
 * Class Cart
 * @package SM\ShoppingList\Controller\Action
 */
class Cart extends ItemAction
{
    /**
     * @return ResponseInterface|ResultInterface
     */
    public function execute()
    {
        $itemId = $this->getRequest()->getParam("id");
        $customerId = $this->currentCustomer->getCustomerId();

        try {
            /** @var ShoppingListItemDataInterface $item */
            $item = $this->shoppingListItemRepository->getById($itemId);
            $result = $this->shoppingListItemRepository->addToCart($customerId, $item);
            if ($result) {
                $productName = $item->getCustomAttribute("product_name")->getValue();
                $this->messageManager->addSuccessMessage(__('You added %1 to your shopping cart.', $productName));
            } else {
                $this->messageManager->addErrorMessage(__("An error occurred while adding item to cart"));
            }
            return $this->_redirect($this->_url->getUrl("*/mylist/detail", ['id' => $item->getWishlistId()]));
        } catch (Exception $e) {
            return $this->_redirect($this->_redirect->getRefererUrl());
        }
    }
}
