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

namespace Trans\IntegrationCatalogStock\Model;

use Trans\Integration\Api\IntegrationCommonInterface;
use Trans\IntegrationCatalogStock\Api\IntegrationCheckUpdatesInterface;
use Trans\IntegrationCatalogStock\Api\IntegrationGetUpdatesInterface;
use Trans\IntegrationCatalogStock\Api\IntegrationInitialStockInterface;
use Trans\IntegrationCatalogStock\Api\Data\IntegrationInitialStockResponseInterfaceFactory;
/**
 * @inheritdoc
 */
class IntegrationInitialStock implements IntegrationInitialStockInterface
{
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

    /**
     * @var IntegrationGetUpdatesInterface
     */
    protected $getUpdates;

    /**
     * @var IntegrationInitialStockResponseInterfaceFactory
     */
    protected $response;


    public function __construct(
        \Trans\Integration\Logger\Logger $logger
        ,IntegrationCommonInterface $commonRepository
        ,IntegrationCheckUpdatesInterface $checkUpdates
        ,IntegrationInitialStockResponseInterfaceFactory $response
        ) {
        $this->logger = $logger;
        $this->commonRepository=$commonRepository;
        $this->checkUpdates=$checkUpdates;
        $this->response=$response;

    }

   /**
    * Write to system.log
    *
    * @return void
    */
    public function execute() {

        $this->logger->info("===>GET ".__CLASS__);
        try {
            $this->logger->info("Get Channel Data");
            $channel    = $this->commonRepository->prepareChannel('product-stock');

            $this->logger->info("Check Waiting Jobs");
            $this->checkUpdates->checkWaitingJobs($channel);

            $this->logger->info("Get Last Complete Jobs");
            $channel    = $this->checkUpdates->checkCompleteJobs($channel);

            $this->logger->info("Set Parameter Request Data");
            $data       = $this->checkUpdates->prepareCall($channel);

            $this->logger->info("Sending Request Data to API");
            $response    = $this->commonRepository->get($data);

            $this->logger->info("Set Response to Job data");
            $jobsData = $this->checkUpdates->prepareJobsDataProperResp($channel,$response);

            $this->logger->info("Save data to databases");
            $result = $this->checkUpdates->saveProperOffset($jobsData);


        } catch (\Exception $ex) {

            $this->logger->error($ex->getMessage());
            return $this->getResponse($ex->getMessage());
        }
        $this->logger->info("<=== GET ".__CLASS__);
        return $this->getResponse("Successfully Create Job");
    }

    /**
     * @param null $message
     * @param array $data
     * @return mixed
     */
    protected function getResponse($message = null) {
        /** \Trans\IntegrationCatalogStock\Api\Data\IntegrationInitialStockResponseInterfaceFactory */
        $result = $this->response->create();

        $result->setMessage(__($message));

        return $result;
    }

}