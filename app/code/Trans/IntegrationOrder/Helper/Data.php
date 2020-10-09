<?php
/**
 * @category Trans
 * @package  Trans_IntegrationOrder
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   Muhammad Randy <muhammad.randy@transdigital.co.id>
 *
 * Copyright © 2019 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\IntegrationOrder\Helper;

/**
 * Class Data
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper {

	/**
	 * @var \Magento\Customer\Model\SessionFactory
	 */
	protected $customerSession;

	/**
	 * @var \Magento\Framework\Stdlib\DateTime\DateTime
	 */
	protected $datetime;

	/**
	 * @var \Magento\Framework\Session\Generic
	 */
	protected $session;

	/**
	 * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
	 */
	protected $timezone;

	/**
	 * @var \Magento\Framework\Message\ManagerInterface
	 */
	protected $messageManager;

	/**
	 * @var \Magento\Framework\App\ResponseFactory
	 */
	protected $responseFactory;

	/**
	 * @var \Magento\Checkout\Model\Session
	 */
	protected $checkoutSession;

	/**
	 * @var \Magento\Sales\Model\ResourceModel\Order\CollectionFactory
	 */
	protected $orderCollectionFactory;

	/**
	 * @var \Magento\Customer\Api\AddressRepositoryInterface
	 */
	protected $customerAddressFactory;

	/**
	 * @var \Magento\Checkout\Model\Cart
	 */
	protected $cart;

	/**
	 * @var \Trans\IntegrationOrder\Logger\Logger
	 */
	protected $logger;

	/**
	 * @var \Magento\Framework\Serialize\Serializer\Json
	 */
	protected $json;

	/**
	 * @var \Magento\Framework\Data\Form\FormKey
	 */
	protected $formKey;

	/**
	 * @var \Magento\Store\Model\StoreManagerInterface
	 */
	protected $storeManager;

	/**
	 * @param \Magento\Framework\App\Helper\Context $context
	 * @param \Magento\Framework\Session\Generic $session
	 * @param \Magento\Framework\Message\ManagerInterface $messageManager
	 * @param \Magento\Framework\App\ResponseFactory $responseFactory
	 * @param \Magento\Framework\Stdlib\DateTime\DateTime $datetime
	 * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone
	 * @param \Magento\Framework\Serialize\Serializer\Json $json
	 * @param \Magento\Framework\Data\Form\FormKey $formKey
	 * @param \Magento\Customer\Model\SessionFactory $customerSession
	 * @param \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory
	 * @param \Magento\Checkout\Model\Session $checkoutSession
	 * @param \Magento\Store\Model\StoreManagerInterface $storeManager
	 * @param \Magento\Customer\Api\AddressRepositoryInterface $customerAddressFactory
	 * @param \Magento\Checkout\Model\Cart $cart
	 * @param \Trans\IntegrationOrder\Logger\Logger $logger
	 */
	public function __construct(
		\Magento\Framework\App\Helper\Context $context,
		\Magento\Framework\Session\Generic $session,
		\Magento\Framework\Serialize\Serializer\Json $json,
		\Magento\Framework\Stdlib\DateTime\DateTime $datetime,
		\Magento\Framework\Message\ManagerInterface $messageManager,
		\Magento\Framework\App\ResponseFactory $responseFactory,
		\Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone,
		\Magento\Framework\Data\Form\FormKey $formKey,
		\Magento\Framework\Api\SearchCriteriaBuilderFactory $searchCriteriaBuilder,
		\Magento\Customer\Model\SessionFactory $customerSession,
		\Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory,
		\Magento\Checkout\Model\Session $checkoutSession,
		\Magento\Store\Model\StoreManagerInterface $storeManager,
		\Magento\Customer\Api\AddressRepositoryInterface $customerAddressFactory,
		\Magento\Directory\Model\Region $regionDirectory,
		\Trans\IntegrationOrder\Logger\Logger $logger,
		\Trans\Sprint\Helper\Data $dataPg
	) {
		parent::__construct($context);

		$this->customerSession        = $customerSession;
		$this->session                = $session;
		$this->messageManager         = $messageManager;
		$this->responseFactory        = $responseFactory;
		$this->orderCollectionFactory = $orderCollectionFactory;
		$this->datetime               = $datetime;
		$this->timezone               = $timezone;
		$this->checkoutSession        = $checkoutSession;
		$this->regionDirectory        = $regionDirectory;
		$this->json                   = $json;
		$this->formKey                = $formKey;
		$this->storeManager           = $storeManager;
		$this->customerAddressFactory = $customerAddressFactory;
		$this->logger                 = $logger;
		$this->dataPg                 = $dataPg;

		$this->pgLog = $this->dataPg->getLogger();
	}

	/**
	 * Get customer data
	 *
	 * @return Magento\Customer\Api\Data\CustomerInterface
	 */
	public function getCustomerData() {
		$session = $this->customerSession->create();
		if ($session->isLoggedIn()) {
			return $session->getCustomerData();
		}

		return false;
	}

	/**
	 * is customer logged in
	 *
	 * @return bool
	 */
	public function isCustomerLoggedIn() {
		$session = $this->customerSession->create();
		if ($session->isLoggedIn()) {
			return true;
		}

		return false;
	}

	/**
	 * Get customer id
	 *
	 * @return int|bool
	 */
	public function getLoggedInCustomerId() {
		if ($this->isCustomerLoggedIn()) {
			$session = $this->customerSession->create();
			return $session->getId();
		}

		return false;
	}

	/**
	 * Get customer address
	 *
	 * @return \Magento\Customer\Api\AddressRepositoryInterface
	 */
	public function customerAddress() {
		return $this->customerAddressFactory;
	}

	/**
	 * Get order id session
	 *
	 * @return string
	 */
	public function getSessionOrderId() {
		$session = $this->customerSession->create();
		$this->logger->info('------------------ run ' . __FUNCTION__);
		$this->logger->info('session getOrderId() = ' . $session->getOrderId());
		$this->logger->info('------------------ end run ' . __FUNCTION__);
		return $session->getOrderId();
	}

	/**
	 * Get Customer Order
	 *
	 * @return bool
	 */
	public function getCustomerOrderId() {
		$orderId = $this->checkoutSession->getLastRealOrder();
		return $orderId;
	}

	/**
	 * Get Quote
	 *
	 * @return bool
	 */
	public function getCustomerQuote() {
		$quote = $this->checkoutSession->getQuote();
		return $quote;
	}

	/**
	 * Get order IDs created during order
	 * @return \Magento\Sales\Model\Order
	 */
	public function getOrderIds() {
		$ids = $this->session->getOrderIds();
		return $ids;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getOrderIncrementId($incrementId) {
		$collection = $this->orderCollectionFactory->create()
			->addFieldToFilter('increment_id', $incrementId);

		return $collection->getFirstItem();
	}

	/**
	 * Change date format
	 *
	 * @param datetime $datetime
	 * @return datetime
	 */
	public function changeDateFormat($datetime, $format = 'd F Y H:i') {
		return $this->datetime->date($format, $datetime);
	}

	/**
	 * get datetime
	 *
	 * @return \Magento\Framework\Stdlib\DateTime\DateTime
	 */
	public function getDatetime() {
		return $this->datetime;
	}

	/**
	 * get timezone
	 *
	 * @return \Magento\Framework\Stdlib\DateTime\TimezoneInterface
	 */
	public function getTimezone() {
		return $this->timezone;
	}

	/**
	 * get formkey
	 *
	 * @return \Magento\Framework\Data\Form\FormKey
	 */
	public function getFormKey() {
		return $this->formKey;
	}

	/**
	 * get logger
	 *
	 * @return Trans\IntegrationOrder\Logger\Logger
	 */
	public function getLogger() {
		return $this->logger;
	}

	/**
	 * get json
	 *
	 * @return \Magento\Framework\Serialize\Serializer\Json
	 */
	public function getJson() {
		return $this->json;
	}

	/**
	 * get response
	 *
	 * @return \Magento\Framework\App\ResponseFactory
	 */
	public function response() {
		return $this->responseFactory;
	}

	/**
	 * get message
	 *
	 * @return \Magento\Framework\Message\ManagerInterface
	 */
	public function message() {
		return $this->messageManager;
	}

	/**
	 * Generate Reference Number for Dummy Data
	 *
	 * @return string
	 */
	public function genRefNumber($length = 4, $chars = '0123456789') {
		if ($length > 0) {
			$len_chars = (strlen($chars) - 1);
			$the_chars = $chars{rand(0, $len_chars)};
			for ($i = 1; $i < $length; $i = strlen($the_chars)) {
				$r = $chars{rand(0, $len_chars)};
				if ($r != $the_chars{$i - 1}) {
					$the_chars .= $r;
				}
			}

			return $the_chars;
		}
	}

	/**
	 * Get Base Url
	 *
	 * @return string
	 */
	public function getBaseUrl() {
		return $this->storeManager->getStore()->getBaseUrl();
	}

	/**
	 * Payment Gateway log
	 *
	 * @return \Trans\Sprint\Logger\Logger
	 */

	public function sprintLog() {
		return $this->pgLog;
	}

}
