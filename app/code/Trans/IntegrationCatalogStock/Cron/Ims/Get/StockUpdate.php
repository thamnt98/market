<?php
/**
 * @category Trans
 * @package  Trans_CatalogStock
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   J.P <jaka.pondan@transdigital.co.id>
 *
 * Copyright © 2019 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\IntegrationCatalogStock\Cron\Ims\Get;

use Trans\Integration\Api\IntegrationCommonInterface;
use Trans\IntegrationCatalogStock\Api\IntegrationGetUpdatesInterface;
use Trans\IntegrationCatalogStock\Api\IntegrationCheckUpdatesInterface;

class StockUpdate {
	/**
	 * @var \Trans\IntegrationCatalogStock\Logger\Logger
	 */
	protected $logger;

	/**
	 * @var IntegrationCommonInterface
	 */
	protected $commonRepository;

    /**
     * @var IntegrationGetUpdatesInterface
     */
	protected $getUpdates;

    /**
     * @var IntegrationCheckUpdatesInterface
     */
	protected $checkUpdates;
	
	/**
     * @var TimezoneInterface
     */
	protected $timezone;


	public function __construct(
		\Trans\IntegrationCatalogStock\Logger\Logger $logger,
		IntegrationCommonInterface $commonRepository,
		IntegrationGetUpdatesInterface $getUpdates,
        IntegrationCheckUpdatesInterface $checkUpdates,
		\Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone
	) {
		$this->logger           = $logger;
		$this->commonRepository = $commonRepository;
		$this->getUpdates       = $getUpdates;
		$this->checkUpdates     = $checkUpdates;
		$this->timezone			= $timezone;
	}

	/**
	 * Write to system.log
	 *
	 * @return void
	 */
	public function execute() {

		$startTime = microtime(true);
		
        $label = "stock-get";
        $label .= " --> ";

        $this->logger->info($label . "start");

		try {
			
			$this->logger->info($label . "retrieving channel-integration-metadata");
			$channelIntegrationMetadata = $this->commonRepository->prepareChannelUsingRawQuery("product-stock-update");
			$this->logger->info($label . "channel-integration-metadata = " . print_r($channelIntegrationMetadata, true));

			$this->logger->info($label . "retrieving last-complete-job");
			$channelIntegrationMetadata = $this->checkUpdates->getLastCompleteJobUsingRawQuery($channelIntegrationMetadata);
			$this->logger->info($label . "last-complete-job = " . (isset($channelIntegrationMetadata['last_complete_job']) ? print_r($channelIntegrationMetadata['last_complete_job'], true) : "not-found"));

			$this->logger->info($label . "retrieving first-waiting-job");
			$channelIntegrationMetadata = $this->getUpdates->getFirstWaitingJobUsingRawQuery($channelIntegrationMetadata);
			$this->logger->info($label . "first-waiting-job = " . (isset($channelIntegrationMetadata['first_waiting_job']) ? print_r($channelIntegrationMetadata['first_waiting_job'], true) : "not-found"));

			$this->logger->info($label . "preparing to call stock-get-api");
			$callData = $this->getUpdates->prepareCallUsingRawQuery($channelIntegrationMetadata);
			$this->logger->info($label . "call-data = " . print_r($callData, true));

			$this->logger->info($label . "calling stock-get-api");
			$callResponse = $this->commonRepository->doCallUsingRawQuery($callData);
			$this->logger->info($label . "call-response received = " . (!empty($callResponse) ? "yes" : "no"));
			$this->logger->info($label . "call-response data number = " . (isset($callResponse['data']) ? count($callResponse['data']) : 0));

			$this->logger->info($label . "preparing stock-data based on stock-get-api call-response");
			$stockData = $this->getUpdates->prepareStockDataUsingRawQuery($channelIntegrationMetadata, $callResponse);
			$this->logger->info($label . "stock-data prepared number = " . (isset($stockData['data']) ? count($stockData['data']) : 0));

			$this->logger->info($label . "persisting stock-data into temporary-stock-table db");
			$persistingResult = $this->getUpdates->insertStockDataUsingRawQuery($channelIntegrationMetadata, $stockData);
            $this->logger->info($label . "stock-data bulk persisted = " . ($persistingResult > 0 ? "success" : "failed"));
            $this->logger->info($label . "complete");

		} 
        catch (\Exception $ex) {
            $this->logger->info($label . "exception = " . strtolower($ex->getMessage()));
        }

        $this->logger->info($label . "finish " . (microtime(true) - $startTime) . " second");

	}
	
	// public function execute() {
	// 	$lastUpdated = $this->timezone->date(new \DateTime())->format('Y-m-d H:i:s');

	// 	try {
    //         $class = str_replace(IntegrationCheckUpdatesInterface::CRON_DIRECTORY,"",get_class($this));
	// 		$this->logger->info("=>".$class." Get Channel Data");
	// 		$channel = $this->commonRepository->prepareChannel('product-stock-update');

	// 		$this->logger->info("=".$class." Check Complete Jobs");
	// 		$channel = $this->checkUpdates->checkCompleteJobs($channel);

	// 		//if there are no waiting_jobs then break by throwing exception
	// 		$this->logger->info("=".$class." Check Waiting Jobs");
	// 		$channel = $this->getUpdates->checkWaitingJobs($channel);

	// 		$this->logger->info("=".$class." Set Parameter Request Data");
	// 		$data = $this->getUpdates->prepareCall($channel);
	// 		$this->logger->info("=".print_r($data,true));

	// 		$this->logger->info("=".$class." Sending Request Data to API");
	// 		$response = $this->commonRepository->get($data);

	// 		$this->logger->info("=".$class." Set Data Value");
	// 		$dataValue = $this->getUpdates->prepareDataProperResp($channel, $response);

	// 		$this->logger->info("=".$class." Save & Update Jobs & Data Value to databases");
	// 		$result = $this->getUpdates->save($channel, $dataValue, $lastUpdated);

	// 	} catch (\Exception $ex) {

	// 		$this->logger->error("<=End " . $class." ".$ex->getMessage());
	// 	}
	// 	$this->logger->info("<=End " . $class);
	// }

}
