<?php


namespace SM\ShoppingList\Controller\Ajax;

use Exception;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Webapi\Exception as WebapiException;
use SM\ShoppingList\Api\Data\ShoppingListDataInterface;
use SM\ShoppingList\Controller\ListAction;

class UpdateList extends ListAction
{
    /**
     * @return ResponseInterface|Json|ResultInterface
     */
    public function execute()
    {
        $data = $this->getRequest()->getPostValue();

        /** @var ShoppingListDataInterface $listData */
        $listData = $this->listDataFactory->create();
        $listData->setName($data["update_list_name"]);
        $listData->setWishlistId($data["wishlist_id"]);
        $customerId = $this->currentCustomer->getCustomerId();

        try {
            /** @var ShoppingListDataInterface $result */
            $result = $this->shoppingListRepository->update(
                $listData,
                $customerId
            );
            $this->messageManager->addSuccessMessage(__("You have successfully renamed this list."));
            return $this->jsonFactory->create()->setData([
                "status" => 1,
                "result" => $result
            ]);
        } catch (WebapiException $e) {
            return $this->jsonFactory->create()->setData([
                "status" => 0,
                "result" => $e->getMessage()
            ]);
        }
    }
}
