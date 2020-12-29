<?php

/**
 * @category  SM
 * @package   SM_Catalog
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 *
 * @author    Logan Ta <phuongtt2@smartosc.com>
 *
 * @copyright 2020 Copyright (c) SmartOSC. All rights reserved.
 * @url http://www.smartosc.com/
 */

namespace SM\Catalog\Helper;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Registry;
use Magento\InventoryApi\Api\Data\SourceInterface;
use Magento\InventoryApi\Api\Data\SourceItemInterface;
use Magento\InventoryApi\Api\SourceItemRepositoryInterface;
use Magento\InventoryApi\Api\SourceRepositoryInterface;

/**
 * Class StorePickup
 * @package SM\Catalog\Helper
 */
class StorePickup
{
    const PRODUCT_CONFIGURABLE = 'configurable';
    const PRODUCT_BUNDLE = 'bundle';
    const PRODUCT_GROUPED = 'grouped';
    const PRODUCT_SIMPLE = 'simple';

    /**
     * @var SearchCriteriaBuilder
     */
    protected $searchCriteriaBuilder;

    /**
     * @var SourceItemRepositoryInterface
     */
    protected $sourceItemRepository;

    /**
     * @var SourceRepositoryInterface
     */
    protected $sourceRepository;

    /**
     * @var CustomerSession
     */
    public $customerSession;

    /**
     * @var ProductRepositoryInterface
     */
    public $productRepository;

    /**
     * @var ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * @var Registry
     */
    protected $registry;

    /**
     * @var \Magento\Customer\Model\CustomerFactory
     */
    protected $customerFactory;

    /**
     * StorePickup constructor.
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param SourceItemRepositoryInterface $sourceItemRepository
     * @param SourceRepositoryInterface $sourceRepository
     * @param CustomerSession $customerSession
     * @param ProductRepositoryInterface $productRepository
     * @param ScopeConfigInterface $scopeConfig
     * @param Registry $registry
     * @param \Magento\Customer\Model\CustomerFactory $customerFactory
     */
    public function __construct(
        SearchCriteriaBuilder $searchCriteriaBuilder,
        SourceItemRepositoryInterface $sourceItemRepository,
        SourceRepositoryInterface $sourceRepository,
        CustomerSession $customerSession,
        ProductRepositoryInterface $productRepository,
        ScopeConfigInterface $scopeConfig,
        Registry $registry,
        \Magento\Customer\Model\CustomerFactory $customerFactory
    ) {
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->sourceItemRepository = $sourceItemRepository;
        $this->sourceRepository = $sourceRepository;
        $this->customerSession = $customerSession;
        $this->productRepository = $productRepository;
        $this->_scopeConfig = $scopeConfig;
        $this->registry = $registry;
        $this->customerFactory = $customerFactory;
    }

    /**
     * @param $sourceListSimple
     * @param $sourceListConfig
     * @param $sourceListBundle
     * @return bool
     */
    public function hasSourceListAvailable($sourceListSimple, $sourceListConfig, $sourceListBundle)
    {
        if (!empty($sourceListSimple) || !empty($sourceListConfig) || !empty($sourceListBundle)) {
            return true;
        }

        return false;
    }

    /**
     * @return mixed
     */
    public function getStoreOpenTime()
    {
        return $this->_scopeConfig->getValue(
            'trans_catalog/available_store_pickup/open_time',
            \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITE
        );
    }

    /**
     * @return mixed
     */
    public function getStoreCloseTime()
    {
        return $this->_scopeConfig->getValue(
            'trans_catalog/available_store_pickup/close_time',
            \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITE
        );
    }

    /**
     * @return string
     */
    public function getStoreTimeAvailable()
    {
        return $this->getStoreOpenTime() . ' - ' . $this->getStoreCloseTime();
    }

    /**
     * @return mixed
     */
    public function getMainCustomerAddress()
    {
        $mainStreet = $this->customerSession->getCustomer()->getDefaultBillingAddress()->getStreet();
        if ($mainStreet[0] != '') {
            return $mainStreet[0];
        }

        return null;
    }

