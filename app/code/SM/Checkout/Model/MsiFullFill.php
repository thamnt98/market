<?php

namespace SM\Checkout\Model;

use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\InventoryApi\Api\Data\SourceInterface;
use Magento\InventoryApi\Api\Data\SourceItemInterface;
use Magento\InventoryApi\Api\SourceItemRepositoryInterface;
use Magento\InventoryApi\Api\SourceRepositoryInterface;
use Magento\InventoryDistanceBasedSourceSelectionApi\Api\Data\LatLngInterfaceFactory;
use Magento\InventoryDistanceBasedSourceSelectionApi\Api\GetDistanceInterface;
use Magento\InventoryDistanceBasedSourceSelectionApi\Api\GetLatLngFromAddressInterface;
use Magento\InventorySourceSelectionApi\Api\Data\AddressInterfaceFactory;
use SM\Checkout\Api\Data\Checkout\SearchStoreInterfaceFactory;

/**
 * Class MsiFullFill
 * @package SM\Checkout\Model
 */
class MsiFullFill
{
    /**
     * @var SourceRepositoryInterface
     */
    protected $sourceRepository;

    /**
     * @var AddressInterfaceFactory
     */
    protected $addressInterfaceFactory;

    /**
     * @var LatLngInterfaceFactory
     */
    protected $latLngInterfaceFactory;

    /**
     * @var GetLatLngFromAddressInterface
     */
    protected $getLatLngFromAddress;

    /**
     * @var GetDistanceInterface
     */
    protected $getDistance;

    /**
     * @var SearchStoreInterfaceFactory
     */
    protected $searchStoreInterfaceFactory;

    /**
     * @var ResourceModel\ConnectionDB
     */
    protected $connectionDB;

