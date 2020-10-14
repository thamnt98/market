<?php
/**
 * @category Trans
 * @package  Trans_IntegrationCatalog
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   J.P <jaka.pondan@transdigital.co.id>
 *
 * Copyright © 2019 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\IntegrationEntity\Cron\Pim\Get;

use Trans\Integration\Api\IntegrationCommonInterface;
use Trans\IntegrationEntity\Api\IntegrationGetUpdatesInterface;
use Trans\IntegrationEntity\Api\IntegrationCheckUpdatesInterface;

class ProductAttribute {
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

	public function __construct(
		\Trans\Integration\Logger\Logger $logger
		,IntegrationCommonInterface $commonRepository
		,IntegrationGetUpdatesInterface $getUpdates
        ,IntegrationCheckUpdatesInterface $checkUpdates

	) {
		$this->logger           = $logger;
		$this->commonRepository = $commonRepository;
		$this->getUpdates       = $getUpdates;
        $this->checkUpdates     = $checkUpdates;

	}

	/**
	 * Write to system.log
	 *
	 * @return void
	 */
	public function execute() {
		$lastUpdated = date('Y-m-d H:i:s');
        $class = str_replace(IntegrationCheckUpdatesInterface::CRON_DIRECTORY,"",get_class($this));

		try {
			$this->logger->info("=>".$class." Get Channel Data");
			$channel = $this->commonRepository->prepareChannel('product-attribute');

			$this->logger->info("=".$class." Check Waiting Jobs");
            $channel = $this->getUpdates->checkWaitingJobs($channel);
            
            try {
                $this->logger->info("=".$class." Check Complete Jobs");
                $channel = $this->checkUpdates->checkCompleteJobs($channel);
            } catch (\Exception $e) {
            }

			$this->logger->info("=".$class." Set Parameter Request Data");
			$data = $this->getUpdates->prepareCall($channel);

			$this->logger->info(print_r($data ,true));

			$this->logger->info("=".$class." Sending Request Data to API");
			$response = $this->commonRepository->get($data);
			if (isset($response['data'])) {
                $this->logger->info('$response total data = ' . count($response['data']));
            }

			$this->logger->info("=".$class." Set Data Value");
			$dataValue = $this->getUpdates->prepareDataProperResp($channel, $response, $data);

			$this->logger->info("=".$class." Save & Update Jobs & Data Value to databases");
			$result = $this->getUpdates->save($channel, $dataValue, $lastUpdated);

		} catch (\Exception $ex) {

			$this->logger->error("<=".$class." ".$ex->getMessage());
		}
		$this->logger->info("<=".$class);
	}

}
