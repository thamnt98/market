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

namespace Trans\Sprint\Block\Frontend\Onepage;

use Magento\Checkout\Block\Onepage\Success as MageSuccessPage;
use Magento\Framework\View\Element\Template;
use Magento\Sales\Model\Order;
use Psr\Log\LoggerInterface;
use Trans\Sprint\Helper\Config as SprintHelper;

/**
 * Class Success
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Success extends MageSuccessPage {
	/**
	 * @var Magento\Sales\Model\Order
	 */
	protected $order;

	/**
	 * @var Psr\Log\LoggerInterface
	 */
	protected $logger;

	/**
	 * @var Trans\Sprint\Model\SprintResponseRepositoryInterface
	 */
	protected $sprintResponseRepository;

	/**
	 * @var \Trans\Sprint\Helper\Config
	 */
	protected $config;

	/**
	 * @var \Magento\Framework\Pricing\Helper\Data
	 */
	protected $priceData;

	/**
	 * @var SprintHelper
	 */
	protected $paymentLogo;

	/**
	 * @param \Magento\Framework\View\Element\Template\Context $context
	 * @param \Magento\Checkout\Model\Session $checkoutSession
	 * @param \Magento\Sales\Model\Order\Config $orderConfig
	 * @param \Magento\Framework\App\Http\Context $httpContext
	 * @param \Magento\Framework\Pricing\Helper\Data $priceData
	 * @param SprintHelper $paymentLogo
	 * @param Order $order
	 * @param Trans\Sprint\Model\SprintResponseRepositoryInterface $sprintResponseRepository
	 * @param array $data
	 * @SuppressWarnings(PHPMD.ExcessiveParameterList)
	 */
	public function __construct(
		\Magento\Framework\View\Element\Template\Context $context,
		\Magento\Checkout\Model\Session $checkoutSession,
		\Magento\Sales\Model\Order\Config $orderConfig,
		\Magento\Framework\App\Http\Context $httpContext,
		\Magento\Framework\Pricing\Helper\Data $priceData,
		Order $order,
		\Trans\Sprint\Api\SprintResponseRepositoryInterface $sprintResponseRepository,
		\Trans\Sprint\Api\SprintPaymentFlagRepositoryInterface $sprintPaymentFlagRepository,
		\Trans\Sprint\Helper\Config $config,
		SprintHelper $paymentLogo,
		\Trans\Sprint\Helper\Data $sprintHelperData,
		array $data = []
	) {
		$this->logger                      = $context->getLogger();
		$this->order                       = $order;
		$this->config                      = $config;
		$this->sprintResponseRepository    = $sprintResponseRepository;
		$this->paymentLogo                 = $paymentLogo;
		$this->priceData                   = $priceData;
		$this->sprintPaymentFlagRepository = $sprintPaymentFlagRepository;
		$this->sprintHelperData            = $sprintHelperData;

		parent::__construct($context, $checkoutSession, $orderConfig, $httpContext, $data);
	}

	/**
	 * Get order
	 *
	 * @return \Magento\Sales\Model\Order
	 */
	protected function getOrder() {
		return $this->_checkoutSession->getLastRealOrder();
	}

	/**
	 * get doku orders
	 *
	 * @return Trans\Sprint\Model\SprintResponseRepositoryInterface
	 */
	protected function getSprintOrder() {
		$order = $this->getOrder();
		return $this->sprintResponseRepository->getByTransactionNo($order->getIncrementId());
	}

	/**
	 * Get payment code
	 *
	 * @return string
	 */
	public function getPaycode() {
		try {
			$sprintOrder = $this->getSprintOrder();

			return $sprintOrder->getCustomerAccount();
		} catch (\Exception $e) {
			$this->logger->info('error : ' . $e->getMessage());
		}
	}

	/**
	 * Check doku payment channel
	 *
	 * @return bool
	 */
	public function checkPaymentChannel() {
		$sprintOrder = $this->getSprintOrder();
		return $this->config->getPaymentChannel($sprintOrder->getPaymentMethod());
	}

	/**
	 * Get how to pay block
	 *
	 * @return html
	 */
	public function getHowtopay() {
		$this->logger->info('===== checkPaymentChannel ===== Start');
		$sprintOrder = $this->getSprintOrder();

		if ($sprintOrder->getId()) {
			$blockId = $this->config->getHowtopay($sprintOrder->getPaymentMethod());
			$this->logger->info('data : ' . $blockId);
			return $this->getLayout()->createBlock('Magento\Cms\Block\Block')->setBlockId($blockId)->toHtml();
		}
		$this->logger->info('===== checkPaymentChannel ===== End');
	}

	/**
	 * Get payment Channel name
	 *
	 * @return NULL|string
	 */
	public function getPaymentChannelName() {
		$sprintOrder = $this->getSprintOrder();

		if ($sprintOrder->getId()) {
			return $this->config->getPaymentChannelName($sprintOrder->getPaymentMethod());
		}
		return '';
	}

	/**
	 * Get payment Channel name
	 *
	 * @return NULL|string
	 */
	public function getExpireDate() {
		$sprintOrder = $this->getSprintOrder();

		if ($sprintOrder->getId()) {
			return $sprintOrder->getExpireDate();
		}
		return '';
	}

	/**
	 * Get grand total
	 *
	 * @return string
	 */
	public function getGrandTotal() {
		$total = $this->getOrder()->getGrandTotal();

		return $this->priceData->currency($total, true, false);
	}

	/**
	 * Get customer name
	 *
	 * @return string
	 */
	public function getCustomerName() {
		return $this->getOrder()->getCustomerName();
	}

	/**
	 * Get Sprint Data for CC
	 *
	 * @return Trans\Sprint\Api\SprintPaymentFlagInterface
	 */
	public function getSprintOrderCc() {
		$order = $this->getOrder();
		return $this->sprintPaymentFlagRepository->getByTransactionNo($order->getIncrementId());
	}

	/**
	 * Get Confirmed Date
	 *
	 * @return datetime | string
	 */
	public function getConfirmedDate() {
		return $this->sprintHelperData->convertDatetime($this->getSprintOrderCc()->getCreatedAt());
	}

	/**
	 * Get Order Number
	 *
	 * @return string
	 */
	public function getTransactionNo() {
		return $this->getSprintOrder()->getTransactionNo();
	}

	/**
	 * Get Magento Order Id
	 *
	 * @return string
	 */
	public function getIncrementId() {
		$order = $this->getOrder();
		return $order->getIncrementId();
	}
}
