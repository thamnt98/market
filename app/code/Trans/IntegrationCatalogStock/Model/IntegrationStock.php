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
        \Magento\Framework\App\ResourceConnection $resourceConnection
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
		$this->resourceConnection = $resourceConnection;

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
		$dataStock = [];
		$result    = [];

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
		// get attribute id
		$queryGetAttrIdIsFresh = "SELECT attribute_id FROM eav_attribute WHERE attribute_code = 'is_fresh' AND backend_type = 'int' AND frontend_input = 'boolean' limit 1";
		$getGetAttrIdIsFresh = $connectionCheck->fetchRow($queryGetAttrIdIsFresh);
		$queryGetAttrIdWeight = "SELECT attribute_id FROM eav_attribute WHERE attribute_code = 'weight' AND backend_type = 'decimal' AND frontend_input = 'weight' limit 1";
		$getGetAttrIdWeight = $connectionCheck->fetchRow($queryGetAttrIdWeight);
		$queryGetAttrIdSoldIn = "SELECT attribute_id FROM eav_attribute WHERE attribute_code = 'sold_in' AND backend_type = 'varchar' AND frontend_input = 'text' limit 1";
		$getGetAttrIdSoldIn = $connectionCheck->fetchRow($queryGetAttrIdSoldIn);

		try {
			// $items = [];
			// $i = 0;			
			foreach ($datas as $data) {

				$dataStock    = $this->curl->jsonToArray($data->getDataValue());
				$productSku   = $this->validation->validateArray(IntegrationStockInterface::IMS_PRODUCT_SKU, $dataStock);
        		$locationCode = $this->validation->validateArray(IntegrationStockInterface::IMS_LOCATION_CODE, $dataStock);
        		$quantity 	  = ($this->validation->validateArray(IntegrationStockInterface::IMS_QUANTITY, $dataStock) == NULL)? 0 : (int) $this->validation->validateArray(IntegrationStockInterface::IMS_QUANTITY, $dataStock);
        	    $checkSource  = $this->checkSourceExist($locationCode);
        	    $messageValue = '';

        	    // get row id
        	    $queryRowId = "select row_id FROM catalog_product_entity where sku = '".$productSku."' limit 1";
        	    $getRowId = $connectionCheck->fetchRow($queryRowId);

				// get value is fresh
        	    $queryIsFresh = "select value FROM catalog_product_entity_int where row_id = '".$getRowId['row_id']."' AND attribute_id = '".$getGetAttrIdIsFresh['attribute_id']."' limit 1";
				$getIsFresh = $connectionCheck->fetchRow($queryIsFresh);

				// get value weight
        	    $queryWeight = "select value FROM catalog_product_entity_decimal where row_id = '".$getRowId['row_id']."' AND attribute_id = '".$getGetAttrIdWeight['attribute_id']."' limit 1";
				$getWeight = $connectionCheck->fetchRow($queryWeight);

				// get value sold_in
        	    $querySoldIn = "select value FROM catalog_product_entity_varchar where row_id = '".$getRowId['row_id']."' AND attribute_id = '".$getGetAttrIdSoldIn['attribute_id']."' limit 1";
				$getSoldIn = $connectionCheck->fetchRow($querySoldIn);

        	    //Check source, if does not exist added as new source (store).
        	    if ($checkSource == NULL && strpos($locationCode, ' ') === false) {
        	    	$this->addNewSource($locationCode);
        	    	$checkSource  = $this->checkSourceExist($locationCode);
        	    	$messageValue = IntegrationStockInterface::MSG_NEW_STORE;
        	    }

				if ($productSku != NULL && $locationCode != NULL) {

					if ($checkSource != NULL) {
						// $sourceItem = $this->sourceItem->create();
		                // $sourceItem->setSku($productSku);
		                // $sourceItem->setSourceCode($locationCode);
		                // $sourceItem->setQuantity($quantity);
		                // $sourceItem->setStatus(IntegrationStockInterface::IMS_STATUS);
		                // $items = array($sourceItem);
		            	// $this->sourceItemSave->execute($items);

						if ($getIsFresh['value'] == 1) {
							if ($getSoldIn['value'] == 'kg' || $getSoldIn['value'] == 'Kg' || $getSoldIn['value'] == 'KG') {
								$qtyCalc = ($quantity * 1000) / $getWeight['value'];
								$quantity = floor($qtyCalc);
							}
						}

						// raw query set stock
						// check if sku and store exist on table inventory_source_item
						$connection = $this->resourceConnection->getConnection();
			        	// $table is table name
				        $tableName = $connection->getTableName("inventory_source_item");

						$sql = "select source_item_id, sku, source_code from " . $tableName . " where sku = '" . $productSku . "' and source_code = '" . $locationCode . "' limit 1";

						$checkSource = $connection->fetchRow($sql);
						
						if (!$checkSource) {
							//insert Data into table
							$query = "insert into " . $tableName . " (source_code, sku, quantity, status) values ('" . $locationCode . "', '" . $productSku . "', '" . $quantity . "', '" . IntegrationStockInterface::IMS_STATUS . "')";
						}
						else {
							$query = "update " . $tableName . " set quantity = '" . $quantity . "' where source_item_id = '" . $checkSource['source_item_id'] . "'";
						}

				        $connection->query($query);

						$this->saveStatusMessage($data, $messageValue, IntegrationDataValueInterface::STATUS_DATA_SUCCESS);
					}
					else {
						$this->saveStatusMessage($data, IntegrationStockInterface::MSG_NO_STORE, IntegrationDataValueInterface::STATUS_DATA_FAIL_UPDATE);

						$this->logger->info("start - store not found");
						$this->logger->info("sku: " . $productSku);
						$this->logger->info("store_code: " . $locationCode);
						$this->logger->info("quantity: " . $quantity);
						$this->logger->info("query: " . $query);
						$this->logger->info("end - store not found");
					}

				}
				else {
					$this->saveStatusMessage($data, IntegrationStockInterface::MSG_DATA_STOCK_NULL, IntegrationDataValueInterface::STATUS_DATA_FAIL_UPDATE);

					$this->logger->info("start - sku and store not provided");
					$this->logger->info("quantity: " . $quantity);
					$this->logger->info("query: " . $query);
					$this->logger->info("end - sku and store not provided");
                }
            }
            
	        $this->sourceItemHelper->stockItemReindex();

		}
		catch (\Exception $exception) {
			if ($dataJobs) {
				$dataJobs->setMessage($exception->getMessage());
				
				if ($tryHit >= IntegrationStockInterface::MAX_TRY_HIT){
					$dataJobs->setHit($tryHit);
					$dataJobs->setStatus(IntegrationJobInterface::STATUS_PROGRESS_FAIL);

					$this->logger->info("start - job exceed maximum hit to save the data");
					$this->logger->info("job_id: " . $jobId);
					$this->logger->info("max_try_hit: " . IntegrationStockInterface::MAX_TRY_HIT);
					$this->logger->info("end - job exceed maximum hit to save the data");
				}
				else {
					$dataJobs->setHit($tryHit);
					$dataJobs->setStatus(IntegrationJobInterface::STATUS_READY);

					$this->logger->info("start - job retried to hit to save the data");
					$this->logger->info("job_id: " . $jobId);
					$this->logger->info("hit: " . $tryHit);
					$this->logger->info("end - job retried to hit to save the data");
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

		return true;
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

}