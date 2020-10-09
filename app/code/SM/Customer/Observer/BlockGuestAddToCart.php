<?php

namespace SM\Customer\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class BlockGuestAddToCart
 * @package SM\Customer\Observer
 */
class BlockGuestAddToCart implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * BlockGuestAddToCart constructor.
     *
     * @param \Magento\Customer\Model\SessionFactory $customerSessionFactory
     */
    public function __construct(
        \Magento\Customer\Model\SessionFactory $customerSessionFactory
    ) {
        $this->customerSession = $customerSessionFactory->create();
    }

    /**
     * @inheritDoc
     * @throws \Exception
     */
    public function execute(Observer $observer)
    {
        if (!$this->customerSession->isLoggedIn()) {
            throw new LocalizedException(
                __('We can\'t add this item to your shopping cart right now. Please log in first.')
            );
        }
    }
}