    /**
     * @var \Magento\InventoryApi\Api\Data\SourceInterfaceFactory
     */
    protected $sourceInterfaceFactory;

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * MsiFullFill constructor.
     * @param SourceRepositoryInterface $sourceRepository
     * @param AddressInterfaceFactory $addressInterfaceFactory
     * @param LatLngInterfaceFactory $latLngInterfaceFactory
     * @param GetLatLngFromAddressInterface $getLatLngFromAddress
     * @param GetDistanceInterface $getDistance
     * @param SearchStoreInterfaceFactory $searchStoreInterfaceFactory
     * @param ResourceModel\ConnectionDB $connectionDB
     * @param \Magento\InventoryApi\Api\Data\SourceInterfaceFactory $sourceInterfaceFactory
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        SourceRepositoryInterface $sourceRepository,
        AddressInterfaceFactory $addressInterfaceFactory,
        LatLngInterfaceFactory $latLngInterfaceFactory,
        GetLatLngFromAddressInterface $getLatLngFromAddress,
        GetDistanceInterface $getDistance,
        SearchStoreInterfaceFactory $searchStoreInterfaceFactory,
        \SM\Checkout\Model\ResourceModel\ConnectionDB $connectionDB,
        \Magento\InventoryApi\Api\Data\SourceInterfaceFactory $sourceInterfaceFactory,
        ScopeConfigInterface $scopeConfig
    ) {
        $this->sourceRepository = $sourceRepository;
        $this->addressInterfaceFactory = $addressInterfaceFactory;
        $this->latLngInterfaceFactory = $latLngInterfaceFactory;
        $this->getLatLngFromAddress = $getLatLngFromAddress;
        $this->getDistance = $getDistance;
        $this->searchStoreInterfaceFactory = $searchStoreInterfaceFactory;
        $this->connectionDB = $connectionDB;
        $this->sourceInterfaceFactory = $sourceInterfaceFactory;
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @param $skus
     * @return array
     */
    public function getMsiFullFill($skus)
    {
        $skuList = $skuListCheck = [];
        foreach ($skus as $sku => $qty) {
            $skuList[strtolower((string)$sku)] = $qty;
            $skuListCheck[] = (string)$sku;
        }
        if ($this->isEnableLimitStorePickUp()) {
            $limitStoreCodeList = $this->getEnableCodeList();
            $items = $this->connectionDB->getMsi($skuListCheck, true, $limitStoreCodeList);
        } else {
            $items = $this->connectionDB->getMsi($skuListCheck);
        }
        $msiListCode = [];
        $result = [];
        foreach ($items as $item) {
            $sku = strtolower($item['sku']);
            if (!isset($skuList[$sku])) {
                continue;
            }
            if ((int)$item['quantity'] >= (int)$skuList[$sku]) {
                $msiListCode[$item['sku']][] = $item['source_code'];
                $result[$item['source_code']] = $item;
            }
        }
        $msi = [];
        $i = 0;
        foreach ($msiListCode as $list) {
            $i++;
            if ($i == 1) {
                $msi = $list;
            } else {
                $msi = array_intersect($msi, $list);
            }
        }
        foreach ($result as $sourceCode => $list) {
            if (!in_array($sourceCode, $msi)) {
                unset($result[$sourceCode]);
            }
        }
        return $result;
    }

    /**
     * @param $sourceCode
     * @param $addressLatLong
     * @return bool|float
     */
    public function getDistanceBetweenCurrentStoreAndAddress($sourceCode, $addressLatLong)
    {
        try {
            $source = $this->sourceRepository->get($sourceCode);
            $sourceLatLong = $this->getLongLat($source);
            $distance = $this->getDistance->execute($sourceLatLong, $addressLatLong);
            return round((int)$distance/1000, 2);
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * @param $sourceCode
     * @param $addressLatLong
     * @return \SM\Checkout\Api\Data\Checkout\SearchStoreInterface
     */
    public function getDistanceBetweenCurrentStoreAndAddressMobile($sourceCode, $addressLatLong)
    {
        $searchStoreInf = $this->searchStoreInterfaceFactory->create();
        try {
            $source = $this->sourceRepository->get($sourceCode);
            $sourceLatLong = $this->getLongLat($source);
            $distance = $this->getDistance->execute($sourceLatLong, $addressLatLong);
            $searchStoreInf->setStore($source);
            $searchStoreInf->setDistance(round((int)$distance/1000, 2));
        } catch (\Exception $e) {
        }
        return $searchStoreInf;
    }

    /**
     * @param $sourceList
     * @param $address
     * @param bool $addressLatLng
     * @return array
     */
    public function sortSourceByDistance($sourceList, $address, $addressLatLng = false)
    {
        $sort = [];
        $distanceBySourceCode = $sortSources = $sourcesWithoutDistance = [];
        if (!$addressLatLng) {
            try {
                $addressLatLong = $this->getLongLat($address);
            } catch (\Exception $e) {
                foreach ($sourceList as $source) {
                    $data = ['source_code' => $source['source_code'], 'distance' => 0];
                    $sort[] = $data;
                    if ($this->getCountOfArray($sort) == 5) {
                        break;
                    }
                }
                return $sort;
            }
        } else {
            $addressLatLong = $address;
        }
        try {
            foreach ($sourceList as $source) {
                try {
                    $sourceLatLong = $this->getLongLatMsi($source);
                    $distanceBySourceCode[$source['source_code']] = $this->getDistance->execute($sourceLatLong, $addressLatLong);
                    $sortSources[] = $source;
                } catch (LocalizedException $e) {
                    $sourcesWithoutDistance[] = $source;
                }
            }

            // Sort sources by distance
            uasort(
                $sortSources,
                function ($a, $b) use ($distanceBySourceCode) {
                    $distanceFromA = $distanceBySourceCode[$a['source_code']];
                    $distanceFromB = $distanceBySourceCode[$b['source_code']];

                    return ($distanceFromA < $distanceFromB) ? -1 : 1;
                }
            );
            foreach (array_merge($sortSources, $sourcesWithoutDistance) as $source) {
                $data = ['source_code' => $source['source_code']];
                if (isset($distanceBySourceCode[$source['source_code']])) {
                    $data['distance'] = round((int)$distanceBySourceCode[$source['source_code']]/1000, 2);
                } else {
                    $data['distance'] = 0;
                }
                $sort[] = $data;
                if ($this->getCountOfArray($sort) == 5) {
                    break;
                }
            }
            return $sort;
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * @param $sourceList
     * @param $address
     * @param $currentStoreCode
     * @param bool $addressLatLng
     * @return array
     */
    public function sortSourceByDistanceMobile($sourceList, $address, $currentStoreCode = false, $addressLatLng = false)
    {
        $data = ['store_list' => [], 'current_store_fulfill' => false];
        if (!$currentStoreCode) {
            $data['current_store_fulfill'] = true;
        }
        $distanceBySourceCode = $sortSources = $sourcesWithoutDistance = [];
        if (!$addressLatLng) {
            try {
                $addressLatLong = $this->getLongLat($address);
            } catch (\Exception $e) {
                foreach ($sourceList as $source) {
                    if ($currentStoreCode && $currentStoreCode == $source['source_code']) {
                        $data['current_store_fulfill'] = true;
                    }
                    $searchStoreInf = $this->searchStoreInterfaceFactory->create();
                    $sourceObject = $this->sourceInterfaceFactory->create();
                    $sourceObject->setCity($source['city']);
                    $sourceObject->setCountryId($source['country_id']);
                    $sourceObject->setLatitude($source['latitude']);
                    $sourceObject->setLongitude($source['longitude']);
                    $sourceObject->setName($source['name']);
                    $sourceObject->setPostcode($source['postcode']);
                    $sourceObject->setRegion($source['region']);
                    $sourceObject->setStreet($source['street']);
                    $sourceObject->setSourceCode($source['source_code']);
                    $searchStoreInf->setStore($sourceObject);
                    $searchStoreInf->setDistance(0);
                    if ($this->getCountOfArray($data['store_list']) < 5) {
                        $data['store_list'][] = $searchStoreInf;
                    }
                    if ($this->getCountOfArray($data['store_list']) >= 5 && $data['current_store_fulfill']) {
                        break;
                    }
                }
                return $data;
            }
        } else {
            $addressLatLong = $address;
        }
        try {
            foreach ($sourceList as $source) {
                try {
                    $sourceLatLong = $this->getLongLatMsi($source);
                    $distanceBySourceCode[$source['source_code']] = $this->getDistance->execute($sourceLatLong, $addressLatLong);
                    $sortSources[] = $source;
                } catch (LocalizedException $e) {
                    $sourcesWithoutDistance[] = $source;
                }
            }

            // Sort sources by distance
            uasort(
                $sortSources,
                function ($a, $b) use ($distanceBySourceCode) {
                    $distanceFromA = $distanceBySourceCode[$a['source_code']];
                    $distanceFromB = $distanceBySourceCode[$b['source_code']];

                    return ($distanceFromA < $distanceFromB) ? -1 : 1;
                }
            );

            foreach (array_merge($sortSources, $sourcesWithoutDistance) as $source) {
                if ($currentStoreCode && $currentStoreCode == $source['source_code']) {
                    $data['current_store_fulfill'] = true;
                }
                $searchStoreInf = $this->searchStoreInterfaceFactory->create();
                $sourceObject = $this->sourceInterfaceFactory->create();
                $sourceObject->setCity($source['city']);
                $sourceObject->setCountryId($source['country_id']);
                $sourceObject->setLatitude($source['latitude']);
                $sourceObject->setLongitude($source['longitude']);
                $sourceObject->setName($source['name']);
                $sourceObject->setPostcode($source['postcode']);
                $sourceObject->setRegion($source['region']);
                $sourceObject->setStreet($source['street']);
                $sourceObject->setSourceCode($source['source_code']);
                $searchStoreInf->setStore($sourceObject);
                if (isset($distanceBySourceCode[$source['source_code']])) {
                    $distance = round((int)$distanceBySourceCode[$source['source_code']]/1000, 2);
                } else {
                    $distance = 0;
                }
                $searchStoreInf->setDistance($distance);
                if ($this->getCountOfArray($data['store_list']) < 5) {
                    $data['store_list'][] = $searchStoreInf;
                }
                if ($this->getCountOfArray($data['store_list']) >= 5 && $data['current_store_fulfill']) {
                    break;
                }
            }
        } catch (\Exception $e) {
        }
        return $data;
    }

    /**
     * @param $source
     * @return \Magento\InventoryDistanceBasedSourceSelectionApi\Api\Data\LatLngInterface
     */
    protected function getLongLatMsi($source)
    {
        if (!$source['latitude'] || !$source['longitude'] || $source['latitude'] == '' || $source['longitude'] == '') {
            $street = $source['street'];
            $sourceAddress = $this->addressInterfaceFactory->create([
                'country' => $source['country_id'] ?? '',
                'postcode' => $source['postcode'] ?? '',
                'street' => $street ?? '',
                'region' => $source['region'] ?? '',
                'city' => $source['city'] ?? ''
            ]);
            return $this->getLatLngFromAddress->execute($sourceAddress);
        }
        return $this->latLngInterfaceFactory->create([
            'lat' => (float) $source['latitude'],
            'lng' => (float) $source['longitude']
        ]);
    }

    /**
     * @param $source
     * @return \Magento\InventoryDistanceBasedSourceSelectionApi\Api\Data\LatLngInterface
     */
    protected function getLongLat($source)
    {
        if (!$source->getLatitude() || !$source->getLongitude()) {
            $street = $source->getStreet();
            if (is_array($street)) {
                $street = implode(", ", $street);
            }
            $sourceAddress = $this->addressInterfaceFactory->create([
                'country' => $source->getCountryId() ?? '',
                'postcode' => $source->getPostcode() ?? '',
                'street' => $street ?? '',
                'region' => $source->getRegion() ?? '',
                'city' => $source->getCity() ?? ''
            ]);

            return $this->getLatLngFromAddress->execute($sourceAddress);
        }

        return $this->latLngInterfaceFactory->create([
            'lat' => (float) $source->getLatitude(),
            'lng' => (float) $source->getLongitude()
        ]);
    }

    /**
     * @param $lat
     * @param $lng
     * @return \Magento\InventoryDistanceBasedSourceSelectionApi\Api\Data\LatLngInterface
     */
    public function addLatLngInterface($lat, $lng)
    {
        return $this->latLngInterfaceFactory->create([
            'lat' => (float) $lat,
            'lng' => (float) $lng
        ]);
    }

    /**
     * @param $array
     * @return int|void
     */
    protected function getCountOfArray($array)
    {
        return count($array);
    }

    /**
     * @return false|string[]
     */
    protected function getEnableCodeList()
    {
        $storeCodeList = $this->scopeConfig->getValue(
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
        return $this->scopeConfig->getValue(
            'trans_catalog/limit_store_pickup/active',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
}
