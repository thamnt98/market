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

use Trans\LocationCoverage\Api\Data\DistrictInterface;
use Trans\LocationCoverage\Model\ResourceModel\District as DistrictResourceModel;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource as AbstractResourceModel;
use Magento\Framework\Registry;

/**
 * Class District
 * @package Trans\LocationCoverage\Model
 */
class District extends AbstractModel implements DistrictInterface
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
     * Construct data District
     */
    public function _construct()
    {
        $this->_init(DistrictResourceModel::class);
    }

    /**
     * Get District Id
     * @return int 
     */
    public function getDistrictId()
    {
        return $this->getData(DistrictInterface::DISTRICT_ID);
    }

    /**
     * Get Entity Id
     * @return string 
     */
    public function getEntityId()
    {
        return $this->getData(DistrictInterface::ENTITY_ID);
    }

    /**
     * Get District Name
     * @return string 
     */
    public function getDistrictName()
    {
        return $this->getData(DistrictInterface::DISTRICT_NAME);
    }

    /**
     * Get District Key
     * @return string 
     */
    public function getDistrictKey()
    {
        return $this->getData(DistrictInterface::DISTRICT_KEY);
    }

    /**
     * Set District Id
     * @return $districtId 
     */
    public function setDistrictId($districtId)
    {
        $this->setData(DistrictInterface::DISTRICT_ID, $districtId);
    }

    /**
     * Set Entity Id
     * @return $entityId 
     */
    public function setEntityId($entityId)
    {
        $this->setData(DistrictInterface::ENTITY_ID, $entityId);
    }

    /**
     * Set District name
     * @return $districtName 
     */
    public function setDistrictName($districtName)
    {
        $this->setData(DistrictInterface::DISTRICT_NAME, $districtName);
    }

    /**
     * Set District key
     * @return $districtKey 
     */
    public function setDistrictKey($districtKey)
    {
        $this->setData(DistrictInterface::DISTRICT_KEY, $districtKey);
    }
}