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

namespace Trans\IntegrationEntity\Cron\Ims\Get;

use Trans\Integration\Api\IntegrationCommonInterface;
use Trans\IntegrationEntity\Api\IntegrationInventoryRepositoryInterface;
use Trans\IntegrationEntity\Api\IntegrationCheckUpdatesInterface;
use Trans\Integration\Helper\ConfigCoreData;

class StoreUpdate {
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
     * @var
     */
    protected $inventoryRepository;


    public function __construct(
        \Trans\Integration\Logger\Logger $logger
        , ConfigCoreData $_config
        , IntegrationCommonInterface $commonRepository
        , IntegrationInventoryRepositoryInterface $inventoryRepository

    ) {
        $this->logger               = $logger;
        $this->_config              = $_config;
        $this->commonRepository     = $commonRepository;
        $this->inventoryRepository  = $inventoryRepository;

    }

    /**
     * Execute Store Updates
     * @return void
     */
    public function execute() {
        $class = str_replace(IntegrationCheckUpdatesInterface::CRON_DIRECTORY,"",get_class($this));
        try {

            $this->logger->info("=>".$class." Get Channel Data Updates Store");
            $channel = $this->commonRepository->prepareChannel('store-updates');
            $this->logger->info("Updates" );

            $this->logger->info("=".$class." Set Parameter Request Data");
            $data = $this->inventoryRepository->prepareRequest($channel);

            $this->logger->info("=".$class." Sending Request Data to API");
            $response = $this->commonRepository->get($data);
            // $this->logger->info(print_r($response,true));

            $this->logger->info("=".$class." Set Jobs");
            $jobdata = $this->inventoryRepository->saveJob($channel,$response);

            $this->logger->info("=".$class." Save Data Value");
            $dataValue = $this->inventoryRepository->saveData($jobdata, $response);


        } catch (\Exception $ex) {

            $this->logger->error("<=End ".$class." ".$ex->getMessage());
        }
        $this->logger->info("<=End ".$class);
    }

}
