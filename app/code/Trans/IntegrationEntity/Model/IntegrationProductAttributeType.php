<?php
/**
 * @category Trans
 * @package  Trans_IntegrationEntity
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   J.P <jaka.pondan@ctcorpdigital.com>
 *
 * Copyright © 2019 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\IntegrationEntity\Model;

use \Trans\Integration\Api\Data\IntegrationChannelInterface;
use \Trans\IntegrationEntity\Api\Data\IntegrationProductAttributeTypeInterface;
use \Trans\IntegrationEntity\Model\ResourceModel\IntegrationProductAttributeType as ResourceModel;

class IntegrationProductAttributeType extends \Magento\Framework\Model\AbstractModel implements
IntegrationProductAttributeTypeInterface
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
        return $this->_getData(IntegrationProductAttributeTypeInterface::ID);
    }

    /**
     * @inheritdoc
     */
    public function setId($id)
    {
        $this->setData(IntegrationProductAttributeTypeInterface::ID, $id);
    }

    /**
     * @inheritdoc
     */
    public function getPimTypeId()
    {
        return $this->_getData(IntegrationProductAttributeTypeInterface::PIM_TYPE_ID);
    }

    /**
     * @inheritdoc
     */
    public function setPimTypeId($id)
    {
        $this->setData(IntegrationProductAttributeTypeInterface::PIM_TYPE_ID, $id);
    }

    /**
     * @inheritdoc
     */
    public function getPimTypeCode()
    {
        return $this->_getData(IntegrationProductAttributeTypeInterface::PIM_TYPE_CODE);
    }

    /**
     * @inheritdoc
     */
    public function setPimTypeCode($code)
    {
        $this->setData(IntegrationProductAttributeTypeInterface::PIM_TYPE_CODE, $code);
    }

    /**
     * @inheritdoc
     */
    public function getPimTypeName()
    {
        return $this->_getData(IntegrationProductAttributeTypeInterface::PIM_TYPE_CODE);
    }

    /**
     * @inheritdoc
     */
    public function setPimTypeName($name)
    {
        $this->setData(IntegrationProductAttributeTypeInterface::PIM_TYPE_NAME, $name);
    }

    /**
     * @inheritdoc
     */
    public function getBackendType()
    {
        return $this->_getData(IntegrationProductAttributeTypeInterface::BACKEND_CODE);
    }

    /**
     * @inheritdoc
     */
    public function setBackendType($code)
    {
        $this->setData(IntegrationProductAttributeTypeInterface::BACKEND_CODE, $code);
    }

    /**
     * @inheritdoc
     */
    public function getFrontendInput()
    {
        return $this->_getData(IntegrationProductAttributeTypeInterface::FRONTEND_CODE);
    }

    /**
     * @inheritdoc
     */
    public function setFrontendInput($code)
    {
        $this->setData(IntegrationProductAttributeTypeInterface::FRONTEND_CODE, $code);
    }

    /**
     * @inheritdoc
     */
    public function getStatus()
    {
        return $this->_getData(IntegrationChannelInterface::STATUS);
    }

    /**
     * @inheritdoc
     */
    public function setStatus($status)
    {
        $this->setData(IntegrationChannelInterface::STATUS, $status);
    }

    /**
     * @inheritdoc
     */
    public function isSwatch()
    {
        return $this->_getData(IntegrationChannelInterface::IS_SWATCH);
    }

    /**
     * @inheritdoc
     */
    public function setSwatch($swatch)
    {
        $this->setData(IntegrationChannelInterface::IS_SWATCH, $swatch);
    }

    /**
     * @inheritdoc
     */
    public function getCreatedAt()
    {
        return $this->_getData(IntegrationChannelInterface::CREATED_AT);
    }

    /**
     * @inheritdoc
     */
    public function setCreatedAt($createdAt)
    {
        $this->setData(IntegrationChannelInterface::CREATED_AT, $createdAt);
    }

    /**
     * @inheritdoc
     */
    public function getUpdatedAt()
    {
        return $this->_getData(IntegrationChannelInterface::UPDATED_AT);
    }

    /**
     * @inheritdoc
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->setData(IntegrationChannelInterface::UPDATED_AT, $updatedAt);
    }

}