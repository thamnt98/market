<?php
/**
 * @category Trans
 * @package  Trans_IntegrationCatalogStock
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   Hariadi <hariadi_wicaksana@transretail.co.id>
 *
 * Copyright © 2020 PT Trans Retail Indonesia. All rights reserved.
 * http://carrefour.co.id
 */

namespace Trans\IntegrationCatalogStock\Model;


use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\StateException;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\InventoryApi\Api\Data\SourceItemInterfaceFactory;
use Magento\InventoryApi\Api\Data\SourceInterfaceFactory;
use Magento\InventoryApi\Api\SourceRepositoryInterface;
use Magento\InventoryApi\Api\SourceItemsSaveInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Trans\IntegrationCatalogStock\Api\Data\IntegrationJobInterface;
use Trans\IntegrationCatalogStock\Api\Data\IntegrationDataValueInterface;
use Trans\IntegrationCatalogStock\Api\IntegrationStockInterface;
use Trans\IntegrationCatalogStock\Api\IntegrationDataValueRepositoryInterface;
use Trans\IntegrationCatalogStock\Api\IntegrationJobRepositoryInterface;
use Trans\IntegrationEntity\Api\IntegrationAssignSourcesInterface;
use Trans\Integration\Helper\Curl;
use Trans\Integration\Helper\Validation;
use Trans\Core\Helper\SourceItem;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as ProductCollectionFactory;
use Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory as AttributeCollectionFactory;

/**
 * @inheritdoc
 */
class IntegrationStock implements IntegrationStockInterface {
	
	/**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;	

	/**
	 * @var SourceItemInterfaceFactory
	 */
    protected $sourceItem;
    
    /**
	 * @var SourceItemsSaveInterface
	 */
	protected $sourceItemSave;

	/**
     * @var SourceRepositoryInterface
     */
    protected $sourceRepository;

	/**
	 * @var IntegrationJobRepositoryInterface 
	 */
	protected $integrationJobRepositoryInterface;
	
	/**
	 * @var IntegrationDataValueRepositoryInterface 
	 */
	protected $integrationDataValueRepositoryInterface;

	/**
	 * @var ProductRepositoryInterface 
	 */
	protected $productRepository;

	/**
     * @var IntegrationAssignSourcesInterface
     */
    protected $integrationAssignSources;
	
	/**
	 * @var Curl 
	 */
	protected $curl;

	/**
	 * @var Validation 
	 */
	protected $validation;

	/**
	 * @var SourceItem 
	*/
	protected $sourceItemHelper;

	/**
     * @var SourceInterfaceFactory
     */
    private $sourceInterfaceFactory;

    /**
     * @var Magento\Framework\App\ResourceConnection
     */
    protected $resourceConnection;

    /**
	 * @var \Magento\Framework\Indexer\IndexerRegistry
	 */
	protected $indexerRegistry;

	/**
	 * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
	 */
	protected $productCollectionFactory;

	/**
	 * @var Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory
	 */
	protected $attributeCollectionFactory;

