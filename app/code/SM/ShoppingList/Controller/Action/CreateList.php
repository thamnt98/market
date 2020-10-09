<?php

namespace SM\ShoppingList\Controller\Action;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;

/**
 * Class CreateList
 * @package SM\ShoppingList\Controller\Action
 */
class CreateList extends Action
{

    /**
     * @return ResponseInterface|ResultInterface
     */
    public function execute()
    {
        $shoppingListName = $this->getRequest()->getPostValue("shoppinglist_name");
        $this->messageManager->addSuccessMessage(__("You have successfully created %1." , $shoppingListName));
        return $this->_redirect("*/mylist");
    }
}
