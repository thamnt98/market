<?php 
/**
 * @category Trans
 * @package  Trans_IntegrationCatalog
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   J.P <jaka.pondan@transdigital.co.id>
 *
 * Copyright © 2019 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */
namespace Trans\IntegrationCatalog\Cron\Pim\Check;

use Magento\Framework\App\ResourceConnection;
use Trans\Integration\Logger\Logger;

class ProductRaw 
{
  protected $resource;
  protected $logger;
  public function __construct(
    ResourceConnection $resource,
    Logger $logger
  ) {
    $this->resource = $resource;
    $this->logger = $logger;
  }
}