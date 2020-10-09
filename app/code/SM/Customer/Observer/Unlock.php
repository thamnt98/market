<?php

namespace SM\Customer\Observer;

use Magento\Framework\Encryption\Helper\Security;
use Magento\Framework\Event\Observer;

/**
 * Class Unlock
 * @package SM\Customer\Observer
 */
class Unlock implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @var \Magento\Customer\Model\AuthenticationInterface
     */
    protected $authentication;

    /**
     * @var \Magento\Customer\Api\CustomerRepositoryInterface
     */
    protected $customerRepository;

    /**
     * @var \Magento\Customer\Model\CustomerRegistry
     */
    protected $customerRegistry;

    /**
     * @var \Magento\Customer\Model\AccountManagement
     */
    protected $accountManagement;

    /**
     * @var \SM\Customer\Helper\Customer
     */
    protected $smCustomerHelper;

    /**
     * Unlock constructor.
     * @param \Magento\Customer\Model\SessionFactory $customerSessionFactory
     * @param \Magento\Customer\Model\AuthenticationInterface $authentication
     * @param \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository
     * @param \Magento\Customer\Model\CustomerRegistry $customerRegistry
     * @param \Magento\Customer\Model\AccountManagement $accountManagement
     * @param \SM\Customer\Helper\Customer $smCustomerHelper
     */
    public function __construct(
        \Magento\Customer\Model\SessionFactory $customerSessionFactory,
        \Magento\Customer\Model\AuthenticationInterface $authentication,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        \Magento\Customer\Model\CustomerRegistry $customerRegistry,
        \Magento\Customer\Model\AccountManagement $accountManagement,
        \SM\Customer\Helper\Customer $smCustomerHelper
    ) {
        $this->customerSession = $customerSessionFactory->create();
        $this->authentication = $authentication;
        $this->customerRepository = $customerRepository;
        $this->customerRegistry = $customerRegistry;
        $this->accountManagement = $accountManagement;
        $this->smCustomerHelper = $smCustomerHelper;
    }

    /**
     * @inheritDoc
     * @throws \Exception
     */
    public function execute(Observer $observer)
    {
        if (!$this->customerSession->isLoggedIn()) {
            $params = $observer->getEvent()->getRequest()->getParams();
            if (isset($params['recoverytoken']) && isset($params['email'])) {
                try {
                    $resetPasswordLinkToken = $params['recoverytoken'];
                    $customerId = $this->customerRepository->get($params['email'])->getId();
                    $customerSecureData = $this->customerRegistry->retrieveSecureData($customerId);
                    $rpToken = $customerSecureData->getRpToken();
                    $rpTokenCreatedAt = $customerSecureData->getRpTokenCreatedAt();
                    if (Security::compareStrings($rpToken, $resetPasswordLinkToken) && !$this->accountManagement->isResetPasswordLinkTokenExpired($rpToken, $rpTokenCreatedAt)) {
                        $this->authentication->unlock($customerId);
                        $this->smCustomerHelper->unLockTokenRequestCustomer($customerId);
                        $this->customerSession->loginById($customerId);
                    }
                } catch (\Exception $e) {
                }
            }
        }
    }
}
