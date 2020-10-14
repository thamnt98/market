<?php
/**
 * @category Trans
 * @package  Trans_Customer
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   J.P <jaka.pondan@ctcorpdigital.com>
 *
 * Copyright © 2019 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\Customer\Model;

use Trans\Customer\Api\Data\CustomerIntegrationDataInterface;

/**
 * Class CustomerIntegrationData
 * @package Trans\Customer\Model
 */
class CustomerIntegrationData extends \Magento\Framework\Model\AbstractModel implements CustomerIntegrationDataInterface
{
    /**
     * @inheritdoc
     */
    public function getCentralId()
    {
        return $this->getData(CustomerIntegrationDataInterface::CENTRAL_ID);
    }

    /**
     * @inheritdoc
     */
    public function setCentralId($cenId)
    {
        $this->setData(CustomerIntegrationDataInterface::CENTRAL_ID, $cenId);
    }

    /**
     * @inheritdoc
     */
    public function getCustomerName()
    {
        return $this->getData(CustomerIntegrationDataInterface::CUST_NAME);
    }

    /**
     * @inheritdoc
     */
    public function setCustomerName($name)
    {
        $this->setData(CustomerIntegrationDataInterface::CUST_NAME, $name);
    }

    /**
     * @inheritdoc
     */
    public function getCustomerPhone()
    {
        return $this->getData(CustomerIntegrationDataInterface::CUST_PHONE);
    }

    /**
     * @inheritdoc
     */
    public function setCustomerPhone($createdAt)
    {
        $this->setData(CustomerIntegrationDataInterface::CUST_PHONE, $createdAt);
    }

    /**
     * @inheritdoc
     */
    public function getCustomerEmail()
    {
        return $this->getData(CustomerIntegrationDataInterface::CUST_EMAIL);
    }

    /**
     * @inheritdoc
     */
    public function setCustomerEmail($email)
    {
        $this->setData(CustomerIntegrationDataInterface::CUST_EMAIL, $email);
    }

    /**
     * @inheritdoc
     */
    public function getCustomerPasswordHash()
    {
        return $this->getData(CustomerIntegrationDataInterface::CUST_PASSHASH);
    }

    /**
     * @inheritdoc
     */
    public function setCustomerPasswordHash($pass)
    {
        $this->setData(CustomerIntegrationDataInterface::CUST_PASSHASH, $pass);
    }

    /**
     * @inheritdoc
     */
    public function getCustomerPassword()
    {
        return $this->getData(CustomerIntegrationDataInterface::CUST_PASS);
    }

    /**
     * @inheritdoc
     */
    public function setCustomerPassword($pass)
    {
        $this->setData(CustomerIntegrationDataInterface::CUST_PASS, $pass);
    }

    /**
     * @inheritdoc
     */
    public function getStatus()
    {
        return $this->getData(CustomerIntegrationDataInterface::STATUS);
    }

    /**
     * @inheritdoc
     */
    public function setStatus($status)
    {
        $this->setData(CustomerIntegrationDataInterface::STATUS, $status);
    }

}
