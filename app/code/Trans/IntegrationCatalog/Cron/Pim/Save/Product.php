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

class Product {
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
     * @var \Trans\IntegrationCatalog\Model\IntegrationProductSync
     */
    protected $integrationProductSync;

    /**
     * @var IntegrationCheckUpdatesInterface
     */
    protected $checkUpdates;

	public function __construct(
		Logger $logger,
		IntegrationCommonInterface $commonRepository,
		IntegrationDataValueRepositoryInterface $dataValue,
		\Trans\IntegrationCatalog\Model\IntegrationProductSync $integrationProductSync,
        IntegrationCheckUpdatesInterface $checkUpdates
	) {
		$this->logger = $logger;
		$this->commonRepository = $commonRepository;
		$this->dataValue = $dataValue;
		$this->integrationProductSync = $integrationProductSync;
        $this->checkUpdates = $checkUpdates;


        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/integration_product.log');
        $logger = new \Zend\Log\Logger();
        $this->logger = $logger->addWriter($writer);
	}

	/**
	 * Write to system.log
	 *
	 * @return void
	 */
	public function execute() {
		$class = str_replace(IntegrationCheckUpdatesInterface::CRON_DIRECTORY,"",get_class($this));
		$this->logger->info('===========================================');
		$this->logger->info('Start ' . $class . ' ' . date('H:i:s'));
		try {
			$this->logger->info("=>".$class." Get Channel Data" . ' ' . date('H:i:s'));
			$start_time = microtime(true);
			$channel = $this->commonRepository->prepareChannel('product');
			$end_time = microtime(true);
			$this->logger->info("prepareChannel time : " . ($end_time - $start_time));		
			
			$this->logger->info("=".$class." Check Onprogress Jobs (Save Product)" . ' ' . date('H:i:s'));
			$start_time = microtime(true);
			$channel = $this->checkUpdates->checkSaveOnProgressJob($channel);
			$end_time = microtime(true);
			$this->logger->info("checkSaveOnProgressJob time : " . ($end_time - $start_time));

			$this->logger->info("=".$class." Check Complete Jobs" . ' ' . date('H:i:s'));
			$start_time = microtime(true);
			$channel = $this->checkUpdates->checkReadyJobs($channel);
			$end_time = microtime(true);
			$this->logger->info("checkReadyJobs time : " . ($end_time - $start_time));

			$this->logger->info("=".$class." Prepare Data" . ' ' . date('H:i:s'));
			$start_time = microtime(true);
			$data = $this->integrationProductSync->prepareData($channel);
			$end_time = microtime(true);
			$this->logger->info("prepareData time : " . ($end_time - $start_time));
			
			$this->logger->info("=".$class." Save Data" . ' ' . date('H:i:s'));
			$start_time = microtime(true);
			$response = $this->integrationProductSync->saveProduct($data, $channel['jobs']);
			$end_time = microtime(true);
			$this->logger->info("saveProduct time : " . ($end_time - $start_time));
		} catch (\Exception $ex) {
			$this->logger->info("<=err".$class." ".$ex->getMessage());
			$this->logger->info($ex->getTraceAsString());
		}

		$this->logger->info('End ' . $class . ' ' . date('H:i:s'));
		$this->logger->info('===========================================');
	}
}