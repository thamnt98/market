<?php

namespace SM\Notification\Controller\Setting;

use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\Context;
use Magento\Review\Controller\Customer as CustomerController;
use Magento\Framework\Controller\ResultFactory;

class Save extends CustomerController
{
    /**
     * @var \SM\Notification\Model\NotificationSettingRepository
     */
    private $notificationSettingRepository;

    /**
     * Save constructor.
     * @param Context $context
     * @param Session $customerSession
     * @param \SM\Notification\Model\NotificationSettingRepository $notificationSettingRepository
     */
    public function __construct(
        Context $context,
        Session $customerSession,
        \SM\Notification\Model\NotificationSettingRepository $notificationSettingRepository
    ) {
        $this->notificationSettingRepository = $notificationSettingRepository;
        parent::__construct($context, $customerSession);
    }

    /**
     * Render notifications
     *
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        $request = $this->_request->getParam('item');
        if ($this->notificationSettingRepository->updateNotificationSetting($this->customerSession->getCustomerId(), $request)) {
            $this->messageManager->addSuccessMessage(__('Notification setting have been saved successful.'));
        } else {
            $this->messageManager->addErrorMessage(__('Notification setting couldn\'t be saved. Please try again!'));
        }
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $resultRedirect->setPath('notification/setting/');
        return $resultRedirect;
    }
}
