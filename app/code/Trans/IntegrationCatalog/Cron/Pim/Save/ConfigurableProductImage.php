<?php
/**
 * @category Trans
 * @package  Trans_IntegrationCatalog
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   Anan Fauzi <anan.fauzi@transdigital.co.id>
 *
 * Copyright © 2019 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */
namespace Trans\IntegrationCatalog\Cron\Pim\Save;

use Magento\Framework\Exception\NoSuchEntityException;
use Trans\Integration\Logger\Logger;
use Trans\IntegrationCatalog\Helper\Pim\Save\ConfigurableProductImage as ImageHelper;

class ConfigurableProductImage {
  /**
	 * @var \Trans\Integration\Logger\Logger
	 */
	protected $logger;

  /**
   * @var \Trans\IntegrationCatalog\Helper\Pim\Save\ConfigurableProductImage
   */
  protected $imageHelper;

	/**
	 * Constructor method
	 *
	 * @param Logger      $logger      [description]
	 * @param ImageHelper $imageHelper [description]
	 */
  public function __construct(
    Logger $logger,
    ImageHelper $imageHelper
  ) {
    $this->logger = $logger;
    $this->imageHelper = $imageHelper;
  }

	/**
	 * Gateway method
	 *
	 * @return void
	 */
  public function execute()
  {
    $cron = $this->getCron();
		$this->logger->info(__('=> Getting Configurable Product <='));
    $productCollection = $this->imageHelper->getConfigurableProductWithoutImage($cron->getCronOffset(), $cron->getCronLength());
    if (count($productCollection) > 0) {
			$this->logger->info(__('=> Process Configurable Product Without Image <='));
      $this->addImageFromChild($productCollection);
    } else if((int)$cron->getCronOffset() > (int) $this->imageHelper->getTotalProductConfigurable()) {
			$this->logger->info(__('=> All Configurable Product Has an Image <='));
      $this->imageHelper->resetCron($cron);
    }
		$this->increaseCron($cron);
  }

	/**
	 * Increase cron counting
	 *
	 * @param  Trans\IntegrationCatalog\Api\Data\ConfigurableProductCronSynchInterface $cron
	 * @return Trans\IntegrationCatalog\Api\Data\ConfigurableProductCronSynchInterface
	 */
  protected function increaseCron($cron)
  {
    $newOffset = (int)$cron->getCronOffset() + (int)$cron->getCronLength();
    $cron->setCronOffset((string)$newOffset);
		$cron->setLastUpdated(date("Y-m-d H:i:s"));
    $this->imageHelper->saveCron($cron);
  }

	/**
	 * Get Cron
	 *
	 * @return Trans\IntegrationCatalog\Api\Data\ConfigurableProductCronSynchInterface
	 */
  protected function getCron()
  {
    $this->logger->info(__('=> Cron Configurable Product Image Synch Start <='));
    try {
      $cron = $this->imageHelper->getCron();
    } catch (NoSuchEntityException $e) {
      $cron = $this->imageHelper->initCron();
    } catch (\Exception $e) {
      $this->logger->info(__('=> '.$e->getMessage().' <='));
      throw new \Exception(__($e->getMessage()));
    }
    $this->logger->info(__('=> '.print_r($cron->getData(), true).' <='));

    return $cron;
  }

	/**
	 * Add image from child
	 *
	 * @param Magento\Catalog\Model\ResourceModel\Collection
	 */
  protected function addImageFromChild($collection)
  {
    foreach ($collection as $key => $value) {
			$this->logger->info(__('=> Process Configurable Product SKU '.$value->getSku().' <='));
      $this->imageHelper->setImageFromChild($value);
    }
  }
}
