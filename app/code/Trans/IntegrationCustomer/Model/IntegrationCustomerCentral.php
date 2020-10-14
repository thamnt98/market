<?php
/**
 * @category Trans
 * @package  Trans_IntegrationCustomer
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   J.P <jaka.pondan@ctcorpdigital.com>
 *
 * Copyright © 2019 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\IntegrationCustomer\Model;

use \Trans\Integration\Api\Data\IntegrationChannelInterface;
use \Trans\IntegrationCustomer\Api\Data\IntegrationCustomerCentralInterface;
use \Trans\IntegrationCustomer\Model\ResourceModel\IntegrationCustomerCentral as ResourceModel;

class IntegrationCustomerCentral extends \Magento\Framework\Model\AbstractModel implements
    IntegrationCustomerCentralInterface
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
        return $this->_getData(IntegrationCustomerCentralInterface::ID);
    }

    /**
     * @inheritdoc
     */
    public function setId($id)
    {
        $this->setData(IntegrationCustomerCentralInterface::ID, $id);
    }

    /**
     * @inheritdoc
     */
    public function getMagentoCustomerId()
    {
        return $this->_getData(IntegrationCustomerCentralInterface::CUST_ID);
    }

    /**
     * @inheritdoc
     */
    public function setMagentoCustomerId($custId)
    {
        $this->setData(IntegrationCustomerCentralInterface::CUST_ID, $custId);
    }

    /**
     * @inheritdoc
     */
    public function getCentralId()
    {
        return $this->_getData(IntegrationCustomerCentralInterface::CENTRAL_ID);
    }

    /**
     * @inheritdoc
     */
    public function setCentralId($cenId)
    {
        $this->setData(IntegrationCustomerCentralInterface::CENTRAL_ID, $cenId);
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