<?php
/**
 * @category Trans
 * @package  Trans_IntegrationEntity
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   J.P <jaka.pondan@transdigital.co.id>
 *
 * Copyright © 2019 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\IntegrationEntity\Model;


use Trans\IntegrationEntity\Api\IntegrationInitialStoreInterface;
use Trans\Integration\Api\IntegrationCommonInterface;
use Trans\IntegrationEntity\Api\IntegrationInventoryRepositoryInterface;
use Trans\Integration\Helper\ConfigCoreData;
use Trans\IntegrationEntity\Api\IntegrationStoreRepositoryInterface;
use \Trans\IntegrationEntity\Api\Data\IntegrationInitialStoreResponseInterfaceFactory;
/**
 * @inheritdoc
 */
class IntegrationInitialStore implements IntegrationInitialStoreInterface
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
     * @var ConfigChannel
     */
    protected $_config;

    /**
     * @var IntegrationInventoryRepositoryInterface
     */
    protected $inventoryRepository;

    /**
     * @var IntegrationStoreRepositoryInterface
     */
    protected $storeRepository;

    /**
     * @var IntegrationInitialStoreResponseInterfaceFactory
     */
    protected $response;

    public function __construct(
        \Trans\Integration\Logger\Logger $logger
        , ConfigCoreData $_config
        , IntegrationCommonInterface $commonRepository
        , IntegrationInventoryRepositoryInterface $inventoryRepository
        , IntegrationStoreRepositoryInterface $storeRepository
        ,IntegrationInitialStoreResponseInterfaceFactory $response

    ) {
        $this->logger               = $logger;
        $this->_config              = $_config;
        $this->commonRepository     = $commonRepository;
        $this->inventoryRepository  = $inventoryRepository;
        $this->storeRepository  = $storeRepository;
        $this->response=$response;

    }

    /**
     * Execute Store Updates
     * @return void
     */
    public function execute() {
        $this->logger->info("===>" . __CLASS__);
        try {

// TODO Config Show 1
//            $updates=$this->_config->getStoreUpdates();
//            $this->logger->info($updates );
//            if(empty($updates)){// initial link
//                $this->logger->info("Get Channel Data Initial Store");
//                $channel = $this->commonRepository->prepareChannel('store');
//                $this->logger->info("Initial" );
//            }else{
//                $this->logger->info("Get Channel Data Updates Store");
//                $channel = $this->commonRepository->prepareChannel('store-updates');
//                $this->logger->info("Updates" );
//            }

            $this->logger->info("Get Channel Data Initial Store");
            $channel = $this->commonRepository->prepareChannel('store');
            $this->logger->info("Initial" );

            $this->logger->info("Set Parameter Request Data");
            $data = $this->inventoryRepository->prepareRequest($channel);

            $this->logger->info("Sending Request Data to API");
            $response = $this->commonRepository->get($data);
           $this->logger->info(print_r($response,true));

            $this->logger->info("Set Jobs");
            $jobdata = $this->inventoryRepository->saveJob($channel,$response);


            $this->logger->info("Save Data Value");
            $dataValue = $this->inventoryRepository->saveData($jobdata, $response);
            if(empty($updates)) {
                $this->_config->setStoreUpdates(1);
            }

        } catch (\Exception $ex) {

            $this->logger->error($ex->getMessage());
            return $this->getResponse($ex->getMessage());
        }
        $this->logger->info("<=== GET " . __CLASS__);
        return $this->getResponse("Successfully Create Job");


    }

    /**
     * @param null $message
     * @param array $data
     * @return mixed
     */
    protected function getResponse($message = null) {
        /** \Trans\IntegrationEntity\Api\Data\IntegrationInitialStoreResponseInterfaceFactory */
        $result = $this->response->create();

        $result->setMessage(__($message));

        return $result;
    }

}
