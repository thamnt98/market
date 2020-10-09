<?php


namespace SM\ShoppingList\Controller\Ajax;

use Exception;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\NoSuchEntityException;
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
        $listData->setWishlistId($data["list_id"]);
        $customerId = $this->currentCustomer->getCustomerId();

        try {
            /** @var ShoppingListDataInterface $result */
            $result = $this->shoppingListRepository->update(
                $listData,
                $customerId
            );
            return $this->jsonFactory->create()->setData([
                "status" => 1,
                "result" => $result
            ]);
        } catch (NoSuchEntityException $e) {
            return $this->jsonFactory->create()->setData([
                "status" => 0,
                "result" => __('Shopping list with id "%1" does not exist', $listData->getWishlistId())
            ]);
        } catch (Exception $e) {
            return $this->jsonFactory->create()->setData([
                "status" => 0,
                "result" => __("Shopping list name is already exist. Please try again")
            ]);
        }
    }
}
