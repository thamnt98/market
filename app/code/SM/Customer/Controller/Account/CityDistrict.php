<?php
/**
 * @category    SM
 * @package     SM_Customer
 * @license     http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 *
 * @author      Logan Ta <phuongtt2@smartosc.com>
 *
 * @copyright   Copyright (c) SmartOSC. All rights reserved.
 * @url http://www.smartosc.com/
 */

namespace SM\Customer\Controller\Account;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;

/**
 * Class CityDistrict
 * @package SM\Customer\Controller\Account
 */
class CityDistrict extends Action
{
    /**
     * @var \Trans\LocationCoverage\Model\ResourceModel\City\CollectionFactory
     */
    protected $cityCollectionFactory;

    /**
     * @var \Trans\LocationCoverage\Model\ResourceModel\District\CollectionFactory
     */
    protected $districtCollectionFactory;

    /**
     * @var \Magento\Directory\Model\ResourceModel\Region\CollectionFactory
     */
    protected $regionCollectionFactory;

    /**
     * @var ResultFactory
     */
    protected $resultFactory;

    /**
     * CityDistrict constructor.
     * @param Context $context
     * @param \Trans\LocationCoverage\Model\ResourceModel\City\CollectionFactory $cityCollectionFactory
     * @param \Trans\LocationCoverage\Model\ResourceModel\District\CollectionFactory $districtCollectionFactory
     * @param \Magento\Directory\Model\ResourceModel\Region\CollectionFactory $regionCollectionFactory
     * @param ResultFactory $resultFactory
     */
    public function __construct(
        Context $context,
        \Trans\LocationCoverage\Model\ResourceModel\City\CollectionFactory $cityCollectionFactory,
        \Trans\LocationCoverage\Model\ResourceModel\District\CollectionFactory $districtCollectionFactory,
        \Magento\Directory\Model\ResourceModel\Region\CollectionFactory $regionCollectionFactory,
        ResultFactory $resultFactory
    ) {
        $this->cityCollectionFactory = $cityCollectionFactory;
        $this->districtCollectionFactory = $districtCollectionFactory;
        $this->regionCollectionFactory = $regionCollectionFactory;
        $this->resultFactory = $resultFactory;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function execute()
    {
        $cityCollection = $this->cityCollectionFactory->create()->addFieldToSelect('*')
            ->setOrder(\Trans\LocationCoverage\Api\Data\CityInterface::CITY_NAME, 'ASC');
        /** @var \Trans\LocationCoverage\Model\ResourceModel\District\Collection $districtCollection */
        $districtCollection = $this->districtCollectionFactory->create()
            ->setOrder(\Trans\LocationCoverage\Api\Data\DistrictInterface::DISTRICT_NAME, 'ASC');

        $regionCollection = $this->regionCollectionFactory->create()->addFieldToFilter('country_id', 'ID');

        $districtWithCityList = [];
        $regionWithCityList = [];
        $cityWithRegionList = [];
        $cityList = [];
        $regionList = [];

        foreach ($regionCollection as $item) {
            $regionList[$item->getCode()] = (int)$item->getId();
        }

        foreach ($districtCollection as $item) {
            $districtWithCityList[$item->getEntityId()][$item->getDistrictId()] = $item->getDistrict();
        }

        foreach ($cityCollection as $item) {
            $cityList[$item->getEntityId()] = $item->getCity();
            $districtWithCityList[$item->getEntityId()] = $this->encodeSortedData($districtWithCityList[$item->getEntityId()]);
            if (isset($regionList[$item->getRegionId()])) {
                $regionWithCityList[$regionList[$item->getRegionId()]][] = ['id' => $item->getEntityId(), 'city' => $item->getCity()];
                $cityWithRegionList[$item->getEntityId()] = $regionList[$item->getRegionId()];
            }
        }

        $cityList = $this->encodeSortedData($cityList);

        $data = ['region' => $regionWithCityList, 'city' => $cityList, 'district' => $districtWithCityList, 'cityRegion' => $cityWithRegionList];

        $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        $resultJson->setData($data);
        return $resultJson;
    }

    /**
     * @param $data
     * @return array
     */
    private function encodeSortedData($data)
    {
        $result = [];
        foreach ($data as $key => $value) {
            $result[] = [$key => $value];
        }

        return $result;
    }
}
