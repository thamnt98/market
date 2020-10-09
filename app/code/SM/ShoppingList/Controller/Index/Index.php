<?php

namespace SM\ShoppingList\Controller\Index;

use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\View\Result\Page;
use SM\ShoppingList\Controller\ListAction;

class Index extends ListAction
{
    /**
     * @return ResponseInterface|ResultInterface
     */
    public function execute()
    {
        if (!$this->customerSession->isLoggedIn()) {
            return $this->_redirect($this->_url->getUrl());
        }
        /** @var Page resultPage */
        return $this->resultFactory->create(ResultFactory::TYPE_PAGE);
    }
}
