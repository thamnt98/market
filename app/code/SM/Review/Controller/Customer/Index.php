<?php

namespace SM\Review\Controller\Customer;

use Magento\Review\Controller\Customer as CustomerController;
use Magento\Framework\Controller\ResultFactory;

/**
 * Class Index
 * @package SM\Review\Controller\Customer
 */
class Index extends CustomerController
{
    /**
     * Render my product reviews
     *
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        /** @var \Magento\Framework\View\Result\Page $resultPage */
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        if ($navigationBlock = $resultPage->getLayout()->getBlock('customer_account_navigation')) {
            $navigationBlock->setActive('review/customer/index');
        }
        if ($block = $resultPage->getLayout()->getBlock('sm_review_customer_list')) {
            $block->setRefererUrl($this->_redirect->getRefererUrl());
        }
        $resultPage->getConfig()->getTitle()->set(__('My Reviews'));
        return $resultPage;
    }
}
