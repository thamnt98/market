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

namespace Trans\Sprint\Plugin\Checkout\Model;

/**
 * Class PaymentInformationManagement
 */
class PaymentInformationManagement {
	/**
	 * @var \Magento\Quote\Model\QuoteRepository
	 */
	protected $quoteRepository;

	/**
	 * @var \Trans\Sprint\Helper\Data
	 */
	protected $dataHelper;

	/**
	 * @var \Trans\Sprint\Helper\Config
	 */
	protected $config;

	/**
	 * ShippingInformationManagement constructor.
	 * @param \Magento\Quote\Model\QuoteRepository $quoteRepository
	 * @param \Trans\Sprint\Helper\Data $dataHelper
	 */
	public function __construct(
		\Magento\Quote\Model\QuoteRepository $quoteRepository,
		\Trans\Sprint\Helper\Data $dataHelper
	) {
		$this->quoteRepository = $quoteRepository;
		$this->dataHelper      = $dataHelper;

		$this->config = $this->dataHelper->getConfigHelper();
		$this->logger = $this->dataHelper->getLogger();
	}
	/**
	 * [beforeSavePaymentInformation description]
	 * @param  \Magento\Checkout\Model\PaymentInformationManagement $subject
	 * @param  int $cartId
	 * @param  \Magento\Quote\Api\Data\PaymentInterface $paymentMethod
	 * @param  \Magento\Quote\Api\Data\AddressInterface $billingAddress
	 * @return void
	 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
	 */
	// phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter
	public function beforeSavePaymentInformation(
		\Magento\Checkout\Model\PaymentInformationManagement $subject,
		$cartId,
		\Magento\Quote\Api\Data\PaymentInterface $paymentMethod,
		\Magento\Quote\Api\Data\AddressInterface $billingAddress = null
	) {
		$extAttributes = $paymentMethod->getExtensionAttributes();
		$this->logger->info('Extra Attributes Term = ' . $extAttributes->getSprintTermChannelid());
		if ($paymentMethod->getMethod() === 'sprint_klikbca') {
			$klikbcaUserid = $extAttributes->getKlikbcaUserid();

			if ($klikbcaUserid) {
				$quote = $this->quoteRepository->getActive($cartId);
				$quote->setKlikbcaUserid($klikbcaUserid);
			}
		}

		if ($this->config->getPaymentChannel($paymentMethod->getMethod()) === $this->config::CREDIT_CARD_CHANNEL && ($this->config->getPaymentChannelIpg($paymentMethod->getMethod()) === 'sprint')) {
			if (method_exists($extAttributes, 'getSprintTermChannelid')) {
				$termChannelid = $extAttributes->getSprintTermChannelid();

				if ($termChannelid) {
					$quote = $this->quoteRepository->getActive($cartId);
					$quote->setSprintTermChannelid($termChannelid);
					
					$serviceFee = $extAttributes->getServiceFee();
					if($serviceFee) {
						$quote->setSprintTermChannelid($serviceFee);
					}
				}
			}
		}
	}
}
