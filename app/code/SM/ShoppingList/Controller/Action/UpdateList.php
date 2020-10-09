<?php

namespace SM\ShoppingList\Controller\Action;

use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\ResultInterface;
use SM\ShoppingList\Controller\ListAction;

/**
 * Class UpdateList
 * @package SM\ShoppingList\Controller\Action
 */
class UpdateList extends ListAction
{
    /**
     * @return ResponseInterface|Redirect|ResultInterface
     */
    public function execute()
    {
        /** @var Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();

        $shoppingListId = $this->getRequest()->getPostValue("shoppinglist_id");
        $this->messageManager->addSuccessMessage(__("You have successfully renamed this list."));
        $resultRedirect->setPath("*/mylist/detail", ['id' => $shoppingListId]);

        return $resultRedirect;
    }
}
