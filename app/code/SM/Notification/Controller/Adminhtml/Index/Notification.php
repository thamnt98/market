<?php

namespace SM\Notification\Controller\Adminhtml\Index;

use Magento\Backend\App\Action;

abstract class Notification extends \Magento\Backend\App\Action
{
    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var \SM\Notification\Model\NotificationFactory
     */
    protected $notificationFactory;

    public function __construct(
        Action\Context $context,
        \Magento\Framework\Registry $registry,
        \SM\Notification\Model\NotificationFactory $notificationFactory
    ) {
        $this->registry = $registry;
        $this->notificationFactory = $notificationFactory;
        parent::__construct($context);
    }

    protected function initPage($resultPage)
    {
        $resultPage->setActiveMenu('SM_Notification::notification_manage');
        $resultPage->getConfig()->getTitle()->prepend(__('Notification'));

        return $resultPage;
    }

    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('SM_Notification::notification_manage');
    }
}
