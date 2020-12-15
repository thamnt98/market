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
use \Trans\IntegrationEntity\Api\Data\IntegrationProductAttributeSetInterface;
use \Trans\IntegrationEntity\Model\ResourceModel\IntegrationProductAttributeSet as ResourceModel;

class IntegrationProductAttributeSet extends \Magento\Framework\Model\AbstractModel implements
IntegrationProductAttributeSetInterface
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
        return $this->_getData(IntegrationProductAttributeSetInterface::ID);
    }

    /**
     * @inheritdoc
     */
    public function setId($id)
    {
        $this->setData(IntegrationProductAttributeSetInterface::ID, $id);
    }

    /**
     * @inheritdoc
     */
    public function getPimId()
    {
        return $this->_getData(IntegrationProductAttributeSetInterface::PIM_ID);
    }

    /**
     * @inheritdoc
     */
    public function setPimId($pimId)
    {
        $this->setData(IntegrationProductAttributeSetInterface::PIM_ID, $pimId);
    }

    /**
     * @inheritdoc
     */
    public function getName()
    {
        return $this->_getData(IntegrationProductAttributeSetInterface::NAME);
    }

    /**
     * @inheritdoc
     */
    public function setName($name)
    {
        $this->setData(IntegrationProductAttributeSetInterface::NAME, $name);
    }

    /**
     * @inheritdoc
     */
    public function getAttributeSetId()
    {
        return $this->_getData(IntegrationProductAttributeSetInterface::ATTRIBUTE_SET_ID);
    }

    /**
     * @inheritdoc
     */
    public function setAttributeSetId($attributeSetId)
    {
        $this->setData(IntegrationProductAttributeSetInterface::ATTRIBUTE_SET_ID, $attributeSetId);
    }

    /**
     * @inheritdoc
     */
    public function getAttributeSetGroup()
    {
        return $this->_getData(IntegrationProductAttributeSetInterface::ATTRIBUTE_SET_GROUP);
    }

    /**
     * @inheritdoc
     */
    public function setAttributeSetGroup($attributeSetGroup)
    {
        $this->setData(IntegrationProductAttributeSetInterface::ATTRIBUTE_SET_GROUP, $attributeSetGroup);
    }

    /**
     * @inheritdoc
     */
    public function getDeleted()
    {
        return $this->_getData(IntegrationProductAttributeSetInterface::DELETED);
    }

    /**
     * @inheritdoc
     */
    public function setDeleted($deleted)
    {
        $this->setData(IntegrationProductAttributeSetInterface::DELETED, $deleted);
    }

    /**
     * @inheritdoc
     */
    public function getStatus()
    {
        return $this->_getData(IntegrationProductAttributeSetInterface::STATUS);
    }

    /**
     * @inheritdoc
     */
    public function setStatus($status)
    {
        $this->setData(IntegrationProductAttributeSetInterface::STATUS, $status);
    }

    /**
     * @inheritdoc
     */
    public function getCreatedAt()
    {
        return $this->_getData(IntegrationProductAttributeSetInterface::CREATED_AT);
    }

    /**
     * @inheritdoc
     */
    public function setCreatedAt($createdAt)
    {
        $this->setData(IntegrationProductAttributeSetInterface::CREATED_AT, $createdAt);
    }

    /**
     * @inheritdoc
     */
    public function getUpdatedAt()
    {
        return $this->_getData(IntegrationProductAttributeSetInterface::UPDATED_AT);
    }

    /**
     * @inheritdoc
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->setData(IntegrationProductAttributeSetInterface::UPDATED_AT, $updatedAt);
    }

}