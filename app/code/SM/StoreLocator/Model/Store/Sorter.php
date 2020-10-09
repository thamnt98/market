<?php

//declare(strict_types=1);

namespace SM\StoreLocator\Model\Store;

use SM\StoreLocator\Api\Data\StoreExtensionFactory;
use SM\StoreLocator\Api\Data\StoreInterface;
use SM\StoreLocator\Helper\Config;
use SM\StoreLocator\Model\Data\Request\StoreSearchCriteria\SortOrder;

class Sorter
{
    const STORE = 'store';
    const DISTANCE = 'distance';
    const DISTANCE_TYPE = 'Km';

    /**
     * @var string
     */
    protected $direction;

    /**
     * @var Calculator
     */
    protected $calculator;

    /**
     * @var StoreExtensionFactory
     */
    protected $storeExtensionFactory;

    /**
     * @var Config
     */
    protected $storeLocatorConfig;

    /**
     * Sorter constructor.
     * @param Calculator $calculator
     * @param StoreExtensionFactory $storeExtensionFactory
     * @param Config $storeLocatorConfig
     * @codeCoverageIgnore
     */
    public function __construct(
        Calculator $calculator,
        StoreExtensionFactory $storeExtensionFactory,
        Config $storeLocatorConfig
    ) {
        $this->calculator = $calculator;
        $this->storeExtensionFactory = $storeExtensionFactory;
        $this->storeLocatorConfig = $storeLocatorConfig;
    }

    /**
     * @param float $fromLat
     * @param float $fromLong
     * @param StoreInterface[] $stores
     * @param string $direction
     * @return StoreInterface[]
     */
    public function sortByDistance(float $fromLat, float $fromLong, array $stores, string $direction): array
    {
        $sortArr = [];
        $maxDistance = $this->storeLocatorConfig->getMaximumDistance();
        foreach ($stores as $store) {
            if (!$store->getAddress()->getLatitude() || !$store->getAddress()->getLongitude()) {
                $kmDistance = null;
            } else {
                $kmDistance = $this->calculator->calculateHaversineCircleDistance(
                    $fromLat,
                    $fromLong,
                    (float) $store->getAddress()->getLatitude(),
                    (float) $store->getAddress()->getLongitude()
                );
                $kmDistance = round($kmDistance * 100) / 100;
            }

            if ($kmDistance != null && $kmDistance <= $maxDistance) {
                $storeExtension = $store->getExtensionAttributes() ?? $this->storeExtensionFactory->create();
                $storeExtension->setDistance($kmDistance . " " . self::DISTANCE_TYPE);
                $store->setExtensionAttributes($storeExtension);

                $item = [
                    self::DISTANCE => $kmDistance,
                    self::STORE => $store
                ];

                $sortArr[] = $item;
            }
        }
        usort($sortArr, [$this, 'sortArrByDistance']);
        $storeList = array_column($sortArr, self::STORE);

        return array_slice(
            $storeList,
            0,
            $this->storeLocatorConfig->getNumberLocationDisplayedConfiguration()
        );
    }

    /**
     * @param array $a
     * @param array $b
     * @return float
     * @codeCoverageIgnore
     */
    protected function sortArrByDistance(array $a, array $b): float
    {
        if (is_null($a[self::DISTANCE])) {
            return 1;
        }
        if (is_null($b[self::DISTANCE])) {
            return -1;
        }
        if ($this->direction == SortOrder::SORT_DESC) {
            return $b[self::DISTANCE] - $a[self::DISTANCE];
        }
        return $a[self::DISTANCE] - $b[self::DISTANCE];
    }
}
