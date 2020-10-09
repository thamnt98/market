<?php

namespace SM\ShoppingList\Controller\Ajax;

use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\ResultInterface;
use SM\ShoppingList\Controller\ItemAction;

/**
 * Class RemoveItem
 * @package SM\ShoppingList\Controller\Ajax
 */
class RemoveItem extends ItemAction
{
    /**
     * @return ResponseInterface|Json|ResultInterface
     * @throws \Exception
     */
    public function execute()
    {
        $myFavoriteListId = $this->wishlistData->getDefaultWishlist()->getId();
        $productId = $this->getRequest()->getParam("product_id");
        $result = false;
        $itemId = $this->shoppinglistHelper->getAddedItemIdInList($productId);
        try {
            if ($this->shoppingListItemRepository->deleteById($itemId)) {
                $result = true;
                $url =  $this->_url->getUrl("shoppinglist/mylist/detail", ["id" => $myFavoriteListId]);
                $this->messageManager->addComplexSuccessMessage(
                    'removeShoppingListMessageItem',
                    [
                        'url' => $url
                    ]
                );
            } else {
                $result = false;
            }
        } catch (Exception $e) {
            $result = false;
        }

        return $this->jsonFactory->create()->setData([
            "result" => $result
        ]);
    }
}