	/**
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param SourceItemsSaveInterface $sourceItemSave
     * @param SourceItemInterfaceFactory $sourceItem
     * @param ProductRepositoryInterface $productRepository
     * @param IntegrationJobRepositoryInterface $integrationJobRepositoryInterface
	 * @param IntegrationDataValueRepositoryInterface $integrationDataValueRepositoryInterface
	 * @param IntegrationAssignSourcesInterface $integrationAssignSources
	 * @param Curl $curl
	 * @param Validation $validation
	 * @param SourceItem $sourceItemHelper
     * @param SourceInterfaceFactory $sourceInterfaceFactory
     * @param Magento\Framework\App\ResourceConnection $resourceConnection
     * @param \Magento\Framework\Indexer\IndexerRegistry $indexerRegistry
     */
	public function __construct
	(	
        SearchCriteriaBuilder $searchCriteriaBuilder,
		SourceItemInterfaceFactory $sourceItem,
        SourceItemsSaveInterface $sourceItemSave,
        SourceRepositoryInterface $sourceRepository,
        ProductRepositoryInterface $productRepository,
		IntegrationJobRepositoryInterface $integrationJobRepositoryInterface,
		IntegrationDataValueRepositoryInterface $integrationDataValueRepositoryInterface,
		IntegrationAssignSourcesInterface $integrationAssignSources,
		Curl $curl,
		Validation $validation,
		SourceItem $sourceItemHelper,
        SourceInterfaceFactory $sourceInterfaceFactory,
        \Magento\Framework\App\ResourceConnection $resourceConnection,
		\Magento\Framework\Indexer\IndexerRegistry $indexerRegistry,
		ProductCollectionFactory $productCollectionFactory,
		AttributeCollectionFactory $attributeCollectionFactory
	) {	

        $this->sourceItem 							   = $sourceItem;
        $this->sourceItemSave 						   = $sourceItemSave;
        $this->sourceRepository  					   = $sourceRepository;
        $this->searchCriteriaBuilder 				   = $searchCriteriaBuilder;   
        $this->productRepository 					   = $productRepository;
        $this->sourceItemHelper						   = $sourceItemHelper;
		$this->curl                                    = $curl;
		$this->validation                              = $validation;
		$this->integrationDataValueRepositoryInterface = $integrationDataValueRepositoryInterface;
		$this->integrationJobRepositoryInterface       = $integrationJobRepositoryInterface;
        $this->sourceInterfaceFactory 				   = $sourceInterfaceFactory; 
		$this->integrationAssignSources 			   = $integrationAssignSources;
		$this->resourceConnection 					   = $resourceConnection;
		$this->indexerRegistry 						   = $indexerRegistry;
		$this->productCollectionFactory 			   = $productCollectionFactory;
		$this->attributeCollectionFactory 			   = $attributeCollectionFactory;

		$writer = new \Zend\Log\Writer\Stream(BP . '/var/log/integration_catalog_stock_model.log');
        $logger = new \Zend\Log\Logger();
        $this->logger = $logger->addWriter($writer);
	}

