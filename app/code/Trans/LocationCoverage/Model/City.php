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

use Trans\LocationCoverage\Api\Data\CityInterface;
use Trans\LocationCoverage\Model\ResourceModel\City as CityResourceModel;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource as AbstractResourceModel;
use Magento\Framework\Registry;

/**
 * Class City
 * @package Trans\LocationCoverage\Model
 */
class City extends AbstractModel implements CityInterface
{
    /**
     * @param Context $context
     * @param Registry $registry
     * @param AbstractResourceModel $resource
     * @param AbstractDb $resourceCollection
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        AbstractResourceModel $resource = null,
        AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $registry,
            $resource,
            $resourceCollection,
            $data
        );
    }

    /**
     * Construct data City
     */
    public function _construct()
    {
        $this->_init(CityResourceModel::class);
    }

    /**
     * Get Entity Id
     * @return int 
     */
    public function getEntityId()
    {
        return $this->getData(CityInterface::ENTITY_ID);
    }

    /**
     * Get Region Id
     * @return int 
     */
    public function getRegionId()
    {
        return $this->getData(CityInterface::REGION_ID);
    }

    /**
     * Get City Name
     * @return string 
     */
    public function getCityName()
    {
        return $this->getData(CityInterface::CITY_NAME);
    }

    /**
     * Set Entity Id
     * @return $entityId 
     */
    public function setEntityId($entityId)
    {
        $this->setData(CityInterface::ENTITY_ID, $entityId);
    }

    /**
     * Set Region Id
     * @return $regionId 
     */
    public function setRegionId($regionId)
    {
        $this->setData(CityInterface::REGION_ID, $regionId);
    }

    /**
     * Set City Name
     * @return $cityName 
     */
    public function setCityName($cityName)
    {
        $this->setData(CityInterface::CITY_NAME, $cityName);
    }
}