<?php


namespace SM\ShoppingList\Controller\MyList;

use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\View\Result\Page;
use SM\ShoppingList\Api\Data\ShoppingListDataInterface;
use SM\ShoppingList\Controller\ListAction;

/**
 * Class Detail
 * @package SM\ShoppingList\Controller\MyList
 */
class Detail extends ListAction
{
    /**
     * @return ResponseInterface|ResultInterface
     */
    public function execute()
    {
        if (!$this->customerSession->isLoggedIn()) {
            return $this->_redirect($this->_url->getUrl());
        }
        /** @var Page $resultPage */
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);

        if ($this->getRequest()->getParam("id")) {
            $shoppingListId = $this->getRequest()->getParam("id");
            $shoppingList = $this->shoppingListRepository->getById($shoppingListId);
            $this->customerSession->setData("current_shoppinglist_detail", $shoppingList);
            $this->addBreadCrumb($shoppingList);
        }

        return $resultPage;
    }

    /**
     * Add breadcrumb for list name
     *
     * @param ShoppingListDataInterface $shoppingList
     */
    public function addBreadCrumb($shoppingList)
    {
        /** @var \Magento\Theme\Block\Html\Breadcrumbs $breadcrumbs */
        $breadcrumbs = $this->_view->getLayout()->getBlock('breadcrumbs');
        if ($breadcrumbs) {
            $breadcrumbs->addCrumb(
                'my_lists',
                [
                    'label' => __("My Lists"),
                    'title' => __("My Lists"),
                    'link' => $this->_url->getUrl("shoppinglist/mylist")
                ]
            );
            $breadcrumbs->addCrumb(
                'list_name',
                [
                    'label' => $shoppingList->getName(),
                    'title' => $shoppingList->getName()
                ]
            );
        }
    }
}
