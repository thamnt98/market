<?php
/**
 * @category Trans
 * @package  Trans_IntegrationCatalog
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   J.P <jaka.pondan@transdigital.co.id>
 * @author   Anan Fauzi <anan.fauzi@transdigital.co.id>
 *
 * Copyright © 2019 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\IntegrationCatalog\Cron\Pim\Save;

use Trans\Integration\Api\IntegrationCommonInterface;
use Trans\IntegrationCatalog\Api\IntegrationGetUpdatesInterface;
use Trans\IntegrationCatalog\Api\IntegrationCheckUpdatesInterface;
use Trans\IntegrationCatalog\Api\IntegrationProductImageInterface;
use Trans\IntegrationCatalog\Api\Data\IntegrationJobInterface;

class ProductImage {
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

	/**
     * @var IntegrationProductImageInterface
     */
	protected $productImage;

	/**
	* @var IntegrationJobInterface
	*/
	protected $jobInterface;

	public function __construct(
		\Trans\Integration\Logger\Logger $logger
		,IntegrationCommonInterface $commonRepository
		,IntegrationGetUpdatesInterface $getUpdates
		,IntegrationCheckUpdatesInterface $checkUpdates
		,IntegrationProductImageInterface $productImage
		,\Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone
		,IntegrationJobInterface $jobInterface
	) {
		$this->logger           = $logger;
		$this->commonRepository = $commonRepository;
		$this->getUpdates       = $getUpdates;
		$this->checkUpdates       = $checkUpdates;
		$this->timezone			= $timezone;
		$this->productImage     = $productImage;
		$this->jobInterface		= $jobInterface;

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
			$channel = $this->commonRepository->prepareChannel('product-image');

			$this->logger->info("=".$class." Check Onprogress Jobs (Save Product)");
			$channel = $this->checkUpdates->checkSaveOnProgressJob($channel);//check status = 1, && last_update not null

			$this->logger->info("=".$class." Check Complete Jobs");
			$channel = $this->checkUpdates->checkReadyJobs($channel);//check status = 30

			$this->logger->info("=".$class." Prepare Data");
			$data = $this->productImage->prepareData($channel);//status 1, on integration catalog product

			$this->logger->info("=".$class." Validate Product Data");
			$productImgBySku = $this->productImage->validateProductImage($data);//build array

			$this->logger->info("=".$class." Save Product Data");
			$result = $this->productImage->saveProductImage($channel['jobs'],$productImgBySku);

		} catch (\Exception $ex) {
			$this->logger->error("<=".$class." ".$ex->getMessage());
			return false;
		}
		$this->logger->info("<=".$class );
	}

}