	/**
     * Save Stock
     * @param array $datas
     * @return mixed
     */
	public function saveStock($datas) {
		$productIds = [];
		$dataStockList = [];

		$messageValue = '';

		$checkData = array_filter($datas->getData());
		if (empty($checkData)) {
			throw new StateException(
				__(IntegrationJobInterface::MSG_DATA_NOTAVAILABLE)
			);
		}

		$jobId    = $datas->getFirstItem()->getJbId();
		$dataJobs = $this->integrationJobRepositoryInterface->getById($jobId);
		$hitCount = $dataJobs->getHit();
		$tryHit   = ($hitCount == NULL)? 0 : (int) $hitCount;
		$tryHit++;

		$dataJobs->setStatus(IntegrationJobInterface::STATUS_PROGRESS_CATEGORY);
		$this->integrationJobRepositoryInterface->save($dataJobs);

		$connectionCheck = $this->resourceConnection->getConnection();

		try{
			foreach ($datas as $data) {
				$dataStock    = $this->curl->jsonToArray($data->getDataValue());
				$productSku   = $this->validation->validateArray(IntegrationStockInterface::IMS_PRODUCT_SKU, $dataStock);
				$locationCode = $this->validation->validateArray(IntegrationStockInterface::IMS_LOCATION_CODE, $dataStock);
				$quantity 	  = ($this->validation->validateArray(IntegrationStockInterface::IMS_QUANTITY, $dataStock) == NULL)? 0 : (int) $this->validation->validateArray(IntegrationStockInterface::IMS_QUANTITY, $dataStock);
				$checkSource  = $this->checkSourceExist($locationCode);

				$stockData[$productSku] = array(
					'productSku' => $productSku,
					'locationCode' => $locationCode,
					'quantity' => $quantity,
					'checkSource' => $checkSource,
					'data' => $data
				);
			}

			$attributeCodes = ['is_fresh', 'weight', 'sold_in'];
			$skus = array_keys($stockData);
			$productsCollection = $this->getProductByMultipleSkuAndAttributeCodes($skus, $attributeCodes);

			foreach($productsCollection as $productCollection){
				$rowId = $productCollection->getRowId();
				$productSku = $productCollection->getSku();

				$isFresh = $productCollection->getData('is_fresh');
				$weight = $productCollection->getData('weight');
				$soldIn = $productCollection->getData('sold_in');

				if(isset($stockData[strtoupper($productSku)])){
					$productSku = strtoupper($productSku);
				}else if(isset($stockData[strtolower($productSku)])){
					$productSku = strtolower($productSku);
				}
				
				$checkSource = $stockData[$productSku]['checkSource'];
				$locationCode = $stockData[$productSku]['locationCode'];
				$quantity = $stockData[$productSku]['quantity'];
				$data = $stockData[$productSku]['data'];

        	    if ($checkSource == NULL && strpos($locationCode, ' ') === false) {
        	    	$this->addNewSource($locationCode);
        	    	$checkSource  = $this->checkSourceExist($locationCode);
        	    	$messageValue = IntegrationStockInterface::MSG_NEW_STORE;
				}
				
				if ($productSku != NULL && $locationCode != NULL) {
					if ($checkSource != NULL) {
						if ($isFresh == 1) {
							if ($soldIn == 'kg' || $soldIn == 'Kg' || $soldIn == 'KG') {
								$quantity = $this->validation->validateArray(IntegrationStockInterface::IMS_QUANTITY, $dataStock);
								$qtyCalc = ($quantity * 1000) / $weight;
								$quantity = floor($qtyCalc);
							}
						}

						$dataStockList[] =
		                    [
		                        "source_code"=>"".$locationCode."",
		                        "sku"=>"".$productSku."",
		                        "quantity"=>"".$quantity."",
		                        "status"=>"1"
		                    ];

						$productIds[] = $productCollection->getId();
						$this->logger->info("success data");
						$this->saveStatusMessage($data, $messageValue, IntegrationDataValueInterface::STATUS_DATA_SUCCESS);
					}
					else {
						$this->logger->info("failed no store data");
						$this->saveStatusMessage($data, IntegrationStockInterface::MSG_NO_STORE, IntegrationDataValueInterface::STATUS_DATA_FAIL_UPDATE);
					}
				}
				else {
					$this->logger->info("failed stock null data");
					$this->saveStatusMessage($data, IntegrationStockInterface::MSG_DATA_STOCK_NULL, IntegrationDataValueInterface::STATUS_DATA_FAIL_UPDATE);
                }
			}

			$connectionCheck->insertOnDuplicate("inventory_source_item", $dataStockList, ['quantity']);

			$this->reindexByProductsIds($productIds, ['inventory', 'cataloginventory_stock']);
		}catch(Exception $exception){
			$this->logger->info("error : " . $exception->getMessage());
			if ($dataJobs) {
				$dataJobs->setMessage($exception->getMessage());
				
				if ($tryHit >= IntegrationStockInterface::MAX_TRY_HIT){
					$dataJobs->setHit($tryHit);
					$dataJobs->setStatus(IntegrationJobInterface::STATUS_PROGRESS_FAIL);
				}
				else {
					$dataJobs->setHit($tryHit);
					$dataJobs->setStatus(IntegrationJobInterface::STATUS_READY);
				}

				$this->integrationJobRepositoryInterface->save($dataJobs);
			}

			throw new CouldNotSaveException(__(
				$exception->getMessage()
			));
		}

		$dataJobs->setHit($tryHit);
		$dataJobs->setStatus(IntegrationJobInterface::STATUS_COMPLETE);

		$this->integrationJobRepositoryInterface->save($dataJobs);

		return $dataStockList;
	}

	/**
	 * reindex bu product ids
	 *
	 * @param array $productIds
	 * @param array $indexLists
	 * @return void
	 */
	protected function reindexByProductsIds($productIds, $indexLists)
    {
        foreach($indexLists as $indexList) {
            $stockIndexer = $this->indexerRegistry->get($indexList);
            if (!$stockIndexer->isScheduled()) {
                $stockIndexer->reindexList(array_unique($productIds));
            }
        }
    }

	/**
     * Add New Store
     * @param string $locationCode
     * @return mixed
     */
	public function addNewSource($locationCode) {
		try {
			// $inventorySource = $this->sourceInterfaceFactory->create();
	        // $inventorySource->setSourceCode($locationCode);
			// $inventorySource->setName($locationCode);
			// $inventorySource->setEnabled(1);
			// $inventorySource->setCountryId('ID');
			// $inventorySource->setPostcode('00000');
			// $inventorySource->setUseDefaultCarrierConfig(1);
		    // $this->sourceRepository->save($inventorySource);
		    // $this->integrationAssignSources->assignSource($locationCode);//assign source to stock

		    // raw sql
		    $connection = $this->resourceConnection->getConnection();
        	// $table is table name
	        $tableName = $connection->getTableName("inventory_source");
	        
	        //Insert Data into table
			$query = "insert into " . $tableName . " (source_code, name, enabled, country_id, postcode, use_default_carrier_config) Values ('" . $locationCode . "', '" . $locationCode . "', '1', 'ID', '00000', '1')";

	        $connection->query($query);
	        
		}
		catch (\Exception $exception) {
			throw new CouldNotSaveException(__(
				$exception->getMessage()
			));
		}
	}

