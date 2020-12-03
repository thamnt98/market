<?php
/**
 * Class LoginAjax
 * @package SM\Customer\Controller\Trans
 * @author Son Nguyen <sonnn@smartosc.com>
 */

namespace SM\Customer\Controller\Trans;

use Magento\Customer\Api\AccountManagementInterface;
use Magento\Customer\Model\Account\Redirect as AccountRedirect;
use Magento\Framework\App\Action\HttpPostActionInterface as HttpPostActionInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Stdlib\Cookie\CookieMetadataFactory;
use Magento\Framework\Stdlib\CookieManagerInterface;
use Magento\Store\Model\StoreResolver;
use Magento\Framework\App\RequestFactory;
use Magento\Customer\Model\CustomerExtractor;
use Magento\Customer\Model\ResourceModel\Customer\CollectionFactory as CustomerCollectionFactory;
use Magento\Customer\Model\ResourceModel\AddressRepository;
use Magento\Customer\Model\AddressFactory;
use Magento\Customer\Model\ResourceModel\CustomerFactory;
use Magento\Customer\Model\Customer;
use Magento\Customer\Api\CustomerRepositoryInterface;

class LoginAjax extends \Magento\Framework\App\Action\Action implements HttpPostActionInterface
{
    const CODE_REGION_DEFAULT_ADDRESS = '1960';
    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @var AccountManagementInterface
     */
    protected $customerAccountManagement;

    /**
     * @var \Magento\Framework\Json\Helper\Data $helper
     */
    protected $helper;

    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $resultJsonFactory;

    /**
     * @var \Magento\Framework\Controller\Result\RawFactory
     */
    protected $resultRawFactory;

    /**
     * @var \SM\AndromedaSms\Api\SmsVerificationInterface
     */
    protected $smsVerification;

    /**
     * @var \SM\Customer\Api\TransCustomerRepositoryInterface
     */
    protected $transCustomer;

    /**
     * @var AccountRedirect
     */
    protected $accountRedirect;

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var CookieManagerInterface
     */
    private $cookieManager;

    /**
     * @var CookieMetadataFactory
     */
    private $cookieMetadataFactory;

    /**
     * @var RequestFactory
     */
    protected $requestFactory;

    /**
     * @var CustomerExtractor
     */
    protected $customerExtractor;

    /**
     * @var CustomerExtractor
     */
    protected $customerRepository;

    /**
     * @var CustomerCollectionFactory
     */
    protected $customerCollectionFactory;

    /**
     * @var AddressFactory
     */
    protected $addressFactory;

    /**
     * @var AddressRepository
     */
    protected $addressRepository;

    /**
     * @var CustomerFactory
     */
    protected $customerFactory;

    /**
     * @var Customer
     */
    protected $customer;

    /**
     * @var Customer
     */
    protected $_customerRepository;

    /**
     * LoginAjax constructor.
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Framework\Json\Helper\Data $helper
     * @param AccountManagementInterface $customerAccountManagement
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     * @param \Magento\Framework\Controller\Result\RawFactory $resultRawFactory
     * @param \SM\AndromedaSms\Api\SmsVerificationInterface $smsVerification
     * @param \SM\Customer\Api\TransCustomerRepositoryInterface $transCustomer
     * @param CookieManagerInterface|NULL $cookieManager
     * @param CookieMetadataFactory|NULL $cookieMetadataFactory
     * @param RequestFactory $requestFactory
     * @param CustomerExtractor $customerExtractor
     * @param CustomerCollectionFactory $customerCollectionFactory
     * @param AddressFactory $addressFactory
     * @param AddressRepository $addressRepository
     * @param CustomerFactory $customerFactory
     * @param Customer $customer
     * @param CustomerRepositoryInterface $customerRepository
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\Json\Helper\Data $helper,
        AccountManagementInterface $customerAccountManagement,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\Framework\Controller\Result\RawFactory $resultRawFactory,
        \SM\AndromedaSms\Api\SmsVerificationInterface $smsVerification,
        \SM\Customer\Api\TransCustomerRepositoryInterface $transCustomer,
        CookieManagerInterface $cookieManager = null,
        CookieMetadataFactory $cookieMetadataFactory = null,
        RequestFactory $requestFactory,
        CustomerExtractor $customerExtractor,
        CustomerCollectionFactory $customerCollectionFactory,
        AddressFactory $addressFactory,
        AddressRepository $addressRepository,
        CustomerFactory $customerFactory,
        Customer $customer,
        CustomerRepositoryInterface $customerRepository
    ) {
        parent::__construct($context);
        $this->customerSession = $customerSession;
        $this->helper = $helper;
        $this->customerAccountManagement = $customerAccountManagement;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->resultRawFactory = $resultRawFactory;
        $this->smsVerification = $smsVerification;
        $this->transCustomer = $transCustomer;
        $this->cookieManager = $cookieManager ?:
            ObjectManager::getInstance()->get(CookieManagerInterface::class);
        $this->cookieMetadataFactory = $cookieMetadataFactory ?:
            ObjectManager::getInstance()->get(CookieMetadataFactory::class);
        $this->requestFactory = $requestFactory;
        $this->customerExtractor = $customerExtractor;
        $this->customerCollectionFactory = $customerCollectionFactory;
        $this->addressFactory = $addressFactory;
        $this->addressRepository = $addressRepository;
        $this->_customerRepository = $customerRepository;
    }

    /**
     * Get account redirect.
     *
     * @return AccountRedirect
     */
    protected function getAccountRedirect()
    {
        if (!is_object($this->accountRedirect)) {
            $this->accountRedirect = ObjectManager::getInstance()->get(AccountRedirect::class);
        }
        return $this->accountRedirect;
    }

