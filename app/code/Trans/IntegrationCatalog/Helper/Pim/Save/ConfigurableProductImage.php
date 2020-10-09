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
namespace Trans\IntegrationCatalog\Helper\Pim\Save;

use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Catalog\Api\Data\ProductInterfaceFactory as ProductFactory;
use Magento\Eav\Model\Entity\Collection\AbstractCollection;
use Magento\ConfigurableProduct\Api\LinkManagementInterface;
use Trans\IntegrationCatalog\Helper\Config;
use Trans\IntegrationCatalog\Api\ConfigurableProductCronSynchRepositoryInterface as CronRepo;
use Trans\IntegrationCatalog\Api\Data\ConfigurableProductCronSynchInterface as CronInterface;
use Trans\IntegrationCatalog\Api\Data\ConfigurableProductCronSynchInterfaceFactory as CronFactory;

class ConfigurableProductImage extends AbstractHelper
{
  /**
   * @var string
   */
  const BASE_IMAGE_ATTRIBUTE = 'image';

  /**
   * @var string
   */
  protected $aliasPrefix;

  /**
   * @var \ConfigurableProductCronSynchRepositoryInterface
   */
  protected $cronRepo;

  /**
   * @var \ConfigurableProductCronSynchInterface
   */
  protected $cronInterface;

  /**
   * @var \ConfigurableProductCronSynchInterfaceFactory
   */
  protected $cronFactory;

  /**
   * @var \Config
   */
  protected $config;

  /**
   * @var \ProductInterfaceFactory
   */
  protected $productFactory;

  /**
   * @var \LinkManagementInterface
   */
  protected $linkManagement;

  /**
   * @var \ProductImage
   */
  protected $helper;

  /**
   * Constructor method
   * @param Context                 $context
   * @param CronRepo                $cronRepo
   * @param CronInterface           $cronInterface
   * @param CronFactory             $cronFactory
   * @param Config                  $config
   * @param ProductFactory          $productFactory
   * @param LinkManagementInterface $linkManagement
   * @param ProductImage            $helper
   */
  public function __construct
  (
    Context $context,
    CronRepo $cronRepo,
    CronInterface $cronInterface,
    CronFactory $cronFactory,
    Config $config,
    ProductFactory $productFactory,
    LinkManagementInterface $linkManagement,
    ProductImage $helper
  ) {
    $this->cronRepo = $cronRepo;
    $this->cronInterface = $cronInterface;
    $this->cronFactory = $cronFactory;
    $this->config = $config;
    $this->productFactory = $productFactory;
    $this->linkManagement = $linkManagement;
    $this->aliasPrefix = AbstractCollection::ATTRIBUTE_TABLE_ALIAS_PREFIX.self::BASE_IMAGE_ATTRIBUTE;
    $this->helper = $helper;
    parent::__construct($context);
  }

  /**
   * Init cron first time
   *
   * @return \Trans\IntegrationCatalog\Api\Data\ConfigurableProductCronSynchInterface
   */
	public function initCron()
	{
    $data = $this->cronFactory->create();
    return $this->resetCron($data);
	}

  /**
   * Reset cron counting
   *
   * @param \Trans\IntegrationCatalog\Api\Data\ConfigurableProductCronSynchInterface
   * @return \Trans\IntegrationCatalog\Api\Data\ConfigurableProductCronSynchInterface
   */
  public function resetCron($data)
  {
    $data->setCronName($this->config->getCronConfigProductSynchName());
    $data->setCronLength($this->config->getCronConfigProductSynchLength());
    $data->setCronOffset('0');
    $data->setLastUpdated(date("Y-m-d H:i:s"));
    return $this->saveCron($data);
  }

  /**
   * Get cron
   *
   * @return \Trans\IntegrationCatalog\Api\Data\ConfigurableProductCronSynchInterface
   */
  public function getCron()
  {
    $name = $this->config->getCronConfigProductSynchName();
    return $this->cronRepo->getByName($name);
  }

  /**
   * Save cron
   *
   * @param  \Trans\IntegrationCatalog\Api\Data\ConfigurableProductCronSynchInterface
   * @return Trans\IntegrationCatalog\Api\Data\ConfigurableProductCronSynchInterface
   */
  public function saveCron($data)
  {
    return $this->cronRepo->save($data);
  }

  /**
   * Get configurable product without image
   *
   * @param  string $offset
   * @param  string $limit
   * @return Magento\Catalog\Model\ResourceModel\Collection
   */
  public function getConfigurableProductWithoutImage($offset, $limit)
  {
    $product = $this->productFactory->create();
    $collection = $product->getCollection();
    $collection->addFieldToFilter('type_id',['eq'=>'configurable']);
    $collection->addAttributeToFilter([
      ['attribute' => self::BASE_IMAGE_ATTRIBUTE, 'null' => true],
      ['attribute' => self::BASE_IMAGE_ATTRIBUTE, 'eq' => 'no_selection']
    ]);
    $collection->getSelect()->limit($limit, $offset);
    $collection->getSelect()
      ->reset(\Zend_Db_Select::COLUMNS)
      ->columns([
        'entity_id',
        'sku',
        'type_id',
        $this->aliasPrefix.'.value as '.self::BASE_IMAGE_ATTRIBUTE
      ]);

    return $collection;
  }

  /**
   * Get total configurable product
   *
   * @return int
   */
  public function getTotalProductConfigurable()
  {
    $resource = $this->productFactory->create()->getResource();
    $connection = $resource->getConnection();
    $select = $connection->select();
    $select->from(
      ['main_table'=>$resource->getEntityTable()],
      [new \Zend_Db_Expr('COUNT(main_table.entity_id)')]
    )->where('main_table.type_id = "configurable"');
    $counts = $connection->fetchOne($select);
    return (int)$counts;
  }

  /**
   * Set image from child
   *
   * @param Magento\Catalog\Model\ResourceModel\Collection
   */
  public function setImageFromChild($parent)
  {
    $childProducts = $this->helper->getProductByMagentoParentId($parent->getId());
    $this->helper->addImageToConfigurableProduct($parent->getId(), $childProducts->getPimId());
  }
}
