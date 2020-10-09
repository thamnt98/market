<?php
/**
 * @category Trans
 * @package  Trans_MasterPayment
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   Muhammad Rifai <muhammad.rifai@ctcorpdigital.com>
 *
 * Copyright © 2019 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\MasterPayment\Helper;

use Magento\Framework\App\Helper\Context;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Trans\MasterPayment\Logger\Logger;

/**
 * Class Data
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper {

	/**
	 * @var DateTime
	 */
	protected $datetime;

	/**
	 * @var TimezoneInterface
	 */
	protected $timezone;

	/**
	 * @var Json
	 */
	protected $json;

	/**
	 * @var Logger
	 */
	protected $logger;

	/**
	 * @param Context           $context
	 * @param DateTime          $datetime
	 * @param TimezoneInterface $timezone
	 * @param Json              $json
	 * @param Logger            $logger
	 */
	public function __construct(
		Context $context,
		DateTime $datetime,
		TimezoneInterface $timezone,
		Json $json,
		Logger $logger
	) {
		parent::__construct($context);

		$this->datetime = $datetime;
		$this->timezone = $timezone;
		$this->json     = $json;
		$this->logger   = $logger;
	}

	/**
	 * convert datetime format
	 *
	 * @param $datetime string
	 * @return datetiime
	 */
	public function convertDatetime($datetime) {
		if ($datetime) {
			//Convert to store timezone
			$created = $this->timezone->date(new \DateTime($datetime));

			$dateAsString = $created->format('Y-m-d H:i:s');

			return $dateAsString;
		}
	}

	/**
	 * Get today timezone
	 *
	 * @return datetime
	 */
	public function getTodayTimezone() {
		return $this->timezone->date()->format('d-m-Y H:i:s');
	}

	/**
	 * Create json serialize
	 *
	 * @param array $data
	 * @return string | null
	 */
	public function serializeJson($data = null) {
		if (!empty($data)) {
			$data = $this->json->serialize($data);
		}

		return $data;
	}

	/**
	 * decode json serialize
	 *
	 * @param string $data
	 * @return array
	 */
	public function unserializeJson($data = null) {
		if (!empty($data)) {
			$data = $this->json->unserialize($data);
		}

		return $data;
	}

	/**
	 * get logger
	 *
	 * @return Trans\MasterPayment\Logger\Logger
	 */
	public function getLogger() {
		return $this->logger;
	}
}