<?php
/**
 * @category Trans
 * @package  Trans_IntegrationCatalogPrice
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   J.P <jaka.pondan@transdigital.co.id>
 *
 * Copyright © 2019 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\IntegrationCatalogPrice\Cron\Pim\Check;

use Trans\Integration\Api\IntegrationCommonInterface;
use Trans\IntegrationCatalogPrice\Api\IntegrationCheckUpdatesInterface;


class Price {
    /**
     * @var \Trans\Integration\Logger\Logger
     */
    protected $logger;

    /**
     * @var IntegrationCommonInterface
     */
    protected $commonRepository;

    /**
     * @var IntegrationCheckUpdatesInterface
     */
    protected $checkUpdates;



    public function __construct(
        \Trans\Integration\Logger\Logger $logger
        ,IntegrationCommonInterface $commonRepository
        ,IntegrationCheckUpdatesInterface $checkUpdates

        ) {
        $this->logger = $logger;
        $this->commonRepository=$commonRepository;
        $this->checkUpdates=$checkUpdates;

        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/integration_price.log');
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

        try {
            $this->logger->info("=>".$class." Get Channel Data");
            $channel    = $this->commonRepository->prepareChannel('product-store-price');

            $this->logger->info("=".$class." Check On Progress Job");
            $this->checkUpdates->checkOnProgressJobs($channel);

            try {
                $this->logger->info("=".$class." Get Last Complete Jobs");
                $channel    = $this->checkUpdates->checkCompleteJobs($channel);
            } catch (\Exception $e) {
            }

            $this->logger->info("=".$class." Set Parameter Request Data");
            $data       = $this->checkUpdates->prepareCall($channel);

            $this->logger->info(print_r($data ,true));

            $this->logger->info("=".$class." Sending Request Data to API");
            $response = $this->commonRepository->get($data);

            $this->logger->info("=".$class." Set Response to Job data");
            $jobsData = $this->checkUpdates->prepareJobsDataProperResp($channel,$response);


            $this->logger->info("=".$class." Save data to databases");
            $result = $this->checkUpdates->saveProperOffset($jobsData);

        } catch (\Exception $ex) {

            $this->logger->info("<=End ".$class." ".$ex->getMessage());
        }
        $this->logger->info("<=End ".$class);
    }



}