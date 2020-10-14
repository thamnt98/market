<?php
/**
 * @category Trans
 * @package  Trans_Integration
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 * @author   Imam Kusuma <imam.kusuma@ctcorpdigital.com>
 *
 * Copyright © 2019 CT Corp Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\Core\Helper;

/**
 * Class Data
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper {
	const RANDOM_LOW_CHAR = 'abcdefghijklmnopqrstuvwxyz';
	/**
	 * @var \Magento\Framework\Stdlib\DateTime\DateTime
	 */
	protected $datetime;

	/**
	 * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
	 */
	protected $timezone;

	/**
	 * @var \Magento\Framework\Serialize\Serializer\Json
	 */
	protected $json;

	/**
	 * @var \Magento\Store\Model\StoreManagerInterface
	 */
	protected $storeManager;

	/**
	 * @param \Magento\Framework\App\Helper\Context $context
	 * @param \Magento\Framework\Stdlib\DateTime\DateTime $datetime
	 * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone
	 * @param \Magento\Framework\Serialize\Serializer\Json $json
	 * @param \Magento\Store\Model\StoreManagerInterface $storeManager
	 */
	public function __construct(
		\Magento\Framework\App\Helper\Context $context,
		\Magento\Framework\Stdlib\DateTime\DateTime $datetime,
		\Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone,
		\Magento\Framework\Serialize\Serializer\Json $json,
		\Magento\Framework\Data\Form\FormKey $formKey,
		\Magento\Store\Model\StoreManagerInterface $storeManager
	) {
		parent::__construct($context);

		$this->datetime     = $datetime;
		$this->timezone     = $timezone;
		$this->json         = $json;
		$this->storeManager = $storeManager;
		$this->urlBuilder   = $context->getUrlBuilder();
	}

	/**
	 * Get Store Manager
	 *
	 * @return \Magento\Store\Model\StoreManagerInterface
	 */
	public function getStoreManager() {
		return $this->storeManager;
	}

	/**
	 * change date format
	 *
	 * @param datetime $datetime
	 * @return datetime
	 */
	public function changeDateFormat($datetime) {
		return $this->datetime->date('d F Y H:i', $datetime);
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
	 * get json
	 *
	 * @return \Magento\Framework\Serialize\Serializer\Json
	 */
	public function getJson() {
		return $this->json;
	}

	/**
	 * Get Date Noew
	 * @param string $format
	 * return dateformat $format
	 */
	public function getDateNow($format = '') {
		if (empty($format)) {
			$format = 'Y-m-d H:i:s';
		}
		$this->getTimezone()->date(new \DateTime())->format($format);
	}

	/**
	 * Generate Random Attr Code
	 * @param int $length
	 * @param string $characters
	 * return string $randomString
	 */
	public function genRandAttrCode($length = 10, $characters = "") {
		if (empty($characters)) {
			$characters = self::RANDOM_LOW_CHAR;
		}

		$charactersLength = strlen($characters);
		$randomString     = '';
		for ($i = 0; $i < $length; $i++) {
			$randomString .= $characters[rand(0, $charactersLength - 1)];
		}
		return $randomString;
	}

	/**
	 * Get Logger
	 *
	 * @return \Trans\Sprint\Logger\Logger
	 */
	public function getUrlBuilder() {
		return $this->urlBuilder;
	}
}
