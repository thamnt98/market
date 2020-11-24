<?php
/**
 * @category Trans
 * @package  Trans_IntegrationCatalogStock
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   Hariadi <hariadi_wicaksana@transretail.co.id>
 *
 * Copyright © 2020 PT Trans Retail Indonesia. All rights reserved.
 * http://carrefour.co.id
 */

namespace Trans\IntegrationCatalogStock\Logger;

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
	protected $fileName = '/var/log/integration_catalog_stock.log';
}
