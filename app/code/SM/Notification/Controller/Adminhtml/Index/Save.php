<?php

namespace SM\Notification\Controller\Adminhtml\Index;

use Magento\Framework\Exception\InputException;

class Save extends \Magento\Backend\App\Action
{
    /**
     * @var \SM\Notification\Model\NotificationFactory
     */
    protected $notificationFactory;

    /**
     * @var \SM\Notification\Model\ResourceModel\Notification
     */
    protected $resource;

    /**
     * Save constructor.
     *
     * @param \SM\Notification\Model\NotificationFactory        $notificationFactory
     * @param \SM\Notification\Model\ResourceModel\Notification $resource
     * @param \Magento\Backend\App\Action\Context               $context
     */
    public function __construct(
        \SM\Notification\Model\NotificationFactory $notificationFactory,
        \SM\Notification\Model\ResourceModel\Notification $resource,
        \Magento\Backend\App\Action\Context $context
    ) {
        parent::__construct($context);

        $this->notificationFactory = $notificationFactory;
        $this->resource = $resource;
    }

    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        /** @var \SM\Notification\Model\Notification $notification */
        $notification = $this->notificationFactory->create();
        try {
            $data = $this->prepareData();
            $notification->setData($data)->setId(null);
            $this->resource->save($notification);
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());

            return $resultRedirect->setUrl($this->_redirect->getRefererUrl());
        }

        $resultRedirect->setPath('*/*/');
        $this->messageManager->addSuccessMessage('The notification has been created successful!');

        return $resultRedirect;
    }

    /**
     * @return mixed
     * @throws InputException
     */
    protected function prepareData()
    {
        $data = $this->getRequest()->getPostValue();
        if (empty($data['start_date']) || empty($data['end_date'])) {
            throw new InputException(__('`Start Date` and `End Date` are not empty!'));
        }

        if (strtotime($data['start_date']) >= strtotime($data['end_date'])) {
            throw new InputException(__('`End Date` must be after `Start Date`!'));
        }

        if ($data['admin_type'] == \SM\Notification\Model\Source\CustomerType::TYPE_CUSTOMER &&
            empty($data['customer_ids'])
        ) {
            throw new InputException(__('`Customer (s)` is required!'));
        } elseif ($data['admin_type'] == \SM\Notification\Model\Source\CustomerType::TYPE_CUSTOMER_SEGMENT &&
            empty($data['segment_ids'])
        ) {
            throw new InputException(__('`Segment (s)` is required!'));
        }

        if (isset($data['image']) && is_array($data['image'])) {
            $data['image'] = $data['image'][0]['url'] ?? '';
        }

        if (isset($data['redirect_id']) && $data['redirect_id'] == '') {
            unset($data['redirect_id']);
        }

        $data['status'] = \SM\Notification\Model\Notification::SYNC_PENDING;

        return $data;
    }
}
