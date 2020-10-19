<?php
/**
 * @category Trans
 * @package  Trans_IntegrationEntity
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   HaDi <ashadi.sejati@transdigital.co.id>
 *
 * Copyright © 2020 PT Trans Digital. All rights reserved.
 * https://ctcorpdigital.com/
 */

namespace Trans\IntegrationEntity\Cron\Pim\Check;

use Trans\Integration\Api\IntegrationCommonInterface;
use Trans\IntegrationEntity\Api\IntegrationCheckUpdatesInterface;


class ProductAttributeSet {
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
            $channel    = $this->commonRepository->prepareChannel('product-attribute-set');

            $this->logger->info("=".$class." Check On Progress Job");
            $this->checkUpdates->checkOnProgressJobs($channel);

            try {
                $this->logger->info("=".$class." Get Last Complete Jobs");
                $channel = $this->checkUpdates->checkCompleteJobs($channel);
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
            $result = $this->checkUpdates->save($jobsData);

        } catch (\Exception $ex) {

            $this->logger->error("<=End ".$class." ".$ex->getMessage());
        }
        $this->logger->info("<=End ".$class);
    }



}