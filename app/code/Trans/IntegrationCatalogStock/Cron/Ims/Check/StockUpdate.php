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
        \Trans\IntegrationCatalogStock\Logger\Logger $logger
        ,IntegrationCommonInterface $commonRepository
        ,IntegrationCheckUpdatesInterface $checkUpdates

        ) {
        $this->logger = $logger;
        $this->commonRepository=$commonRepository;
        $this->checkUpdates=$checkUpdates;

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



}