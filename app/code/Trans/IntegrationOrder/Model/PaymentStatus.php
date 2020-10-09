<?php
/**
 * @category Trans
 * @package  Trans_IntegrationOrder
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 * @author   Muhammad Randy <muhammad.randy@ctcorpdigital.co.id>
 *
 * Copyright © 2019 CT Corp Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\IntegrationOrder\Model;

use Trans\IntegrationOrder\Api\Data\IntegrationOrderPaymentInterface;
use Trans\IntegrationOrder\Api\IntegrationOrderPaymentRepositoryInterface;
use Trans\IntegrationOrder\Api\PaymentStatusInterface;

/**
 * @api
 * Class PaymentStatus
 */
class PaymentStatus implements PaymentStatusInterface {

	/**
	 * @var \Magento\Framework\HTTP\Client\Curl
	 */
	protected $curl;

	/**
	 * @var \Magento\Sales\Api\OrderRepositoryInterface
	 */
	protected $orderRepoInterface;

	/**
	 * @var \Trans\IntegrationOrder\Helper\Integration
	 */
	protected $integrationHelper;

	/**
	 * @var \Trans\IntegrationOrder\Helper\Data
	 */
	protected $dataHelper;

	/**
	 * @var \Trans\IntegrationOrder\Helper\Config
	 */
	protected $configHelper;

	/**
	 * @var \Trans\IntegrationOrder\Api\Data\IntegrationOrderPaymentInterfaceFactory
	 */
	protected $orderIntPaymentInterfaceFactory;

	/**
	 * @var \Trans\IntegrationOrder\Api\IntegrationOrderPaymentRepositoryInterface
	 */
	protected $orderPaymentRepo;

	/**
	 * @param \Magento\Framework\HTTP\Client\Curl $curl
	 * @param \Magento\Sales\Api\OrderRepositoryInterface $orderRepoInterface
	 * @param \Trans\IntegrationOrder\Helper\Integration $integrationHelper
	 * @param \Trans\IntegrationOrder\Helper\Data $dataHelper
	 * @param \Trans\IntegrationOrder\Helper\Config $configHelper
	 * @param \Trans\IntegrationOrder\Api\Data\IntegrationOrderPaymentInterfaceFactory $orderIntPaymentInterfaceFactory
	 * @param \Trans\IntegrationOrder\Api\IntegrationOrderPaymentRepositoryInterface $orderPaymentRepo
	 */
	public function __construct(
		\Magento\Framework\HTTP\Client\Curl $curl,
		\Magento\Sales\Api\OrderRepositoryInterface $orderRepoInterface,
		\Trans\IntegrationOrder\Helper\Integration $integrationHelper,
		\Trans\IntegrationOrder\Helper\Data $dataHelper,
		\Trans\IntegrationOrder\Helper\Config $configHelper,
		\Trans\IntegrationOrder\Api\Data\IntegrationOrderPaymentInterfaceFactory $orderIntPaymentInterfaceFactory,
		\Trans\IntegrationOrder\Api\IntegrationOrderPaymentRepositoryInterface $orderPaymentRepo
	) {
		$this->curl                            = $curl;
		$this->orderRepoInterface              = $orderRepoInterface;
		$this->integrationHelper               = $integrationHelper;
		$this->dataHelper                      = $dataHelper;
		$this->configHelper                    = $configHelper;
		$this->orderIntPaymentInterfaceFactory = $orderIntPaymentInterfaceFactory;
		$this->orderPaymentRepo                = $orderPaymentRepo;
		$this->logger                          = $dataHelper->getLogger();
	}

	/**
	 * prepare oms header
	 *
	 * @return array
	 */
	protected function getHeader() {
		$token                    = $this->integrationHelper->getToken();
		$headers['dest']          = $this->configHelper->getOmsDest();
		$headers['Content-Type']  = 'application/json';
		$headers['Authorization'] = 'Bearer ' . $token;

		return $headers;
	}

	/**
	 * Send Status Payment to OMS
	 *
	 * @param string $refNumber
	 * @param string $paymentStatus
	 * @return object
	 */
	public function sendStatusPayment($refNumber, $paymentStatus) {
		$url = $this->configHelper->getOmsBaseUrl() . $this->configHelper->getOmsPaymentStatusApi();

		try {
			$headers  = $this->getHeader();
			$data     = $this->prepareUpdatePaymentStatus($refNumber, $paymentStatus);
			$dataJson = json_encode($data);
			$this->logger->info($dataJson);

			$this->curl->setOption(CURLOPT_RETURNTRANSFER, true);
			$this->curl->setOption(CURLOPT_CUSTOMREQUEST, 'PATCH');
			$this->curl->setHeaders($headers);
			$this->curl->post($url, $dataJson);
			$response = $this->curl->getBody();

			$this->logger->info($response);

			$obj = json_decode($response);
			if ($obj->code == 200) {
				return json_decode($response, true);
			}
		} catch (\Exception $e) {
			$this->logger->info($e);
			return json_decode($response, true);
		}
	}

	/**
	 * prepare raw body
	 *
	 * @param string $refNumber
	 * @param int $paymentStatus
	 * @return object
	 */
	public function prepareUpdatePaymentStatus($refNumber, $paymentStatus) {
		$model           = $this->orderPaymentRepo->loadDataByReferenceNumber($refNumber);
		$referenceNumber = $model->getReferenceNumber();
		if ($referenceNumber) {
			$model->setPaymentStatus((int) $paymentStatus);

			$paymentSave = $this->orderPaymentRepo->save($model);
		}
		$paymentStat = array(
			"reference_number" => $refNumber,
			"status" => (int) $paymentStatus,
		);
		return $paymentStat;
	}
}
