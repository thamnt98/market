<?php
/**
 * @category Trans
 * @package  Trans_Integration
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   J.P <jaka.pondan@ctcorpdigital.com>
 *
 * Copyright © 2019 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\Integration\Model;

use \Trans\Integration\Api\Data\IntegrationChannelInterface;
use \Trans\Integration\Model\ResourceModel\IntegrationChannel as ResourceModel;

class IntegrationChannel extends \Magento\Framework\Model\AbstractModel implements
    \Trans\Integration\Api\Data\IntegrationChannelInterface
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
        return $this->_getData(IntegrationChannelInterface::ID);
    }

    /**
     * @inheritdoc
     */
    public function setId($id)
    {
        $this->setData(IntegrationChannelInterface::ID, $id);
    }

    /**
     * @inheritdoc
     */
    public function getName()
    {
        return $this->_getData(IntegrationChannelInterface::NAME);
    }

    /**
     * @inheritdoc
     */
    public function setName($name)
    {
        $this->setData(IntegrationChannelInterface::NAME, $name);
    }

    /**
     * @inheritdoc
     */
    public function getUrl()
    {
        return $this->_getData(IntegrationChannelInterface::URL);
    }

    /**
     * @inheritdoc
     */
    public function setUrl($url)
    {
        $this->setData(IntegrationChannelInterface::URL, $url);
    }

    /**
     * @inheritdoc
     */
    public function getEnvironment()
    {
        return $this->_getData(IntegrationChannelInterface::ENV);
    }

    /**
     * @inheritdoc
     */
    public function setEnvironment($env)
    {
        $this->setData(IntegrationChannelInterface::ENV, $env);
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

    /**
     * @inheritdoc
     */
    public function getCreatedBy()
    {
        return $this->_getData(IntegrationChannelInterface::CREATED_BY);
    }

    /**
     * @inheritdoc
     */
    public function setCreatedBy($createdBy)
    {
        $this->setData(IntegrationChannelInterface::CREATED_BY, $createdBy);
    }

    /**
     * @inheritdoc
     */
    public function getUpdatedBy()
    {
        return $this->_getData(IntegrationChannelInterface::UPDATED_BY);
    }

    /**
     * @inheritdoc
     */
    public function setUpdatedBy($updatedBy)
    {
        $this->setData(IntegrationChannelInterface::UPDATED_BY, $updatedBy);
    }
}