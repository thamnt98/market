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
use Trans\IntegrationCatalog\Api\IntegrationCheckUpdatesInterface;
use Trans\IntegrationCatalog\Api\IntegrationDataValueRepositoryInterface;
use Trans\IntegrationCatalog\Api\IntegrationProductLogicInterface;
use Trans\Integration\Logger\Logger;

class ProductDigital {
	/* @var \Trans\Integration\Logger\Logger
	 */
	protected $logger;

    /**
     * @var IntegrationCommonInterface
     */
    protected $commonRepository;

    /**
     * @var IntegrationDataValueRepositoryInterface
     */
    protected $dataValue;

    /**
     * @var IntegrationProductLogicInterface
     */
    protected $integrationProductLogicInterface;

    /**
     * @var IntegrationCheckUpdatesInterface
     */
    protected $checkUpdates;

	public function __construct(
		Logger $logger
		,IntegrationCommonInterface $commonRepository
		,IntegrationDataValueRepositoryInterface $dataValue
		,IntegrationProductLogicInterface $integrationProductLogicInterface
        ,IntegrationCheckUpdatesInterface $checkUpdates
	) {
		$this->logger                           = $logger;
		$this->commonRepository                 = $commonRepository;
		$this->dataValue                        = $dataValue;
		$this->integrationProductLogicInterface = $integrationProductLogicInterface;
        $this->checkUpdates                     = $checkUpdates;

	}

	/**
	 * Write to system.log
	 *
	 * @return void
	 */
	public function execute() {

        $class = str_replace(IntegrationCheckUpdatesInterface::CRON_DIRECTORY,"",get_class($this));
		try {
			$this->logger->info("=>".$class." Get Channel Data");
			$channel = $this->commonRepository->prepareChannel('product-digital');
			
			$this->logger->info("=".$class." Check Onprogress Jobs (Save Product)");
			$channel = $this->checkUpdates->checkSaveOnProgressJob($channel);

			$this->logger->info("=".$class." Check Complete Jobs");
			$channel = $this->checkUpdates->checkReadyJobs($channel);

			$this->logger->info("=".$class." Prepare Data");
			$data = $this->integrationProductLogicInterface->prepareData($channel);
			
			$this->logger->info("=".$class." Save Data");
			$response = $this->integrationProductLogicInterface->saveProduct($data);
			

		} catch (\Exception $ex) {

			$this->logger->error("<=err".$class." ".$ex->getMessage());
		}
		$this->logger->info("<=End".$class );
	}
}