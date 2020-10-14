<?php

/**
 * @category Trans
 * @package  Trans_Product
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   Muhammad Rifai <muhammad.rifai@ctcorpdigital.com>
 *
 * Copyright © 2019 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\Integration\Helper;

use Magento\Framework\App\Helper\Context;

class Validation extends \Magento\Framework\App\Helper\AbstractHelper {
	/**
	 * @var  Context
	 */
	protected $context;

	/**
	 * @var \Magento\Framework\Stdlib\DateTime\DateTimeFactory
	 */
	protected $dateTimeFactory;

	/**
	 * @param
	 */
	public function __construct(
		Context $context
	) {
		parent::__construct($context);
	}

	public function validateArray($key, $data) {
		$result = NULL;
		if (array_key_exists($key, $data)) {
			$result = $data[$key];
		}

		return $result;
	}

	public function validateArrayReturnZero($key, $data) {
		$result = 0;
		if (array_key_exists($key, $data)) {
			$result = $data[$key];
		}

		return $result;
	}

}