    /**
     * @return array
     */
    public function getLatLongAddressPinPoint()
    {
        //Default lat and long for customer
        $lat = '-6.175392';
        $long = '106.827153';
        $pinpoint = '';

        try {
            $customer = $this->customerSession->getCustomer()->getDefaultBillingAddress();
            //Mobile API don't have session, so when mobile call api get product detail, we store customer id in registry and use in here!
            if (!$customer) {
                $customerId = $this->registry->registry('customer_id');
                $customer   = $this->customerFactory->create()->load($customerId)->getDefaultBillingAddress();
            }

            if ($customer) {
                if ($customer->getCustomAttribute('latitude')) {
                    $lat = $customer->getCustomAttribute('latitude')->getValue();
                }
                if ($customer->getCustomAttribute('longitude')) {
                    $long = $customer->getCustomAttribute('longitude')->getValue();
                }
                if ($customer->getCustomAttribute('pinpoint_location')) {
                    $pinpoint = $customer->getCustomAttribute('pinpoint_location')->getValue();
                }

                return ['lat' => $lat, 'long' => $long, 'pinpoint' => $pinpoint];
            }
        } catch (\Exception $exception) {
            return ['lat' => $lat, 'long' => $long, 'pinpoint' => $pinpoint];
        }

        return ['lat' => $lat, 'long' => $long, 'pinpoint' => $pinpoint];
    }

    /**
     * @param $product
     * @return array
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getSourcesListBundle($product)
    {
        if (!$this->isBundleProductPDP($product)) {
            return [];
        }
        $childBundleIds = [];
        $typeInstance = $product->getTypeInstance();
        $requiredChildrenIds = $typeInstance->getChildrenIds($product->getId(), true);
        //get all child of bundle product
        foreach ($requiredChildrenIds as $keyCB => $valCB) {
            foreach ($valCB as $childId) {
                $parent = $this->productRepository->getById($childId);
                if ($parent->getTypeId() == 'configurable') {
                    if ($parent->getIsWarehouse()) {
                        return [];
                    }
                    $child = $parent->getTypeInstance()->getUsedProducts($parent);
                    foreach ($child as $item) {
                        $childBundleIds[] = $item->getId();
                    }
                } else {
                    $childBundleIds[] = $childId;
                }
            }
        }
        //get source list each child bundle
        $sourceFirstCB = null;

        $hasSourceAval = true;
        if (!empty($childBundleIds)) {
            foreach ($childBundleIds as $childCBID) {
                $product = $this->productRepository->getById($childCBID);
                // if product has not available in store
                if ($product->getIsWarehouse()) {
                    return [];
                }
                $skuChildB = $product->getSku();
                if ($sourceFirstCB == null) {
                    $sourceFirstCB = $this->getSourceCodesBySKU($skuChildB);
                }

                //compare source code
                $resultSourceCode = array_intersect($sourceFirstCB, $this->getSourceCodesBySKU($skuChildB));
                if (empty($resultSourceCode)) {
                    $hasSourceAval = false;
                }
            }
        }

        //get source list available for all child
        if ($hasSourceAval && !empty($resultSourceCode)) {
            $sourceListData = $this->getListDataBySourceCode($resultSourceCode);
            try {
                $sourceDataSortUpdated = $this->calculateDistanceAndSortUpdated($sourceListData);

                return $this->convertSourcesListSorted($sourceListData, $sourceDataSortUpdated);
            } catch (\Exception $e) {
                return [];
            }
        }

        return [];
    }

    /**
     * @param $product
     * @return array
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getSourcesListConfigurable($product)
    {
        if (!$this->isConfigurableProductPDP($product)) {
            return [];
        }
        $childConfigIds = [];
        $typeInstance = $product->getTypeInstance();
        $requiredChildrenIds = $typeInstance->getUsedProducts($product);
        //get all child of bundle product
        foreach ($requiredChildrenIds as $childId) {
            $childConfigIds[] = $childId->getId();
        }

        //get source list each child bundle
        $sourceFirstC = null;
        $hasSourceAval = true;
        if (!empty($childConfigIds)) {
            foreach ($childConfigIds as $childCID) {
                $product = $this->productRepository->getById($childCID);
                // if product has not available in store
                if ($product->getIsWarehouse()) {
                    return [];
                }
                $skuChildC = $product->getSku();
                if ($sourceFirstC == null) {
                    $sourceFirstC = $this->getSourceCodesBySKU($skuChildC);
                }

                //compare source code
                $resultSourceCode = array_intersect($sourceFirstC, $this->getSourceCodesBySKU($skuChildC));
                if (empty($resultSourceCode)) {
                    $hasSourceAval = false;
                }
            }
        }

        //get source list available for all child
        if ($hasSourceAval && !empty($resultSourceCode)) {
            $sourceListData = $this->getListDataBySourceCode($resultSourceCode);
            $sourceDataSortUpdated = $this->calculateDistanceAndSortUpdated($sourceListData);
            try {
                return $this->convertSourcesListSorted($sourceListData, $sourceDataSortUpdated);
            } catch (\Exception $e) {
                return [];
            }
        }

        return [];
    }

    /**
     * @param $resultSourceCode
     * @return array
     */
    public function getListDataBySourceCode(array $resultSourceCode)
    {
        $isEnableLimitStorePickUp = $this->isEnableLimitStorePickUp();
        if ($isEnableLimitStorePickUp) {
            $limitStoreCodeList = $this->getEnableCodeList();
            $resultSourceCode = array_intersect($resultSourceCode, $limitStoreCodeList);
        }
        $sourceListData = [];
        $sourceCriteria = $this->searchCriteriaBuilder
            ->addFilter(SourceInterface::ENABLED, 1)
            ->addFilter(SourceInterface::SOURCE_CODE, [$resultSourceCode], 'in')
            ->create();
        try {
            $sourceData = $this->sourceRepository->getList($sourceCriteria)->getItems();
            if (is_array($sourceData) && !empty($sourceData)) {
                foreach ($sourceData as $item) {
                    $sourceListData[] = $item->getData();
                }
            }
        } catch (\Exception $e) {
            return [];
        }

        return $sourceListData;
    }

