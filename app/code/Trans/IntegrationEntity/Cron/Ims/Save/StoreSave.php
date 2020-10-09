<?php
/**
 * @category Trans
 * @package  Trans_IntegrationEntity
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   Dwi Septha Kurniawan <septha.kurniawan@transdigital.co.id>
 *
 * Copyright © 2019 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\IntegrationEntity\Cron\Ims\Save;

use Trans\IntegrationEntity\Api\IntegrationStoreInterface;
use Trans\Integration\Api\IntegrationCommonInterface;
use Trans\IntegrationEntity\Api\IntegrationCheckUpdatesInterface;

class StoreSave {
	/**
	 * @var \Trans\Integration\Logger\Logger
	 */
	protected $logger;

	public function __construct(
		\Trans\Integration\Logger\Logger $logger
		,IntegrationCommonInterface $commonRepository
		,IntegrationStoreInterface $integrationStoreInterface
        ,IntegrationCheckUpdatesInterface $checkUpdates
	) {
		$this->logger                    = $logger;
		$this->commonRepository          = $commonRepository;
		$this->integrationStoreInterface = $integrationStoreInterface;
        $this->checkUpdates=$checkUpdates;
	}

	/**
	 * Write to integrations.log
	 *
	 * @return void
	 */
	public function execute() {
        $class = str_replace(IntegrationCheckUpdatesInterface::CRON_DIRECTORY,"",get_class($this));
		try {

			$this->logger->info("=>".$class." Get Channel Data");
			$channel = $this->commonRepository->prepareChannelMultiTag(["store","store-updates"]);

			$this->logger->info("=".$class." Check Onprogress Jobs (Save Store)");
			$channel = $this->checkUpdates->checkMultiSaveOnProgressJob($channel);

			$this->logger->info("=".$class." Check Complete Jobs");
			$channel = $this->checkUpdates->checkMultiCompleteJobs($channel);

			$this->logger->info("=".$class." Prepare Data");
			$data = $this->integrationStoreInterface->prepareData($channel);

			$this->logger->info("=".$class." Save Data");
			$store = $this->integrationStoreInterface->saveStore($data);
			
		} catch (\Exception $ex) {

			$this->logger->error("<=End ".$class." ".$ex->getMessage());
		}
		$this->logger->info("<=End ".$class);
	}

}
