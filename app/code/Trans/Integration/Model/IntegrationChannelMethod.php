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
use \Trans\Integration\Api\Data\IntegrationChannelMethodInterface;
use \Trans\Integration\Model\ResourceModel\IntegrationChannelMethod as ResourceModel;

class IntegrationChannelMethod extends \Magento\Framework\Model\AbstractModel implements
IntegrationChannelMethodInterface
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
        return $this->_getData(IntegrationChannelMethodInterface::ID);
    }

    /**
     * @inheritdoc
     */
    public function setId($id)
    {
        $this->setData(IntegrationChannelMethodInterface::ID, $id);
    }

    /**
     * @inheritdoc
     */
    public function getChId()
    {
        return $this->_getData(IntegrationChannelMethodInterface::CHANNEL_ID);
    }

    /**
     * @inheritdoc
     */
    public function setChId($chid)
    {
        $this->setData(IntegrationChannelMethodInterface::CHANNEL_ID, $chid);
    }

    /**
     * @inheritdoc
     */
    public function getDataDesc()
    {
        return $this->_getData(IntegrationChannelMethodInterface::DESCRIPTION);
    }

    /**
     * @inheritdoc
     */
    public function setDataDesc($desc)
    {
        $this->setData(IntegrationChannelMethodInterface::DESCRIPTION, $desc);
    }

    /**
     * @inheritdoc
     */
    public function getLimits()
    {
        return $this->_getData(IntegrationChannelMethodInterface::LIMIT);
    }

    /**
     * @inheritdoc
     */
    public function setLimits($limit)
    {
        $this->setData(IntegrationChannelMethodInterface::LIMIT, $limit);
    }

     /**
     * @inheritdoc
     */
    public function getTags()
    {
        return $this->_getData(IntegrationChannelMethodInterface::TAG);
    }

    /**
     * @inheritdoc
     */
    public function setTags($tags)
    {
        $this->setData(IntegrationChannelMethodInterface::TAG, $tags);
    }

    /**
     * @inheritdoc
     */
    public function getDataMethod()
    {
        return $this->_getData(IntegrationChannelMethodInterface::METHOD);
    }

    /**
     * @inheritdoc
     */
    public function setDataMethod($method)
    {
        $this->setData(IntegrationChannelMethodInterface::METHOD, $method);
    }

    /**
     * @inheritdoc
     */
    public function getDataHeaders()
    {
        return $this->_getData(IntegrationChannelMethodInterface::HEADERS);
    }

    /**
     * @inheritdoc
     */
    public function setDataHeaders($headers)
    {
        $this->setData(IntegrationChannelMethodInterface::HEADERS, $headers);
    }

     /**
     * @inheritdoc
     */
    public function getQueryParams()
    {
        return $this->_getData(IntegrationChannelMethodInterface::QUERY_PARAMS);
    }

    /**
     * @inheritdoc
     */
    public function setQueryParams($params)
    {
        $this->setData(IntegrationChannelMethodInterface::QUERY_PARAMS, $params);
    }

     /**
     * @inheritdoc
     */
    public function getDataBody()
    {
        return $this->_getData(IntegrationChannelMethodInterface::BODY);
    }

    /**
     * @inheritdoc
     */
    public function setDataBody($body)
    {
        $this->setData(IntegrationChannelMethodInterface::BODY, $body);
    }

     /**
     * @inheritdoc
     */
    public function getDataPath()
    {
        return $this->_getData(IntegrationChannelMethodInterface::PATH);
    }

    /**
     * @inheritdoc
     */
    public function setDataPath($path)
    {
        $this->setData(IntegrationChannelMethodInterface::PATH, $path);
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