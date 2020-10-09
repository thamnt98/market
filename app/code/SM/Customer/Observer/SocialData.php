<?php

namespace SM\Customer\Observer;

use Magento\Customer\Model\Session;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Mageplaza\SocialLogin\Model\ResourceModel\Social\Collection as SocialCollection;
use Mageplaza\SocialLogin\Model\ResourceModel\Social\CollectionFactory as SocialCollectionFactory;

/**
 * Class SocialData
 * @package SM\Customer\Observer
 */
class SocialData implements ObserverInterface
{
    /**
     * @var Session
     */
    protected $customerSession;

    /**
     * @var SocialCollectionFactory
     */
    protected $socialCollectionFactory;

    /**
     * SocialData constructor.
     * @param Session $customerSession
     * @param SocialCollectionFactory $socialCollectionFactory
     */
    public function __construct(
        Session $customerSession,
        SocialCollectionFactory $socialCollectionFactory
    ) {
        $this->customerSession = $customerSession;
        $this->socialCollectionFactory = $socialCollectionFactory;
    }

    /**
     * @inheritDoc
     */
    public function execute(Observer $observer)
    {
        /** @var \Magento\Customer\Model\Customer $customer */
        $customer = $observer->getEvent()->getCustomer();
        if (!$customer->getId()) {
            return $this;
        }
        /** @var SocialCollection $socialCollection */
        $socialCollection = $this->socialCollectionFactory->create();
        $customerSocial = $socialCollection
            ->join("customer_entity", "customer_entity.entity_id = main_table.customer_id", ["password_hash"])
            ->addFieldToFilter('customer_id', $customer->getId())->getFirstItem();
        if ($customerSocial->getId() && $customerSocial->getData("password_hash") == null) {
            $this->customerSession->setIsSocial(true);
        } else {
            $this->customerSession->setIsSocial(false);
        }
        return $this;
    }
}
