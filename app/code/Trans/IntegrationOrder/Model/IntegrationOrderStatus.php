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

use \Trans\IntegrationOrder\Api\Data\IntegrationOrderStatusInterface;
use \Trans\IntegrationOrder\Model\ResourceModel\IntegrationOrderStatus as ResourceModel;

class IntegrationOrderStatus extends \Magento\Framework\Model\AbstractModel implements
    IntegrationOrderStatusInterface
{
    protected function _construct()
    {
        $this->_init(ResourceModel::class);
    }

    /**
     * @inheritdoc
     */
    public function getStatusId()
    {
        return $this->getData(IntegrationOrderStatusInterface::STATUS_ID);
    }

    /**
     * @inheritdoc
     */
    public function setStatusId($statusId)
    {
        return $this->setData(IntegrationOrderStatusInterface::STATUS_ID, $statusId);
    }

    /**
     * @inheritdoc
     */
    public function getStatusOms()
    {
        return $this->getData(IntegrationOrderStatusInterface::OMS_STATUS_NO);
    }

    /**
     * @inheritdoc
     */
    public function setStatusOms($omsStatusNo)
    {
        return $this->setData(IntegrationOrderStatusInterface::OMS_STATUS_NO, $omsStatusNo);
    }

    /**
     * @inheritdoc
     */
    public function getActionOms()
    {
        return $this->getData(IntegrationOrderStatusInterface::OMS_ACTION_NO);
    }

    /**
     * @inheritdoc
     */
    public function setActionOms($omsActionNo)
    {
        return $this->setData(IntegrationOrderStatusInterface::OMS_ACTION_NO, $omsActionNo);
    }

    /**
     * @inheritdoc
     */
    public function getSubActionOms()
    {
        return $this->getData(IntegrationOrderStatusInterface::OMS_SUB_ACTION_NO);
    }

    /**
     * @inheritdoc
     */
    public function setSubActionOms($omsSubActionNo)
    {
        return $this->setData(IntegrationOrderStatusInterface::OMS_SUB_ACTION_NO, $omsSubActionNo);
    }

    /**
     * @inheritdoc
     */
    public function getFeStatusNo()
    {
        return $this->getData(IntegrationOrderStatusInterface::FE_STATUS_NO);
    }

    /**
     * @inheritdoc
     */
    public function setFeStatusNo($feStatusNo)
    {
        return $this->setData(IntegrationOrderStatusInterface::FE_STATUS_NO, $feStatusNo);
    }

    /**
     * @inheritdoc
     */
    public function getFeStatus()
    {
        return $this->getData(IntegrationOrderStatusInterface::FE_STATUS);
    }

    /**
     * @inheritdoc
     */
    public function setFeStatus($feStatus)
    {
        return $this->setData(IntegrationOrderStatusInterface::FE_STATUS, $feStatus);
    }

    /**
     * @inheritdoc
     */
    public function getFeSubStatusNo()
    {
        return $this->getData(IntegrationOrderStatusInterface::FE_SUB_STATUS_NO);
    }

    /**
     * @inheritdoc
     */
    public function setFeSubStatusNo($feSubStatusNo)
    {
        return $this->setData(IntegrationOrderStatusInterface::FE_SUB_STATUS_NO, $feSubStatusNo);
    }

    /**
     * @inheritdoc
     */
    public function getFeSubStatus()
    {
        return $this->getData(IntegrationOrderStatusInterface::FE_SUB_STATUS);
    }

    /**
     * @inheritdoc
     */
    public function setFeSubStatus($feSubStatus)
    {
        return $this->setData(IntegrationOrderStatusInterface::FE_SUB_STATUS, $feSubStatus);
    }

    /**
     * @inheritdoc
     */
    public function getOmsPaymentStatus()
    {
        return $this->getData(IntegrationOrderStatusInterface::OMS_PAYMENT_STATUS);
    }

    /**
     * @inheritdoc
     */
    public function setOmsPaymentStatus($omsPaymentStatus)
    {
        return $this->setData(IntegrationOrderStatusInterface::OMS_PAYMENT_STATUS, $omsPaymentStatus);
    }

    /**
     * @inheritdoc
     */
    public function getPgStatusNo()
    {
        return $this->getData(IntegrationOrderStatusInterface::PG_STATUS_NO);
    }

    /**
     * @inheritdoc
     */
    public function setPgStatusNo($pgStatusNo)
    {
        return $this->setData(IntegrationOrderStatusInterface::PG_STATUS_NO, $pgStatusNo);
    }
}
