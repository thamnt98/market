<?php
/**
 * @category Trans
 * @package  Trans_IntegrationCategory
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   Muhammad Rifai <muhammad.rifai@ctcorpdigital.com>
 *
 * Copyright © 2019 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\IntegrationCategory\Cron\Pim\Save;

use Trans\IntegrationCategory\Api\IntegrationCategoryLogicInterface;
use Trans\Integration\Api\IntegrationCommonInterface;
use Trans\IntegrationCategory\Api\IntegrationDataValueRepositoryInterface;
use Trans\IntegrationCategory\Api\IntegrationCheckUpdatesInterface;

class Category {
	/**
	 * @var \Trans\Integration\Logger\Logger
	 */
	protected $logger;

    /**
     * @var IntegrationCheckUpdatesInterface
     */
	protected $checkUpdates;

	public function __construct(
		\Trans\Integration\Logger\Logger $logger
		,IntegrationCommonInterface $commonRepository
		,IntegrationDataValueRepositoryInterface $dataValue
		,IntegrationCategoryLogicInterface $integrationCategoryLogicInterface
        ,IntegrationCheckUpdatesInterface $checkUpdates
	) {
		$this->logger                            = $logger;
		$this->commonRepository                  = $commonRepository;
		$this->dataValue                         = $dataValue;
		$this->integrationCategoryLogicInterface = $integrationCategoryLogicInterface;
        $this->checkUpdates                         = $checkUpdates;
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
			$channel = $this->commonRepository->prepareChannel('category');

			$this->logger->info("=".$class." Check Onprogress Jobs (Save Product)");
			$channel = $this->checkUpdates->checkSaveOnProgressJob($channel);

			$this->logger->info("=".$class."  Check Complete Jobs");
			$channel = $this->checkUpdates->checkReadyJobs($channel);

			$data = $this->integrationCategoryLogicInterface->prepareData($channel);			

			$this->logger->info("=".$class." Save Data");
			$category = $this->integrationCategoryLogicInterface->saveCategory($data);
			

		} catch (\Exception $ex) {

            $this->logger->error("<=".$class." ".$ex->getMessage());
		}
		$this->logger->info("<=" . $class);
	}

}
