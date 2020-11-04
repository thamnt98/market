<?php
/**
 * @category Trans
 * @package  Trans_IntegrationLogistc
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   Muhammad Randy <muhammad.randy@ctcorpdigital.com>
 *
 * Copyright © 2020 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\IntegrationLogistic\Api\Data;

/**
 * @api
 */
interface TrackingLogisticInterface
{
    /**
     * Constants for keys of data array. Identical to the name of the getter in snake case
     */

    /**
     * Constant for table name
     */
    const TABLE_NAME = 'integration_tpl_tracking';

    /**
     * Constant for field table
     */
    const TRACKING_ID         = 'tracking_id';
    const COURIER_ID          = 'courier_id';
    const COURIER_NAME        = 'courier_name';
    const ORDER_NUMBER        = 'order_number';
    const AWB_NUMBER          = 'awb_number';
    const TPL_STATUS_ID       = 'tpl_status_id';
    const TPL_STATUS_NAME     = 'tpl_status_name';
    const STATUS_COURIER_ID   = 'status_courier_id';
    const STATUS_COURIER_NAME = 'status_courier_name';
    const DRIVER_NAME         = 'driver_name';
    const DRIVER_PHONE        = 'driver_phone';
    const DRIVER_PLATE        = 'driver_plate';
    const URL_TRACKING        = 'url_tracking';
    const TRACKING_NOTES      = 'tracking_notes';
    const SERVICE_NAME        = 'service_name';
    const TIMESTAMP_DATE      = 'timestamp_date';

    /**
     * Get Tracking id
     *
     * @return int
     */
    public function getTrackingId();

    /**
     * Set Tracking id
     *
     * @param int $trackingId
     * @return mixed
     */
    public function setTrackingId($trackingId);

    /**
     * Get Courier Id
     *
     * @return int
     */
    public function getCourierId();

    /**
     * Set Courier Id
     *
     * @param int $courierId
     * @return mixed
     */
    public function setCourierId($courierId);

    /**
     * Get Courier Name
     *
     * @return string
     */
    public function getCourierName();

    /**
     * Set Courier Name
     *
     * @param string $courierName
     * @return mixed
     */
    public function setCourierName($courierName);

    /**
     * Get Order Number
     *
     * @return string
     */
    public function getOrderNumber();

    /**
     * Set Order Number
     *
     * @param string $orderNumber
     * @return mixed
     */
    public function setOrderNumber($orderNumber);

    /**
     * Get AWB Number
     *
     * @return string
     */
    public function getAwbNumber();

    /**
     * Set AWB Number
     *
     * @param string $awbNumber
     * @return mixed
     */
    public function setAwbNumber($awbNumber);

    /**
     * Get TPL Status Id
     *
     * @return int
     */
    public function getTplStatusId();

    /**
     * Set TPL Status Id
     *
     * @param int $tplStatusId
     * @return mixed
     */
    public function setTplStatusId($tplStatusId);

    /**
     * Get TPL Status Name
     *
     * @return string
     */
    public function getTplStatusName();

    /**
     * Set TPL Status Name
     *
     * @param string $tplStatusName
     * @return mixed
     */
    public function setTplStatusName($tplStatusName);

    /**
     * Get Courier Status Id
     *
     * @return int
     */
    public function getStatusCourierId();

    /**
     * Set Courier Status Id
     *
     * @param int $courierStatusId
     * @return mixed
     */
    public function setStatusCourierId($courierStatusId);

    /**
     * Get Courier Status Name
     *
     * @return string
     */
    public function getStatusCourierName();

    /**
     * Set Courier Status Name
     *
     * @param string $courierStatusName
     * @return mixed
     */
    public function setStatusCourierName($courierStatusName);

    /**
     * Get Driver Name
     *
     * @return string
     */
    public function getDriverName();

    /**
     * Set Driver Name
     *
     * @param string $driverName
     * @return mixed
     */
    public function setDriverName($driverName);

    /**
     * Get Driver Phone
     *
     * @return string
     */
    public function getDriverPhone();

    /**
     * Set Driver Phone
     *
     * @param string $driverPhone
     * @return mixed
     */
    public function setDriverPhone($driverPhone);

    /**
     * Get Driver Plate
     *
     * @return string
     */
    public function getDriverPlate();

    /**
     * Set Driver Plate
     *
     * @param string $driverPlate
     * @return mixed
     */
    public function setDriverPlate($driverPlate);

    /**
     * Get Url Tracking
     *
     * @return string
     */
    public function getUrlTracking();

    /**
     * Set Url Tracking
     *
     * @param string $urlTracking
     * @return mixed
     */
    public function setUrlTracking($urlTracking);

    /**
     * Get Tracking Notes
     *
     * @return string
     */
    public function getTrackingNotes();

    /**
     * Set Tracking Notes
     *
     * @param string $trackingNotes
     * @return mixed
     */
    public function setTrackingNotes($trackingNotes);

    /**
     * Get Service Name
     *
     * @return string
     */
    public function getServiceName();

    /**
     * Set Service Name
     *
     * @param string $serviceName
     * @return mixed
     */
    public function setServiceName($serviceName);

    /**
     * Get Time Stamp
     *
     * @return string
     */
    public function getTimestamp();

    /**
     * Set Time Stamp
     *
     * @param string $timeStamp
     * @return mixed
     */
    public function setTimestamp($timeStamp);
}
