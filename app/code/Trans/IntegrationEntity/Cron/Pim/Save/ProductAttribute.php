<?php
/**
 * @category Trans
 * @package  Trans_IntegrationCatalog
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   Dwi Septha Kurniawan <septha.kurniawan@transdigital.co.id>
 * @modify   J.P <jaka.pondan@transdigital.co.id>
 *
 * Copyright © 2019 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\IntegrationEntity\Cron\Pim\Save;

use Trans\Integration\Api\IntegrationCommonInterface;
use Trans\IntegrationEntity\Api\IntegrationCheckUpdatesInterface;
use Trans\IntegrationEntity\Api\IntegrationDataValueRepositoryInterface;
use Trans\IntegrationEntity\Api\IntegrationProductAttributeInterface;
use Trans\Integration\Logger\Logger;

class ProductAttribute {
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
     * @var IntegrationProductAttributeInterface
     */
    protected $integrationProductAttributeInterface;

    /**
     * @var IntegrationCheckUpdatesInterface
     */
    protected $checkUpdates;

	public function __construct(
		Logger $logger
		,IntegrationCommonInterface $commonRepository
		,IntegrationDataValueRepositoryInterface $dataValue
		,IntegrationProductAttributeInterface $integrationProductAttributeInterface
        ,IntegrationCheckUpdatesInterface $checkUpdates
	) {
		$this->logger                               = $logger;
		$this->commonRepository                     = $commonRepository;
		$this->dataValue                            = $dataValue;
		$this->integrationProductAttributeInterface = $integrationProductAttributeInterface;
        $this->checkUpdates                         = $checkUpdates;

        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/integration_attribute.log');
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
			$channel = $this->commonRepository->prepareChannel('product-attribute');


			$this->logger->info("=".$class." Check Onprogress Jobs (Save Data)");
			$this->checkUpdates->checkSaveOnProgressJob($channel);

			$this->logger->info("=".$class." Check Complete Jobs");
			$channel = $this->checkUpdates->checkReadyJobs($channel);

			$this->logger->info("=".$class." Prepare Data");
			$data = $this->integrationProductAttributeInterface->prepareData($channel);

			$this->logger->info("=".$class." Save Data");
			$response = $this->integrationProductAttributeInterface->save($data);
			// $this->logger->info(print_r($response,true));

		} catch (\Exception $ex) {

			$this->logger->info("<=".$class." ".$ex->getMessage());
		}
		$this->logger->info("<=".$class );
	}
}