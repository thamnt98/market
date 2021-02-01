<?php

namespace SM\Customer\Controller\Trans;

use Magento\Framework\App\Action\HttpPostActionInterface as HttpPostActionInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Encryption\Helper\Security;
use Magento\Framework\Stdlib\Cookie\CookieMetadataFactory;
use Magento\Framework\Stdlib\CookieManagerInterface;

class ResetPass extends \Magento\Framework\App\Action\Action implements HttpPostActionInterface
{
    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $resultJsonFactory;

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
     * @var \Magento\Customer\Model\CustomerFactory
     */
    protected $customerFactory;

    /**
     * @var CookieManagerInterface|mixed
     */
    protected $cookieManager;

    /**
     * @var CookieMetadataFactory|mixed
     */
    protected $cookieMetadataFactory;

    /**
     * ResetPass constructor.
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     * @param \Magento\Customer\Model\SessionFactory $customerSessionFactory
     * @param \Magento\Customer\Model\AuthenticationInterface $authentication
     * @param \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository
     * @param \Magento\Customer\Model\CustomerRegistry $customerRegistry
     * @param \Magento\Customer\Model\AccountManagement $accountManagement
     * @param \SM\Customer\Helper\Customer $smCustomerHelper
     * @param \Magento\Customer\Model\CustomerFactory $customerFactory
     * @param CookieManagerInterface|null $cookieManager
     * @param CookieMetadataFactory|null $cookieMetadataFactory
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\Customer\Model\SessionFactory $customerSessionFactory,
        \Magento\Customer\Model\AuthenticationInterface $authentication,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        \Magento\Customer\Model\CustomerRegistry $customerRegistry,
        \Magento\Customer\Model\AccountManagement $accountManagement,
        \SM\Customer\Helper\Customer $smCustomerHelper,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        CookieManagerInterface $cookieManager = null,
        CookieMetadataFactory $cookieMetadataFactory = null
    ) {
        parent::__construct($context);
        $this->resultJsonFactory = $resultJsonFactory;
        $this->customerSession = $customerSessionFactory->create();
        $this->authentication = $authentication;
        $this->customerRepository = $customerRepository;
        $this->customerRegistry = $customerRegistry;
        $this->accountManagement = $accountManagement;
        $this->smCustomerHelper = $smCustomerHelper;
        $this->customerFactory = $customerFactory;
        $this->cookieManager = $cookieManager ?:
            ObjectManager::getInstance()->get(CookieManagerInterface::class);
        $this->cookieMetadataFactory = $cookieMetadataFactory ?:
            ObjectManager::getInstance()->get(CookieMetadataFactory::class);
    }

    /**
     * Login registered users and initiate a session.
     *
     * Expects a POST. ex for JSON {"username":"user@magento.com", "password":"userpassword"}
     *
     * @return \Magento\Framework\Controller\ResultInterface
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function execute()
    {
        $response = [
            'status' => false,
            'name' => '',
            'message' => ''
        ];
        $params = $this->getRequest()->getParams();
        if (isset($params['recoverytoken']) && isset($params['email'])) {
            try {
                $resetPasswordLinkToken = $params['recoverytoken'];
                $customer = $this->customerRepository->get($params['email']);
                $customerId = $customer->getId();
                $customerSecureData = $this->customerRegistry->retrieveSecureData($customerId);
                $rpToken = $customerSecureData->getRpToken();
                $rpTokenCreatedAt = $customerSecureData->getRpTokenCreatedAt();
                if (Security::compareStrings($rpToken, $resetPasswordLinkToken) && !$this->accountManagement->isResetPasswordLinkTokenExpired($rpToken, $rpTokenCreatedAt)) {
                    $this->authentication->unlock($customerId);
                    $this->smCustomerHelper->unLockTokenRequestCustomer($customerId);
                    $this->customerSession->setCustomerDataAsLoggedIn($customer);
                    if ($this->cookieManager->getCookie('mage-cache-sessid')) {
                        $metadata = $this->cookieMetadataFactory->createCookieMetadata();
                        $metadata->setPath('/');
                        $this->cookieManager->deleteCookie('mage-cache-sessid', $metadata);
                    }
                    $response['status'] = true;
                    $response['name'] = $customer->getFirstname();
                }
            } catch (\Exception $e) {
                $response['message'] = $e->getMessage();
            }
        }
        /** @var \Magento\Framework\Controller\Result\Json $resultJson */
        $resultJson = $this->resultJsonFactory->create();
        return $resultJson->setData($response);
    }
}
