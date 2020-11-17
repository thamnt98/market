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
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Filesystem\Io\File;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Store\Model\StoreManagerInterface;
use Trans\IntegrationCatalog\Api\Data\IntegrationProductInterfaceFactory;
use Trans\IntegrationCatalog\Model\ResourceModel\IntegrationProduct\CollectionFactory;
use Trans\IntegrationCatalog\Api\Data\IntegrationDataValueInterfaceFactory as IntegrationCatalogDataFactory;

class ProductImage extends AbstractHelper
{
  /**
   * @var \Magento\Framework\Serialize\Serializer\Json
   */
  protected $json;

  /**
   * @var \Trans\IntegrationCatalog\Api\Data\IntegrationProductInterfaceFactory
   */
  protected $integrationProductFactory;

  /**
   * @var \Trans\IntegrationCatalog\Api\Data\IntegrationDataValueInterfaceFactory
   */
  protected $integrationCatalogDataFactory;

  /**
   * @var \Magento\Catalog\Api\ProductRepositoryInterface
   */
  protected $productRepo;

  /**
   * @var \Magento\Framework\Filesystem\Io\File
   */
  protected $file;

  /**
   * @var \Magento\Framework\App\Filesystem\DirectoryList
   */
  protected $directoryList;

  /**
   * @var \Trans\IntegrationCatalog\Model\ResourceModel\IntegrationProduct\CollectionFactory
   */
  protected $integrationProductCollection;

  protected $storeManager;

  /**
   * Constructor method
   * @param Context                            $context
   * @param Json                               $json
   * @param ProductRepositoryInterface         $productRepo
   * @param File                               $file
   * @param DirectoryList                      $directoryList
   * @param IntegrationProductInterfaceFactory $integrationProductFactory
   * @param IntegrationCatalogDataFactory      $integrationCatalogDataFactory
   * @param IntegrationCatalogDataFactory      $integrationCatalogDataFactory
   * @param \Trans\IntegrationCatalog\Model\ResourceModel\IntegrationProduct\CollectionFactory $integrationProductCollection
   */
  public function __construct(
    Context $context,
    Json $json,
    ProductRepositoryInterface $productRepo,
    File $file,
    DirectoryList $directoryList,
    IntegrationProductInterfaceFactory $integrationProductFactory,
    IntegrationCatalogDataFactory $integrationCatalogDataFactory,
    CollectionFactory $integrationProductCollection,
    StoreManagerInterface $storeManager
  ) {
    $this->json = $json;
    $this->integrationProductFactory = $integrationProductFactory;
    $this->productRepo = $productRepo;
    $this->file = $file;
    $this->directoryList = $directoryList;
    $this->integrationCatalogDataFactory = $integrationCatalogDataFactory;
    $this->integrationProductCollection = $integrationProductCollection;
    $this->storeManager = $storeManager;
    $this->storeManager->setCurrentStore(0);
    parent::__construct($context);
  }

  /**
   * Add configurable product image
   *
   * @param string $jsonProduct
   */
  public function addConfigurableProductImage($jsonProduct)
  {
    $data = $this->json->unserialize($jsonProduct);
    $collection = $this->integrationProductCollection->create();
    $collection->addFieldToFilter('pim_id',['eq' => $data['id']]);
    $collection->setPageSIze(1);

    $productCollection = $collection->getFirstItem();
    if (empty($productCollection->getData()) == false && $configurableId = $productCollection->getMagentoParentId()) {
        $childProducts = $this->getProductByMagentoParentId($configurableId);
        $this->addImageToConfigurableProduct($configurableId, $childProducts->getPimId());
    }
  }

  /**
   * Get integration product image collection
   *
   * @return Trans\IntegrationCatalog\Model\ResourceModel\IntegrationProduct\Collection
   */
  public function getIntegrationProductCollection()
  {
    $integrationModel = $this->integrationProductFactory->create();
    return $integrationModel->getCollection();
  }

  /**
   * Get integration product by pim id
   *
   * @param  Trans\IntegrationCatalog\Model\ResourceModel\IntegrationProduct\Collection $collection
   * @param  string                                                                     $pimId
   * @return Trans\IntegrationCatalog\Model\ResourceModel\IntegrationProduct\Collection
   */
  public function getProductByPimId($collection, $pimId)
  {
    $collection->addFieldToFilter('pim_id',['eq'=>$pimId]);
    return $collection->getFirstItem();
  }

