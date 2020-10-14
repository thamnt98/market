<?php
/**
 * @category Trans
 * @package  Trans_IntegrationCatalog
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   Muhammad Rifai <muhammad.rifai@ctcorpdigital.com>
 *
 * Copyright © 2019 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\IntegrationCatalog\Cron\Pim\Save;

use Trans\Integration\Api\IntegrationCommonInterface;
use Trans\IntegrationCatalog\Api\IntegrationProductLogicInterface;
use Trans\Integration\Logger\Logger;
use Trans\IntegrationCatalog\Api\IntegrationCheckUpdatesInterface;

class ProductConfigurable {
	/* @var \Trans\Integration\Logger\Logger
	 */
	protected $logger;

    /**
     * @var IntegrationCommonInterface
     */
    protected $commonRepository;

    /**
     * @var IntegrationProductLogicInterface
     */
    protected $integrationProductLogicInterface;

	public function __construct(
		Logger $logger
		,IntegrationCommonInterface $commonRepository
		,IntegrationProductLogicInterface $integrationProductLogicInterface

	) {
		$this->logger                           = $logger;
		$this->commonRepository                 = $commonRepository;		
		$this->integrationProductLogicInterface = $integrationProductLogicInterface;
	}

	/**
	 * Write to system.log
	 *
	 * @return void
	 */
	public function execute() {

        $class = str_replace(IntegrationCheckUpdatesInterface::CRON_DIRECTORY,"",get_class($this));
		try {
			
			$this->logger->info("=".$class." Prepare Data");
			$data = $this->integrationProductLogicInterface->prepareDataConfigurable();
		
			
			$this->logger->info("=".$class." Save Data");
		 	$response = $this->integrationProductLogicInterface->saveDataConfigurable($data);

		} catch (\Exception $ex) {

			$this->logger->error("<=".$class." ".$ex->getMessage());
		}
		$this->logger->info("<=".$class );
	}
}