    /**
     * @param $sku
     * @return array
     */
    public function getSourceCodesBySKU($sku)
    {
        $sourceCodes = [];
        //get Inventory Source Item table by sku
        $sourceItemCriteria = $this->searchCriteriaBuilder
            ->addFilter(SourceItemInterface::SKU, $sku)
            ->addFilter(SourceItemInterface::STATUS, 1)
            ->create();

        try {
            $sourceItemData = $this->sourceItemRepository->getList($sourceItemCriteria)->getItems();
            if (is_array($sourceItemData) && !empty($sourceItemData)) {
                foreach ($sourceItemData as $item) {
                    $sourceCodes[] = $item->getSourceCode();
                }
            }
        } catch (\Exception $e) {
            return [];
        }

        //get Inventory Source table by code
        if (!empty($sourceCodes)) {
            $sourceListData = [];
            $sourceCriteria = $this->searchCriteriaBuilder
                ->addFilter(SourceInterface::ENABLED, 1)
                ->addFilter(SourceInterface::SOURCE_CODE, [$sourceCodes], 'in')
                ->create();

            try {
                $sourceData = $this->sourceRepository->getList($sourceCriteria)->getItems();
                if (is_array($sourceData) && !empty($sourceData)) {
                    foreach ($sourceData as $item) {
                        $sourceListData[] = $item->getSourceCode();
                    }
                }
            } catch (\Exception $e) {
                return [];
            }

            return $sourceListData;
        }

        return [];
    }

