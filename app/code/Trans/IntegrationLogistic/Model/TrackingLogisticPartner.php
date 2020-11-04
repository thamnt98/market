<?php
; /**
 * @category Trans
 * @package  Trans_IntegrationLogistic
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 * @author   Muhammad Randy <muhammad.randy@transdigital.co.id>
 *
 * Copyright © 2020 CT Corp Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\IntegrationLogistic\Model;

use Trans\IntegrationLogistic\Api\TrackingLogisticPartnerInterface;

class TrackingLogisticPartner implements TrackingLogisticPartnerInterface
{
    /**
     * @var \Magento\Framework\HTTP\Client\Curl
     */
    private $magentoCurl;

    /**
     * @var \Trans\IntegrationLogistic\Helper\Data
     */
    private $integrationLogisticHelper;

    /**
     * @var \Trans\IntegrationLogistic\Logger\Logger
     */
    private $logger;

    /**
     * @var \Trans\Integration\Helper\Curl
     */
    private $curlHelper;

    /**
     * @var \Trans\Core\Helper\Data
     */
    private $coreHelper;

    /**
     * @var \Trans\IntegrationLogistic\Api\Data\TrackingLogisticInterfaceFactory
     */
    private $trackingInterfaceFactory;

    /**
     * @var \Trans\IntegrationLogistic\Api\TrackingLogisticRepositoryInterface
     */
    private $trackingLogisticRepo;

    /**
     * TrackingLogisticPartner constructor.
     * @param \Magento\Framework\HTTP\Client\Curl $magentoCurl
     * @param \Trans\IntegrationLogistic\Helper\Data $integrationLogisticHelper
     * @param \Trans\IntegrationLogistic\Logger\Logger $logger
     * @param \Trans\Integration\Helper\Curl $curlHelper
     * @param \Trans\Core\Helper\Data $coreHelper
     * @param \Trans\IntegrationLogistic\Api\Data\TrackingLogisticInterfaceFactory $trackingInterfaceFactory
     * @param \Trans\IntegrationLogistic\Api\TrackingLogisticRepositoryInterface $trackingLogisticRepo
     */
    public function __construct(
        \Magento\Framework\Serialize\Serializer\Json $jsonRenderer,
        \Trans\IntegrationLogistic\Helper\Data $integrationLogisticHelper,
        \Trans\IntegrationLogistic\Logger\Logger $logger,
        \Trans\IntegrationLogistic\Api\Data\TrackingLogisticInterfaceFactory $trackingInterfaceFactory,
        \Trans\IntegrationLogistic\Api\TrackingLogisticRepositoryInterface $trackingLogisticRepo
    ) {
        $this->jsonRenderer              = $jsonRenderer;
        $this->integrationLogisticHelper = $integrationLogisticHelper;
        $this->logger                    = $logger;
        $this->trackingInterfaceFactory  = $trackingInterfaceFactory;
        $this->trackingLogisticRepo      = $trackingLogisticRepo;
    }

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
    public function getTracking($id, $courierInfo, $orderNumber, $awb, $statusTpl, $statusCourier, $serviceName, $timestampDate, $note, $url, $driverInformation)
    {
        try {
            $courierInfo = array(
                'courier_id' => $courierInfo['courier_id'],
                'courier_name' => $courierInfo['courier_name'],
            );

            $statusTpl = array(
                'status_id' => $statusTpl['status_id'],
                'status_name' => $statusTpl['status_name'],
            );

            $statusCourier = array(
                'status_courier_id' => $statusCourier['status_courier_id'],
                'status_courier_name' => $statusCourier['status_courier_name'],
            );

            $driverInformation = array(
                'driver_name' => $driverInformation['driver_name'],
                'driver_phone' => $driverInformation['driver_phone'],
                'driver_plate' => $driverInformation['driver_plate'],
            );

            $request[] = array(
                'id' => $id,
                'courier_info' => $courierInfo,
                'order_number' => $orderNumber,
                'awb' => $awb,
                'status_tpl' => $statusTpl,
                'status_courier' => $statusCourier,
                'service_name' => $serviceName,
                'timestamp_date' => $timestampDate,
                'note' => $note,
                'url' => $url,
                'driver_information' => $driverInformation,
            );

            $saveResponse = $this->trackingInterfaceFactory->create();
            $saveResponse->setCourierId($courierInfo['courier_id']);
            $saveResponse->setCourierName($courierInfo['courier_name']);
            $saveResponse->setAwbNumber($awb);
            $saveResponse->setOrderNumber($orderNumber);
            $saveResponse->setTplStatusId($statusTpl['status_id']);
            $saveResponse->setTplStatusName($statusTpl['status_name']);
            $saveResponse->setCourierStatusId($statusCourier['status_courier_id']);
            $saveResponse->setCourierStatusName($statusCourier['status_courier_name']);
            $saveResponse->setDriverName($driverInformation['driver_name']);
            $saveResponse->setDriverPhone($driverInformation['driver_phone']);
            $saveResponse->setDriverPlate($driverInformation['driver_plate']);
            $saveResponse->setServiceName($serviceName);
            $saveResponse->setUrlTracking($url);
            $saveResponse->setTrackingNotes($note);
            $saveResponse->setTimestamp($timestampDate);

            $saveDataResponse = $this->trackingLogisticRepo->save($saveResponse);

            $result = ["message" => "code : 200"];
        } catch (\Exception $e) {
            $this->logger->info($e->getMessage());
            $result            = array();
            $result['message'] = "Internal Server Error";
            $result['code']    = 500;
        }

        return $this->jsonRenderer->serialize($result);
    }
}
