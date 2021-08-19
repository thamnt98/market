<?php

namespace SM\ShoppingList\Controller\Action;

use BadMethodCallException;
use Exception;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Webapi\Exception as WebapiException;
use SM\ShoppingList\Api\Data\ShoppingListDataInterface;
use SM\ShoppingList\Controller\ListAction;

/**
 * Class RemoveList
 * @package SM\ShoppingList\Controller\Action
 */
class RemoveList extends ListAction
{
    /**
     * @return ResponseInterface|ResultInterface
     */
    public function execute()
    {
        $shoppingListId = $this->getRequest()->getParam("wishlist_id");
        /** @var ShoppingListDataInterface $shoppingList */
        try {
            $shoppingList = $this->shoppingListRepository->getById($shoppingListId);
            $this->shoppingListRepository->delete($shoppingList->getWishlistId());
            $this->messageManager->addSuccessMessage(
                __("%1 has been successfully deleted.", $shoppingList->getName())
            );
        } catch (WebapiException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        }

        return $this->_redirect("wishlist/mylist");
    }
}
