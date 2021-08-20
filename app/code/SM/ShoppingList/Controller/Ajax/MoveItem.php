<?php

namespace SM\ShoppingList\Controller\Ajax;

use Exception;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Webapi\Exception as WebapiException;
use SM\ShoppingList\Api\Data\ResultDataInterface;
use SM\ShoppingList\Api\Data\ShoppingListDataInterface;
use SM\ShoppingList\Controller\ItemAction;

/**
 * Class MoveItem
 * @package SM\ShoppingList\Controller\Ajax
 */
class MoveItem extends ItemAction
{
    /**
     * @return ResponseInterface|ResultInterface
     */
    public function execute()
    {
        $data = $this->getRequest()->getPostValue();
        $itemId = $data["item_id"];
        $selected = $data["shopping_list_ids"];

        try {
            /** @var ResultDataInterface $result */
            $result = $this->shoppingListItemRepository->move($itemId, $selected);
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
                return $this->jsonFactory->create()->setData([
                    "status" => $result->getStatus(),
                    "result" => $data
                ]);
            } else {
                return $this->jsonFactory->create()->setData([
                    "status" => $result->getStatus(),
                    "result" => $result->getMessage()
                ]);
            }
        } catch (WebapiException $e) {
            return $this->jsonFactory->create()->setData([
                "status" => 0,
                "result" => __("Item with '%1' does not exist", $itemId)
            ]);
        }
    }
}
