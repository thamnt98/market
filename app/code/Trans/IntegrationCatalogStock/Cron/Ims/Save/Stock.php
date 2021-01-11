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

use Trans\Integration\Exception\WarningException;
use Trans\Integration\Exception\ErrorException;
use Trans\Integration\Exception\FatalException;


class Stock {

	/**
	 * @var \Trans\IntegrationCatalogStock\Logger\Logger
	 */
	protected $loggerfile;

    /**
     * @var \Trans\Integration\Model\IntegrationCronLogToDatabase
     */
	protected $loggerdb;

    /**
     * @var IntegrationCheckUpdatesInterface
     */
	protected $checkUpdates;

    /**
     * @var string
     */
    protected $cronType;

    /**
     * @var string
     */
    protected $cronTypeDetail;

    /**
     * @var string
     */
    protected $cronFileLabel;	


	public function __construct(
        \Trans\IntegrationCatalogStock\Logger\Logger $loggerfile,
        \Trans\Integration\Model\IntegrationCronLogToDatabase $loggerdb,
		IntegrationCommonInterface $commonRepository,
		IntegrationStockInterface $integrationStockInterface,
        IntegrationCheckUpdatesInterface $checkUpdates
	) {
		$this->loggerfile					= $loggerfile;
		$this->loggerdb 					= $loggerdb;
		$this->commonRepository         	= $commonRepository;
		$this->integrationStockInterface 	= $integrationStockInterface;
		$this->checkUpdates              	= $checkUpdates;
		
		$this->adjustCronLogger();
	}

    protected function adjustCronLogger() {
        $this->cronType = "stock";
        $this->cronTypeDetail = "save";
        $this->cronFileLabel = $this->cronType . "-" . $this->cronTypeDetail . " --> ";
    }	

