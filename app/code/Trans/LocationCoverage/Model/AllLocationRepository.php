<?php
/**
 * @category Trans
 * @package  Trans_LocationCoverage
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   Muhammad Randy <muhammad.randy@transdigital.co.id>
 *
 * Copyright © 2019 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\LocationCoverage\Model;

use Magento\Directory\Model\ResourceModel\Region\Collection;
use Magento\Directory\Model\ResourceModel\Region\CollectionFactory;
use Trans\LocationCoverage\Api\AllLocationRepositoryInterface;
use Trans\LocationCoverage\Api\Data\CityInterface;
use Trans\LocationCoverage\Api\Data\DistrictInterface;
use Trans\LocationCoverage\Model\ResourceModel\City\Collection as CityCollection;
use Trans\LocationCoverage\Model\ResourceModel\City\CollectionFactory as CityCollectionFactory;
use Trans\LocationCoverage\Model\ResourceModel\District\Collection as DistrictCollection;
use Trans\LocationCoverage\Model\ResourceModel\District\CollectionFactory as DistrictCollectionFactory;

class AllLocationRepository implements AllLocationRepositoryInterface
{
    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    /**
     * @var CityCollectionFactory
     */
    private $cityFactory;

    /**
     * @var DistrictCollectionFactory
     */
    private $districtFactory;

    /**
     * AllLocationRepository constructor.
     * @param CollectionFactory $collectionFactory
     * @param CityCollectionFactory $cityFactory
     * @param DistrictCollectionFactory $districtFactory
     */

    public function __construct(
        CollectionFactory $collectionFactory,
        CityCollectionFactory $cityFactory,
        DistrictCollectionFactory $districtFactory,
        DistrictInterface $districtInterface,
        CityInterface $cityInterface
    ) {
        $this->collectionFactory         = $collectionFactory;
        $this->cityCollectionFactory     = $cityFactory;
        $this->districtCollectionFactory = $districtFactory;
        $this->districtInterface         = $districtInterface;
        $this->cityInterface             = $cityInterface;
    }

    /**
     * @param string $region
     * @return string[]
     */
    public function getRegionCode(string $region): array
    {
        $regionCode = $this->collectionFactory->create();
        $regionName = $regionCode->addRegionNameFilter($region);
        if ($regionName->getData()) {
            $result = $regionName->toArray();
        } else {
            $result = ["message" => "Error : Data Not Found"];
        }
        return $result;
    }

    /**
     * @param string $city
     * @return string[]
     */
    public function getCityCode(string $city): array
    {
        $cityCode = $this->cityCollectionFactory->create();
        $cityName = $cityCode->addCityIdFilter($city);
        if ($cityName->getData()) {
            $result = $cityName->toArray();
        } else {
            $result = ["message" => "Error : Data Not Found"];
        }
        return $result;
    }

    /**
     * @param string $district
     * @return string[]
     */
    public function getDistrictCode(string $district): array
    {
        $districtCode = $this->districtCollectionFactory->create();
        $districtName = $districtCode->addDistrictIdFilter($district);
        if ($districtName->getData()) {
            $result = $districtName->toArray();
        } else {
            $result = ["message" => "Error : Data Not Found"];
        }
        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function districtName($districtId)
    {
        $collection = $this->districtCollectionFactory->create();
        $collection->addFieldToFilter(DistrictInterface::DISTRICT_ID, $districtId);

        return $collection->getFirstItem();
    }

    /**
     * {@inheritdoc}
     */
    public function cityName($cityId)
    {
        $collection = $this->cityCollectionFactory->create();
        $collection->addFieldToFilter(CityInterface::ENTITY_ID, $cityId);

        return $collection->getFirstItem();
    }
}