	/**
     * Check Store Exist
     * @param $locationCode
     * @return mixed
     */
	protected function checkSourceExist($locationCode) {
		// $sourceData 	= $this->sourceRepository->getList();		
		// $checkSource 	= NULL;

  //   	if ($sourceData->getTotalCount()) {
  //       	foreach ($sourceData->getItems() as $sourceDatas) {
  //       		if($sourceDatas['source_code'] == $locationCode){
	 //        		$checkSource = $sourceDatas['source_code'];
	 //        	} 
	 //        }
  //       }

		// raw query check source exist
		$checkSource = NULL;

		$connection = $this->resourceConnection->getConnection();
        // $table is table name
	    $tableName = $connection->getTableName('inventory_source');
		$sql = "Select source_code FROM " . $tableName ." where source_code = '".$locationCode."' limit 1";
		$checkSource = $connection->fetchOne($sql);

        return $checkSource;
	}

	/**
     * Save Status & Message Data Value
     */
	public function saveStatusMessage($data, $message, $status) {
		$data->setMessage($message);
		$data->setStatus($status);
		$this->integrationDataValueRepositoryInterface->save($data);
	}

	/**
     * Prepare Data
     * @param array $channel
     * @return mixed
     */
	public function prepareData($channel = []) {
		if (empty($channel)) {
			throw new StateException(__(
				'Parameter Channel are empty !'
			));
		}
		$jobs      = $channel['jobs'];
		$jobId     = $jobs->getFirstItem()->getId();
		$jobStatus = $jobs->getFirstItem()->getStatus();
		$status    = IntegrationStockInterface::STATUS_JOB;

		if ($jobStatus != IntegrationJobInterface::STATUS_READY) {
			throw new NoSuchEntityException(__('Data already updated'));
		}
		$result = $this->integrationDataValueRepositoryInterface->getByJobIdWithStatus($jobId, $status);
		if (!$result) {
			throw new NoSuchEntityException(__('Requested Data Config doesn\'t exist'));
		}

		$checkResult = array_filter($result->getData());
		if (empty($checkResult)){
			$dataJobs = $this->integrationJobRepositoryInterface->getById($jobId);
			$dataJobs->setMessage(IntegrationJobInterface::MSG_DATA_NOTAVAILABLE);
			$dataJobs->setStatus(IntegrationJobInterface::STATUS_PROGRESS_FAIL);
			$this->integrationJobRepositoryInterface->save($dataJobs);
			throw new NoSuchEntityException(__(IntegrationJobInterface::MSG_DATA_NOTAVAILABLE));
		}

		return $result;
	}

	/**
     * Get product by multiple sku
     */
    protected function getProductByMultipleSku($skuList)
    {
        $result = [];
        if (empty($skuList) == false) {
            $this->logger->info('Before get product ' . date('d-M-Y H:i:s'));
            $collection = $this->productCollectionFactory->create()->addFieldToFilter('sku', ['in'=>$skuList]);
            $collection->getSelect()->reset(\Zend_Db_Select::COLUMNS)->columns(['entity_id','sku','row_id','type_id']);
            $result = $collection->getItems();
            $this->logger->info('After get product ' . date('d-M-Y H:i:s'));
        }
        return $result;
	}

	/**
     * Get product by multiple sku
     */
    protected function getProductByMultipleSkuAndAttributeCodes($skuList, $attributeCodes)
    {
        $result = [];
        if (empty($skuList) == false) {
            $this->logger->info('Before get product ' . date('d-M-Y H:i:s'));
            $collection = $this->productCollectionFactory->create()->addFieldToFilter('sku', ['in'=>$skuList])->addAttributeToSelect($attributeCodes);
            $collection->getSelect()->reset(\Zend_Db_Select::COLUMNS)->columns(['entity_id','sku','row_id','type_id']);
            $result = $collection->getItems();
            $this->logger->info('After get product ' . date('d-M-Y H:i:s'));
        }
        return $result;
	}

}