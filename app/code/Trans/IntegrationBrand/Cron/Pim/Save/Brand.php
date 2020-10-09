<?php
/**
 * @category Trans
 * @package  Trans_Brand
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   Muhammad Rifai <muhammad.rifai@ctcorpdigital.com>
 * @author   Anan Fauzi<anan.fauzi@transdigital.co.id>
 * @author   HaDi <ashadi.sejati@transdigital.co.id>
 *
 * Copyright © 2019 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\IntegrationBrand\Cron\Pim\Save;


use Trans\Integration\Api\IntegrationCommonInterface;
use Trans\IntegrationBrand\Api\IntegrationCheckUpdatesInterface;
use Trans\IntegrationBrand\Api\IntegrationBrandLogicInterface;


/**
 * Class \Trans\IntegrationBrand\Cron\Pim\Save\Brand
 */
class Brand {
  /**
   * @var \Trans\Integration\Logger\Logger
  */
  protected $logger;

  /**
   * @var IntegrationCommonInterface
   */
  protected $commonRepository;

  /**
   * @var IntegrationCheckUpdatesInterface
   */
  protected $checkUpdates;

  /**
   * @var IntegrationBrandLogicInterface
   */
  protected $integrationBrandLogic;


  /**
   * Constructor method
   * @param T\rans\Integration\Logger\Logger     $logger
   * @param IntegrationCommonInterface           $commonRepository
   * @param IntegrationCheckUpdatesInterface     $checkUpdates
   * @param IntegrationBrandLogicInterface       $integrationBrandLogic
   */
  public function __construct(
    \Trans\Integration\Logger\Logger $logger
    ,IntegrationCommonInterface $commonRepository
    ,IntegrationCheckUpdatesInterface $checkUpdates
    ,IntegrationBrandLogicInterface $integrationBrandLogic
  ) {
    $this->logger                   = $logger;
    $this->commonRepository         = $commonRepository;
    $this->checkUpdates        = $checkUpdates;
    $this->integrationBrandLogic  = $integrationBrandLogic;

    $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/integration_brand.log');
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
          $channel = $this->commonRepository->prepareChannel('brand');

          $this->logger->info("=".$class." Check Onprogress Jobs (Save Product)");
          $channel = $this->checkUpdates->checkSaveOnProgressJob($channel);

          $this->logger->info("=".$class."  Check Complete Jobs");
          $channel = $this->checkUpdates->checkReadyJobs($channel);

          $this->logger->info("=".$class."  Prepare Jobs Data Value");
          $data = $this->integrationBrandLogic->prepareData($channel);

          $this->logger->info("=".$class." Save Data");
          $result = $this->integrationBrandLogic->save($data);

        } catch (\Exception $ex) {
                $this->logger->info("<=".$class." ".$ex->getMessage());
        }
        $this->logger->info("<=" . $class);
  }

}
