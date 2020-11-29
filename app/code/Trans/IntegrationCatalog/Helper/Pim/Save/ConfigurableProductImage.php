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
use Magento\Framework\App\ResourceConnection;
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
   * @var ResourceConnection
   */
  protected $connection;

  /**
   * Constructor method
   * @param Context $context
   * @param Config $config
   * @param ProductFactory $productFactory
   * @param LinkManagementInterface $linkManagement
   * @param ProductImage $helper
   * @param ResourceConnection $resourceConnection
   */
  public function __construct
  (
    Context $context,
    Config $config,
    ProductFactory $productFactory,
    LinkManagementInterface $linkManagement,
    ProductImage $helper,
    ResourceConnection $resourceConnection
  ) {
    $this->config = $config;
    $this->productFactory = $productFactory;
    $this->linkManagement = $linkManagement;
    $this->aliasPrefix = AbstractCollection::ATTRIBUTE_TABLE_ALIAS_PREFIX.self::BASE_IMAGE_ATTRIBUTE;
    $this->helper = $helper;
    $this->resourceConnection = $resourceConnection;
    parent::__construct($context);
  }

  /**
   * Get configurable product without image
   *
   * @param  string $offset
   * @param  string $limit
   * @return Magento\Catalog\Model\ResourceModel\Collection
   */
  public function getConfigurableProductWithoutImage()
  {
    $product = $this->productFactory->create();
    $collection = $product->getCollection();
    $collection->addFieldToFilter('type_id',['eq'=>'configurable']);
    $collection->addAttributeToFilter([
      ['attribute' => self::BASE_IMAGE_ATTRIBUTE, 'null' => true],
      ['attribute' => self::BASE_IMAGE_ATTRIBUTE, 'eq' => 'no_selection']
    ]);
    $collection->getSelect()->limit($this->getCronConfigLength());
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
    $childProducts = $this->helper->getProductByMagentoParentId($parent->getEntityId());
    $this->helper->addImageToConfigurableProduct($parent->getId(), $childProducts->getPimId());
  }
  
  /**
   * Set configurable image
   *
   * @param int[] $entityIds
   * @param \SM\Catalog\Override\MagentoCatalog\Model\Product[] $items
   */
  public function setConfigurablesImage($entityIds, $items)
  {
    $childProducts = $this->helper->getProductByMagentoParentIdBulk($entityIds);
    $pimIds = $this->getPimIds($childProducts);
    $connection = $this->resourceConnection->getConnection();
    foreach($items as $key => $val) {
      if (array_key_exists($val->getId(), $pimIds)) {
        try {
          $pimId = $pimIds[$val->getId()];
          $integrationCatalogDataQuery = $this->helper->getProductCatalogDataByPimId($pimId);
          $data = $connection->fetchRow($integrationCatalogDataQuery);
          if ($imageUrl = $this->helper->getCatalogDataImageUrl($data)) {
            $this->helper->addImage($val, $imageUrl);
          }
        } catch (\Exception $e) {
          throw $e;
        }
      }
    }
  }
  
  /**
   * Get Pim Ids
   *
   * @param \Trans\IntegrationCatalog\Model\ResourceModel\IntegrationProduct\Collection
   * @return int[]
   */
  public function getPimIds($collection)
  {
    $results = [];
    $existingParent = [];
    foreach ($collection as $key => $val)
    {
      if(in_array($val->getMagentoParentId(), $existingParent) == false)
      {
        $results[$val->getMagentoParentId()] = $val->getPimId();
        $existingParent[] = $val->getMagentoParentId();
      }
    }
    return $results;
  }
  
  /**
   * Get cron config length
   * 
   * @return int
   */
  public function getCronConfigLength()
  {
    return $this->config->getCronConfigProductSynchLength();
  }

}
