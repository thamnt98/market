<?php

namespace SM\Review\Controller\Customer;

use Magento\Framework\Controller\ResultFactory;
use Magento\Review\Controller\Customer as CustomerController;

/**
 * Class Add
 * @package SM\Review\Controller\Customer
 */
class Add extends CustomerController
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
        if ($block = $resultPage->getLayout()->getBlock('sm_review_customer_add')) {
            $block->setRefererUrl($this->_redirect->getRefererUrl());
        }
        $resultPage->getConfig()->getTitle()->set(__('Review Product'));
        return $resultPage;
    }
}
