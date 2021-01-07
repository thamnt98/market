<?php

namespace SM\Notification\Controller\Index;


class Test extends \Magento\Framework\App\Action\Action
{
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $pageFactory,
        \Magento\Sales\Model\OrderFactory $orderFactory
    ) {
        $this->_pageFactory = $pageFactory;
        $this->orderFactory = $orderFactory;
        return parent::__construct($context);
    }

    /**
     * Render notifications
     *
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        $params = $this->getRequest()->getParams();
        $order = $this->orderFactory->create()->load($params['order_id']);
        /** @var \Magento\Quote\Model\Order $order */
        $order->setStatus($params['status']);
        $order->save();
    }
}