  /**
   * Get integration product by Magento entity id
   *
   * @param  \Trans\IntegrationCatalog\Model\ResourceModel\IntegrationProduct\Collection $collection
   * @param  string $entityId
   * @return \Trans\IntegrationCatalog\Model\ResourceModel\IntegrationProduct\Collection
   */
  public function getProductByMagentoEntityId($collection, $entityId)
  {
    $collection->addFieldToFilter('magento_entity_id',['eq'=>$entityId]);
    return $collection->getFirstItem();
  }

  public function getProductByMagentoParentId($entityId)
  {
    $collection = $this->integrationProductCollection->create();
    $collection->addFieldToFilter('magento_parent_id',['eq'=>$entityId])->setOrder('pim_id','ASC')->setPageSize(1);
    return $collection->getFirstItem();
  }

  /**
   * Get configurable product child by configurable product entity id
   *
   * @param  string $entityId
   * @return \Magento\Catalog\Model\Product[]
   */
  public function getConfigurableChildById($collection, $entityId)
  {
    return $this->getProductByMagentoParentId($collection, $entityId);
  }

  /**
   * Add image to configurable product
   *
   * @param string $configurableEntityId
   * @param string $sourcePimId
   */
  public function addImageToConfigurableProduct($configurableEntityId, $sourcePimId)
  {
    $configurableObj = $this->productRepo->getById($configurableEntityId);

    $catalogData = $this->getProductCatalogDataByPimId($sourcePimId);
    if ($imageUrl = $this->getCatalogDataImageUrl($catalogData)) {
      $this->addImage($configurableObj, $imageUrl);
    }
  }

  /**
   * Get integration catalog data by pim id
   *
   * @param  string $pimId
   * @return \Trans\IntegrationCatalog\Model\ResourceModel\IntegrationDataValue\Collection
   */
  public function getProductCatalogDataByPimId($pimId)
  {
    $catalogData = $this->integrationCatalogDataFactory->create();
    $collection = $catalogData->getCollection();
    $collection->addFieldToFilter('data_value', ['like'=>'%"id":"'.$pimId.'"%']);
    $collection->setPageSize(1);
    return $collection->getFirstItem();
  }

  /**
   * Get image url from itegration catalog data
   *
   * @param  \Trans\IntegrationCatalog\Model\ResourceModel\IntegrationDataValue\Collection $catalogData
   * @return string
   */
  public function getCatalogDataImageUrl($catalogData)
  {
    if ($catalogData->getData()) {
      $data = $this->json->unserialize($catalogData['data_value']);
      return (isset($data['image_url']))? $data['image_url'] : '';
    }
  }

  /**
   * Add image
   *
   * @param \Magento\Catalog\Model\Product $product
   * @param string $imageUrl
   */
  public function addImage($product, $imageUrl)
  {
    $imageType = ['image', 'small_image', 'thumbnail'];
    $tmpDir  = $this->getMediaDirTmpDir();
    $this->file->checkAndCreateFolder($tmpDir);
    $imgName = $tmpDir . baseName($imageUrl);
    $result = $this->file->read($imageUrl, $imgName);

    if ($result) {
        /** add saved file to the $product gallery */
        $product->addImageToMediaGallery($imgName, $imageType, false, false);
        $this->productRepo->save($product);
    }
  
    $this->deleteTmpFile($imgName);
  }

  /**
   * Get media temp dir
   *
   * @return string
   */
  protected function getMediaDirTmpDir() {

		return $this->directoryList->getPath(DirectoryList::MEDIA) . DIRECTORY_SEPARATOR . 'tmp' . DIRECTORY_SEPARATOR;
	}

  /**
   * Delete temp image file
   *
   * @param  string $imgName
   * @return void
   */
  public function deleteTmpFile($imgName)
  {
    $baseName     = basename($imgName);
    $firstWord    = substr($baseName, 0, 1);
    $secondWord   = substr($baseName, 1, 1);
    $pathTmpImage = str_replace($baseName, '', $imgName);

    if (file_exists($imgName) === true) {
      unlink($imgName);
    }

    if (file_exists($pathTmpImage . "catalog/product/" . $firstWord . "/" . $secondWord . "/" . $baseName) === true) {
      unlink($pathTmpImage . "catalog/product/" . $firstWord . "/" . $secondWord . "/" . $baseName);
    }
  }
}
