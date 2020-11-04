<?php
/**
 * @category Trans
 * @package  Trans_IntegrationLogistic
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 * @author   Muhammad Randy <muhammad.randy@ctcorpdigital.co.id>
 *
 * Copyright © 2019 CT Corp Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\IntegrationLogistic\Api;

/**
 * @api
 * Interface TrackingLogisticPartnerInterface
 */
interface TrackingLogisticPartnerInterface
{
    /**
     * Get Data info Tracking from TPL
     *
     * @param int $id
     * @param mixed $courierInfo
     * @param string $orderNumber
     * @param string $awb
     * @param mixed $statusTpl
     * @param mixed $statusCourier
     * @param string $serviceName
     * @param string $timestampDate
     * @param string $note
     * @param string $url
     * @param mixed $driverInformation
     * @return array
     */
    public function getTracking($id, $courierInfo, $orderNumber, $awb, $statusTpl, $statusCourier, $serviceName, $timestampDate, $note, $url, $driverInformation);
}
