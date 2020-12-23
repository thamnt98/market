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
	protected $loggerfile;

    /**
     * @var \Trans\Integration\Api\IntegrationLogToDatabaseInterface
     */
    protected $loggerdb;	

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
        \Trans\IntegrationCatalogStock\Logger\Logger $loggerfile,
        \Trans\Integration\Api\IntegrationLogToDatabaseInterface $loggerdb,
		IntegrationCommonInterface $commonRepository,
		IntegrationGetUpdatesInterface $getUpdates,
        IntegrationCheckUpdatesInterface $checkUpdates,
		\Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone
	) {
		$this->loggerfile		= $loggerfile;
		$this->loggerdb 		= $loggerdb;
		$this->commonRepository = $commonRepository;
		$this->getUpdates       = $getUpdates;
		$this->checkUpdates     = $checkUpdates;
		$this->timezone			= $timezone;
	}


	public function execute() {

        $startTime = microtime(true);

        $cronType = "stock";
        $cronTypeDetail = "get";
        $cronLabel = $cronType . "-" . $cronTypeDetail . " --> ";

        $logMessageTopic = "start";
        $logMessage = "start";
        $this->loggerfile->info($cronLabel . $logMessage);
        $this->loggerdb->logCronInfo($cronType, $cronTypeDetail, $logMessageTopic, $logMessage);

		try {
			
			$logMessageTopic = "prepareChannelUsingRawQuery";

			$logMessage = "retrieving channel-integration-metadata";
            $this->loggerfile->info($cronLabel . $logMessage);
            $this->loggerdb->logCronInfo($cronType, $cronTypeDetail, $logMessageTopic, $logMessage);

			$channelIntegrationMetadata = $this->commonRepository->prepareChannelUsingRawQuery("product-stock-update");

            $logMessage = "channel-integration-metadata = " . print_r($channelIntegrationMetadata, true);
            $this->loggerfile->info($cronLabel . $logMessage);
            $this->loggerdb->logCronInfo($cronType, $cronTypeDetail, $logMessageTopic, $logMessage);


			$logMessageTopic = "getLastCompleteJobUsingRawQuery";

            $logMessage = "retrieving last-complete-job";
            $this->loggerfile->info($cronLabel . $logMessage);
            $this->loggerdb->logCronInfo($cronType, $cronTypeDetail, $logMessageTopic, $logMessage);

			$channelIntegrationMetadata = $this->checkUpdates->getLastCompleteJobUsingRawQuery($channelIntegrationMetadata);

            $logMessage = "last-complete-job = " . (isset($channelIntegrationMetadata['last_complete_job']) ? print_r($channelIntegrationMetadata['last_complete_job'], true) : "not-found");
            $this->loggerfile->info($cronLabel . $logMessage);
            $this->loggerdb->logCronInfo($cronType, $cronTypeDetail, $logMessageTopic, $logMessage);


			$logMessageTopic = "getFirstWaitingJobUsingRawQuery";

			$logMessage = "retrieving first-waiting-job";
            $this->loggerfile->info($cronLabel . $logMessage);
            $this->loggerdb->logCronInfo($cronType, $cronTypeDetail, $logMessageTopic, $logMessage);
			
			$channelIntegrationMetadata = $this->getUpdates->getFirstWaitingJobUsingRawQuery($channelIntegrationMetadata);

			$logMessage = "first-waiting-job = " . (isset($channelIntegrationMetadata['first_waiting_job']) ? print_r($channelIntegrationMetadata['first_waiting_job'], true) : "not-found");
            $this->loggerfile->info($cronLabel . $logMessage);
            $this->loggerdb->logCronInfo($cronType, $cronTypeDetail, $logMessageTopic, $logMessage);


			$logMessageTopic = "prepareCallUsingRawQuery";

			$logMessage = "preparing to call stock-get-api";
            $this->loggerfile->info($cronLabel . $logMessage);
            $this->loggerdb->logCronInfo($cronType, $cronTypeDetail, $logMessageTopic, $logMessage);
			
			$callData = $this->getUpdates->prepareCallUsingRawQuery($channelIntegrationMetadata);

			$logMessage = "call-data = " . print_r($callData, true);
            $this->loggerfile->info($cronLabel . $logMessage);
            $this->loggerdb->logCronInfo($cronType, $cronTypeDetail, $logMessageTopic, $logMessage);


			$logMessageTopic = "doCallUsingRawQuery";

			$logMessage = "calling stock-get-api";
            $this->loggerfile->info($cronLabel . $logMessage);
            $this->loggerdb->logCronInfo($cronType, $cronTypeDetail, $logMessageTopic, $logMessage);
			
			$callResponse = $this->commonRepository->doCallUsingRawQuery($callData);

			$logMessage = "call-response received = " . (!empty($callResponse) ? "yes" : "no");
            $this->loggerfile->info($cronLabel . $logMessage);
			$this->loggerdb->logCronInfo($cronType, $cronTypeDetail, $logMessageTopic, $logMessage);
						
			$logMessage = "call-response data number = " . (isset($callResponse['data']) ? count($callResponse['data']) : 0);
            $this->loggerfile->info($cronLabel . $logMessage);
            $this->loggerdb->logCronInfo($cronType, $cronTypeDetail, $logMessageTopic, $logMessage);


			$logMessageTopic = "prepareStockDataUsingRawQuery";
			
			$logMessage = "preparing stock-data based on stock-get-api call-response";
            $this->loggerfile->info($cronLabel . $logMessage);
            $this->loggerdb->logCronInfo($cronType, $cronTypeDetail, $logMessageTopic, $logMessage);
			
			$stockData = $this->getUpdates->prepareStockDataUsingRawQuery($channelIntegrationMetadata, $callResponse);

			$logMessage = "stock-data prepared number = " . (isset($stockData['data']) ? count($stockData['data']) : 0);
            $this->loggerfile->info($cronLabel . $logMessage);
            $this->loggerdb->logCronInfo($cronType, $cronTypeDetail, $logMessageTopic, $logMessage);


			$logMessageTopic = "insertStockDataUsingRawQuery";

			$logMessage = "persisting stock-data into temporary-stock-table db";
            $this->loggerfile->info($cronLabel . $logMessage);
            $this->loggerdb->logCronInfo($cronType, $cronTypeDetail, $logMessageTopic, $logMessage);
			
			$persistingResult = $this->getUpdates->insertStockDataUsingRawQuery($channelIntegrationMetadata, $stockData);

			$logMessage = "stock-data bulk persisted = " . ($persistingResult > 0 ? "success" : "failed");
            $this->loggerfile->info($cronLabel . $logMessage);
            $this->loggerdb->logCronInfo($cronType, $cronTypeDetail, $logMessageTopic, $logMessage);


			$logMessageTopic = "complete";
			$logMessage = "complete";
            $this->loggerfile->info($cronLabel . $logMessage);
            $this->loggerdb->logCronInfo($cronType, $cronTypeDetail, $logMessageTopic, $logMessage);

		} 
        catch (\Exception $ex) {

            $logMessageTopic = "exception";
            $logMessage = "exception = " . $ex->getMessage();
            $this->loggerfile->info($cronLabel . $logMessage);
            $this->loggerdb->logCronErrorFatal($cronType, $cronTypeDetail, $logMessageTopic, $logMessage);

        }

        $logMessageTopic = "finish";
        $logMessage = "finish " . (microtime(true) - $startTime) . " second";
        $this->loggerfile->info($cronLabel . $logMessage);
        $this->loggerdb->logCronInfo($cronType, $cronTypeDetail, $logMessageTopic, $logMessage);

	}
	
	// public function execute() {
	// 	$lastUpdated = $this->timezone->date(new \DateTime())->format('Y-m-d H:i:s');

	// 	try {
    //         $class = str_replace(IntegrationCheckUpdatesInterface::CRON_DIRECTORY,"",get_class($this));
	// 		$this->loggerfile->info("=>".$class." Get Channel Data");
	// 		$channel = $this->commonRepository->prepareChannel('product-stock-update');

	// 		$this->loggerfile->info("=".$class." Check Complete Jobs");
	// 		$channel = $this->checkUpdates->checkCompleteJobs($channel);

	// 		//if there are no waiting_jobs then break by throwing exception
	// 		$this->loggerfile->info("=".$class." Check Waiting Jobs");
	// 		$channel = $this->getUpdates->checkWaitingJobs($channel);

	// 		$this->loggerfile->info("=".$class." Set Parameter Request Data");
	// 		$data = $this->getUpdates->prepareCall($channel);
	// 		$this->loggerfile->info("=".print_r($data,true));

	// 		$this->loggerfile->info("=".$class." Sending Request Data to API");
	// 		$response = $this->commonRepository->get($data);

	// 		$this->loggerfile->info("=".$class." Set Data Value");
	// 		$dataValue = $this->getUpdates->prepareDataProperResp($channel, $response);

	// 		$this->loggerfile->info("=".$class." Save & Update Jobs & Data Value to databases");
	// 		$result = $this->getUpdates->save($channel, $dataValue, $lastUpdated);

	// 	} catch (\Exception $ex) {

	// 		$this->loggerfile->error("<=End " . $class." ".$ex->getMessage());
	// 	}
	// 	$this->loggerfile->info("<=End " . $class);
	// }

}