	public function execute() {

        $startTime = microtime(true);

        $logMessageTopic = "start";
        $logMessage = "start";

        $this->loggerfile->info($this->cronFileLabel . $logMessageTopic . " = " . $logMessage);
        $this->loggerdb->info($this->cronType, $this->cronTypeDetail, $logMessageTopic, $logMessage);

		try {

            $logMessageTopic = "prepareChannelUsingRawQuery";

            $logMessage = "retrieving channel-integration-metadata";            
            $this->loggerfile->info($this->cronFileLabel . $logMessageTopic . " = " . $logMessage);
            $this->loggerdb->info($this->cronType, $this->cronTypeDetail, $logMessageTopic, $logMessage);
			
			$channelIntegrationMetadata = $this->commonRepository->prepareChannelUsingRawQuery("product-stock-update");
			
            $logMessage = "channel-integration-metadata = " . print_r($channelIntegrationMetadata, true);
            $this->loggerfile->info($this->cronFileLabel . $logMessageTopic . " = " . $logMessage);
            $this->loggerdb->info($this->cronType, $this->cronTypeDetail, $logMessageTopic, $logMessage);


			$logMessageTopic = "checkOnProgressDataSavingJobUsingRawQuery";

			$logMessage = "checking on-progress-data-saving-job";
			$this->loggerfile->info($this->cronFileLabel . $logMessageTopic . " = " . $logMessage);
			$this->loggerdb->info($this->cronType, $this->cronTypeDetail, $logMessageTopic, $logMessage);
			
			$channelIntegrationMetadata = $this->checkUpdates->checkOnProgressDataSavingJobUsingRawQuery($channelIntegrationMetadata);

			$logMessage = "on-progress-data-saving-job not-found then process continued ...";
			$this->loggerfile->info($this->cronFileLabel . $logMessageTopic . " = " . $logMessage);
			$this->loggerdb->info($this->cronType, $this->cronTypeDetail, $logMessageTopic, $logMessage);
			

			$logMessageTopic = "getFirstDataReadyJobUsingRawQuery";

			$logMessage = "retrieving first-data-ready-job";
			$this->loggerfile->info($this->cronFileLabel . $logMessageTopic . " = " . $logMessage);
			$this->loggerdb->info($this->cronType, $this->cronTypeDetail, $logMessageTopic, $logMessage);
			
			$channelIntegrationMetadata = $this->checkUpdates->getFirstDataReadyJobUsingRawQuery($channelIntegrationMetadata);
			
			$logMessage = "first-data-ready-job = " . (isset($channelIntegrationMetadata['first_data_ready_job']) ? print_r($channelIntegrationMetadata['first_data_ready_job'], true) : "not-found");
			$this->loggerfile->info($this->cronFileLabel . $logMessageTopic . " = " . $logMessage);
			$this->loggerdb->info($this->cronType, $this->cronTypeDetail, $logMessageTopic, $logMessage);
		

			$logMessageTopic = "prepareStockDataUsingRawQuery";

			$logMessage = "retrieving stock-data from temporary-stock-table db";
			$this->loggerfile->info($this->cronFileLabel . $logMessageTopic . " = " . $logMessage);
			$this->loggerdb->info($this->cronType, $this->cronTypeDetail, $logMessageTopic, $logMessage);		
			
			$stockData = $this->integrationStockInterface->prepareStockDataUsingRawQuery($channelIntegrationMetadata);
			
			$logMessage = "stock-data retrieved number = " . count($stockData);
			$this->loggerfile->info($this->cronFileLabel . $logMessageTopic . " = " . $logMessage);
			$this->loggerdb->info($this->cronType, $this->cronTypeDetail, $logMessageTopic, $logMessage);		


			$logMessageTopic = "insertStockDataUsingRawQuery";

			$logMessage = "persisting stock-data into stock-magento-table db";
			$this->loggerfile->info($this->cronFileLabel . $logMessageTopic . " = " . $logMessage);
			$this->loggerdb->info($this->cronType, $this->cronTypeDetail, $logMessageTopic, $logMessage);
			
			$persistingResult = $this->integrationStockInterface->insertStockDataUsingRawQuery($channelIntegrationMetadata, $stockData);
			
			$logMessage = "stock-data bulk persisted = " . ($persistingResult > 0 ? "success" : "failed");
			$this->loggerfile->info($this->cronFileLabel . $logMessageTopic . " = " . $logMessage);
			$this->loggerdb->info($this->cronType, $this->cronTypeDetail, $logMessageTopic, $logMessage);


            $logMessageTopic = "complete";
            $logMessage = "complete";
            $this->loggerfile->info($this->cronFileLabel . $logMessageTopic . " = " . $logMessage);
            $this->loggerdb->info($this->cronType, $this->cronTypeDetail, $logMessageTopic, $logMessage);

		}
        catch (\Trans\Integration\Exception\WarningException $ex) {
            $logMessageTopic = "warning-exception";
            $logMessage = $ex->getMessage();
            $this->loggerfile->info($this->cronFileLabel . $logMessageTopic . " = " . $logMessage);
            $this->loggerdb->warn($this->cronType, $this->cronTypeDetail, $logMessageTopic, $logMessage);
        }
        catch (\Trans\Integration\Exception\ErrorException $ex) {
            $logMessageTopic = "error-exception";
            $logMessage = $ex->getMessage();
            $this->loggerfile->info($this->cronFileLabel . $logMessageTopic . " = " . $logMessage);
            $this->loggerdb->error($this->cronType, $this->cronTypeDetail, $logMessageTopic, $logMessage);
        }
        catch (\Trans\Integration\Exception\FatalException $ex) {
            $logMessageTopic = "fatal-exception";
            $logMessage = $ex->getMessage();
            $this->loggerfile->info($this->cronFileLabel . $logMessageTopic . " = " . $logMessage);
            $this->loggerdb->fatal($this->cronType, $this->cronTypeDetail, $logMessageTopic, $logMessage);
        }
        catch (\Exception $ex) {
            $logMessageTopic = "generic-exception";
            $logMessage = $ex->getMessage();
            $this->loggerfile->info($this->cronFileLabel . $logMessageTopic . " = " . $logMessage);
            $this->loggerdb->fatal($this->cronType, $this->cronTypeDetail, $logMessageTopic, $logMessage);
        }

        $logMessageTopic = "finish";
        $logMessage = "finish in " . (microtime(true) - $startTime) . " second";
        $this->loggerfile->info($this->cronFileLabel . $logMessageTopic . " = " . $logMessage);
        $this->loggerdb->info($this->cronType, $this->cronTypeDetail, $logMessageTopic, $logMessage);

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
