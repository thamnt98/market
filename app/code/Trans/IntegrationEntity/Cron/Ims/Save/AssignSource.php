<?php
/**
 * @category Trans
 * @package  Trans_IntegrationEntity
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   Dwi Septha Kurniawan <septha.kurniawan@transdigital.co.id>
 *
 * Copyright © 2019 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\IntegrationEntity\Cron\Ims\Save;

use Trans\IntegrationEntity\Api\IntegrationAssignSourcesInterface;
use Trans\Integration\Api\IntegrationCommonInterface;
use Trans\IntegrationEntity\Api\IntegrationCheckUpdatesInterface;

class AssignSource extends \Magento\Framework\App\Action\Action{

	/**
	 * @var \Trans\Integration\Logger\Logger
	 */
	protected $logger;

	public function __construct(
		\Trans\Integration\Logger\Logger $logger
		,IntegrationCommonInterface $commonRepository
		,IntegrationAssignSourcesInterface $integrationAssignSourcesInterface
        ,IntegrationCheckUpdatesInterface $checkUpdates
	) {
		$this->logger                   		 = $logger;
		$this->commonRepository        		     = $commonRepository;
		$this->IntegrationAssignSourcesInterface = $integrationAssignSourcesInterface;
        $this->checkUpdates 					 = $checkUpdates;
	}

	/**
	 * Write to integrations.log
	 *
	 * @return void
	 */
	public function execute() {
        $class = str_replace(IntegrationCheckUpdatesInterface::CRON_DIRECTORY,"",get_class($this));
		try {

			$this->logger->info("=>".$class);

			$this->logger->info("=".$class." Assign Ready Sources");
			$response = $this->IntegrationAssignSourcesInterface->assignSourceAvailable();
			
			$this->logger->info(print_r(json_encode($response),true));

		} catch (\Exception $ex) {

			$this->logger->error("<=End ".$class." ".$ex->getMessage());
		}
		$this->logger->info("<=End ".$class);
	}

}
