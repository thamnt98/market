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
    protected $logger;

    /**
     * @var IntegrationCommonInterface
     */
    protected $commonRepository;

    /**
     * @var
     */
    protected $checkUpdates;


    public function __construct(
        \Trans\IntegrationCatalogStock\Logger\Logger $logger,
        IntegrationCommonInterface $commonRepository,
        IntegrationCheckUpdatesInterface $checkUpdates
    ) {
        $this->logger = $logger;
        $this->commonRepository = $commonRepository;
        $this->checkUpdates = $checkUpdates;
    }

   /**
    * Write to system.log
    *
    * @return void
    */

    public function execute() {

        $startTime = microtime(true);

        $label = "stock-check";
        $label .= " --> ";

        $this->logger->info($label . "start");

        try {
            
            $this->logger->info($label . "retrieving channel-integration-metadata");
            $channelIntegrationMetadata = $this->commonRepository->prepareChannelUsingRawQuery("product-stock-update");
            $this->logger->info($label . "channel-integration-metadata = " . print_r($channelIntegrationMetadata, true));

            $this->logger->info($label . "retrieving on-progress-job");
            $this->checkUpdates->checkOnProgressJobUsingRawQuery($channelIntegrationMetadata);
            $this->logger->info($label . "channel-integration-metadata = " . print_r($channelIntegrationMetadata, true));

            $this->logger->info($label . "retrieving last-complete-job");
            $channelIntegrationMetadata = $this->checkUpdates->getLastCompleteJobUsingRawQuery($channelIntegrationMetadata);
            $this->logger->info($label . "channel-integration-metadata = " . print_r($channelIntegrationMetadata, true));

            $this->logger->info($label . "preparing to call stock-check-api");
            $callData = $this->checkUpdates->prepareCallUsingRawQuery($channelIntegrationMetadata);
            $this->logger->info($label . "call-data = " . print_r($callData, true));

            $this->logger->info($label . "calling stock-check-api");
            $callResponse = $this->commonRepository->doCallUsingRawQuery($data);
            $this->logger->info($label . "call-response = " . print_r($callResponse, true));

            $this->logger->info($label . "preparing job-candidates based on stock-check-api result");
            $jobCandidates = $this->checkUpdates->prepareJobCandidatesUsingRawQuery($channelIntegrationMetadata, $callResponse);
            $this->logger->info($label . "job-candidates = " . print_r($jobCandidates, true));
            
            $this->logger->info($label . "persisting job-candidates into job-temporary-table db");
            $persistingResult = $this->checkUpdates->insertJobCandidatesUsingRawQuery($jobCandidates);
            $this->logger->info($label . "persisting-result = " . print_r($persistingResult, true));
            $this->logger->info($label . "complete");
       
        }
        catch (\Exception $ex) {
            $this->logger->info($label . "exception = " . strtolower($ex->getMessage()));
        }

        $this->logger->info($label . "finish " . (microtime(true) - $startTime) . " second");

    }
    
    /*
    public function execute() {
        $class = str_replace(IntegrationCheckUpdatesInterface::CRON_DIRECTORY,"",get_class($this));

        try {
            $this->logger->info("=>".$class." Get Channel Data");
            $channel    = $this->commonRepository->prepareChannel('product-stock-update');

            $this->logger->info("=".$class." Check On Progress Job");
            $this->checkUpdates->checkOnProgressJobs($channel);

            $this->logger->info("=".$class." Get Last Complete Jobs");
            $channel    = $this->checkUpdates->checkCompleteJobs($channel);

            $this->logger->info("=".$class." Set Parameter Request Data");
            $data       = $this->checkUpdates->prepareCall($channel);
            $this->logger->info("=".print_r($data ,true));

            $this->logger->info("=".$class." Sending Request Data to API");
            $response    = $this->commonRepository->get($data);

            $this->logger->info("=".$class." Set Response to Job data");
            $jobsData = $this->checkUpdates->prepareJobsDataImsStockUpdate($channel,$response);
            
            $this->logger->info("=".$class." Save data to databases");
            $result = $this->checkUpdates->saveProperOffset($jobsData);


        } catch (\Exception $ex) {

            $this->logger->error("<=End ".$class." ".$ex->getMessage());
        }
        $this->logger->info("<=End ".$class);
    }
    */



}