<?php
/**
 * @category Trans
 * @package  Trans_Sprint
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   Imam Kusuma <imam.kusuma@transdigital.co.id>
 *
 * Copyright © 2019 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\Sprint\Model\Ui;

use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Framework\Pricing\Helper\Data as PricingData;
use Trans\Sprint\Helper\Config as ConfigHelper;
use Trans\Sprint\Helper\Data;

/**
 * Class SprintConfigProvider
 */
class SprintConfigProvider implements ConfigProviderInterface
{
	/**
	 * @var \Magento\Payment\Model\Config
	 */
	protected $paymentConfig;

	/**
	 * @var \Magento\Checkout\Model\Session
	 */
	protected $checkoutSession;

	/**
	 * @var PricingData
	 */
	protected $pricingData;

	/**
	 * @var Trans\Sprint\Helper\Config
	 */
	protected $configHelper;

	/**
	 * @var LoggerInterface
	 */
	protected $logger;

	/**
	 * @param \Magento\Payment\Model\Config $paymentConfig
	 * @param \Magento\Checkout\Model\Session $checkoutSession
	 * @param PricingData as $pricingData
	 * @param \Trans\Sprint\Helper\Config $configHelper
	 */
	public function __construct(
		\Magento\Payment\Model\Config $paymentConfig,
		\Magento\Checkout\Model\Session $checkoutSession,
		PricingData $pricingData,
		Data $dataHelper
	) {
		$this->pricingData     = $pricingData;
		$this->paymentConfig   = $paymentConfig;
		$this->checkoutSession = $checkoutSession;
		$this->dataHelper      = $dataHelper;

		$this->configHelper = $this->dataHelper->getConfigHelper();
		$this->logger       = $this->dataHelper->getLogger();
	}

	/**
	 * {@inheritdoc}
	 */
	public function getConfig() {
		$config = [
			'payment' => [
				'sprint' => [
					'is_production'    => $this->configHelper->isProduction(),
					'expiry'           => $this->configHelper->getExpiry(),
					'installmentTerms' => $this->getPaymentTerm(),
				],
			],
		];

		return $config;
	}

	/**
	 * Get Payment installment term All Sprint Payment Channel
	 *
	 * @return array
	 * @SuppressWarnings(PHPMD.UnusedLocalVariable)
	 * @SuppressWarnings(PHPMD.NPathComplexity)
	 */
	protected function getPaymentTerm() {
		$quote    = $this->checkoutSession->getQuote();
		$payments = $this->paymentConfig->getActiveMethods();

		$grandTotal = $quote->getGrandTotal();
		$array      = array();

		foreach ($payments as $paymentCode => $paymentModel) {
			if (strpos($paymentCode, 'sprint') !== false) {
				$channel = $this->configHelper->getPaymentChannel($paymentCode);
				
				if ($channel === ConfigHelper::CREDIT_CARD_CHANNEL) {
					$arrayTerm = [];

					$installmentTerm = $this->configHelper->getInstallmentTerm($paymentCode);
					$terms           = $this->dataHelper->unserializeJson($installmentTerm);
					
					if (!empty($terms) && is_array($terms)) {
						foreach ($terms as $key => $value) {
							if ($value['enable']) {
								$price = $grandTotal / (int) $value['term'];
								$serviceFeeValue = 0;
								if(isset($value['serviceFee'])) {
									$serviceFeeValue = $value['serviceFee'];
								}
								
								$serviceFee = ($grandTotal*(int) $serviceFeeValue)/100;

								$term  = [
									'label' => $this->pricingData->currency($price, true, false) . ' x ' . $value['term'],
									'value' => $value['term'],
									'serviceFee' => $this->pricingData->currency($serviceFee, true, false),
									'serviceFeeValue' => $serviceFeeValue,
								];

								array_push($arrayTerm, $term);
							}
						}
						$jsonEncode = json_encode($arrayTerm);
						array_push($array, array($paymentCode => $arrayTerm));
					}
				}
			}
		}
		return $array;
	}
}
