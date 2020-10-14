<?php
/**
 * @category Trans
 * @package  Trans_IntegrationCatalogPrice
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   Muhammad Rifai <muhammad.rifai@ctcorpdigital.com>
 *
 * Copyright © 2019 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\IntegrationCatalogPrice\Cron\Pim\Save;

use Trans\Integration\Api\IntegrationCommonInterface;
use Trans\IntegrationCatalogPrice\Api\IntegrationCheckUpdatesInterface;
use Trans\IntegrationCatalogPrice\Api\IntegrationDataValueRepositoryInterface;
use Trans\IntegrationCatalogPrice\Api\StorePriceLogicInterface;

use Trans\Integration\Logger\Logger;

class Price {
	/* @var \Trans\Integration\Logger\Logger
	 */
	protected $logger;

    /**
     * @var IntegrationCommonInterface
     */
    protected $commonRepository;

    /**
     * @var IntegrationDataValueRepositoryInterface
     */
    protected $dataValue;

    /**
     * @var IntegrationCheckUpdatesInterface
     */
    protected $checkUpdates;
	/** 
	 * @var StorePriceLogicInterface
	*/
	protected $storePriceLogicInterface;

	public function __construct(
		Logger $logger
		,IntegrationCommonInterface $commonRepository
		,IntegrationDataValueRepositoryInterface $dataValue
		,IntegrationCheckUpdatesInterface $checkUpdates
		,StorePriceLogicInterface $storePriceLogicInterface
	) {
		$this->logger                           = $logger;
		$this->commonRepository                 = $commonRepository;
		$this->dataValue                        = $dataValue;
		$this->checkUpdates                     = $checkUpdates;
		$this->storePriceLogicInterface 		= $storePriceLogicInterface;

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
			$channel = $this->commonRepository->prepareChannel('product-store-price');
			
			$this->logger->info("=".$class." Check Onprogress Jobs (Save Product)");
			$this->checkUpdates->checkSaveOnProgressJob($channel);

			$this->logger->info("=".$class." Check Ready Jobs");
			$jobs = $this->checkUpdates->checkReadyJobs($channel);

			$this->logger->info("=".$class." Prepare Data");
			$data = $this->storePriceLogicInterface->prepareData($jobs);

			$this->logger->info("=".$class." Remap Data");
			$dataProduct = $this->storePriceLogicInterface->remapData($jobs,$data);

			$this->logger->info("=".$class." Save Product Price");
			$response = $this->storePriceLogicInterface->save($jobs,$dataProduct);
		} catch (\Exception $ex) {

			$this->logger->error("<=".$class." ".$ex->getMessage());
		}
		$this->logger->info("<=".$class );
	}
}