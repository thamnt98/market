<?php

namespace SM\Notification\Controller\Adminhtml\Index;

class View extends Notification implements \Magento\Framework\App\Action\HttpGetActionInterface
{
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultFactory->create(\Magento\Framework\Controller\ResultFactory::TYPE_PAGE);

        $id    = $this->getRequest()->getParam('id');
        /** @var \SM\Notification\Model\Notification $model */
        $model = $this->notificationFactory->create()->load($id);
        if (!$model || !$model->getId()) {
            $this->messageManager->addErrorMessage(__('This notification no longer exists.'));

            return $this->resultRedirectFactory->create()->setPath('*/*/');
        }

        $resultPage = $this->initPage($resultPage);
        $resultPage->getConfig()->getTitle()->prepend($model->getTitle());

        return $resultPage;
    }
}