    /**
     * Account redirect setter for unit tests.
     *
     * @param AccountRedirect $value
     * @return void
     */
    public function setAccountRedirect($value)
    {
        $this->accountRedirect = $value;
    }

    /**
     * Initializes config dependency.
     *
     * @return ScopeConfigInterface
     */
    protected function getScopeConfig()
    {
        if (!is_object($this->scopeConfig)) {
            $this->scopeConfig = ObjectManager::getInstance()->get(ScopeConfigInterface::class);
        }
        return $this->scopeConfig;
    }

    /**
     * Sets config dependency.
     *
     * @param ScopeConfigInterface $value
     * @return void
     * @deprecated 100.0.10
     */
    public function setScopeConfig($value)
    {
        $this->scopeConfig = $value;
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
        $credentials = null;
        $httpBadRequestCode = 400;

        /** @var \Magento\Framework\Controller\Result\Raw $resultRaw */
        $resultRaw = $this->resultRawFactory->create();
        try {
            $credentials = $this->helper->jsonDecode($this->getRequest()->getContent());
        } catch (\Exception $e) {
            return $resultRaw->setHttpResponseCode($httpBadRequestCode);
        }
        if (!$credentials || $this->getRequest()->getMethod() !== 'POST' || !$this->getRequest()->isXmlHttpRequest()) {
            return $resultRaw->setHttpResponseCode($httpBadRequestCode);
        }
        $response = [
            'status' => false,
            'errors' => false,
            'message' => __('Login successful.')
        ];

        $browserCustomer = $credentials['device_customers'] ?? [];
        /** login with case customer exist on ecosystem*/
        if (isset($credentials['case_user_on_eco_system'])
            && isset($credentials['username'])
            && isset($credentials['phonenumber'])
            && $credentials['case_user_on_eco_system']) {
            try {
                $result = false;
                if(isset($credentials['step']) && $credentials['step'] == 3){
                    $result = $this->smsVerification->verify($credentials['username'], $credentials['otp'], '');
                }
                if ($result == true) {
                    $customerCollection = $this->customerCollectionFactory->create();
                    $customerHasExist           = $customerCollection->addFieldToFilter('email', $credentials['email'])->getItems();
                    if (empty($customerHasExist)) {
                        $lastName = $credentials['lastname'] != '' ? $credentials['lastname'] : $credentials['firstname'];
                        $customerData = [
                            'firstname' => $credentials['firstname'],
                            'lastname' => $lastName,
                            'email' => $credentials['email'],
                        ];
                        $redirectUrl = $this->_url->getBaseUrl();
                        $password = NULL;
                        $request = $this->requestFactory->create();
                        $request->setParams($customerData);
                        $customer = $this->customerExtractor->extract('customer_account_create', $request);
                        $customer = $this->customerAccountManagement->createAccount($customer, $password, $redirectUrl);
                        $this->saveCustomerTelephone($customer->getId(), $credentials['username']);

                        /*process login*/
                        $customer = $this->transCustomer->getByPhone($credentials['phonenumber']);
                        if ($customer) {
                            $this->addAddressDefault($customer, $credentials['phonenumber'], $credentials['city'],
                                $credentials['district']);

                            $this->customerSession->setCustomerDataAsLoggedIn($customer);
                            $redirectRoute = $this->getAccountRedirect()->getRedirectCookie();
                            $browserCustomer[] = $customer->getId();
                            $response['device_customers'] = array_unique($browserCustomer);
                            if ($this->cookieManager->getCookie('mage-cache-sessid')) {
                                $metadata = $this->cookieMetadataFactory->createCookieMetadata();
                                $metadata->setPath('/');
                                $this->cookieManager->deleteCookie('mage-cache-sessid', $metadata);
                            }
                            if (!$this->getScopeConfig()->getValue('customer/startup/redirect_dashboard') && $redirectRoute) {
                                $response['redirectUrl'] = $this->_redirect->success($redirectRoute);
                                $this->getAccountRedirect()->clearRedirectCookie();
                            } else {
                                $response['redirectUrl'] = $this->_url->getBaseUrl();
                            }

                            // Handle Shopping List share after customer login
                            // Start
                            if (!is_null($this->customerSession->getData("after_auth_url"))) {
                                $url = $this->customerSession->getData("after_auth_url");
                                if (strpos($url, "shoppinglist") !== false) {
                                    $response['redirectUrl'] = $url;
                                    $this->customerSession->setAfterAuthUrl("");
                                }
                            } // END
                        }
                    } else {
                        $response = [
                            'errors' => true,
                            'message' => __('Email customer has exist!'),
                        ];
                    }
                }
            } catch (LocalizedException $e) {
                $response = [
                    'errors' => true,
                    'message' => $e->getMessage(),
                ];
            } catch (\Exception $e) {
                $response = [
                    'errors' => true,
                    'message' => $e->getMessage(),
                ];
            }

            /** @var \Magento\Framework\Controller\Result\Json $resultJson */
            $resultJson = $this->resultJsonFactory->create();
            return $resultJson->setData($response);
        }

        // case login account normally
        try {
            $customer = false;
            if (isset($credentials['password'])) {
                /*login by email*/
                $customer = $this->customerAccountManagement->authenticate(
                    $credentials['username'],
                    $credentials['password']
                );
            } else {
                /*login by mobile*/
                $result = $this->smsVerification->verify($credentials['username'], $credentials['otp'], '');
                if ($result == true) {
                    $customer = $this->transCustomer->getByPhone($credentials['username']);
                }
            }
            if ($customer) {
                $this->customerSession->setCustomerDataAsLoggedIn($customer);
                $redirectRoute = $this->getAccountRedirect()->getRedirectCookie();
                if ($this->cookieManager->getCookie('mage-cache-sessid')) {
                    $metadata = $this->cookieMetadataFactory->createCookieMetadata();
                    $metadata->setPath('/');
                    $this->cookieManager->deleteCookie('mage-cache-sessid', $metadata);
                }

                $browserCustomer[] = $customer->getId();
                $response['device_customers'] = array_unique($browserCustomer);
                if (!$this->getScopeConfig()->getValue('customer/startup/redirect_dashboard') && $redirectRoute) {
                    $response['redirectUrl'] = $this->_redirect->success($redirectRoute);
                    $this->getAccountRedirect()->clearRedirectCookie();
                } else {
                    $response['redirectUrl'] = $this->_url->getBaseUrl();
                }

                // Handle Shopping List share after customer login
                // Start
                if (!is_null($this->customerSession->getData("after_auth_url"))) {
                    $url = $this->customerSession->getData("after_auth_url");
                    if (strpos($url, "shoppinglist") !== false) {
                        $response['redirectUrl'] = $url;
                        $this->customerSession->setAfterAuthUrl("");
                    }
                } // END
            } else {
                $response = [
                    'status' => false,
                    'errors' => true,
                    'message' => __('System error, please try again later.')
                ];
            }
        } catch (LocalizedException $e) {
            $response = [
                'errors' => true,
                'message' => $e->getMessage(),
            ];
            if ($e->getMessage() == __('The account is locked.')) {
                $response['status'] = true;
            }
        } catch (\Exception $e) {
            $response = [
                'errors' => true,
                'message' => $e->getMessage(),
            ];
            if ($e->getMessage() == __('The account is locked.')) {
                $response['status'] = true;
            }
        }

        /** @var \Magento\Framework\Controller\Result\Json $resultJson */
        $resultJson = $this->resultJsonFactory->create();
        return $resultJson->setData($response);
    }

