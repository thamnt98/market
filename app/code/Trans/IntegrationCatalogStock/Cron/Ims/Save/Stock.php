<?php
/**
 * @category Trans
 * @package  Trans_CatalogStock
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   Dwi Septha Kurniawan <septha.kurniawan@transdigital.co.id>
 *
 * Copyright © 2019 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\IntegrationCatalogStock\Cron\Ims\Save;

use Trans\IntegrationCatalogStock\Api\IntegrationCheckUpdatesInterface;
use Trans\IntegrationCatalogStock\Api\IntegrationStockInterface;
use Trans\Integration\Api\IntegrationCommonInterface;

class Stock {
	/**
	 * @var \Trans\Integration\Logger\Logger
	 */
	protected $logger;
    /**
     * @var IntegrationCheckUpdatesInterface
     */
    protected $checkUpdates;

	public function __construct(
		\Trans\IntegrationCatalogStock\Logger\Logger $logger,
		IntegrationCommonInterface $commonRepository,
		IntegrationStockInterface $integrationStockInterface,
        IntegrationCheckUpdatesInterface $checkUpdates
	) {
		$this->logger                    = $logger;
		$this->commonRepository          = $commonRepository;
		$this->integrationStockInterface = $integrationStockInterface;
        $this->checkUpdates              = $checkUpdates;
	}

	/**
	 * Write to integrations.log
	 *
	 * @return void
	 */
	public function execute() {

        $startTime = microtime(true);

        $label = "stock-save";
        $label .= " --> ";

        $this->logger->info($label . "start");

		try {

            $this->logger->info($label . "retrieving channel-integration-metadata");
            $channelIntegrationMetadata = $this->commonRepository->prepareChannelUsingRawQuery("product-stock-update");
            $this->logger->info($label . "channel-integration-metadata = " . print_r($channelIntegrationMetadata, true));

			$this->logger->info($label . "retrieving on-progress-data-saving-job");
			$channelIntegrationMetadata = $this->checkUpdates->checkOnProgressDataSavingJobUsingRawQuery($channelIntegrationMetadata);
			$this->logger->info($label . "channel-integration-metadata = " . print_r($channelIntegrationMetadata, true));

			$this->logger->info($label . "retrieving first-data-ready-job");
			$channelIntegrationMetadata = $this->checkUpdates->getFirstDataReadyJobUsingRawQuery($channelIntegrationMetadata);
			$this->logger->info($label . "channel-integration-metadata = " . print_r($channelIntegrationMetadata, true));
		
			$this->logger->info($label . "retrieving stock-data from stock-temporary-table db");
			$stockData = $this->integrationStockInterface->prepareStockDataUsingRawQuery($channelIntegrationMetadata);
			$this->logger->info($label . "stock-data = " . print_r($stockData, true));

			$this->logger->info($label . "persisting stock-data into stock-magento-table db");
			$persistingResult = $this->integrationStockInterface->insertStockDataUsingRawQuery($stockData);
			$this->logger->info($label . "persisting-result = " . print_r($persistingResult, true));
			$this->logger->info($label . "complete");

		}
        catch (\Exception $ex) {
            $this->logger->info($label . "exception = " . strtolower($ex->getMessage()));
        }

		$this->logger->info($label . "finish " . (microtime(true) - $startTime) . " second");

	}
	
	// public function execute() {
    //     $class = str_replace(IntegrationCheckUpdatesInterface::CRON_DIRECTORY,"",get_class($this));
	// 	try {

	// 		$this->logger->info("=>".$class." Get Channel Data");
	// 		$channel = $this->commonRepository->prepareChannel('product-stock-update');

	// 		$this->logger->info("=".$class." Check Onprogress Jobs (Save Product)");
	// 		$channel = $this->checkUpdates->checkSaveOnProgressJob($channel);

	// 		$this->logger->info("=".$class."  Check Complete Jobs");
	// 		$channel = $this->checkUpdates->checkReadyJobs($channel);

	// 		$this->logger->info("=".$class." Prepare Data");
	// 		$data = $this->integrationStockInterface->prepareData($channel);

	// 		$this->logger->info("=".$class." Save Data");
	// 		$stock = $this->integrationStockInterface->saveStock($data);			

	// 	} catch (\Exception $ex) {

	// 		$this->logger->error("<=End ".$class." ".$ex->getMessage());
	// 	}
	// 	$this->logger->info("<=End ".$class);
	// }

}
