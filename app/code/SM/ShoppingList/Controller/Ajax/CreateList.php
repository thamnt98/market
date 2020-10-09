<?php

namespace SM\ShoppingList\Controller\Ajax;

use Exception;
use LengthException;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\DB\Adapter\DuplicateException;
use SM\ShoppingList\Api\Data\ShoppingListDataInterface;
use SM\ShoppingList\Controller\ListAction;

/**
 * Class CreateList
 * @package SM\ShoppingList\Controller\Ajax
 */
class CreateList extends ListAction
{
    /**
     * @return ResponseInterface|Json|ResultInterface
     */
    public function execute()
    {
        $data = $this->getRequest()->getPostValue();

        /** @var ShoppingListDataInterface $listData */
        $listData = $this->listDataFactory->create();
        $listData->setName($data["shopping_list_name"]);

        try {
            /** @var ShoppingListDataInterface $result */
            $result = $this->shoppingListRepository->create(
                $listData,
                $this->currentCustomer->getCustomerId()
            );
            return $this->jsonFactory->create()->setData([
                "status" => 1,
                "result" => [
                    "name" => $result->getName(),
                    "list_id" => $result->getWishlistId(),
                ]
            ]);
        } catch (DuplicateException $e) {
            return $this->jsonFactory->create()->setData([
                "status" => 0,
                "result" => __("Shopping list name is already exist. Please try again")
            ]);
        } catch (LengthException $e) {
            return $this->jsonFactory->create()->setData([
                "status" => 0,
                "result" => __("You have reached maximum shopping list number")
            ]);
        } catch (Exception $e) {
            return $this->jsonFactory->create()->setData([
                "status" => 0,
                "result" => __("Unable to create shopping list")
            ]);
        }
    }
}