    /**
     * @param $customer
     * @param $telephone
     * @param $city
     * @param $district
     * @throws LocalizedException
     */
    private function addAddressDefault($customer, $telephone, $city, $district)
    {
        $address = $this->addressFactory->create();
        $address->setCustomAttribute('address_tag', 'Home');
        $address->setTelephone($telephone);
        $address->setRegionId(self::CODE_REGION_DEFAULT_ADDRESS);
        $address->setCity($city);
        $address->setCustomAttribute('district', $district);
        $address->setCustomerId($customer->getId());
        $address->setFirstname($customer->getFirstname());
        $address->setLastname($customer->getLastname());
        $address->setStreet(['N/A']);
        $address->setPostcode('*****');
        $address->setCountryId('ID');
        $address->setIsDefaultBilling(true);
        $address->setIsDefaultShipping(true);

        try {
            $address->save();
        } catch (LocalizedException $e) {
            throw new LocalizedException(__($e->getMessage()));
        }
    }

    /**
     * @param $customerId
     * @param $telephone
     * @throws LocalizedException
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\State\InputMismatchException
     */
    private function saveCustomerTelephone($customerId, $telephone)
    {
        $customer = $this->_customerRepository->getById($customerId);
        $customer->setCustomAttribute('telephone',$telephone);
        $this->_customerRepository->save($customer);
    }
}
