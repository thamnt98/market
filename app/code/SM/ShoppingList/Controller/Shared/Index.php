<?php

namespace SM\ShoppingList\Controller\Shared;

use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Webapi\Exception as WebapiException;
use Magento\Wishlist\Model\Wishlist;
use SM\ShoppingList\Api\Data\ShoppingListDataInterface;
use SM\ShoppingList\Controller\ListAction;

/**
 * Class Index
 * @package SM\ShoppingList\Controller\Shared
 */
class Index extends ListAction
{
    /**
     * @return ResponseInterface|ResultInterface
     */
    public function execute()
    {
        /** @var Wishlist $shoppingList */
        $shoppingList = $this->wishlistProvider->getWishlist();
        if ($shoppingList->getId()) {
            try {
                /** @var ShoppingListDataInterface $result */
                $result = $this->shoppingListRepository
                    ->share($shoppingList->getId(), $this->currentCustomer->getCustomerId());
                $this->messageManager->addSuccessMessage(
                    __($result->getName() . " has been shared with you")
                );
            } catch (WebapiException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            }
        } else {
            $this->messageManager->addErrorMessage(__("Unable to find this list"));
        }

        return $this->_redirect($this->_url->getUrl("*/mylist"));
    }
}
