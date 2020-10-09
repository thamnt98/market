<?php

namespace SM\GTM\Model\Data\Collectors;

use Magento\Authorization\Model\UserContextInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Psr\Log\LoggerInterface;
use SM\GTM\Api\CollectorInterface;
use SM\GTM\Api\MapperInterface;
use SM\GTM\Helper\Data;
use SM\GTM\Model\BasketFactory;
use SM\GTM\Model\ResourceModel\Basket\CollectionFactory;
use SM\StoreLocator\Api\Data\Request\StoreSearchCriteriaInterfaceFactory;
use SM\StoreLocator\Api\StoreLocationRepositoryInterface;
use SM\StoreLocator\Model\Data\Request\StoreSearchCriteria\SortOrderFactory;

/**
 * Class Customer
 * @package SM\GTM\Model\Data\Collectors
 */
class Customer implements CollectorInterface
{
    /**
     * @var UserContextInterface
     */
    private $userContext;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var CustomerRepositoryInterface
     */
    private $customerRepository;

    /**
     * @var MapperInterface
     */
    private $customerMapper;

    /**
     * @var CustomerInterface|null
     */
    private $_customer;
    /**
     * @var CustomerInterface
     */
    private $customerTemplate;
    /**
     * @var CollectionFactory
     */
    private $basketCollectionFactory;
    /**
     * @var BasketFactory
     */
    private $basketFactory;
    /**
     * @var StoreLocationRepositoryInterface
     */
    private $storeLocationRepository;
    /**
     * @var StoreSearchCriteriaInterfaceFactory
     */
    private $storeSearchCriteriaInterfaceFactory;
    /**
     * @var SortOrderFactory
     */
    private $sortOrder;
    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var Session
     */
    protected $customerSession;
    /**
     * @var Data
     */
    protected $helperGtm;

    /**
     * Customer constructor.
     * @param CustomerRepositoryInterface $customerRepository
     * @param MapperInterface $customerMapper
     * @param UserContextInterface $userContext
     * @param LoggerInterface $logger
     * @param CustomerInterface $customerTemplate
     * @param CollectionFactory $basketCollectionFactory
     * @param BasketFactory $basketFactory
     * @param StoreLocationRepositoryInterface $storeLocationRepository
     * @param StoreSearchCriteriaInterfaceFactory $storeSearchCriteriaInterfaceFactory
     * @param SortOrderFactory $sortOrder
     * @param ScopeConfigInterface $scopeConfig
     * @param Session $customerSession
     * @param Data $helperGtm
     */
    public function __construct(
        CustomerRepositoryInterface $customerRepository,
        MapperInterface $customerMapper,
        UserContextInterface $userContext,
        LoggerInterface $logger,
        CustomerInterface $customerTemplate,
        CollectionFactory $basketCollectionFactory,
        BasketFactory $basketFactory,
        StoreLocationRepositoryInterface $storeLocationRepository,
        StoreSearchCriteriaInterfaceFactory $storeSearchCriteriaInterfaceFactory,
        SortOrderFactory $sortOrder,
        ScopeConfigInterface $scopeConfig,
        Session $customerSession,
        Data $helperGtm
    ) {
        $this->customerRepository = $customerRepository;
        $this->customerMapper = $customerMapper;
        $this->userContext = $userContext;
        $this->logger = $logger;
        $this->customerTemplate = $customerTemplate;
        $this->basketCollectionFactory = $basketCollectionFactory;
        $this->basketFactory = $basketFactory;
        $this->storeLocationRepository = $storeLocationRepository;
        $this->storeSearchCriteriaInterfaceFactory = $storeSearchCriteriaInterfaceFactory;
        $this->sortOrder = $sortOrder;
        $this->scopeConfig = $scopeConfig;
        $this->customerSession = $customerSession;
        $this->helperGtm = $helperGtm;
    }

    /**
     * @return bool|\Magento\Customer\Api\Data\CustomerExtensionInterface|CustomerInterface|null
     * @throws \Magento\Framework\Exception\InputException
     */
    private function getCustomer()
    {
        if (!$this->customerSession->isLoggedIn()) {
            $ext = $this->customerTemplate->getExtensionAttributes();
            $ext->setUniqueUserIdGtm('null');
            $ext->setUserIdGtm('null');
            $ext->setLoginTypeGtm('null');
            $ext->setCustomerTypeGtm('Anonymous');
            $ext->setLoyaltyGtm('Not available');
            $ext->setCustomerStatusGtm('New');
            $ext->setStoreGtmName("Not available");
            $ext->setStoreGtmID("Not available");
            $this->customerTemplate->setExtensionAttributes($ext);
            return $this->customerTemplate->getExtensionAttributes();
        }

        try {
            $gtmData = $this->customerSession->getGtmData();
            if (!$gtmData) {
                $data = $this->helperGtm->setStoreInfo();
                if ($data) {
                    $this->_customer = $this->customerSession->getGtmData();
                } else {
                    $this->_customer = $this->customerTemplate->getExtensionAttributes();
                }
            } else {
                $this->_customer = $this->customerSession->getGtmData();
            }
            return $this->_customer;
        } catch (NoSuchEntityException $noSuchEntityException) {
            return false;
        }
    }

    /**
     * @return string|null
     */
    public function getGoogleApiKey()
    {
        return (string)$this->scopeConfig->getValue(self::GOOGLE_API_KEY_XML_PATH) ?? null;
    }

    /**
     * @return string
     */
    public function getAddress()
    {
        $ext = $this->_customer->getExtensionAttributes();
        return $ext->getDistrictName() . ' ' . $ext->getCityName();
    }

    /**
     * @inheritDoc
     * @throws LocalizedException
     */
    public function collect()
    {
        if ($this->getCustomer()) {
            return $this->customerMapper->map($this->getCustomer())->toArray();
        }

        return [];
    }
}
