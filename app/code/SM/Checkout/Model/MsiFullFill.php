<?php

namespace SM\Checkout\Model;

use Magento\Framework\Api\SearchCriteriaBuilder;
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
     * @var SourceItemRepositoryInterface
     */
    protected $sourceItemRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    protected $searchCriteriaBuilder;

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
     * MsiFullFill constructor.
     * @param SourceItemRepositoryInterface $sourceItemRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param SourceRepositoryInterface $sourceRepository
     * @param AddressInterfaceFactory $addressInterfaceFactory
     * @param LatLngInterfaceFactory $latLngInterfaceFactory
     * @param GetLatLngFromAddressInterface $getLatLngFromAddress
     * @param GetDistanceInterface $getDistance
     * @param SearchStoreInterfaceFactory $searchStoreInterfaceFactory
     */
    public function __construct(
        SourceItemRepositoryInterface $sourceItemRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        SourceRepositoryInterface $sourceRepository,
        AddressInterfaceFactory $addressInterfaceFactory,
        LatLngInterfaceFactory $latLngInterfaceFactory,
        GetLatLngFromAddressInterface $getLatLngFromAddress,
        GetDistanceInterface $getDistance,
        SearchStoreInterfaceFactory $searchStoreInterfaceFactory
    ) {
        $this->sourceItemRepository = $sourceItemRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->sourceRepository = $sourceRepository;
        $this->addressInterfaceFactory = $addressInterfaceFactory;
        $this->latLngInterfaceFactory = $latLngInterfaceFactory;
        $this->getLatLngFromAddress = $getLatLngFromAddress;
        $this->getDistance = $getDistance;
        $this->searchStoreInterfaceFactory = $searchStoreInterfaceFactory;
    }

    /**
     * @param array $skuList
     * @return array
     */
    public function getMsiFullFill($skuList)
    {
        $searchCriteria = $this->searchCriteriaBuilder
            ->addFilter(SourceItemInterface::SOURCE_CODE, 'default', 'neq')
            ->addFilter(SourceItemInterface::SKU, array_keys($skuList), 'in')
            ->addFilter(SourceItemInterface::STATUS, SourceItemInterface::STATUS_IN_STOCK)
            ->create();

        $items = $this->sourceItemRepository->getList($searchCriteria)->getItems();
        $msi = [];
        foreach ($items as $item) {
            if (!isset($skuList[$item->getSku()])) {
                continue;
            }
            if ((int)$item->getQuantity() >= (int)$skuList[$item->getSku()]) {
                $msi[$item->getSku()][] = $item->getSourceCode();
            }
        }
        $result = [];
        $i = 0;
        foreach ($msi as $list) {
            $i++;
            if ($i == 1) {
                $result = $list;
            } else {
                $result = array_intersect($result, $list);
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
        try {
            $sources = [];
            $sort = [];
            foreach ($sourceList as $sourceCode) {
                try {
                    $sources[] = $this->sourceRepository->get($sourceCode);
                } catch (\Exception $e) {
                }
            }
            $distanceBySourceCode = $sortSources = $sourcesWithoutDistance = [];
            if (!$addressLatLng) {
                $addressLatLong = $this->getLongLat($address);
            } else {
                $addressLatLong = $address;
            }
            foreach ($sources as $source) {
                try {
                    $sourceLatLong = $this->getLongLat($source);
                    $distanceBySourceCode[$source->getSourceCode()] = $this->getDistance->execute($sourceLatLong, $addressLatLong);
                    $sortSources[] = $source;
                } catch (LocalizedException $e) {
                    $sourcesWithoutDistance[] = $source;
                }
            }

            // Sort sources by distance
            uasort(
                $sortSources,
                function (SourceInterface $a, SourceInterface $b) use ($distanceBySourceCode) {
                    $distanceFromA = $distanceBySourceCode[$a->getSourceCode()];
                    $distanceFromB = $distanceBySourceCode[$b->getSourceCode()];

                    return ($distanceFromA < $distanceFromB) ? -1 : 1;
                }
            );

            foreach (array_merge($sortSources, $sourcesWithoutDistance) as $source) {
                $data = ['source_code' => $source->getSourceCode()];
                if (isset($distanceBySourceCode[$source->getSourceCode()])) {
                    $data['distance'] = round((int)$distanceBySourceCode[$source->getSourceCode()]/1000, 2);
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
        $sources = [];
        try {
            foreach ($sourceList as $sourceCode) {
                try {
                    $sources[] = $this->sourceRepository->get($sourceCode);
                } catch (\Exception $e) {
                }
            }
            $distanceBySourceCode = $sortSources = $sourcesWithoutDistance = [];
            if (!$addressLatLng) {
                $addressLatLong = $this->getLongLat($address);
            } else {
                $addressLatLong = $address;
            }
            foreach ($sources as $source) {
                try {
                    $sourceLatLong = $this->getLongLat($source);
                    $distanceBySourceCode[$source->getSourceCode()] = $this->getDistance->execute($sourceLatLong, $addressLatLong);
                    $sortSources[] = $source;
                } catch (LocalizedException $e) {
                    $sourcesWithoutDistance[] = $source;
                }
            }

            // Sort sources by distance
            uasort(
                $sortSources,
                function (SourceInterface $a, SourceInterface $b) use ($distanceBySourceCode) {
                    $distanceFromA = $distanceBySourceCode[$a->getSourceCode()];
                    $distanceFromB = $distanceBySourceCode[$b->getSourceCode()];

                    return ($distanceFromA < $distanceFromB) ? -1 : 1;
                }
            );

            foreach (array_merge($sortSources, $sourcesWithoutDistance) as $source) {
                if ($currentStoreCode && $currentStoreCode == $source->getSourceCode()) {
                    $data['current_store_fulfill'] = true;
                }
                $searchStoreInf = $this->searchStoreInterfaceFactory->create();
                $searchStoreInf->setStore($source);
                if (isset($distanceBySourceCode[$source->getSourceCode()])) {
                    $distance = round((int)$distanceBySourceCode[$source->getSourceCode()]/1000, 2);
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
}
