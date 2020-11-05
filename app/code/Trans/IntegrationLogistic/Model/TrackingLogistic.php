<?php
/**
 * @category Trans
 * @package  Trans_IntegrationLogistic
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   Muhammad Randy <muhammad.randy@ctcorpdigital.com>
 *
 * Copyright © 2020 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\IntegrationLogistic\Model;

use \Trans\IntegrationLogistic\Api\Data\TrackingLogisticInterface;
use \Trans\IntegrationLogistic\Model\ResourceModel\TrackingLogistic as ResourceModel;

class TrackingLogistic extends \Magento\Framework\Model\AbstractModel implements
    TrackingLogisticInterface
{
    protected function _construct()
    {
        $this->_init(ResourceModel::class);
    }

    /**
     * @inheritdoc
     */
    public function getTrackingId()
    {
        return $this->getData(TrackingLogisticInterface::TRACKING_ID);
    }

    /**
     * @inheritdoc
     */
    public function setTrackingId($trackingId)
    {
        return $this->setData(TrackingLogisticInterface::TRACKING_ID, $trackingId);
    }

    /**
     * @inheritdoc
     */
    public function getCourierId()
    {
        return $this->getData(TrackingLogisticInterface::COURIER_ID);
    }

    /**
     * @inheritdoc
     */
    public function setCourierId($courierId)
    {

        return $this->setData(TrackingLogisticInterface::COURIER_ID, $courierId);
    }

    /**
     * @inheritdoc
     */
    public function getCourierName()
    {
        return $this->getData(TrackingLogisticInterface::COURIER_NAME);
    }

    /**
     * @inheritdoc
     */
    public function setCourierName($courierName)
    {

        return $this->setData(TrackingLogisticInterface::COURIER_NAME, $courierName);
    }

    /**
     * @inheritdoc
     */
    public function getOrderNumber()
    {
        return $this->getData(TrackingLogisticInterface::ORDER_NUMBER);
    }

    /**
     * @inheritdoc
     */
    public function setOrderNumber($orderNumber)
    {

        return $this->setData(TrackingLogisticInterface::ORDER_NUMBER, $orderNumber);
    }

    /**
     * @inheritdoc
     */
    public function getAwbNumber()
    {
        return $this->getData(TrackingLogisticInterface::AWB_NUMBER);
    }

    /**
     * @inheritdoc
     */
    public function setAwbNumber($awbNumber)
    {

        return $this->setData(TrackingLogisticInterface::AWB_NUMBER, $awbNumber);
    }

    /**
     * @inheritdoc
     */
    public function getTplStatusId()
    {
        return $this->getData(TrackingLogisticInterface::TPL_STATUS_ID);
    }

    /**
     * @inheritdoc
     */
    public function setTplStatusId($tplStatusId)
    {
        return $this->setData(TrackingLogisticInterface::TPL_STATUS_ID, $tplStatusId);
    }

    /**
     * @inheritdoc
     */
    public function getTplStatusName()
    {
        return $this->getData(TrackingLogisticInterface::TPL_STATUS_NAME);
    }

    /**
     * @inheritdoc
     */
    public function setTplStatusName($tplStatusName)
    {
        return $this->setData(TrackingLogisticInterface::TPL_STATUS_NAME, $tplStatusName);
    }

    /**
     * @inheritdoc
     */
    public function getStatusCourierId()
    {
        return $this->getData(TrackingLogisticInterface::STATUS_COURIER_ID);
    }

    /**
     * @inheritdoc
     */
    public function setStatusCourierId($courierStatusId)
    {
        return $this->setData(TrackingLogisticInterface::STATUS_COURIER_ID, $courierStatusId);
    }

    /**
     * @inheritdoc
     */
    public function getStatusCourierName()
    {
        return $this->getData(TrackingLogisticInterface::STATUS_COURIER_NAME);
    }

    /**
     * @inheritdoc
     */
    public function setStatusCourierName($courierStatusName)
    {
        return $this->setData(TrackingLogisticInterface::STATUS_COURIER_NAME, $courierStatusName);
    }

    /**
     * @inheritdoc
     */
    public function getDriverName()
    {
        return $this->getData(TrackingLogisticInterface::DRIVER_NAME);
    }

    /**
     * @inheritdoc
     */
    public function setDriverName($driverName)
    {
        return $this->setData(TrackingLogisticInterface::DRIVER_NAME, $driverName);
    }

    /**
     * @inheritdoc
     */
    public function getDriverPhone()
    {
        return $this->getData(TrackingLogisticInterface::DRIVER_PHONE);
    }

    /**
     * @inheritdoc
     */
    public function setDriverPhone($driverPhone)
    {
        return $this->setData(TrackingLogisticInterface::DRIVER_PHONE, $driverPhone);
    }

    /**
     * @inheritdoc
     */
    public function getDriverPlate()
    {
        return $this->getData(TrackingLogisticInterface::DRIVER_PLATE);
    }

    /**
     * @inheritdoc
     */
    public function setDriverPlate($driverPlate)
    {
        return $this->setData(TrackingLogisticInterface::DRIVER_PLATE, $driverPlate);
    }

    /**
     * @inheritdoc
     */
    public function getUrlTracking()
    {
        return $this->getData(TrackingLogisticInterface::URL_TRACKING);
    }

    /**
     * @inheritdoc
     */
    public function setUrlTracking($urlTracking)
    {
        return $this->setData(TrackingLogisticInterface::URL_TRACKING, $urlTracking);
    }

    /**
     * @inheritdoc
     */
    public function getTrackingNotes()
    {
        return $this->getData(TrackingLogisticInterface::TRACKING_NOTES);
    }

    /**
     * @inheritdoc
     */
    public function setTrackingNotes($trackingNotes)
    {
        return $this->setData(TrackingLogisticInterface::TRACKING_NOTES, $trackingNotes);
    }

    /**
     * @inheritdoc
     */
    public function getServiceName()
    {
        return $this->getData(TrackingLogisticInterface::SERVICE_NAME);
    }

    /**
     * @inheritdoc
     */
    public function setServiceName($serviceName)
    {
        return $this->setData(TrackingLogisticInterface::SERVICE_NAME, $serviceName);
    }

    /**
     * @inheritdoc
     */
    public function getTimestamp()
    {
        return $this->getData(TrackingLogisticInterface::TIMESTAMP_DATE);
    }

    /**
     * @inheritdoc
     */
    public function setTimestamp($timeStamp)
    {
        return $this->setData(TrackingLogisticInterface::TIMESTAMP_DATE, $timeStamp);
    }
}
