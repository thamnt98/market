<?php
/**
 * @category Trans
 * @package  Trans_Sprint
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 * @author   Imam Kusuma <imam.kusuma@ctcorpdigital.com>
 *
 * Copyright © 2019 CT Corp Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\Sprint\Helper;

use \Magento\Framework\Exception\NoSuchEntityException;
use Trans\Sprint\Helper\Config;
use Trans\Sprint\Helper\Data;

/**
 * Class Gateway
 */
class Gateway extends \Magento\Framework\App\Helper\AbstractHelper
{
	/**
	 * payment query path
	 */
	const PAYMENT_QUERY_PATH = 'PaymentQuery';

	/**
	 * transaction status
	 */
	const TRX_STATUS_SUCCESS = '00';
	const TRX_STATUS_DECLINED = '01';
	const TRX_STATUS_NOTPAYMENT = '03';
	const TRX_STATUS_TECHPROBLEM = '04';

	/**
	 * @var \Trans\Sprint\Helper\Data
	 */
	protected $dataHelper;

	/**
	 * @var Logger
	 */
	protected $logger;

	/**
	 * @param \Trans\DokuPayment\Helper\Data $dataHelper
	 */
	public function __construct(
		Data $dataHelper
	) {
		$this->dataHelper = $dataHelper;
		
		$this->config = $dataHelper->getConfigHelper();
		$this->logger = $dataHelper->getLogger();
		$this->urlBuilder = $dataHelper->getUrlBuilder();
	}

	/**
	 * @param \Magento\Sales\Api\Data\OrderInterface $order
	 *
	 * @return bool
	 */
	public function checkTrxStatus(\Magento\Sales\Api\Data\OrderInterface $order)
	{
		$dataOrder = $order->getData();
		$this->logger->info('Data Order Ref Number = ' . $dataOrder['reference_number']);
		$paymentMethod = $dataOrder['payment_method'];

		$query['serviceCode'] = 1021; //BCA VA
		$query['channelId'] = $dataOrder['channel_id'];
		$queryRequest['transactionNo'] = $dataOrder['reference_number'];
		$queryRequest['transactionDate'] = $dataOrder['insert_date'];
		// $queryRequest['transactionNo'] = 'App-4000012671';
		// $queryRequest['transactionDate'] = '2021-02-25 17:26:19';
		$query['queryRequest'][] = $queryRequest;
		// $this->logger->info('PARAM = ' . json_encode($query));
		
		$hit = $this->dataHelper->doHitApi($query, self::PAYMENT_QUERY_PATH, $paymentMethod);
		
		if(isset($hit['queryResponse'][0]['transactionStatus']) && $hit['queryResponse'][0]['transactionStatus'] === self::TRX_STATUS_SUCCESS) {
			return true;
		}

		return false;
	}
}
