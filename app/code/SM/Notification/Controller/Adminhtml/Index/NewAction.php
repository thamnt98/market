<?php

namespace SM\Notification\Controller\Adminhtml\Index;
use Magento\Backend\App\Action;
use Magento\Framework\App\Action\HttpGetActionInterface;

class NewAction extends \SM\Notification\Controller\Adminhtml\Index\Notification implements HttpGetActionInterface
{
    /**
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultFactory->create(\Magento\Framework\Controller\ResultFactory::TYPE_PAGE);

        $resultPage = $this->initPage($resultPage);
        $resultPage->getConfig()->getTitle()->prepend(__('New Notification'));

        return $resultPage;
    }
}
