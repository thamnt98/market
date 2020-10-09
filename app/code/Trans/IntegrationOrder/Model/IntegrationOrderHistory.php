<?php
/**
 * @category Trans
 * @package  Trans_IntegrationOrder
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   Muhammad Randy <muhammad.randy@ctcorpdigital.com>
 *
 * Copyright © 2020 PT CT Corp Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\IntegrationOrder\Model;

use \Trans\IntegrationOrder\Api\Data\IntegrationOrderHistoryInterface;
use \Trans\IntegrationOrder\Model\ResourceModel\IntegrationOrderHistory as ResourceModel;

class IntegrationOrderHistory extends \Magento\Framework\Model\AbstractModel implements
    IntegrationOrderHistoryInterface
{
    protected function _construct()
    {
        $this->_init(ResourceModel::class);
    }

    /**
     * @inheritdoc
     */
    public function getHistoryId()
    {
        return $this->getData(IntegrationOrderHistoryInterface::HISTORY_ID);
    }

    /**
     * @inheritdoc
     */
    public function setHistoryId($historyId)
    {
        return $this->setData(IntegrationOrderHistoryInterface::HISTORY_ID, $historyId);
    }

    /**
     * @inheritdoc
     */
    public function getReferenceNumber()
    {
        return $this->getData(IntegrationOrderHistoryInterface::REFERENCE_NUMBER);
    }

    /**
     * @inheritdoc
     */
    public function setReferenceNumber($refNumber)
    {
        return $this->setData(IntegrationOrderHistoryInterface::REFERENCE_NUMBER, $refNumber);
    }

    /**
     * @inheritdoc
     */
    public function getOrderId()
    {
        return $this->getData(IntegrationOrderHistoryInterface::ORDER_ID);
    }

    /**
     * @inheritdoc
     */
    public function setOrderId($orderId)
    {
        return $this->setData(IntegrationOrderHistoryInterface::ORDER_ID, $orderId);
    }

    /**
     * @inheritdoc
     */
    public function getAwbNumber()
    {
        return $this->getData(IntegrationOrderHistoryInterface::AWB_NUMBER);
    }

    /**
     * @inheritdoc
     */
    public function setAwbNumber($awbNumber)
    {
        return $this->setData(IntegrationOrderHistoryInterface::AWB_NUMBER, $awbNumber);
    }

    /**
     * @inheritdoc
     */
    public function getLogCourierNo()
    {
        return $this->getData(IntegrationOrderHistoryInterface::LOGISTIC_COURIER);
    }

    /**
     * @inheritdoc
     */
    public function setLogCourierNo($logCourierNo)
    {
        return $this->setData(IntegrationOrderHistoryInterface::LOGISTIC_COURIER, $logCourierNo);
    }

    /**
     * @inheritdoc
     */
    public function getFeStatusNo()
    {
        return $this->getData(IntegrationOrderHistoryInterface::FE_STATUS_NO);
    }

    /**
     * @inheritdoc
     */
    public function setFeStatusNo($feStatusNo)
    {
        return $this->setData(IntegrationOrderHistoryInterface::FE_STATUS_NO, $feStatusNo);
    }

    /**
     * @inheritdoc
     */
    public function getFeSubStatusNo()
    {
        return $this->getData(IntegrationOrderHistoryInterface::FE_SUB_STATUS_NO);
    }

    /**
     * @inheritdoc
     */
    public function setFeSubStatusNo($feSubStatusNo)
    {
        return $this->setData(IntegrationOrderHistoryInterface::FE_SUB_STATUS_NO, $feSubStatusNo);
    }

    /**
     * @inheritdoc
     */
    public function getUpdatedAt()
    {
        return $this->getData(IntegrationOrderHistoryInterface::UPDATED_AT);
    }

    /**
     * @inheritdoc
     */
    public function setUpdatedAt($updatedAt)
    {
        return $this->setData(IntegrationOrderHistoryInterface::UPDATED_AT, $updatedAt);
    }
}
