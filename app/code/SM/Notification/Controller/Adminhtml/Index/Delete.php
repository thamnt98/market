<?php

namespace SM\Notification\Controller\Adminhtml\Index;

class Delete extends \Magento\Backend\App\Action
{
    /**
     * @var \SM\Notification\Model\NotificationFactory
     */
    protected $modelFactory;

    /**
     * @var \SM\Notification\Model\ResourceModel\Notification
     */
    protected $resource;

    /**
     * Delete constructor.
     *
     * @param \Magento\Backend\App\Action\Context               $context
     * @param \SM\Notification\Model\NotificationFactory        $model
     * @param \SM\Notification\Model\ResourceModel\Notification $resource
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \SM\Notification\Model\NotificationFactory $model,
        \SM\Notification\Model\ResourceModel\Notification $resource
    ) {
        parent::__construct($context);

        $this->modelFactory = $model;
        $this->resource = $resource;
    }

    public function execute()
    {
        /** @var \SM\Notification\Model\Notification $notification */
        $notification = $this->modelFactory->create()
            ->load($this->getRequest()->getParam('id', 0));
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($notification && $notification->getId()) {
            try {
                $this->resource->delete($notification);
                $this->messageManager->addSuccessMessage(__('The data has been deleted.'));

                return $resultRedirect->setPath('*/*/');
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());

                return $resultRedirect->setPath('*/*/');
            }
        } else {
            $this->messageManager->addErrorMessage(__('This data no longer exists.'));

            return $resultRedirect->setPath('*/*/');
        }
    }
}
