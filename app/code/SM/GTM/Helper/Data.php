<?php

/**
 * SMCommerce
 *
 * @category  SM
 * @package   SM_GTM
 *
 * Date: April, 03 2020
 * Time: 10:52 AM
 * User: VooThanh DEV
 *
 * @author    SMCommerce Core Team <connect@smartosc.com>
 * @copyright Copyright SMCommerce (http://smartosc.com/)
 */

namespace SM\GTM\Helper;

use Magento\Customer\Model\Customer;
use Magento\Customer\Model\ResourceModel\CustomerFactory;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Session\SessionManagerInterface as CoreSession;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory as salesOrderCollectionFactory;
use Mageplaza\SocialLogin\Model\ResourceModel\Social\CollectionFactory as socialLoginCollectionFactory;
use SM\GTM\Api\EncryptorInterface;
use SM\GTM\Model\BasketFactory;
use SM\GTM\Model\ResourceModel\Basket\CollectionFactory;
use SM\StoreLocator\Api\Data\Request\StoreSearchCriteriaInterfaceFactory;
use SM\StoreLocator\Api\StoreLocationRepositoryInterface;
use SM\StoreLocator\Model\Data\Request\StoreSearchCriteria\SortOrderFactory;
use Trans\LocationCoverage\Api\CityRepositoryInterface;
use Trans\LocationCoverage\Api\DistrictRepositoryInterface;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * Path promo
     */
    const PROMO_PATH = 'todaydeal/';

    /**
     * Path config
     */
    const XML_ACTIVE_PATH = 'sm_gtm/general/is_active';

    /**
     * Promo events.
     */
    const PROMO_KEY_NAME            = 'promo';
    const PROMO_ON_LOAD_EVENT_NAME  = 'promotionImpressions';
    const PROMO_ON_CLICK_EVENT_NAME = 'promotionClick';
    const PROMO_VIEW_ALL_EVENT_NAME = 'see_all_latest_promo';

    /**
     * Category landing pages events.
     */
    const CATEGORY_KEY_NAME                 = 'category';
    const CATE_LANDING_SUB_CLICK_EVENT_NAME = 'shop_by_category';

    /**
     * Layer filter events.
     */
    const LAYER_FILTER_KEY_NAME         = 'filter';
    const LAYER_FILTER_CLICK_EVENT_NAME = 'filter_category_page';

    /**
     * Search events.
     */
    const SEARCH_KEY_NAME                           = 'search';
    const SEARCH_LATEST_DELETE_ALL_CLICK_EVENT_NAME = 'deleteAll_latest_search';
    const SEARCH_LATEST_DELETE_CLICK_EVENT_NAME     = 'delete_latest_search';
    const SEARCH_LATEST_CLICK_EVENT_NAME            = 'latest_search';
    const GOOGLE_API_KEY_XML_PATH = 'cms/pagebuilder/google_maps_api_key';

    /**
     * @var EncryptorInterface
     */
    protected $encryptor;

    /**
     * @var Session
     */
    protected $customerSession;

    /**
     * @var SortOrderFactory
     */
    protected $sortOrder;
    /**
     * @var StoreSearchCriteriaInterfaceFactory
     */
    protected $storeSearchCriteriaInterfaceFactory;
    /**
     * @var StoreLocationRepositoryInterface
     */
    protected $storeLocationRepository;

    /**
     * @var socialLoginCollectionFactory
     */
    protected $socialLoginCollectionFactory;

    /**
     * @var salesOrderCollectionFactory
     */
    protected $salesOrderCollectionFactory;

    /**
     * @var CoreSession
     */
    protected $coreSession;
    /**
     * @var DistrictRepositoryInterface
     */
    protected $districtRepository;
    /**
     * @var CityRepositoryInterface
     */
    protected $cityRepository;

    /**
     * @var CollectionFactory
     */
    protected $basketCollectionFactory;
    /**
     * @var BasketFactory
     */
    protected $basketFactory;
    /**
     * @var CustomerFactory
     */
    protected $customerFactory;
    /**
     * @var Customer
     */
    protected $customer;

    /**
     * Data constructor.
     * @param StoreSearchCriteriaInterfaceFactory $storeSearchCriteriaInterfaceFactory
     * @param SortOrderFactory $sortOrder
     * @param StoreLocationRepositoryInterface $storeLocationRepository
     * @param Session $customerSession
     * @param socialLoginCollectionFactory $socialLoginCollectionFactory
     * @param salesOrderCollectionFactory $salesOrderCollectionFactory
     * @param CoreSession $coreSession
     * @param DistrictRepositoryInterface $districtRepository
     * @param CityRepositoryInterface $cityRepository
     * @param CollectionFactory $basketCollectionFactory
     * @param BasketFactory $basketFactory
     * @param CustomerFactory $customerFactory
     * @param Customer $customer
     * @param Context $context
     */
    public function __construct(
        StoreSearchCriteriaInterfaceFactory $storeSearchCriteriaInterfaceFactory,
        SortOrderFactory $sortOrder,
        StoreLocationRepositoryInterface $storeLocationRepository,
        Session $customerSession,
        socialLoginCollectionFactory $socialLoginCollectionFactory,
        salesOrderCollectionFactory $salesOrderCollectionFactory,
        CoreSession $coreSession,
        DistrictRepositoryInterface $districtRepository,
        CityRepositoryInterface $cityRepository,
        CollectionFactory $basketCollectionFactory,
        BasketFactory $basketFactory,
        CustomerFactory $customerFactory,
        Customer $customer,
        Context $context,
        EncryptorInterface $encryptor
    ) {
        $this->storeLocationRepository = $storeLocationRepository;
        $this->sortOrder = $sortOrder;
        $this->storeSearchCriteriaInterfaceFactory = $storeSearchCriteriaInterfaceFactory;
        $this->customerSession = $customerSession;
        $this->socialLoginCollectionFactory = $socialLoginCollectionFactory;
        $this->salesOrderCollectionFactory = $salesOrderCollectionFactory;
        $this->coreSession = $coreSession;
        $this->districtRepository = $districtRepository;
        $this->cityRepository = $cityRepository;
        $this->basketCollectionFactory = $basketCollectionFactory;
        $this->basketFactory = $basketFactory;
        $this->customerFactory = $customerFactory;
        $this->customer = $customer;
        $this->encryptor = $encryptor;
        parent::__construct($context);
    }

    /**
     * Is Enable GTM.
     *
     * @param string          $scopeType
     * @param null|string|int $scopeCode
     *
     * @return bool
     */
    public function isEnabled($scopeType = \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $scopeCode = null)
    {
        return (bool)$this->scopeConfig->getValue(self::XML_ACTIVE_PATH, $scopeType, $scopeCode);
    }

    /**
     * Prepare GTM data from Today Deal
     *
     * @param \SM\TodayDeal\Model\Post $dealItem
     *
     * @return string
     */
    public function prepareLatestDealData($dealItem)
    {
        return \Zend_Json_Encoder::encode([
            'id'       => $dealItem->getId(),
            'name'     => $dealItem->getData('title'),
            'creative' => $dealItem->getData('content_heading'),
            'position' => $dealItem->getData('sort_order'),
            'url'      => $this->_getUrl(self::PROMO_PATH . $dealItem->getData('identifier'))
        ], true);
    }

    /**
     * @return bool
     * @throws NoSuchEntityException
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Exception
     */
    public function setStoreInfo()
    {
        if (!$this->customerSession->isLoggedIn()) {
            return false;
        }
        try {
            $customer=$this->getGtmCustomerInfo($this->customerSession->getCustomerId());
            $this->customerSession->setGtmData($customer->getExtensionAttributes());
        } catch (NoSuchEntityException $noSuchEntityException) {
            throw new NoSuchEntityException(__($noSuchEntityException));
        }
        return true;
    }


    /**
     * @param $id
     * @return string
     */
    protected function getLoginType($id)
    {
        $socialCollectionFactory = $this->socialLoginCollectionFactory->create();
        $customerSocial = $socialCollectionFactory->addFieldToFilter(
            'customer_id',
            $id
        )->getFirstItem();

        return $customerSocial->getId() ? $customerSocial->getType() :
            ($this->coreSession->getLoginTypeGtm() ? $this->coreSession->getLoginTypeGtm() : 'Email');
    }

    /**
     * @param $id
     * @return string
     */
    protected function getCustomerType($id)
    {
        $orderCollection = $this->salesOrderCollectionFactory->create();
        $orderCollection->addFieldToFilter('customer_id', $id)
            ->addFieldToFilter('status', 'complete');
        return $orderCollection->getSize() ? 'Shopped' : 'Registered';
    }

    /**
     * @return string
     */
    protected function getCustomerStatus()
    {
        return 'Existing';
    }

    /**
     * @param $city
     * @param $district
     * @return array
     * @throws \Magento\Framework\Exception\InputException
     */
    public function setCustomerStore($city, $district)
    {
        $storeName = "Not available";
        $storeID = "Not available";
        $address = $this->getDistrict($district) . ' ' . $this->getCity($city);
        $prepAddr = str_replace(' ', '+', $address);
        $geocode = file_get_contents('https://maps.google.com/maps/api/geocode/json?key=' .
            $this->getGoogleApiKey() . '&address=' . $prepAddr . '&sensor=false');
        $output = json_decode($geocode);
        if (!empty($output->results[0]->geometry->location->lat) && !empty($output->results[0]->geometry->location->lng)) {
            $latitude = $output->results[0]->geometry->location->lat;
            $longitude = $output->results[0]->geometry->location->lng;
            $search = $this->storeSearchCriteriaInterfaceFactory->create();
            $sort = $this->sortOrder->create();
            $sort->setField('distance');
            $sort->setDirection('ASC');
            $sort->setLat($latitude);
            $sort->setLong($longitude);
            $sortParams = [
                0 => $sort
            ];
            $search->setSortOrders($sortParams);
            $list = $this->storeLocationRepository->getList($search);
            if ($list->getTotalCount()) {
                $arrStore = $list->getItems();
                if ($arrStore) {
                    if (!empty($arrStore[0])) {
                        $storeName = $arrStore[0]->getName();
                        $storeID = $arrStore[0]->getStoreCode();
                    }
                }
            }
        }
        return [
            'store_name' => $storeName,
            'store_id' => $storeID
        ];

    }

    /**
     * @param $cityCode
     * @return string
     */
    protected function getCity($cityCode)
    {
        if (is_numeric($cityCode)) {
            $city = $this->cityRepository->getById($cityCode);
            if ($city) {
                $data = $city->getData();
                return array_key_exists('city', $data) ? $data['city'] : "Not available";
            }
        }
        return $cityCode;
    }

    /**
     * @param $districtCode
     * @return string
     */
    protected function getDistrict($districtCode)
    {
        if (is_numeric($districtCode)) {
            $district = $this->districtRepository->getById($districtCode);
            if ($district) {
                $data = $district->getData();
                return array_key_exists('district', $data) ? $data['district'] : "Not available";
            }
        }
        return $districtCode;
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
    public function getAddress($customer)
    {
        $ext = $customer->getExtensionAttributes();
        return $ext->getDistrictNameGtm() . ' ' . $ext->getCityNameGtm();
    }

    /**
     * @param $customerId
     * @return mixed
     */
    public function getBasketID($customerId)
    {
        $basket = $this->basketCollectionFactory->create()
            ->addFieldToFilter('customer_id', $customerId)->getFirstItem();
        if ($basket->getData()) {
            return $basket->getData('basket_id');
        }
        $basket = $this->basketFactory->create();
        $basket->setData('customer_id', $customerId);
        $basket->save();
        return $basket->getData('basket_id');
    }

    /**
     * @param $storeName
     * @param $storeId
     * @param $customerId
     * @throws \Exception
     */
    public function saveCustomer($storeName, $storeId, $customerId)
    {
        $customer = $this->customer->load($customerId);
        $customerData = $customer->getDataModel();

        $customerData->setCustomAttribute('store_name_gtm', $storeName);
        $customer->updateData($customerData);
        $customerResource = $this->customerFactory->create();
        $customerResource->saveAttribute($customer, 'store_name_gtm');

        $customerData->setCustomAttribute('store_id_gtm', $storeId);
        $customer->updateData($customerData);
        $customerResource = $this->customerFactory->create();
        $customerResource->saveAttribute($customer, 'store_id_gtm');
    }

    /**
     * @param $customerId
     * @return \Magento\Customer\Api\Data\CustomerInterface
     * @throws \Magento\Framework\Exception\InputException
     */
    public function getGtmCustomerInfo($customerId)
    {
        $customer=$this->customer->load($customerId)->getDataModel();
        if ($customer->getFirstname() || $customer->getLastname()) {
            if ($customer->getFirstname() == $customer->getLastname()) {
                if ($customer->getFirstname()) {
                    $name = $customer->getFirstname();
                }
            } else {
                if ($customer->getFirstname() && $customer->getLastname()) {
                    $name = $customer->getFirstname() . " " . $customer->getLastname();
                } else {
                    if ($customer->getFirstname()) {
                        $name = $customer->getFirstname();
                    } else {
                        $name = $customer->getFirstname();
                    }
                }
            }
        } else {
            $name = "Not available";
        }
        $ext = $customer->getExtensionAttributes();
        $ext->setUniqueUserIdGtm($customerId);
        $ext->setUserIdGtm($this->getLoginType($customerId) == 'Phone Number' ?
            $this->getCustomerPhone($customer) :
            $customer->getEmail());
        $ext->setLoginTypeGtm($this->getLoginType($customerId));
        $ext->setCustomerTypeGtm($this->getCustomerType($customerId));
        $ext->setLoyaltyGtm($customer->getCustomAttribute('loyalty') ?
            ($customer->getCustomAttribute('loyalty')->getValue() ?
                $customer->getCustomAttribute('loyalty')->getValue() :
                "Not available") : "Not available");
        $ext->setCustomerStatusGtm($this->getCustomerStatus());
        $ext->setFullNameGtm($name);
        $ext->setEmailGtm($customer->getEmail() ? $customer->getEmail() : "Not available");
        $ext->setPhoneNumberGtm($this->getCustomerPhone($customer));
        $cityName = $customer->getCustomAttribute('city') ?
            ($customer->getCustomAttribute('city')->getValue() ?
                $this->getCity($customer->getCustomAttribute('city')->getValue()) :
                "Not available") : "Not available";
        $districtName = $customer->getCustomAttribute('district') ?
            ($customer->getCustomAttribute('district')->getValue() ?
                $this->getDistrict($customer->getCustomAttribute('district')->getValue()) :
                "Not available") : "Not available";
        $ext->setCityNameGtm($cityName);
        $ext->setDistrictNameGtm($districtName);
        $ext->setBasketIdGtm($this->getBasketID($customerId));
        if (!$customer->getCustomAttribute('store_name_gtm') ||
            !$customer->getCustomAttribute('store_id_gtm')) {
            $storeData = $this->setCustomerStore($cityName, $districtName);
            $storeName = $storeData['store_name'];
            $storeId = $storeData['store_id'];
            $this->saveCustomer($storeName, $storeId, $customerId);
        } else {
            $storeName = $customer->getCustomAttribute('store_name_gtm') ?
                $customer->getCustomAttribute('store_name_gtm')->getValue() : "Not available";
            $storeId = $customer->getCustomAttribute('store_id_gtm') ?
                $customer->getCustomAttribute('store_id_gtm')->getValue() : "Not available";
        }

        $ext->setStoreGtmName(($storeName == "Not available") ? '' : $storeName);
        $ext->setStoreGtmId(($storeId == "Not available") ? '' : $storeId);
        return $customer;
    }

    public function getCustomerPhone($customer)
    {
        return $customer->getCustomAttribute('telephone') ?
            ($customer->getCustomAttribute('telephone')->getValue() ?
                $customer->getCustomAttribute('telephone')->getValue() :
                "Not available") : "Not available";
    }
}
