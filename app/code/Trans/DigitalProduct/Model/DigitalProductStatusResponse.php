<?php
/**
 * @category Trans
 * @package  Trans_DigitalProduct
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   hadi <ashadi.sejati@ctcorpdigital.com>
 *
 * Copyright © 2019 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\DigitalProduct\Model;

use Trans\DigitalProduct\Api\Data\DigitalProductStatusResponseInterface;
use Trans\DigitalProduct\Model\ResourceModel\DigitalProductStatusResponse as DigitalProductStatusResponseResourceModel;

/**
 * Class DigitalProductStatusResponse
 *
 * @SuppressWarnings(PHPMD)
 */
class DigitalProductStatusResponse extends \Magento\Framework\Model\AbstractModel implements DigitalProductStatusResponseInterface
{
    /**
     * cache tag
     *
     * @var string
     */
    const CACHE_TAG = 'trans_digitalproduct_transaction_status';

    /**
     * cache tag
     *
     * @var string
     */
    protected $_cacheTag = 'trans_digitalproduct_transaction_status';

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'trans_digitalproduct_transaction_status';

    /**
     * @return void
     * @SuppressWarnings(PHPMD)
     */
    protected function _construct()
    {
        $this->_init(DigitalProductStatusResponseResourceModel::class);
    }

    /**
     * Get identities
     *
     * @return array
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    public function getDefaultValues()
    {
        $values = [];

        return $values;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->getData(DigitalProductStatusResponseInterface::ID);
    }

    /**
     * @param int $id
     * @return void
     */
    public function setId($id)
    {
        return $this->setData(DigitalProductStatusResponseInterface::ID, $id);
    }

    /**
     * @return string
     */
    public function getCustomerId()
    {
        return $this->getData(DigitalProductStatusResponseInterface::CUSTOMER_ID);
    }

    /**
     * @param string $customerId
     * @return void
     */
    public function setCustomerId($customerId)
    {
        return $this->setData(DigitalProductStatusResponseInterface::CUSTOMER_ID, $customerId);
    }

    /**
     * @return string
     */
    public function getRequest()
    {
        return $this->getData(DigitalProductStatusResponseInterface::REQUEST);
    }

    /**
     * @param string $request
     * @return void
     */
    public function setRequest($request)
    {
        return $this->setData(DigitalProductStatusResponseInterface::REQUEST, $request);
    }

    /**
     * @return string
     */
    public function getResponse()
    {
        return $this->getData(DigitalProductStatusResponseInterface::RESPONSE);
    }

    /**
     * @param string $response
     * @return void
     */
    public function setResponse($response)
    {
        return $this->setData(DigitalProductStatusResponseInterface::RESPONSE, $response);
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->getData(DigitalProductStatusResponseInterface::STATUS);
    }

    /**
     * @param string $status
     * @return void
     */
    public function setStatus($status)
    {
        return $this->setData(DigitalProductStatusResponseInterface::STATUS, $status);
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        return $this->getData(DigitalProductStatusResponseInterface::MESSAGE);
    }

    /**
     * @param string $message
     * @return void
     */
    public function setMessage($message)
    {
        return $this->setData(DigitalProductStatusResponseInterface::MESSAGE, $message);
    }

    /**
     * @return string
     */
    public function getCreatedAt()
    {
        return $this->getData(DigitalProductStatusResponseInterface::CREATED_AT);
    }

    /**
     * @param string $createdAt
     * @return void
     */
    public function setCreatedAt($createdAt)
    {
        return $this->setData(DigitalProductStatusResponseInterface::CREATED_AT, $createdAt);
    }

    /**
     * @return string
     */
    public function getUpdatedAt()
    {
        return $this->getData(DigitalProductStatusResponseInterface::UPDATED_AT);
    }

    /**
     * @param string $updatedAt
     * @return void
     */
    public function setUpdatedAt($updatedAt)
    {
        return $this->setData(DigitalProductStatusResponseInterface::UPDATED_AT, $updatedAt);
    }
}
