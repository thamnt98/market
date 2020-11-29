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
    $this->logger->info(__('=> Getting Configurable Product Without Image <='));
    $productCollection = $this->imageHelper->getConfigurableProductWithoutImage();
    if ($count = $productCollection->getSize()) {
        $productItems = $productCollection->getItems();
        $this->logger->info(__('=> Start Process Configurable Product Without Image <='));
        $this->addImage($productCollection, $productItems);
    }
  }

	/**
	 * Add image
	 *
	 * @param \Magento\Catalog\Model\ResourceModel\Collection
	 * @param \SM\Catalog\Override\MagentoCatalog\Model\Product[]
	 */
  protected function addImage($collection, $items)
  {
    $entityIds = $this->getProductEntityIds($collection);
    $this->imageHelper->setConfigurablesImage($entityIds, $items);
  }
  
  /**
   * Get Product entity ids
   *
   * @param \Magento\Catalog\Model\ResourceModel\Collection
   * @return int[]
   */
  protected function getProductEntityIds($collection)
  {
    $result = [];
    foreach ($collection as $key => $value) {
      $result[] = $value->getEntityId();
    }
    return $result;
  }
}
