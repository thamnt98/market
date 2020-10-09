<?php

namespace SM\GTM\Observer;

use Magento\Authorization\Model\UserContextInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\View\Layout;

/**
 * Class LayoutLoadBefore
 * @package SM\GTM\Observer
 */
class LayoutLoadBefore implements ObserverInterface
{
    /**
     * @var UserContextInterface
     */
    private $userContext;

    /**
     * LayoutLoadBefore constructor.
     * @param UserContextInterface $sessionUserContext
     */
    public function __construct(UserContextInterface $sessionUserContext)
    {
        $this->userContext = $sessionUserContext;
    }

    /**
     * @inheritDoc
     */
    public function execute(Observer $observer)
    {
        if ($this->userContext->getUserId()) {
            /** @var Layout $layout */
            $layout = $observer->getLayout();
            $layout->getUpdate()->addHandle('customer_logged_in');
        }
    }
}
