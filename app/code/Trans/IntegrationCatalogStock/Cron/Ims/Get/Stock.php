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

namespace Trans\IntegrationCatalogStock\Cron\Ims\Get;

use Trans\Integration\Api\IntegrationCommonInterface;
use Trans\IntegrationCatalogStock\Api\IntegrationGetUpdatesInterface;
use Trans\IntegrationCatalogStock\Api\IntegrationCheckUpdatesInterface;

class Stock {
	/**
	 * @var \Trans\Integration\Logger\Logger
	 */
	protected $logger;

	/**
	 * @var IntegrationCommonInterface
	 */
	protected $commonRepository;

    /**
     * @var IntegrationGetUpdatesInterface
     */
	protected $getUpdates;

    /**
     * @var IntegrationCheckUpdatesInterface
     */
	protected $checkUpdates;
	
	/**
     * @var TimezoneInterface
     */
	protected $timezone;

	public function __construct(
		\Trans\Integration\Logger\Logger $logger
		, IntegrationCommonInterface $commonRepository
		, IntegrationGetUpdatesInterface $getUpdates
		, IntegrationCheckUpdatesInterface $checkUpdates
		,\Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone
	) {
		$this->logger           = $logger;
		$this->commonRepository = $commonRepository;
		$this->getUpdates       = $getUpdates;
		$this->checkUpdates		= $checkUpdates;
		$this->timezone			= $timezone;

	}

	/**
	 * Write to system.log
	 *
	 * @return void
	 */
	public function execute() {
		$lastUpdated = $this->timezone->date(new \DateTime())->format('Y-m-d H:i:s');
		$class = str_replace(IntegrationCheckUpdatesInterface::CRON_DIRECTORY,"",get_class($this));
		
		try {
			$this->logger->info("=>". $class." Get Channel Data");
			$channel = $this->commonRepository->prepareChannel('product-stock');

			$this->logger->info("=". $class." Check Complete Jobs");
			$channel = $this->checkUpdates->checkCompleteJobs($channel);

			$this->logger->info("=". $class." Check Waiting Jobs");
			$channel = $this->getUpdates->checkWaitingJobs($channel);

			$this->logger->info("=". $class." Set Parameter Request Data");
			$data = $this->getUpdates->prepareCall($channel);

			$this->logger->info("=". $class." Sending Request Data to API");
			$response = $this->commonRepository->get($data);

			$this->logger->info("=". $class." Set Data Value");
			$dataValue = $this->getUpdates->prepareDataProperResp($channel, $response);

			$this->logger->info("=". $class." Save & Update Jobs & Data Value to databases");
			$result = $this->getUpdates->save($channel, $dataValue, $lastUpdated);

		} catch (\Exception $ex) {

			$this->logger->error("<=End ". $class." ".$ex->getMessage());
		}
		$this->logger->info("<=End ". $class);
	}

}
