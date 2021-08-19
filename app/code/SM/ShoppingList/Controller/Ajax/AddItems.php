<?php

namespace SM\ShoppingList\Controller\Ajax;

use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\ResultInterface;
use SM\ShoppingList\Api\Data\ShoppingListDataInterface;
use SM\ShoppingList\Controller\ItemAction;

/**
 * Class AddItems
 * @package SM\ShoppingList\Controller\Ajax
 */
class AddItems extends ItemAction
{
    /**
     * @return ResponseInterface|Json|ResultInterface
     */
    public function execute()
    {
        $data = $this->getRequest()->getPostValue();
        $productId = $data["product_id"];

        if (isset($data["shopping_list_ids"])) {
            $shoppingListIds = $data["shopping_list_ids"];
        } else {
            $shoppingListIds = [$this->wishlistData->getDefaultWishlist()->getId()];
        }

        $myFavoriteListId = $this->wishlistData->getDefaultWishlist()->getId();

        $result = $this->shoppingListItemRepository->add($shoppingListIds, $productId);
        $isShowToast = $this->isShowToast($data);
        if ($result->getStatus()) {
            $data = [];
            /** @var ShoppingListDataInterface $list */
            foreach ($result->getResult() as $list) {
                $data[] = [
                    "name" => $list->getName(),
                    "url" => $this->_url->getUrl(
                        "wishlist/index/index",
                        ["wishlist_id" => $list->getWishlistId()]
                    ),
                ];
            }
            if ($isShowToast && isset($list)) {
                if (sizeof($shoppingListIds) > 1) {
                    $url =  $this->_url->getUrl("wishlist/index/index", ["wishlist_id" => $myFavoriteListId]);
                    $this->messageManager->addComplexSuccessMessage(
                        'addShoppingListMessageSuccessItems',
                        [
                            'url' => $url
                        ]
                    );
                } else {
                    $name = $this->escaper->escapeHtml($list->getName());
                    $url =  $this->_url->getUrl("wishlist/index/index", ["wishlist_id" => $list->getWishlistId()]);
                    $this->messageManager->addComplexSuccessMessage(
                        'addShoppingListMessageSuccessItem',
                        [
                            'name' => $name,
                            'url' => $url
                        ]
                    );
                }
            }

            return $this->jsonFactory->create()->setData([
                "myFavoriteListId" => $myFavoriteListId,
                "status" => $result->getStatus(),
                "result" => $data
            ]);
        } else {
            return $this->jsonFactory->create()->setData([
                "myFavoriteListId" => $myFavoriteListId,
                "status" => $result->getStatus(),
                "result" => $result->getMessage()
            ]);
        }
    }

    public function isShowToast($data)
    {
        return isset($data["show_toast"]) && $data["show_toast"] == "true";
    }
}
