<?php

namespace SM\Notification\Controller\Index;

use Magento\Review\Controller\Customer as CustomerController;

class Test extends CustomerController
{
    /**
     * Render notifications
     *
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        $params = $this->getRequest()->getParams();
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $order = $objectManager->create('\Magento\Sales\Model\Order') ->load($params['order_id']);
        $order->setStatus('failed_delivery');
        $order->save();
    }
}
