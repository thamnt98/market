<?php

namespace SM\ShoppingList\Controller\Action;

use BadMethodCallException;
use Exception;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\NoSuchEntityException;
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
        $shoppingListId = $this->getRequest()->getParam("id");
        /** @var ShoppingListDataInterface $shoppingList */
        try {
            $shoppingList = $this->shoppingListRepository->getById($shoppingListId);
            $this->shoppingListRepository->delete($shoppingList->getWishlistId());
            $this->messageManager->addSuccessMessage(
                __("%1 has been successfully deleted.", $shoppingList->getName())
            );
        } catch (NoSuchEntityException $e) {
            $this->messageManager->addErrorMessage(
                __('Shopping list with id "%1" does not exist.', $shoppingList->getWishlistId())
            );
        } catch (BadMethodCallException $e) {
            $this->messageManager->addErrorMessage(
                __("You can not delete " . $this->shoppingListRepository->getDefaultShoppingListName())
            );
        } catch (Exception $e) {
            $this->messageManager->addErrorMessage(
                __("Unable to delete " . $shoppingList->getName())
            );
        }

        return $this->_redirect("shoppinglist/mylist");
    }
}
