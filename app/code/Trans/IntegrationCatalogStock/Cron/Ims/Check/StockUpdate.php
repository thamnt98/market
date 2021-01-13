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

namespace Trans\IntegrationCatalogStock\Cron\Ims\Check;

use Trans\Integration\Api\IntegrationCommonInterface;
use Trans\IntegrationCatalogStock\Api\IntegrationCheckUpdatesInterface;


class StockUpdate {

	/**
	 * @var \Trans\IntegrationCatalogStock\Logger\Logger
	 */
	protected $loggerfile;

    /**
     * @var \Trans\Integration\Model\IntegrationCronLogToDatabase
     */
    protected $loggerdb;

    /**
     * @var IntegrationCommonInterface
     */
    protected $commonRepository;

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
        IntegrationCheckUpdatesInterface $checkUpdates
    ) {
        $this->loggerfile = $loggerfile;
        $this->loggerdb = $loggerdb;
        $this->commonRepository = $commonRepository;        
        $this->checkUpdates = $checkUpdates;

        $this->adjustCronLogger();
    }
    
    protected function adjustCronLogger() {
        $this->cronType = "stock";
        $this->cronTypeDetail = "check";
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


            $logMessageTopic = "checkOnProgressJobUsingRawQuery";

            $logMessage = "checking first-waiting-job and first-data-ready-job and on-progress-data-saving-job";
            $this->loggerfile->info($this->cronFileLabel . $logMessageTopic . " = " . $logMessage);
            $this->loggerdb->info($this->cronType, $this->cronTypeDetail, $logMessageTopic, $logMessage);

            $this->checkUpdates->checkOnProgressJobUsingRawQuery($channelIntegrationMetadata);
            
            $logMessage = "first-waiting-job and first-data-ready-job and on-progress-data-saving-job not-found then process continued ...";
            $this->loggerfile->info($this->cronFileLabel . $logMessageTopic . " = " . $logMessage);
            $this->loggerdb->info($this->cronType, $this->cronTypeDetail, $logMessageTopic, $logMessage);


            $logMessageTopic = "getLastCompleteJobUsingRawQuery";

            $logMessage = "retrieving last-complete-job";
            $this->loggerfile->info($this->cronFileLabel . $logMessageTopic . " = " . $logMessage);
            $this->loggerdb->info($this->cronType, $this->cronTypeDetail, $logMessageTopic, $logMessage);

            $channelIntegrationMetadata = $this->checkUpdates->getLastCompleteJobUsingRawQuery($channelIntegrationMetadata);

            $logMessage = "last-complete-job = " . (isset($channelIntegrationMetadata['last_complete_job']) ? print_r($channelIntegrationMetadata['last_complete_job'], true) : "not-found");
            $this->loggerfile->info($this->cronFileLabel . $logMessageTopic . " = " . $logMessage);
            $this->loggerdb->info($this->cronType, $this->cronTypeDetail, $logMessageTopic, $logMessage);


            $logMessageTopic = "prepareCallUsingRawQuery";

            $logMessage = "preparing to call stock-check-api";
            $this->loggerfile->info($this->cronFileLabel . $logMessageTopic . " = " . $logMessage);
            $this->loggerdb->info($this->cronType, $this->cronTypeDetail, $logMessageTopic, $logMessage);

            $callData = $this->checkUpdates->prepareCallUsingRawQuery($channelIntegrationMetadata);

            $logMessage = "call-data = " . print_r($callData, true);
            $this->loggerfile->info($this->cronFileLabel . $logMessageTopic . " = " . $logMessage);
            $this->loggerdb->info($this->cronType, $this->cronTypeDetail, $logMessageTopic, $logMessage);


            $logMessageTopic = "doCallUsingRawQuery";
            
            $logMessage = "calling stock-check-api";
            $this->loggerfile->info($this->cronFileLabel . $logMessageTopic . " = " . $logMessage);
            $this->loggerdb->info($this->cronType, $this->cronTypeDetail, $logMessageTopic, $logMessage);

            $callResponse = $this->commonRepository->doCallUsingRawQuery($callData);

            $logMessage = "call-response = " . print_r($callResponse, true);
            $this->loggerfile->info($this->cronFileLabel . $logMessageTopic . " = " . $logMessage);
            $this->loggerdb->info($this->cronType, $this->cronTypeDetail, $logMessageTopic, $logMessage);

            
            $logMessageTopic = "prepareJobCandidatesUsingRawQuery";

            $logMessage = "preparing job-candidates based on stock-check-api call-response";
            $this->loggerfile->info($this->cronFileLabel . $logMessageTopic . " = " . $logMessage);
            $this->loggerdb->info($this->cronType, $this->cronTypeDetail, $logMessageTopic, $logMessage);

            $jobCandidates = $this->checkUpdates->prepareJobCandidatesUsingRawQuery($channelIntegrationMetadata, $callResponse);

            $logMessage = "job-candidates prepared = " . print_r($jobCandidates, true);
            $this->loggerfile->info($this->cronFileLabel . $logMessageTopic . " = " . $logMessage);
            $this->loggerdb->info($this->cronType, $this->cronTypeDetail, $logMessageTopic, $logMessage);
            

            $logMessageTopic = "insertJobCandidatesUsingRawQuery";

            $logMessage = "persisting job-candidates into job-temporary-table db";
            $this->loggerfile->info($this->cronFileLabel . $logMessageTopic . " = " . $logMessage);
            $this->loggerdb->info($this->cronType, $this->cronTypeDetail, $logMessageTopic, $logMessage);

            $persistingResult = $this->checkUpdates->insertJobCandidatesUsingRawQuery($jobCandidates);

            $logMessage = "job-candidates bulk persisted = " . ($persistingResult > 0 ? "success" : "failed");
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
    
    /*
    public function execute() {
        $class = str_replace(IntegrationCheckUpdatesInterface::CRON_DIRECTORY,"",get_class($this));

        try {
            $this->loggerfile->info("=>".$class." Get Channel Data");
            $channel    = $this->commonRepository->prepareChannel('product-stock-update');

            $this->loggerfile->info("=".$class." Check On Progress Job");
            $this->checkUpdates->checkOnProgressJobs($channel);

            $this->loggerfile->info("=".$class." Get Last Complete Jobs");
            $channel    = $this->checkUpdates->checkCompleteJobs($channel);

            $this->loggerfile->info("=".$class." Set Parameter Request Data");
            $data       = $this->checkUpdates->prepareCall($channel);
            $this->loggerfile->info("=".print_r($data ,true));

            $this->loggerfile->info("=".$class." Sending Request Data to API");
            $response    = $this->commonRepository->get($data);

            $this->loggerfile->info("=".$class." Set Response to Job data");
            $jobsData = $this->checkUpdates->prepareJobsDataImsStockUpdate($channel,$response);
            
            $this->loggerfile->info("=".$class." Save data to databases");
            $result = $this->checkUpdates->saveProperOffset($jobsData);


        } catch (\Exception $ex) {

            $this->loggerfile->error("<=End ".$class." ".$ex->getMessage());
        }
        $this->loggerfile->info("<=End ".$class);
    }
    */

}