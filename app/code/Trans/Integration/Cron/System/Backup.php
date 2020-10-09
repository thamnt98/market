<?php
/**
 * @category Trans
 * @package  Trans_Integration
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   Muhammad Rifai <muhammad.rifai@ctcorpdigital.com>
 *
 * Copyright © 2019 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\Integration\Cron\System;

use Trans\Integration\Api\IntegrationCommonInterface;

class Backup {
	/**
	 * @var \Trans\Integration\Logger\Logger
	 */
	protected $logger;
    protected $commonRepository;


	public function __construct(
		\Trans\Integration\Logger\Logger $logger,
		IntegrationCommonInterface $commonRepository

	) {
		$this->logger = $logger;
        $this->commonRepository = $commonRepository;
	}

	/**
	 * Write to system.log
	 *
	 * @return void
	 */
	public function execute() {
		$this->logger->info("===>" . __CLASS__);
		try {



		} catch (\Exception $ex) {

			$this->logger->error($ex->getMessage());
		}
		$this->logger->info("<===" . __CLASS__);
	}

}
