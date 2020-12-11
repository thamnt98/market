<?php
/**
 * @category Trans
 * @package  Trans_IntegrationEntity
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   HaDi <ashadi.sejati@transdigital.co.id>
 *
 * Copyright © 2020 PT Trans Digital. All rights reserved.
 * https://ctcorpdigital.com/
 */

namespace Trans\IntegrationEntity\Model;

use \Trans\Integration\Api\Data\IntegrationChannelInterface;
use \Trans\IntegrationEntity\Api\Data\IntegrationProductAttributeSetChildInterface;
use \Trans\IntegrationEntity\Model\ResourceModel\IntegrationProductAttributeSetChild as ResourceModel;

class IntegrationProductAttributeSetChild extends \Magento\Framework\Model\AbstractModel implements
IntegrationProductAttributeSetChildInterface
{
    protected function _construct()
    {
        $this->_init(ResourceModel::class);
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->_getData(IntegrationProductAttributeSetChildInterface::ID);
    }

    /**
     * @inheritdoc
     */
    public function setId($id)
    {
        $this->setData(IntegrationProductAttributeSetChildInterface::ID, $id);
    }

    /**
     * @inheritdoc
     */
    public function getPimId()
    {
        return $this->_getData(IntegrationProductAttributeSetChildInterface::PIM_ID);
    }

    /**
     * @inheritdoc
     */
    public function setPimId($pimId)
    {
        $this->setData(IntegrationProductAttributeSetChildInterface::PIM_ID, $pimId);
    }

    /**
     * @inheritdoc
     */
    public function getCode()
    {
        return $this->_getData(IntegrationProductAttributeSetChildInterface::CODE);
    }

    /**
     * @inheritdoc
     */
    public function setCode($code)
    {
        $this->setData(IntegrationProductAttributeSetChildInterface::CODE, $code);
    }

    /**
     * @inheritdoc
     */
    public function getDeletedAttributeList()
    {
        return $this->_getData(IntegrationProductAttributeSetChildInterface::DELETED_ATTRIBUTE_LIST);
    }

    /**
     * @inheritdoc
     */
    public function setDeletedAttributeList($deletedAttributeList)
    {
        $this->setData(IntegrationProductAttributeSetChildInterface::DELETED_ATTRIBUTE_LIST, $deletedAttributeList);
    }

    /**
     * @inheritdoc
     */
    public function getAttributeSetGroup()
    {
        return $this->_getData(IntegrationProductAttributeSetChildInterface::ATTRIBUTE_SET_GROUP);
    }

    /**
     * @inheritdoc
     */
    public function setAttributeSetGroup($attributeSetGroup)
    {
        $this->setData(IntegrationProductAttributeSetChildInterface::ATTRIBUTE_SET_GROUP, $attributeSetGroup);
    }
    
    /**
     * @inheritdoc
     */
    public function getStatus()
    {
        return $this->_getData(IntegrationProductAttributeSetChildInterface::STATUS);
    }

    /**
     * @inheritdoc
     */
    public function setStatus($status)
    {
        $this->setData(IntegrationProductAttributeSetChildInterface::STATUS, $status);
    }

    /**
     * @inheritdoc
     */
    public function getCreatedAt()
    {
        return $this->_getData(IntegrationProductAttributeSetChildInterface::CREATED_AT);
    }

    /**
     * @inheritdoc
     */
    public function setCreatedAt($createdAt)
    {
        $this->setData(IntegrationProductAttributeSetChildInterface::CREATED_AT, $createdAt);
    }

    /**
     * @inheritdoc
     */
    public function getUpdatedAt()
    {
        return $this->_getData(IntegrationProductAttributeSetChildInterface::UPDATED_AT);
    }

    /**
     * @inheritdoc
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->setData(IntegrationProductAttributeSetChildInterface::UPDATED_AT, $updatedAt);
    }

}