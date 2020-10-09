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

namespace Trans\IntegrationBrand\Model;

use \Trans\Integration\Api\Data\IntegrationChannelInterface;
use \Trans\IntegrationBrand\Api\Data\IntegrationJobInterface;
use \Trans\IntegrationBrand\Model\ResourceModel\IntegrationJob as ResourceModel;

class IntegrationJob extends \Magento\Framework\Model\AbstractModel implements
IntegrationJobInterface
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
        return $this->_getData(IntegrationJobInterface::ID);
    }

    /**
     * @inheritdoc
     */
    public function setId($id)
    {
        $this->setData(IntegrationJobInterface::ID, $id);
    }

    /**
     * @inheritdoc
     */
    public function getMdId()
    {
        return $this->_getData(IntegrationJobInterface::METHOD_ID);
    }

    /**
     * @inheritdoc
     */
    public function setMdId($mdid)
    {
        $this->setData(IntegrationJobInterface::METHOD_ID, $mdid);
    }

    /**
     * @inheritdoc
     */
    public function getBatchId()
    {
        return $this->_getData(IntegrationJobInterface::BATCH_ID);
    }

    /**
     * @inheritdoc
     */
    public function setBatchId($batchId)
    {
        $this->setData(IntegrationJobInterface::BATCH_ID, $batchId);
    }

    /**
     * @inheritdoc
     */
    public function getMessages()
    {
        return $this->_getData(IntegrationJobInterface::MESSAGE);
    }

    /**
     * @inheritdoc
     */
    public function setMessages($msg)
    {
        $this->setData(IntegrationJobInterface::MESSAGE, $msg);
    }

    /**
     * @inheritdoc
     */
    public function getLastUpdated()
    {
        return $this->_getData(IntegrationJobInterface::LAST_UPDATED);
    }

    /**
     * @inheritdoc
     */
    public function setLastUpdated($lastupdated)
    {
        $this->setData(IntegrationJobInterface::LAST_UPDATED, $lastupdated);
    }

    /**
     * @inheritdoc
     */
    public function getTotalData()
    {
        return $this->_getData(IntegrationJobInterface::TOTAL_DATA);
    }

    /**
     * @inheritdoc
     */
    public function setTotalData($total)
    {
        $this->setData(IntegrationJobInterface::TOTAL_DATA, $total);
    }

    /**
     * @inheritdoc
     */
    public function getLimits()
    {
        return $this->_getData(IntegrationJobInterface::LIMIT);
    }

    /**
     * @inheritdoc
     */
    public function setLimits($limit)
    {
        $this->setData(IntegrationJobInterface::LIMIT, $limit);
    }

    /**
     * @inheritdoc
     */
    public function getOffset()
    {
        return $this->_getData(IntegrationJobInterface::OFFSET);
    }

    /**
     * @inheritdoc
     */
    public function setOffset($offset)
    {
        $this->setData(IntegrationJobInterface::OFFSET, $offset);
    }

    /**
     * @inheritdoc
     */
    public function getStartJob()
    {
        return $this->_getData(IntegrationJobInterface::START_JOB);
    }

    /**
     * @inheritdoc
     */
    public function setStartJob($startJob)
    {
        $this->setData(IntegrationJobInterface::START_JOB, $startJob);
    }

    /**
     * @inheritdoc
     */
    public function getEndJob()
    {
        return $this->_getData(IntegrationJobInterface::END_JOB);
    }

    /**
     * @inheritdoc
     */
    public function setEndJob($endJob)
    {
        $this->setData(IntegrationJobInterface::END_JOB, $endJob);
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

    /**
     * @inheritdoc
     */
    public function getHit()
    {
        return $this->_getData(IntegrationJobInterface::HIT);
    }

     /**
     * @inheritdoc
     */
    public function setHit($hit)
    {
        $this->setData(IntegrationJobInterface::HIT, $hit);
    }

     /**
     * @inheritdoc
     */
    public function getLastJbId()
    {
        return $this->_getData(IntegrationJobInterface::LAST_JB_ID);
    }

     /**
     * @inheritdoc
     */
    public function setLastJbId($jbid)
    {
        $this->setData(IntegrationJobInterface::LAST_JB_ID, $jbid);
    }
}