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
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Json\Helper\Data as JsonHelper;
use Trans\LocationCoverage\Model\ResourceModel\City\CollectionFactory as CityCollection;
use Trans\LocationCoverage\Model\ResourceModel\District\Collection;
use Trans\LocationCoverage\Model\ResourceModel\District\CollectionFactory as DistrictCollection;

class CollectDistrict extends Action
{

    /**
     * @var JsonFactory
     */
    protected $resultJsonFactory;

    /**
     * @var DistrictCollection
     */
    protected $_districtCollection;

    /**
     * @var CityCollection
     */
    protected $_cityCollection;

    /**
     * @var JsonHelper
     */
    protected $jsonHelper;

    /**
     * CollectDistrict constructor.
     *
     * @param Context $context
     * @param JsonFactory $resultJsonFactory
     * @param DistrictCollection $districtCollection
     * @param CityCollection $cityCollection
     * @param JsonHelper $jsonHelper
     */
    public function __construct(
        Context $context,
        JsonFactory $resultJsonFactory,
        DistrictCollection $districtCollection,
        CityCollection $cityCollection,
        JsonHelper $jsonHelper
    ) {
        $this->resultJsonFactory = $resultJsonFactory;
        $this->_districtCollection = $districtCollection;
        $this->_cityCollection = $cityCollection;
        $this->jsonHelper = $jsonHelper;
        parent::__construct($context);
    }

    public function execute()
    {
        $resultJson = $this->resultJsonFactory->create();
        $city = $this->getRequest()->getParam('city');
        $cityId = $this->getCityId($city);
        if ($cityId != '') {
            $districts = null;
            $districts = $this->_districtCollection->create();
            $districts->addFieldToFilter('entity_id', $cityId);

            $resultJson->setData(
                ['response' => true, 'cityId' => $cityId, 'districts' => $districts->getData()]
            );

            return $resultJson;
        }

        $resultJson->setData(
            ['response' => false, 'cityId' => $cityId, 'districts' => false]
        );

        return $resultJson;
    }

    /**
     * @return Collection
     */
    private function getDistrictCollect()
    {
        return $this->_districtCollection->create();
    }

    /**
     * @param $city
     *
     * @return mixed
     */
    private function getCityId($city)
    {
        $cityCollect = $this->_cityCollection->create();
        $cityCollect->addFieldToFilter('city', $city)->getFirstItem();
        $cityId = '';
        foreach ($cityCollect as $key => $value) {
            $cityId = $value->getEntityId();
        }

        return $cityId;
    }
}
