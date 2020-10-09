<?php
/**
 * @category Trans
 * @package  Trans_CatalogStock
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   J.P <jaka.pondan@ctcorpdigital.com>
 *
 * Copyright © 2019 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\IntegrationCatalogStock\Model;

use \Trans\Integration\Api\Data\IntegrationChannelInterface;
use \Trans\IntegrationCatalogStock\Api\Data\IntegrationDataValueInterface;
use \Trans\IntegrationCatalogStock\Model\ResourceModel\IntegrationDataValue as ResourceModel;

class IntegrationDataValue extends \Magento\Framework\Model\AbstractModel implements
IntegrationDataValueInterface
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
        return $this->_getData(IntegrationDataValueInterface::ID);
    }

    /**
     * @inheritdoc
     */
    public function setId($id)
    {
        $this->setData(IntegrationDataValueInterface::ID, $id);
    }

    /**
     * @inheritdoc
     */
    public function getJbId()
    {
        return $this->_getData(IntegrationDataValueInterface::JOB_ID);
    }

    /**
     * @inheritdoc
     */
    public function setJbId($jbid)
    {
        $this->setData(IntegrationDataValueInterface::JOB_ID, $jbid);
    }

    /**
     * @inheritdoc
     */
    public function getMessages()
    {
        return $this->_getData(IntegrationDataValueInterface::MESSAGE);
    }

    /**
     * @inheritdoc
     */
    public function setMessages($msg)
    {
        $this->setData(IntegrationDataValueInterface::MESSAGE, $msg);
    }

    /**
     * @inheritdoc
     */
    public function getDataValue()
    {
        return $this->_getData(IntegrationDataValueInterface::DATA_VALUE);
    }

    /**
     * @inheritdoc
     */
    public function setDataValue($dataValue)
    {
        $this->setData(IntegrationDataValueInterface::DATA_VALUE, $dataValue);
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

   
}