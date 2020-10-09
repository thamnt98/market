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

namespace Trans\MasterPayment\Logger;

use Monolog\Logger;

class Handler extends \Magento\Framework\Logger\Handler\Base {
	/**
	 * Logging level
	 * @var int
	 */
	protected $loggerType = Logger::INFO;

	/**
	 * File name
	 * @var string
	 */
	protected $fileName = '/var/log/masterpayment.log';
}
