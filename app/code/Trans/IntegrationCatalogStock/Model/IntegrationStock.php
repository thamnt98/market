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
use Magento\InventoryIndexer\Indexer\SourceItem\IndexDataBySkuListProvider;

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
	 * @var Magento\InventoryIndexer\Indexer\SourceItem\IndexDataBySkuListProvider
	 */
	protected $indexDataBySkuListProvider;

    /**
     * @var ResourceConnection
     */
	protected $dbConnection;

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
		AttributeCollectionFactory $attributeCollectionFactory,
		IndexDataBySkuListProvider $indexDataBySkuListProvider
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
		$this->indexDataBySkuListProvider			   = $indexDataBySkuListProvider;

		$this->dbConnection = $resourceConnection->getConnection();

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
		$sources = [];
		$productIds = [];
		$dataStockList = [];
		
		$monitoringStockSql = "update monitoring_stock set has_processed = 1, processed_at = current_timestamp() where writer_id in ";
		$monitoringStockLabelKey = "writer_id";
		$monitoringStockIds = [];

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
		$tryHit   = ($hitCount == null)? 0 : (int) $hitCount;
		$tryHit++;

		$dataJobs->setStatus(IntegrationJobInterface::STATUS_PROGRESS_CATEGORY);
		$this->integrationJobRepositoryInterface->save($dataJobs);

		try {			

			foreach ($datas as $data) {
				$dataStock    = $this->curl->jsonToArray($data->getDataValue());
				$productSku   = $this->validation->validateArray(IntegrationStockInterface::IMS_PRODUCT_SKU, $dataStock);
				$locationCode = $this->validation->validateArray(IntegrationStockInterface::IMS_LOCATION_CODE, $dataStock);
				$quantity 	  = ($this->validation->validateArray(IntegrationStockInterface::IMS_QUANTITY, $dataStock) == null)? 0 : (int) $this->validation->validateArray(IntegrationStockInterface::IMS_QUANTITY, $dataStock);
				$checkSource  = $this->checkSourceExist($locationCode);

				if (isset($dataStock[$monitoringStockLabelKey])) {
					$monitoringStockIds[] = $dataStock[$monitoringStockLabelKey];					
				}				

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

				$productSku = $this->validateSku($productSku, $stockData);

				if (!isset($stockData[$productSku])) {
					continue;
				}

				$checkSource = $stockData[$productSku]['checkSource'];
				$locationCode = $stockData[$productSku]['locationCode'];
				$quantity = $stockData[$productSku]['quantity'];
				$data = $stockData[$productSku]['data'];

        	    if ($checkSource == null && strpos($locationCode, ' ') === false) {
        	    	$this->addNewSource($locationCode);
        	    	$checkSource  = $this->checkSourceExist($locationCode);
        	    	$messageValue = IntegrationStockInterface::MSG_NEW_STORE;
				}

				if ($checkSource != null) {
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
							"status"=> ($quantity > 0 ? "1" : "0")
						];

					$this->logger->info("success data");
					$this->saveStatusMessage($data, $messageValue, IntegrationDataValueInterface::STATUS_DATA_SUCCESS);
				}
				else {
					$this->logger->info("failed no store data");
					$this->saveStatusMessage($data, IntegrationStockInterface::MSG_NO_STORE, IntegrationDataValueInterface::STATUS_DATA_FAIL_UPDATE);
				}				
			}

			$this->dbConnection->insertOnDuplicate("inventory_source_item", $dataStockList, ['quantity']);
			$this->reindexByStockIdAndProductsId(1, $skus);
			$this->reindexByStockIdAndProductsId(2, $skus);			

		} catch(Exception $exception){
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


		try {

			if (!empty($monitoringStockIds)) {

				$monitoringStockSql .= "(" . implode(",", $monitoringStockIds) . ")";
				$this->logger->info("monitoring_stock query: " . $monitoringStockSql);
				
				$this->logger->info("start executing monitoring_stock query");
				
				$startTime = microtime(true);				
				$monitoringQueryResult = $this->dbConnection->exec($monitoringStockSql);

				$this->logger->info("finish executing monitoring_stock query" . 
					" - result: " . $monitoringQueryResult .
					" - duration: " . (microtime(true) - $startTime) . " second");
				
			}

		}
		catch (Exception $exception) {

			$this->logger->info("failed executing monitoring_stock query");

		}


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
	
	protected function validateSku($productSku, $stockData) {
		
		$productSkuUpperCase = strtoupper($productSku);
		if (isset($stockData[$productSkuUpperCase])) {
			return $productSkuUpperCase;
		}
		
		$productSkuLowerCase = strtoupper($productSku);
		if (isset($stockData[$productSkuLowerCase])) {
			return $productSkuLowerCase;
		}

		return $productSku;

	}	

	/**
     * Add New Store
     * @param string $locationCode
     * @return mixed
     */
	public function addNewSource($locationCode) {

		try {		    
	        
			$sql = "insert ignore into `inventory_source` (`source_code`, `name`, `enabled`, `country_id`, `postcode`, `use_default_carrier_config`) values ('{$locationCode}', '{$locationCode}', 1, 'ID', '00000', 1)";

			return $this->dbConnection->exec($sql);
			      
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
		$sql = "select `source_code` from `inventory_source` where `source_code` = '{$locationCode}' limit 1";			
		return $this->dbConnection->fetchOne($sql);
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

        if (!empty($skuList)) {
			
			$this->logger->info('Before get product ' . date('d-M-Y H:i:s'));
			$startTime = microtime(true);
			
			$collection = $this->productCollectionFactory->create()->addFieldToFilter('sku', ['in' => $skuList])->addAttributeToSelect($attributeCodes);
            $collection->getSelect()->reset(\Zend_Db_Select::COLUMNS)->columns(['entity_id','sku','row_id','type_id']);
            $result = $collection->getItems();
			
			$this->logger->info('After get product ' . date('d-M-Y H:i:s')
				. " - duration: " . (microtime(true) - $startTime) . " second");

		}

        return $result;
	}

	protected function reindexByStockIdAndProductsId($stockId, $skuList){
		$this->indexDataBySkuListProvider->execute($stockId, $skuList);
	}


     /**
     * @param array $channel
     * @return array
     */
    public function prepareStockDataUsingRawQuery($channel) {

        try {            

            if (empty($channel['first_data_ready_job'])) {
                throw new StateException(
                    __(IntegrationCommonInterface::MSG_JOB_NOTAVAILABLE)
                );
			}
			
			$stockData = $this->integrationDataValueRepositoryInterface->getAllByJobIdStatusUsingRawQuery(
				$channel['first_data_ready_job']['id'], 
				IntegrationStockInterface::STATUS_JOB
			);

			if (empty($stockData)) {
		
				$updates = array();
				$updates['message'] = IntegrationJobInterface::MSG_DATA_NOTAVAILABLE;
				$updates['status'] = IntegrationJobInterface::STATUS_PROGRESS_FAIL;
				$this->integrationJobRepositoryInterface->updateUsingRawQuery($channel['first_data_ready_job']['id'], $updates);

				throw new NoSuchEntityException(__(IntegrationJobInterface::MSG_DATA_NOTAVAILABLE));

			}
	
            return $stockData;

        }
        catch (\Exception $ex) {
            throw new StateException(
                __($ex->getMessage())
            );
        }

	}


    /**
     * @param array $channel
     * @param array $data
     * @return int
     */
	public function insertStockDataUsingRawQuery($channel, $data) 
	{

		$startTime = microtime(true);

		$stockPersisted = -1;
		
		$locationCodeList = array();
		$skuList = array();
				
		$stockListInvalid = array();

		$stockListValid = array();		

		$stockCandidateIndex = -1;
		$stockCandidateList = array();
		$stockCandidatePointerList = array();

		$monitoringLabel = "writer_id";
		$monitoringIdList = [];

		$label = "upsert-stock";
		$label .= " --> ";

		$this->logger->info($label . "start");

		if (empty($channel['first_data_ready_job'])) {
			$this->logger->info($label . "error = " . IntegrationJobInterface::MSG_JOB_NOTAVAILABLE);
			$this->logger->info($label . "finish " . (microtime(true) - $startTime) . " second");

			throw new StateException(
				__(IntegrationCommonInterface::MSG_JOB_NOTAVAILABLE)
			);			
		}

		if (empty($data)) {
			$this->logger->info($label . "error = " . IntegrationJobInterface::MSG_DATA_NOTAVAILABLE);
			$this->logger->info($label . "finish " . (microtime(true) - $startTime) . " second");

			throw new StateException(
				__(IntegrationCheckUpdatesInterface::MSG_DATA_NOTAVAILABLE)
			);
		}

		$tryHit = (int) $channel['first_data_ready_job']['hit'];			
		$tryHit = ($tryHit > 0 ? $tryHit++ : 0);

        try {
				
			$updates = array();            
			$updates['status'] = IntegrationJobInterface::STATUS_PROGRESS_CATEGORY;
			$updates['message'] = null;
			$this->integrationJobRepositoryInterface->updateUsingRawQuery($channel['first_data_ready_job']['id'], $updates);
			
			foreach ($data as $dataRecord) {

				$theStockValue = $this->curl->jsonToArray($dataRecord['data_value']);

				$locationCode = null;
				if (isset($theStockValue[IntegrationStockInterface::IMS_LOCATION_CODE])) {
					$locationCode = $theStockValue[IntegrationStockInterface::IMS_LOCATION_CODE];
				}

				$productSku = null;
				if (isset($theStockValue[IntegrationStockInterface::IMS_PRODUCT_SKU])) {
					$productSku = $theStockValue[IntegrationStockInterface::IMS_PRODUCT_SKU];
				}
				
				$quantityFloat = 0;
				$quantity = 0;				
				if (isset($theStockValue[IntegrationStockInterface::IMS_QUANTITY])) {
					$quantityFloat = (float) $theStockValue[IntegrationStockInterface::IMS_QUANTITY];
					$quantity = (int) floor($quantityFloat);
					if ($quantity < 0) {
						$quantity = 0;
					}
				}

				if (empty($locationCode) || empty($productSku)) {
					$stockListInvalid[] = $dataRecord['id'];
				}
				else {
					$stockListValid[] = $dataRecord['id'];

					if (isset($theStockValue[$monitoringLabel])) {
						$monitoringIdList[] = $theStockValue[$monitoringLabel];	
					}

					if (!isset($locationCodeList[$locationCode])) {
						$locationCodeList[$locationCode] = "('{$locationCode}', '{$locationCode}', 1, 'ID', '00000', 1)";
					}
				
					if (!isset($stockCandidatePointerList[$productSku])) {
						$stockCandidatePointerList[$productSku] = array();
						$skuList[] = $productSku;
					}
					
					$stockCandidate = array(
						"source_code" => $locationCode,
						"sku" => $productSku,
						"quantity_float" => $quantityFloat,
						"quantity" => $quantity,
						"status" => ($quantity > 0 ? 1 : 0)
					);					
					$stockCandidateIndex++;
					$stockCandidateList[$stockCandidateIndex] = $stockCandidate;

					$stockCandidatePointerList[$productSku][] = $stockCandidateIndex;					
				}

			}

			if (!empty($stockListValid)) {

				$attributeCodes = ['is_fresh', 'weight', 'sold_in'];
				$productsCollection = $this->getProductByMultipleSkuAndAttributeCodes($skuList, $attributeCodes);

				foreach ($productsCollection as $productCollection) {
					$productSku = $productCollection->getSku();
					$this->logger->info($label . "sku-by-magento = {$productSku}");

					if ($productSku === NULL) {
						$this->logger->info($label . "sku-by-magento is null then skipped");
						continue;
					}

					$productSku = $this->validateSku($productSku, $stockCandidatePointerList);
					$this->logger->info($label . "sku-by-magento-validated = {$productSku}");

					if (!isset($stockCandidatePointerList[$productSku])) {
						$this->logger->info($label . "sku-by-magento-validated not-found-in-api-response = {$productSku} then skipped");
						continue;
					}

					$isFresh = $productCollection->getData('is_fresh');
					$soldIn = $productCollection->getData('sold_in');
					$weight = $productCollection->getData('weight');

					foreach ($stockCandidatePointerList[$productSku] as $idx) {
						if ($isFresh == 1) {
							if ($soldIn == 'kg' || $soldIn == 'Kg' || $soldIn == 'KG') {				
								$newQuantity = (int) floor(($stockCandidateList[$idx]['quantity_float'] * 1000) / $weight);
								$stockCandidateList[$idx]['quantity'] = $newQuantity;
								$this->logger->info($label . "sku-quantity-new-calc = " . $stockCandidateList[$idx]['quantity']);
								$stockCandidateList[$idx]['status'] = ($newQuantity > 0 ? 1 : 0);
								$this->logger->info($label . "sku-status-new = " . $stockCandidateList[$idx]['status']);
							}
						}						
					}	
				}

				$this->dbConnection->beginTransaction();

				$sql = "insert ignore into `inventory_source` (`source_code`, `name`, `enabled`, `country_id`, `postcode`, `use_default_carrier_config`) values " . implode(",", $locationCodeList);
				$this->dbConnection->exec($sql);

				$this->dbConnection->insertOnDuplicate("inventory_source_item", $stockCandidateList, ['quantity', 'status']);

				$sql =	" update `integration_catalogstock_data` " . 
						" set " .
						" `status` = " . IntegrationDataValueInterface::STATUS_DATA_SUCCESS .
						" where `id` in (" . implode(",", $stockListValid) . ")";
				$this->dbConnection->exec($sql);

				if (!empty($stockListInvalid)) {
					$sql =	" update `integration_catalogstock_data` " . 
							" set " .
							" `status` = " . IntegrationDataValueInterface::STATUS_DATA_FAIL_UPDATE . ", " . 
							" `message` = '" . IntegrationStockInterface::MSG_DATA_STOCK_NULL . "' " .
							" where `id` in (" . implode(",", $stockListInvalid) . ")";
					$this->dbConnection->exec($sql);

					$sql =	" update `integration_catalogstock_job` " . 
							" set " .
							" `status` = " . IntegrationJobInterface::STATUS_PARTIAL_COMPLETE . ", " . 
							" `message` = '" . IntegrationJobInterface::MSG_DATA_PARTIALLY_AVAILABLE . "' " .
							" where `id` = " . $channel['first_data_ready_job']['id'];
					$this->dbConnection->exec($sql);
				}
				else {
					$sql =	" update `integration_catalogstock_job` " . 
							" set " .
							" `status` = " . IntegrationJobInterface::STATUS_COMPLETE . ", " . 
							" `message` = null" .
							" where `id` = " . $channel['first_data_ready_job']['id'];
					$this->dbConnection->exec($sql);
				}

				$this->dbConnection->commit();

				$this->reindexByStockIdAndProductsId(1, $skuList);
				$this->reindexByStockIdAndProductsId(2, $skuList);

				$stockPersisted = $stockCandidateIndex + 1;
				
			}
			else {

				$this->dbConnection->beginTransaction();

				$sql =	" update `integration_catalogstock_data` " . 
						" set " .
						" `status` = " . IntegrationDataValueInterface::STATUS_DATA_FAIL_UPDATE . ", " . 
						" `message` = '" . IntegrationStockInterface::MSG_DATA_STOCK_NULL . "' " .
						" where `id` in (" . implode(",", $stockListInvalid) . ")";
				$this->dbConnection->exec($sql);

				$sql =	" update `integration_catalogstock_job` " . 
						" set " .
						" `status` = " . IntegrationJobInterface::STATUS_PROGRESS_FAIL . ", " . 
						" `message` = '" . IntegrationJobInterface::MSG_DATA_NOTAVAILABLE . "' " .
						" where `id` = " . $channel['first_data_ready_job']['id'];
				$this->dbConnection->exec($sql);

				$this->dbConnection->commit();

			}

        }
		catch (\Exception $ex) {

			if ($tryHit >= IntegrationStockInterface::MAX_TRY_HIT) {

				try {

					$updates = array();            
					$updates['status'] = IntegrationJobInterface::STATUS_PROGRESS_FAIL;
					$updates['hit'] = $tryHit;
					$this->integrationJobRepositoryInterface->updateUsingRawQuery($channel['first_data_ready_job']['id'], $updates);

					$this->logger->info($label . "job-exceeding-max-try-hit = " . IntegrationStockInterface::MAX_TRY_HIT);
					$this->logger->info($label . "job-hit = {$tryHit}");
					$this->logger->info($label . "job = " . print_r($channel['first_data_ready_job'], true));					
		
				}
				catch (\Exception $ex1) {

					$this->logger->info($label . "finish " . (microtime(true) - $startTime) . " second");

					throw new StateException(
						__($ex1->getMessage())
					);

				}

			}
			else {

				try {

					$updates = array();
					$updates['status'] = IntegrationJobInterface::STATUS_READY;
					$updates['hit'] = $tryHit;
					$this->integrationJobRepositoryInterface->updateUsingRawQuery($channel['first_data_ready_job']['id'], $updates);

					$this->logger->info($label . "job-increasing-hit");
					$this->logger->info($label . "job-hit = {$tryHit}");
					$this->logger->info($label . "job = " . print_r($channel['first_data_ready_job'], true));					
		
				}
				catch (\Exception $ex2) {

					$this->logger->info($label . "finish " . (microtime(true) - $startTime) . " second");

					throw new StateException(
						__($ex2->getMessage())
					);

				}

			}


			$this->logger->info($label . "finish " . (microtime(true) - $startTime) . " second");

			throw new StateException(
				__($ex->getMessage())
			);

		}		


		try {
			if (!empty($monitoringIdList)) {
				$sql = "update monitoring_stock set has_processed = 1, processed_at = current_timestamp() where writer_id in (" . implode(",", $monitoringIdList) . ")";
				
				$this->logger->info($label . "start executing monitoring-stock query");				
				$st = microtime(true);

				$res = $this->dbConnection->exec($sql);

				$this->logger->info($label . "finish executing monitoring-stock query" . 
					" - result: " . $res .
					" - duration: " . (microtime(true) - $st) . " second");				
			}
		}
		catch (\Exception $exmon) {
			$this->logger->info($label . "failed executing monitoring-stock query");
		}		


		$this->logger->info($label . "stock-data persisted = {$stockPersisted}");
		$this->logger->info($label . "finish " . (microtime(true) - $startTime) . " second");


		return $stockPersisted;

	}
	

}