    /**
     * @param $product
     * @return array
     */
    public function getSourcesListSimple($product)
    {
        if (!$this->isSimpleProductPDP($product)) {
            return [];
        }

        //product has not available in store
        if ($product->getIsWarehouse()) {
            return [];
        }

        try {
            $sourceDataOri = $this->getSourcesSimpleBySKU($product->getSku());
            $sourceDataSortUpdated = $this->calculateDistanceAndSortUpdated($sourceDataOri);

            return $this->convertSourcesListSorted($sourceDataOri, $sourceDataSortUpdated);
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * @param $sourceDataOri
     * @return array
     */
    public function calculateDistanceAndSortUpdated($sourceDataOri)
    {
        if (is_array($sourceDataOri) && !empty($sourceDataOri)) {
            $latLongMainAddress = $this->getLatLongAddressPinPoint();
            $sourceDistanceCalculate = [];

            foreach ($sourceDataOri as $source) {
                $kmDistance = null;
                if ($source['latitude'] != null || $source['longitude'] != null) {
                    $kmDistance = $this->calculateHaversineCircleDistance(
                        (float)$latLongMainAddress['lat'],
                        (float)$latLongMainAddress['long'],
                        (float)$source['latitude'],
                        (float)$source['longitude']
                    );

                    $kmDistance = round(($kmDistance * 100 / 100), 2);
                }

                $sourceDistanceCalculate[$source['source_code']] = $kmDistance;
            }

            //sort array distance
            asort($sourceDistanceCalculate);
            $sourceDistanceCalSorted = $sourceDistanceCalculate;

            return $sourceDistanceCalSorted;
        }

        return [];
    }

    /**
     * @param $sku
     * @return array
     */
    public function getSourcesSimpleBySKU($sku)
    {
        $sourceCodes = [];
        //get Inventory Source Item table by sku
        $sourceItemCriteria = $this->searchCriteriaBuilder
            ->addFilter(SourceItemInterface::SKU, $sku)
            ->addFilter(SourceItemInterface::STATUS, 1)
            ->create();

        try {
            $sourceItemData = $this->sourceItemRepository->getList($sourceItemCriteria)->getItems();
            if (is_array($sourceItemData) && !empty($sourceItemData)) {
                foreach ($sourceItemData as $item) {
                    $sourceCodes[] = $item->getSourceCode();
                }
            }
        } catch (\Exception $e) {
            return [];
        }

        //get Inventory Source table by code
        if (!empty($sourceCodes)) {
            $sourceListBySourceCodeData = $this->getListDataBySourceCode($sourceCodes);

            return $sourceListBySourceCodeData;
        }

        return [];
    }

    /**
     * @param $product
     * @return mixed
     */
    public function getProductType($product)
    {
        return $product->getTypeId();
    }

    /**
     * @param $product
     * @return bool
     */
    public function isSimpleProductPDP($product)
    {
        return $product->getTypeId() == self::PRODUCT_SIMPLE ? true : false;
    }

    /**
     * @param $product
     * @return bool
     */
    public function isBundleProductPDP($product)
    {
        return $product->getTypeId() == self::PRODUCT_BUNDLE ? true : false;
    }

    /**
     * @param $product
     * @return bool
     */
    public function isConfigurableProductPDP($product)
    {
        return $product->getTypeId() == self::PRODUCT_CONFIGURABLE ? true : false;
    }

    /**
     * @param $sourceDataOri
     * @param $sourceDistanceCalSorted
     * @return array
     */
    public function convertSourcesListSorted($sourceDataOri, $sourceDistanceCalSorted)
    {
        if (!empty($sourceDataOri) && !empty($sourceDataOri)) {
            $sourceDataSortUpdated = [];
            //convert to array sorted
            foreach ($sourceDistanceCalSorted as $keyCode => $distanceVal) {
                foreach ($sourceDataOri as $sourceOri) {
                    if ($keyCode == $sourceOri['source_code']) {
                        $sourceDataSortUpdated[(string)$distanceVal] = $sourceOri;
                    }
                }
            }

            return $sourceDataSortUpdated;
        }

        return [];
    }

    /**
     * Calculates the great-circle distance between two points, with the Haversine formula.
     * @param float $latitudeFrom Latitude of start point in [deg decimal]
     * @param float $longitudeFrom Longitude of start point in [deg decimal]
     * @param float $latitudeTo Latitude of target point in [deg decimal]
     * @param float $longitudeTo Longitude of target point in [deg decimal]
     * @param int $earthRadius Mean earth radius in [km]
     * @return float Distance between points in [km] (same as earthRadius)
     * @codeCoverageIgnore
     */
    public function calculateHaversineCircleDistance(
        float $latitudeFrom,
        float $longitudeFrom,
        float $latitudeTo,
        float $longitudeTo,
        int $earthRadius = 6371
    ): float {
        // convert from degrees to radians
        $latFrom = deg2rad($latitudeFrom);
        $lonFrom = deg2rad($longitudeFrom);
        $latTo = deg2rad($latitudeTo);
        $lonTo = deg2rad($longitudeTo);

        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;

        $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) +
                cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)));

        return $angle * $earthRadius;
    }

    /**
     * @return false|string[]
     */
    protected function getEnableCodeList()
    {
        $storeCodeList = $this->_scopeConfig->getValue(
            'trans_catalog/limit_store_pickup/store_code_list',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        return explode(',', $storeCodeList);
    }

    /**
     * @return mixed
     */
    protected function isEnableLimitStorePickUp()
    {
        return $this->_scopeConfig->getValue(
            'trans_catalog/limit_store_pickup/active',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
}
