<?php
/**
 * @category Trans
 * @package  Trans_IntegrationCatalog
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   J.P <jaka.pondan@ctcorpdigital.com>
 *
 * Copyright © 2019 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\IntegrationCatalog\Model;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Exception\StateException;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\Io\File;
use Trans\Core\Helper\Data;
use Trans\IntegrationCatalog\Api\Data\IntegrationDataValueInterface;
use Trans\IntegrationCatalog\Api\Data\IntegrationDataValueInterfaceFactory;
use Trans\IntegrationCatalog\Api\Data\IntegrationJobInterface;
use Trans\IntegrationCatalog\Api\Data\IntegrationProductInterface;
use Trans\IntegrationCatalog\Api\IntegrationDataValueRepositoryInterface;
use Trans\IntegrationCatalog\Api\IntegrationJobRepositoryInterface;
use Trans\IntegrationCatalog\Api\IntegrationProductImageInterface;
use Trans\IntegrationCatalog\Api\IntegrationProductRepositoryInterface;
use Trans\IntegrationCatalog\Model\IntegrationJob;
use Trans\Integration\Helper\Validation;
use Trans\Integration\Logger\Logger;

class IntegrationProductImage implements IntegrationProductImageInterface {
	/**
	 * @var Logger
	 */
	protected $logger;
	/**
	 * @var ProductRepositoryInterface
	 */
	protected $product;
	/**
	 * @var DirectoryList
	 */
	protected $directoryList;
	/**
	 * @var DirectoryList
	 */
	protected $file;
	/**
	 * @var IntegrationProductRepositoryInterface
	 */
	protected $productRepo;
	/**
	 * @var Filesystem
	 */
	protected $filesystem;

	/**
	 * Private Class
	 */
	protected $class;

	/**
	 * @var Validation
	 */
	protected $validation;

	/**
	 * @var \Magento\Catalog\Model\Product\Gallery\Processor
	 */
	protected $galleryProcessor;

	/**
	 * @var \Magento\Catalog\Model\Product\Media\Config
	 */
	protected $mediaConfig;

	/**
	 * @var IntegrationDataValueRepositoryInterface
	 */
	protected $integrationDataValueRepositoryInterface;

	/**
	 * @var IntegrationJobRepositoryInterface
	 */
	protected $integrationJobRepositoryInterface;

	/**
	 * @var \Trans\IntegrationCatalog\Helper\Pim\Save\ProductImage
	 */
	protected $productImageHelper;

	/**
	 * @var \Magento\Framework\Indexer\IndexerRegistry
	 */
	protected $indexerRegistry;

	/**
	 * @param Logger $Logger
	 * @param ProductRepositoryInterface $product
	 * @param DirectoryList $directoryList
	 * @param File $file
	 * @param IntegrationProductRepositoryInterface $productRepo
	 * @param Filesystem $filesystem
	 * @param \Magento\Catalog\Model\Product\Media\Config $mediaConfig
	 * @param \Magento\Catalog\Model\Product\Gallery\Processor $galleryProcessor
	 * @param IntegrationDataValueRepositoryInterface $integrationDataValueRepositoryInterface
	 * @param IntegrationJobRepositoryInterface $integrationJobRepositoryInterface
	 * @param \Trans\IntegrationCatalog\Helper\Pim\Save\ProductImage $productImageHelper
	 * @param \Magento\Framework\Indexer\IndexerRegistry $indexerRegistry
	 */
	public function __construct
	(
		Logger $logger,
		Data $coreHelper,
		ProductRepositoryInterface $product,
		IntegrationJob $jobModel,
		DirectoryList $directoryList,
		File $file,
		IntegrationProductRepositoryInterface $productRepo,
		Filesystem $filesystem,
		Validation $validation,
		\Magento\Catalog\Model\Product\Media\Config $mediaConfig,
		\Magento\Catalog\Model\Product\Gallery\Processor $galleryProcessor,
		IntegrationDataValueRepositoryInterface $integrationDataValueRepositoryInterface,
		IntegrationDataValueInterfaceFactory $dataValFactory,
		IntegrationJobRepositoryInterface $integrationJobRepositoryInterface,
		\Trans\IntegrationCatalog\Helper\Pim\Save\ProductImage $productImageHelper,
		\Magento\Framework\Indexer\IndexerRegistry $indexerRegistry
	) {
		$this->coreHelper = $coreHelper;
		$this->logger = $logger;
		$this->jobModel = $jobModel;
		$this->product = $product;
		$this->directoryList = $directoryList;
		$this->file = $file;
		$this->productRepo = $productRepo;
		$this->filesystem = $filesystem;
		$this->validation = $validation;
		$this->galleryProcessor = $galleryProcessor;
		$this->mediaConfig = $mediaConfig;
		$this->integrationDataValueRepositoryInterface = $integrationDataValueRepositoryInterface;
		$this->dataValFactory = $dataValFactory;
		$this->integrationJobRepositoryInterface = $integrationJobRepositoryInterface;
		$this->productImageHelper = $productImageHelper;
		$this->indexerRegistry = $indexerRegistry;
		$this->class = str_replace(IntegrationProductImageInterface::CRON_DIRECTORY, "", get_class($this));
		$this->mediaDirectory = $filesystem->getDirectoryWrite(DirectoryList::MEDIA);

		$writer = new \Zend\Log\Writer\Stream(BP . '/var/log/integration_image.log');
        $logger = new \Zend\Log\Logger();
        $this->logger = $logger->addWriter($writer);
	}

	/**
	 * @param array $channel
	 * @return mixed
	 * @throws NoSuchEntityException
	 * @throws StateException
	 */
	public function prepareData($channel = []) {
		if (empty($channel)) {
			throw new StateException(__(
				'Parameter Channel are empty !'
			));
		}
		try {
			$jobs = $channel['jobs'];

			$jobId     = $jobs->getFirstItem()->getId();
			$jobStatus = $jobs->getFirstItem()->getStatus();
			$status    = IntegrationProductInterface::STATUS_JOB;

			$result = $this->integrationDataValueRepositoryInterface->getByJobIdWithStatus($jobId, $status);
			if ($result->getSize() < 1) {
				throw new StateException(__(__FUNCTION__ . "------ Error : Data Value is not Exist"));
			}
		} catch (\Exception $exception) {
			throw new StateException(__($exception->getMessage()));
		}

		return $result;
	}

	/**
	 * Validate Data Value
	 * @param $dataValue dataValueInterface
	 * @return $result array
	 */
	public function validateProductImage($dataValue) {
		$result = [];
		$json   = [];
		$data   = [];
		$i      = 0;
		$sku    = [];
		$no     = 0;
		foreach ($dataValue as $rowData) {
			try {
				$json[$i] = json_decode($rowData->getDataValue());
				$data[$i] = (array) $json[$i];
				// $this->logger->info("Data-Value ==>".print_r($data[$i],true));
				$sku[$i]                         = $this->validation->validateArray(IntegrationProductInterface::SKU, $data[$i]);
				$result[$sku[$i]][$i]            = $data[$i];
				$result[$sku[$i]][$i]['data_id'] = $rowData->getId();

			} catch (\Exception $exception) {
				$this->logger->info("=" . $this->class . "-" . __FUNCTION__ . " " . $exception->getMessage());
				continue;
			}
			$i++;
		}
		return $result;
	}

	protected function getSku($sku = "") {
		$result = Null;
		if (empty($sku)) {
			return $result;
		}
		try {
			$result = $this->product->get($sku);

		} catch (\Exception $exception) {
			$msg = __FUNCTION__ . " Error : " . $exception->getMessage();
			throw new StateException(__($msg));
		}
		return $result;

	}

	/**
	 * Save Product Image
	 * @param $param
	 * @return string
	 */
	public function saveProductImage($jobs = null, $productData = null)
	{
		if (is_null($jobs) || is_null($productData)) {
			$msg = $this->class . "-" . __FUNCTION__ . " Job / Product data is not available!";
			throw new StateException(__($msg));
		}

		$jobId = $jobs->getFirstItem()->getId();
		$this->updateJobData($jobId, IntegrationJobInterface::STATUS_PROGRESS_CATEGORY);

		$productIds = [];
		$product = [];
		$tmp = [];
		$index = 1;
		$dataImages = [];

		try {
			$this->logger->info('start loop ' . date('d-M-Y H:i:s'));
			foreach ($productData as $data) {
				$dataArray = $data->getData();
				try {
					$dataValue = [];
					try {
						$dataValue = json_decode($dataArray['data_value'], true); 
					} catch (\Exception $e) {
						$this->logger->info('Decode data_value fail. Error = ' . $e->getMessage());
					}

					if(empty($dataValue)) {
						$this->logger->info('data_value is empty.');
						continue;
					}

					$sku = isset($dataValue['sku']) ? $dataValue['sku'] : '';
					
					$filename = isset($dataValue['image_name']) ? $dataValue['image_name'] : '';
					$imageUrl = isset($dataValue['image_url']) ? $dataValue['image_url'] : '';
					
					$this->logger->info('start loop row ' . $index . ' ' . date('d-M-Y H:i:s'));
					try {
						$this->logger->info(__FUNCTION__ . ' SKU ' . $sku);
						$product[$index] = $this->product->get($sku);
					} catch (NoSuchEntityException $exception) {
						$this->logger->info("=" . $this->class . "-" . __FUNCTION__ . " " . $exception->getMessage());
						$product[$index] = false;
					}

					if ($product[$index]) {
						$this->deleteImage($product[$index], $filename);

						$productRowId = $product[$index]->getRowId();
						
						$no = 0;

						if($this->checkIsFirstImage($filename)) {
							$baseImageAttr = $this->productImageHelper->getAttributeIdByCode('image');
							$smallImageAttr = $this->productImageHelper->getAttributeIdByCode('small_image');
							$thumbnailImageAttr = $this->productImageHelper->getAttributeIdByCode('thumbnail');
							$imageAttr = [$baseImageAttr, $smallImageAttr, $thumbnailImageAttr];

							$this->productImageHelper->deleteProductImageByAttrIds($productRowId, $imageAttr);
						} else {
							$swatchImage = $this->productImageHelper->getAttributeIdByCode('swatch_image');
							$imageAttr = [$swatchImage];
						}

						$tmp[$index] = $this->saveImageData($product[$index], $filename, [$imageUrl], $imageAttr);

						$this->deleteTmpFile($tmp[$index]);
						try {
							$file = $tmp[$index][0];

							$filepath = $file['filename'];

							$this->productImageHelper->saveProductImage($productRowId, $filepath, $imageAttr);
							$mediaGalleryAttr = $this->productImageHelper->getAttributeIdByCode('media_gallery');

							$this->productImageHelper->insertMediaGalleryValue($mediaGalleryAttr, $filepath, $productRowId);

							$productIds[] = $product[$index]->getId();
						} catch (\Exception $e) {
							$this->logger->info("saveProductImage fail: " . $e->getMessage());
							continue;
						}

						$this->saveStatusMessage($dataArray['id'], null, IntegrationDataValueInterface::STATUS_DATA_SUCCESS);
					}

					try {
						$this->reindexByProductsIds($productIds, ['catalog_product_attribute', 'catalogsearch_fulltext']);
					} catch (\Exception $e) {
						$this->logger->info('reindex fail ' . date('d-M-Y H:i:s'));	
					}

					$this->logger->info('end loop row ' . $index . ' ' . date('d-M-Y H:i:s'));
					$this->logger->info('-----------------------------------------------------');
					$index++;
				} catch (\Exception $exception) {
					$this->logger->info("= SAVE :: " . $this->class . "-" . __FUNCTION__ . " " . $exception->getMessage());
					$this->saveStatusMessage($dataArray['id'], $exception->getMessage(), IntegrationDataValueInterface::STATUS_DATA_FAIL_UPDATE);
					$this->logger->info('end loop row ' . $index . ' ' . date('d-M-Y H:i:s'));
					$this->logger->info('-----------------------------------------------------');
					continue;
				}
			}
			
			$this->logger->info('end loop ' . date('d-M-Y H:i:s'));

		} catch (\Exception $exception) {
			$msg = $this->class . "-" . __FUNCTION__ . " " . $exception->getMessage();
			$this->updateJobData($jobId, IntegrationJobInterface::STATUS_PROGRES_UPDATE_FAIL, $msg);
			throw new StateException(__($msg));
		}

		$this->updateJobData($jobId, IntegrationJobInterface::STATUS_COMPLETE);
	}

	/**
	 * reindex bu product ids
	 *
	 * @param array $productIds
	 * @param array $indexLists
	 * @return void
	 */
	public function reindexByProductsIds($productIds, $indexLists)
    {
        foreach($indexLists as $indexList) {
            $categoryIndexer = $this->indexerRegistry->get($indexList);
            if (!$categoryIndexer->isScheduled()) {
                $categoryIndexer->reindexList(array_unique($productIds));
            }
        }
     }
	

	protected function checkIsFirstImage($filename)
	{
		$expl = explode('.', $filename);
		$filename = $expl[0];

		if(substr($filename, -2) == '-1') {
			return true;
		}

		return false;
	}

	/**
	 * Save Product Image
	 * @param $param
	 * @return string
	 */
	public function saveProductImageBackup($jobs = null, $productData = null) {
		var_dump(date('H:i:s'));
		// die();
		if (is_null($jobs) || is_null($productData)) {
			$msg = $this->class . "-" . __FUNCTION__ . " Job / Product data is not available!";
			throw new StateException(__($msg));
		}

		$jobId = $jobs->getFirstItem()->getId();
		// $this->updateJobData($jobId, IntegrationJobInterface::STATUS_PROGRESS_CATEGORY);

		$product    = [];
		$tmp        = [];
		$index          = 1;
		$dataImages = [];
		try {
			$this->logger->info('start loop ' . date('d-M-Y H:i:s'));
			foreach ($productData as $sku => $images) {
				try {
					$this->logger->info('start loop row ' . $index . ' ' . date('d-M-Y H:i:s'));
					try {
						$this->logger->info(__FUNCTION__ . ' SKU ' . $sku);
						$product[$index] = $this->product->get($sku);

					} catch (\Exception $exception) {
						$this->logger->info("=" . $this->class . "-" . __FUNCTION__ . " " . $exception->getMessage());
						$product[$index] = false;
					}

					if ($product[$index]) {
						$this->deleteImage($product[$index]);
						// $this->unsetImageGalerry($product[$index]);
						$no = 0;

						try {
							foreach ($images as $img) {
								if (isset($img['image_url']) && !empty($img['image_url'])) {
									$dataImages[$index][$no] = $img['image_url'];
									$no++;
								}
							}
						} catch (\Exception $exception) {
							$this->logger->info("=" . __FUNCTION__ . " " . $exception->getMessage());
							$this->logger->info('end loop row ' . $index . ' ' . date('d-M-Y H:i:s'));

							$msgFailed = $exception->getMessage();

							foreach ($images as $statusFailed) {
								$this->saveStatusMessage($statusFailed['data_id'], $msgFailed, IntegrationDataValueInterface::STATUS_DATA_FAIL_UPDATE);
							}

							continue;
						}

						// $this->logger->info("= test " . __FUNCTION__ . print_r($dataImages[$index], true));
						$tmp[$index] = $this->saveImageData($product[$index], $dataImages[$index]);
						$this->deleteTmpFile($tmp[$index]);
						$this->product->save($product[$index]);

						foreach ($images as $statusSuccess) {
							$this->saveStatusMessage($statusSuccess['data_id'], null, IntegrationDataValueInterface::STATUS_DATA_SUCCESS);
						}
					}

					$this->logger->info('end loop row ' . $index . ' ' . date('d-M-Y H:i:s'));
					$this->logger->info('-----------------------------------------------------');
					$index++;
				} catch (\Exception $exception) {
					$this->logger->info("= SAVE :: " . $this->class . "-" . __FUNCTION__ . " " . $exception->getMessage());
					foreach ($images as $statusSuccess) {
						$this->saveStatusMessage($statusSuccess['data_id'], $exception->getMessage(), IntegrationDataValueInterface::STATUS_DATA_FAIL_UPDATE);
					}
					$this->logger->info('end loop row ' . $index . ' ' . date('d-M-Y H:i:s'));
					$this->logger->info('-----------------------------------------------------');
					continue;
				}
			}
			$this->logger->info('end loop ' . date('d-M-Y H:i:s'));

		} catch (\Exception $exception) {
			$msg = $this->class . "-" . __FUNCTION__ . " " . $exception->getMessage();
			$this->updateJobData($jobId, IntegrationJobInterface::STATUS_PROGRES_UPDATE_FAIL, $msg);
			throw new StateException(__($msg));
		}

		$this->updateJobData($jobId, IntegrationJobInterface::STATUS_COMPLETE);
	}

	protected function remapImage($images = "") {
		$result['img']     = Null;
		$result['data_id'] = Null;
		if (empty($images)) {
			$msg = __FUNCTION__ . " Error : Images Empty!";
			throw new StateException(__($msg));
		}
		$no = 0;
		foreach ($images as $img) {
			if (isset($img['image_url']) && !empty($img['image_url'])) {
				$result['img'][$no] = $img['image_url'];
			}
			if (isset($img['data_id']) && !empty($img['data_id'])) {
				$result['data_id'][$no] = $img['data_id'];
			}
			$no++;
		}
		return $result;

	}
	/**
	 * Check Empty Array
	 *
	 * @param $array []
	 * @return bool
	 */
	protected function checkEmptyArray($array = []) {
		$cek = array_filter($array);
		if (empty($cek)) {
			return false;
		}
		return true;
	}

	/**
	 * Unset Product Image galery
	 * @param $product
	 */
	protected function unsetImageGalerry($product) {
		$existingMediaGalleryEntries = $product->getMediaGalleryEntries();
		if ($existingMediaGalleryEntries) {
			foreach ($existingMediaGalleryEntries as $key => $entry) {
				unset($existingMediaGalleryEntries[$key]);
			}
			$product->setMediaGalleryEntries($existingMediaGalleryEntries);

		}
	}

	/**
	 * Get Media Absolute Path
	 *
	 * @return string
	 */
	protected function getMediaAbsolutePath() {
		return $this->filesystem->getDirectoryRead(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA)->getAbsolutePath();
	}
	/**
	 * Media directory name for the temporary file storage
	 * pub/media/tmp
	 *
	 * @return string
	 */
	protected function getMediaDirTmpDir() {

		return $this->directoryList->getPath(DirectoryList::MEDIA) . DIRECTORY_SEPARATOR . 'tmp' . DIRECTORY_SEPARATOR;
	}

	/**
	 * Update Job data
	 * @param object $datas
	 * @param int $status
	 * @param string $msg
	 * @throw error
	 */
	protected function updateJobData($jobId = 0, $status = "", $msg = "") {

		if ($jobId < 1) {
			throw new StateException(
				__(IntegrationJobInterface::MSG_DATA_NOTAVAILABLE)
			);
		}
		try {
			$dataJobs = $this->integrationJobRepositoryInterface->getById($jobId);
			$dataJobs->setStatus($status);
			if (!empty($msg)) {
				$dataJobs->setMessages($msg);
			}
			$this->integrationJobRepositoryInterface->save($dataJobs);
		} catch (\Exception $exception) {
			$this->logger->info(__FUNCTION__ . "------ ERROR " . $exception->getMessage());
			throw new CouldNotSaveException(__("Error : Cannot Update Job data - " . $exception->getMessage()));
		}
	}

	/**
	 * Main service executor
	 *
	 * @param array $product
	 * @param array $images
	 * @return array
	 */
	protected function saveImageData($product, $filename, $images, $attrType)
	{
		$imageType = ['image', 'small_image', 'thumbnail'];
		if($attrType) {
			$imageType = $attrType;
		}

		$imgAttrAssigned = 0;
		$tmp = [];

		// exec("find " . $this->directoryList->getPath(DirectoryList::MEDIA) . " -type d -exec chmod 0777 {} +");

		$i = 0;
		$tmpDir = $this->getMediaDirTmpDir();
		$readImg = [];
		$imgName = [];
		$i = 0;

		$this->logger->info('=== START loop images url save' . date('d-M-Y H:i:s'));
		foreach ($images as $imageUrl) {
			try {
				$this->file->checkAndCreateFolder($tmpDir);

				$this->logger->info("--" . __FUNCTION__ . '  IMGURL = ' . $imageUrl);
				$imgName[$i] = $tmpDir . baseName($imageUrl);
				$this->logger->info("--" . __FUNCTION__ . '  IMG NAME = ' . $imgName[$i]);

				$readImg[$i] = $this->file->read($imageUrl, $imgName[$i]);
				$this->logger->info("--" . __FUNCTION__ . '$result = ' . json_encode($readImg[$i]));

				// if ($readImg[$i]) {
				// 	$product->addImageToMediaGallery($imgName[$i], $imageType, false, false);
				// }
				// $filename = $this->galleryProcessor->addImage($product, $imgName[$i], $imageType, false, false);
				// $product->save();

		        $fileName = \Magento\MediaStorage\Model\File\Uploader::getCorrectFileName($filename);
		        $dispersionPath = \Magento\MediaStorage\Model\File\Uploader::getDispersionPath($fileName);
		        $fileName = $dispersionPath . '/' . $fileName;

		        $fileName = $this->getNotDuplicatedFilename($fileName, $dispersionPath);

		        $destinationFile = $this->mediaConfig->getMediaPath($fileName);

				$this->mediaDirectory->copyFile(
		            $imgName[$i],
		            $destinationFile
		        );

				$tmp[$i]['tmp'] = $imgName[$i];
				$tmp[$i]['filename'] = $fileName;
				$i++;

			} catch (Exception $exception) {
				$msg = "=" . __FUNCTION__ . " " . $exception->getMessage();
				$this->logger->info($msg);
				throw new StateException(__($msg));
			}

		}

		$this->logger->info('=== END loop images url save' . date('d-M-Y H:i:s'));

		$this->logger->info("=" . __FUNCTION__ . ' Save Image ' . print_r($tmp, true));

		return $tmp;
	}

	/**
	 * Delete Tmp Product Image
	 *
	 * @param array $tmp
	 * @return bool
	 */
	protected function deleteTmpFile($tmp) {

		foreach ($tmp as $key => $row) {
			try {

				if ($row) {
					$tmpPath = $row['tmp'];
					$filepath = $row['filename'];
					
					$baseName     = basename($tmpPath);
					$firstWord    = substr($baseName, 0, 1);
					$secondWord   = substr($baseName, 1, 1);
					$pathTmpImage = str_replace($baseName, '', $tmpPath);

					if (file_exists($tmpPath) === true) {
						unlink($tmpPath);
					}

					if (file_exists($pathTmpImage . "catalog/product/" . $filepath) === true) {
						unlink($pathTmpImage . "catalog/product/" . $filepath);
					}
				}
			} catch (Exception $exception) {
				$this->logger->info("=" . $this->class . "-" . __FUNCTION__ . " " . $exception->getMessage());
			}
		}
		$this->logger->info(' Delete TMP File ' . $baseName);
	}

	/**
	 * Delete Product Image
	 *
	 * @param array $product
	 * @return bool
	 */
	public function deleteImage($product, $filename = '')
	{
		$productRowId = $product->getRowId();
		$this->logger->info('====> delete start: ' . date('H:i:s'));

		$mediaGalleryEntries = $product->getMediaGalleryEntries();
		
		$productImageDeleted = [];
		foreach ($mediaGalleryEntries as $key => $imageObject) {
			$productImage = $imageObject->getFile();
			$extract = explode('/', $productImage);

			if(is_array($extract) && $filename && $filename == end($extract)) {
				unset($mediaGalleryEntries[$key]);
				$this->logger->info('====> delete existed image: '.$this->directoryList->getPath(DirectoryList::MEDIA) .'/catalog/product' . $imageObject->getFile());
				try {
					if(file_exists($this->directoryList->getPath(DirectoryList::MEDIA) .'/catalog/product'.$imageObject->getFile())) {
						unlink($this->directoryList->getPath(DirectoryList::MEDIA) .'/catalog/product'.$imageObject->getFile());
					}

					$productImageDeleted[] = (string) $imageObject->getFile();
					
					$galleryValue = $this->productImageHelper->getMediaGalleryValue($productRowId, $imageObject->getFile());
					$this->productImageHelper->deleteMediaGalleryValue($galleryValue['value_id']);
				} catch (\Exception $e) {
					$this->logger->info($e->getMessage());
					continue;
				}
			}
			
			$this->logger->info('====> existed image deletion done');
		}
		
		if(!empty($productImageDeleted)) {
			$this->productImageHelper->deleteProductImageByValue($productRowId, $productImageDeleted);		
		}
		// $product->setMediaGalleryEntries($mediaGalleryEntries);

		// $this->product->save($product);

		$this->logger->info('====> delete end: ' . date('H:i:s'));
		return $product;
	}

	/**
	 * Save Status and Message to Integration Data Value
	 *
	 * @param IntegrationDataValueInterface $data
	 * @param string $message
	 * @param int $status
	 * @return $result
	 */
	protected function saveStatusMessageMulti($dataImage = [], $message, $status) {
		if (!is_array($dataImage)) {
			return false;
		}

		$check = array_filter($dataImage);
		if (empty($check)) {
			return false;
		}
		foreach ($dataImage as $row) {
			$data = $this->integrationDataValueRepositoryInterface->getById($row['data_id']);
			$data->setMessage($message);
			$data->setStatus($status);
			$this->integrationDataValueRepositoryInterface->save($data);
		}

	}

	/**
	 * Save Status and Message to Integration Data Value
	 *
	 * @param IntegrationDataValueInterface $data
	 * @param string $message
	 * @param int $status
	 * @return $result
	 */
	protected function saveStatusMessage($dataId = "", $message, $status) {
		if (empty($dataId)) {
			return false;
		}
		$data = $this->integrationDataValueRepositoryInterface->getById($dataId);
		$data->setMessage($message);
		$data->setStatus($status);
		$this->integrationDataValueRepositoryInterface->save($data);
	}

	/**
     * Get filename which is not duplicated with other files in media temporary and media directories
     *
     * @param string $fileName
     * @param string $dispersionPath
     * @return string
     * @since 101.0.0
     */
    protected function getNotDuplicatedFilename($fileName, $dispersionPath)
    {
        $fileMediaName = $dispersionPath . '/'
            . \Magento\MediaStorage\Model\File\Uploader::getNewFileName($this->mediaConfig->getMediaPath($fileName));
        $fileTmpMediaName = $dispersionPath . '/'
            . \Magento\MediaStorage\Model\File\Uploader::getNewFileName($this->mediaConfig->getTmpMediaPath($fileName));

        if ($fileMediaName != $fileTmpMediaName) {
            if ($fileMediaName != $fileName) {
                return $this->getNotDuplicatedFilename(
                    $fileMediaName,
                    $dispersionPath
                );
            } elseif ($fileTmpMediaName != $fileName) {
                return $this->getNotDuplicatedFilename(
                    $fileTmpMediaName,
                    $dispersionPath
                );
            }
        }

        return $fileMediaName;
    }
}
