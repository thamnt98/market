<?php
/**
 * @category Trans
 * @package  Trans_IntegrationNotification
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 * @author   Imam Kusuma <imam.kusuma@ctcorpdigital.com>
 *
 * Copyright © 2019 CT Corp Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\IntegrationNotification\Model;

use \Trans\IntegrationNotification\Api\Data\IntegrationNotificationLogInterface;
use \Trans\IntegrationNotification\Model\ResourceModel\IntegrationNotificationLog as ResourceModel;

class IntegrationNotificationLog extends \Magento\Framework\Model\AbstractModel implements IntegrationNotificationLogInterface
{
    /**
     * cache tag
     *
     * @var string
     */
    const CACHE_TAG = 'trans_notification_log';

    /**
     * cache tag
     *
     * @var string
     */
    protected $_cacheTag = 'trans_notification_log';

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'trans_notification_log';
    
    /**
     * @return void
     * @SuppressWarnings(PHPMD)
     */
    protected function _construct()
    {
        $this->_init(ResourceModel::class);
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->getData(IntegrationNotificationLogInterface::ID);
    }

    /**
     * @inheritdoc
     */
    public function setId($id)
    {
        $this->setData(IntegrationNotificationLogInterface::ID, $id);
    }

    /**
     * @inheritdoc
     */
    public function getChannel()
    {
        return $this->getData(IntegrationNotificationLogInterface::CHANNEL);
    }

    /**
     * @inheritdoc
     */
    public function setChannel($channel)
    {
        return $this->setData(IntegrationNotificationLogInterface::CHANNEL, $channel);
    }

    /**
     * @inheritdoc
     */
    public function getParam()
    {
        return $this->getData(IntegrationNotificationLogInterface::PARAM);
    }

    /**
     * @inheritdoc
     */
    public function setParam($param)
    {
        return $this->setData(IntegrationNotificationLogInterface::PARAM, $param);
    }

    /**
     * @inheritdoc
     */
    public function getParamEncrypt()
    {
        return $this->getData(IntegrationNotificationLogInterface::PARAM_ENCRYPT);
    }

    /**
     * @inheritdoc
     */
    public function setParamEncrypt($param)
    {
        return $this->setData(IntegrationNotificationLogInterface::PARAM_ENCRYPT, $param);
    }

    /**
     * @inheritdoc
     */
    public function getResponse()
    {
        return $this->getData(IntegrationNotificationLogInterface::RESPONSE);
    }

    /**
     * @inheritdoc
     */
    public function setResponse($response)
    {
        return $this->setData(IntegrationNotificationLogInterface::RESPONSE, $response);
    }

    /**
     * @inheritdoc
     */
    public function getCreatedAt()
    {
        return $this->getData(IntegrationNotificationLogInterface::CREATED_AT);
    }

    /**
     * @inheritdoc
     */
    public function setCreatedAt($createdAt)
    {
        return $this->setData(IntegrationNotificationLogInterface::CREATED_AT, $createdAt);
    }

    /**
     * @inheritdoc
     */
    public function getUpdatedAt()
    {
        return $this->getData(IntegrationNotificationLogInterface::UPDATED_AT);
    }

    /**
     * @inheritdoc
     */
    public function setUpdatedAt($updatedAt)
    {
        return $this->setData(IntegrationNotificationLogInterface::UPDATED_AT, $updatedAt);
    }
}
