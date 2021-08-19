<?php

namespace SM\ShoppingList\Controller\Action;

use Exception;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Webapi\Exception as WebapiException;
use SM\ShoppingList\Api\Data\ShoppingListItemDataInterface;
use SM\ShoppingList\Controller\ItemAction;

/**
 * Class RemoveItem
 * @package SM\ShoppingList\Controller\Action
 */
class RemoveItem extends ItemAction
{
    /**
     * @return ResponseInterface|ResultInterface
     */
    public function execute()
    {
        $itemId = $this->getRequest()->getParam("id");

        try {
            /** @var \Magento\Wishlist\Model\Item $item */
            $item = $this->itemFactory->create()->load($itemId);
            $productName = $item->getProduct()->getName();
            if ($this->shoppingListItemRepository->deleteById($itemId)) {
                if ($this->wishlistData->getDefaultWishlist()->getId() == $item->getWishlistId()) {
                    $this->messageManager->addSuccessMessage(
                        __("%1 has been removed from Shopping List.", $productName)
                    );
                } else {
                    $this->messageManager->addSuccessMessage(
                        __("%1 has been removed from this list.", $productName)
                    );
                }
            } else {
                $this->messageManager->addErrorMessage(__("Unable to delete item"));
            }
        } catch (WebapiException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        }

        $urlReferer = $this->_redirect->getRefererUrl();

        return $this->_redirect($urlReferer);
    }
}
