<?php

namespace SM\ShoppingList\Controller\Ajax;

use Exception;
use LengthException;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\DB\Adapter\DuplicateException;
use Magento\Framework\Webapi\Exception as WebapiException;
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
            $this->messageManager->addSuccessMessage(__("You have successfully created %1.", $result->getName()));
            return $this->jsonFactory->create()->setData([
                "status" => 1,
                "result" => [
                    "name" => $result->getName(),
                    "list_id" => $result->getWishlistId(),
                ]
            ]);
        } catch (WebapiException $e) {
            return $this->jsonFactory->create()->setData([
                "status" => 0,
                "result" => $e->getMessage()
            ]);
        }
    }
}
