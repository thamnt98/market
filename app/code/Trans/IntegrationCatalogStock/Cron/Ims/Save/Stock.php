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
	 * @var \Trans\IntegrationCatalogStock\Logger\Logger
	 */
	protected $loggerfile;

    /**
     * @var \Trans\Integration\Api\IntegrationLogToDatabaseInterface
     */
	protected $loggerdb;

    /**
     * @var IntegrationCheckUpdatesInterface
     */
	protected $checkUpdates;


	public function __construct(
        \Trans\IntegrationCatalogStock\Logger\Logger $loggerfile,
        \Trans\Integration\Api\IntegrationLogToDatabaseInterface $loggerdb,
		IntegrationCommonInterface $commonRepository,
		IntegrationStockInterface $integrationStockInterface,
        IntegrationCheckUpdatesInterface $checkUpdates
	) {
		$this->loggerfile					= $loggerfile;
		$this->loggerdb 					= $loggerdb;
		$this->commonRepository         	= $commonRepository;
		$this->integrationStockInterface 	= $integrationStockInterface;
        $this->checkUpdates              	= $checkUpdates;
	}


	public function execute() {

        $startTime = microtime(true);

        $cronType = "stock";
        $cronTypeDetail = "save";
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


			$logMessageTopic = "checkOnProgressDataSavingJobUsingRawQuery";

			$logMessage = "checking on-progress-data-saving-job";
			$this->loggerfile->info($cronLabel . $logMessage);
			$this->loggerdb->logCronInfo($cronType, $cronTypeDetail, $logMessageTopic, $logMessage);
			
			$channelIntegrationMetadata = $this->checkUpdates->checkOnProgressDataSavingJobUsingRawQuery($channelIntegrationMetadata);

			$logMessage = "on-progress-data-saving-job not-found then process continued ...";
			$this->loggerfile->info($cronLabel . $logMessage);
			$this->loggerdb->logCronInfo($cronType, $cronTypeDetail, $logMessageTopic, $logMessage);
			

			$logMessageTopic = "getFirstDataReadyJobUsingRawQuery";

			$logMessage = "retrieving first-data-ready-job";
			$this->loggerfile->info($cronLabel . $logMessage);
			$this->loggerdb->logCronInfo($cronType, $cronTypeDetail, $logMessageTopic, $logMessage);
			
			$channelIntegrationMetadata = $this->checkUpdates->getFirstDataReadyJobUsingRawQuery($channelIntegrationMetadata);
			
			$logMessage = "first-data-ready-job = " . (isset($channelIntegrationMetadata['first_data_ready_job']) ? print_r($channelIntegrationMetadata['first_data_ready_job'], true) : "not-found");
			$this->loggerfile->info($cronLabel . $logMessage);
			$this->loggerdb->logCronInfo($cronType, $cronTypeDetail, $logMessageTopic, $logMessage);
		

			$logMessageTopic = "prepareStockDataUsingRawQuery";

			$logMessage = "retrieving stock-data from temporary-stock-table db";
			$this->loggerfile->info($cronLabel . $logMessage);
			$this->loggerdb->logCronInfo($cronType, $cronTypeDetail, $logMessageTopic, $logMessage);		
			
			$stockData = $this->integrationStockInterface->prepareStockDataUsingRawQuery($channelIntegrationMetadata);
			
			$logMessage = "stock-data retrieved number = " . count($stockData);
			$this->loggerfile->info($cronLabel . $logMessage);
			$this->loggerdb->logCronInfo($cronType, $cronTypeDetail, $logMessageTopic, $logMessage);		


			$logMessageTopic = "insertStockDataUsingRawQuery";

			$logMessage = "persisting stock-data into stock-magento-table db";
			$this->loggerfile->info($cronLabel . $logMessage);
			$this->loggerdb->logCronInfo($cronType, $cronTypeDetail, $logMessageTopic, $logMessage);
			
			$persistingResult = $this->integrationStockInterface->insertStockDataUsingRawQuery($channelIntegrationMetadata, $stockData);
			
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
    //     $class = str_replace(IntegrationCheckUpdatesInterface::CRON_DIRECTORY,"",get_class($this));
	// 	try {

	// 		$this->loggerfile->info("=>".$class." Get Channel Data");
	// 		$channel = $this->commonRepository->prepareChannel('product-stock-update');

	// 		$this->loggerfile->info("=".$class." Check Onprogress Jobs (Save Product)");
	// 		$channel = $this->checkUpdates->checkSaveOnProgressJob($channel);

	// 		$this->loggerfile->info("=".$class."  Check Complete Jobs");
	// 		$channel = $this->checkUpdates->checkReadyJobs($channel);

	// 		$this->loggerfile->info("=".$class." Prepare Data");
	// 		$data = $this->integrationStockInterface->prepareData($channel);

	// 		$this->loggerfile->info("=".$class." Save Data");
	// 		$stock = $this->integrationStockInterface->saveStock($data);			

	// 	} catch (\Exception $ex) {

	// 		$this->loggerfile->error("<=End ".$class." ".$ex->getMessage());
	// 	}
	// 	$this->loggerfile->info("<=End ".$class);
	// }

}
