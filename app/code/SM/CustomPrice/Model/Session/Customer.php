<?php

namespace SM\CustomPrice\Model\Session;

use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Api\GroupManagementInterface;
use Magento\Customer\Model\AccountConfirmation;
use Magento\Customer\Model\Config\Share;
use Magento\Customer\Model\CustomerFactory;
use Magento\Customer\Model\ResourceModel\Customer as ResourceCustomer;
use Magento\Customer\Model\Session;
use Magento\Customer\Model\Url;
use Magento\Framework\App\Area;
use Magento\Framework\App\State;
use SM\CustomPrice\Model\ResourceModel\District;

class Customer extends Session
{
    /**
     * @var District
     */
    protected $district;
    /**
     * @var State
     */
    protected $state;

    /**
     * @var \SM\MobileApi\Model\Authorization\TokenUserContext
     */
    protected $smartTokenUserContext;

    /**
     * @var \Magento\Eav\Model\Config
     */
    protected $eavConfig;


    public function __construct(
        \Magento\Eav\Model\Config $eavConfig,
        \Magento\Framework\App\Request\Http $request,
        \Magento\Framework\Session\SidResolverInterface $sidResolver,
        \Magento\Framework\Session\Config\ConfigInterface $sessionConfig,
        \Magento\Framework\Session\SaveHandlerInterface $saveHandler,
        \Magento\Framework\Session\ValidatorInterface $validator,
        \Magento\Framework\Session\StorageInterface $storage,
        \Magento\Framework\Stdlib\CookieManagerInterface $cookieManager,
        \Magento\Framework\Stdlib\Cookie\CookieMetadataFactory $cookieMetadataFactory,
        \Magento\Framework\App\State $appState,
        Share $configShare,
        \Magento\Framework\Url\Helper\Data $coreUrl,
        Url $customerUrl,
        ResourceCustomer $customerResource,
        CustomerFactory $customerFactory,
        \Magento\Framework\UrlFactory $urlFactory,
        \Magento\Framework\Session\Generic $session,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Framework\App\Http\Context $httpContext,
        CustomerRepositoryInterface $customerRepository,
        GroupManagementInterface $groupManagement,
        \Magento\Framework\App\Response\Http $response,
        District $district,
        State $state,
        \SM\MobileApi\Model\Authorization\TokenUserContext $smartTokenUserContext,
        AccountConfirmation $accountConfirmation = null
    ) {
        parent::__construct(
            $request,
            $sidResolver,
            $sessionConfig,
            $saveHandler,
            $validator,
            $storage,
            $cookieManager,
            $cookieMetadataFactory,
            $appState,
            $configShare,
            $coreUrl,
            $customerUrl,
            $customerResource,
            $customerFactory,
            $urlFactory,
            $session,
            $eventManager,
            $httpContext,
            $customerRepository,
            $groupManagement,
            $response,
            $accountConfirmation
        );

        $this->district              = $district;
        $this->state                 = $state;
        $this->customerRepository    = $customerRepository;
        $this->smartTokenUserContext = $smartTokenUserContext;
        $this->eavConfig = $eavConfig;
    }

    /**
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getOmniFinalPriceAttributeCode()
    {
        $omni_store_id = $this->getOmniStoreId();
        return \SM\CustomPrice\Model\Customer::PREFIX_OMNI_FINAL_PRICE . $omni_store_id;
    }

    /**
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getOmniNormalPriceAttributeCode()
    {
        $omni_store_id = $this->getOmniStoreId();
        return \SM\CustomPrice\Model\Customer::PREFIX_OMNI_NORMAL_PRICE . $omni_store_id;
    }

    /**
     * @return mixed
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getOmniStoreId()
    {
        $isLoginByAPI = $this->isLoggedInByAPI();
        if ($isLoginByAPI) {
            $customerId = $this->smartTokenUserContext->getUserId();
            $customer   = $this->customerRepository->getById($customerId);
            if (!empty($customer)) {
                $omni_store_id = $customer->getCustomAttribute(\SM\CustomPrice\Model\Customer::OMNI_STORE_ID);
            }
        } elseif ($this->isLoggedIn()) {
            $omni_store_id = $this->getCustomerData()->getCustomAttribute(\SM\CustomPrice\Model\Customer::OMNI_STORE_ID);
        } else {
            return $this->getDefaultOmniStoreCode();
        }
        if (!empty($omni_store_id)) {
            $basePriceCode = \SM\CustomPrice\Model\Customer::PREFIX_OMNI_NORMAL_PRICE . $omni_store_id->getValue();
            $basePriceAttr = $this->eavConfig->getAttribute(\Magento\Catalog\Model\Product::ENTITY, $basePriceCode);
            if ($basePriceAttr && $basePriceAttr->getId()) {
                return $omni_store_id->getValue();
            }
        }
        return $this->getDefaultOmniStoreCode();
    }

    /**
     * @return mixed
     */
    public function getDefaultOmniStoreCode()
    {
        $customer = $this->getCustomer();
        return $customer->getDefaultOmniStoreCode();
    }

    /**
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Webapi\Exception
     */
    public function isLoggedInByAPI()
    {
        if ($this->state->getAreaCode() == Area::AREA_WEBAPI_REST && (bool)$this->smartTokenUserContext->getUserId()) {
            return true;
        }
        return false;
    }
}
