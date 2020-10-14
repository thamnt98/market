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
namespace Trans\Sprint\Controller\Api;

use Magento\Framework\Pricing\Helper\Data as PricingData;

/**
 * Class InstallmentTerm
 * @SuppressWarnings(PHPMD)
 */
class InstallmentTerm extends \Magento\Framework\App\Action\Action {
	/**
	 * @var \Magento\Framework\Controller\Result\JsonFactory
	 */
	protected $resultJsonFactory;

	/**
	 * @var \Magento\Checkout\Model\Cart
	 */
	protected $cart;

	/**
	 * @var \Trans\Sprint\Helper\Data
	 */
	protected $dataHelper;

	/**
	 * @var PricingData
	 */
	protected $pricingData;

	/**
	 * @var \Trans\Sprint\Helper\Config
	 */
	protected $config;

	/**
	 * @var Logger
	 */
	protected $logger;

	/**
	 * @param \Magento\Framework\App\Action\Context $context
	 * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
	 * @param \Magento\Checkout\Model\Cart $cart
	 * @param PricingData $pricingData
	 * @param \Trans\Sprint\Helper\Data $dataHelper
	 */
	public function __construct(
		\Magento\Framework\App\Action\Context $context,
		\Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
		\Magento\Checkout\Model\Cart $cart,
		PricingData $pricingData,
		\Trans\Sprint\Helper\Data $dataHelper
	) {
		parent::__construct($context);

		$this->dataHelper        = $dataHelper;
		$this->resultJsonFactory = $resultJsonFactory;
		$this->cart              = $cart;
		$this->pricingData       = $pricingData;
		$this->logger            = $this->dataHelper->getLogger();
		$this->config            = $this->dataHelper->getConfigHelper();
	}

	/**
	 * {@inheritdoc}
	 */
	public function execute() {
		$this->logger->info('===== Notify Controller ===== Start');
		$array = array();

		$granTotal = $this->cart->getQuote()->getGrandTotal();

		try {
			$method = $this->getRequest()->getParam('method');

			$channel = $this->config->getPaymentChannel($method);

			if ($channel === $this->config::CREDIT_CARD_CHANNEL) {
				$installmentTerm = $this->config->getInstallmentTerm($method);

				$terms = $this->dataHelper->unserializeJson($installmentTerm);
				if (is_array($terms)) {
					$this->logger->info('$terms : ' . json_encode($terms, JSON_PRETTY_PRINT));
					foreach ($terms as $key => $value) {
						$this->logger->info('$value : ' . json_encode($value, JSON_PRETTY_PRINT));
						if ($value['enable']) {

							$price   = $granTotal / (int) $value['term'];
							$array[] = [
								'label' => $value['term'] . ' Bulan - ' . $this->pricingData->currency($price, true, false),
								'value' => $value['term'],
							];
						}
					}
				}
			}
		} catch (\Exception $e) {
			$this->logger->info('===== Notify Controller ===== Generate code error : ' . $e->getMessage());
			$result = 'STOP';
		}

		$this->logger->info('===== Notify Controller ===== End');

		$result = $this->resultJsonFactory->create();
		return $result->setData($array);
	}
}
