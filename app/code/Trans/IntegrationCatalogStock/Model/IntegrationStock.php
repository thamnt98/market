<?php
/**
 * @category Trans
 * @package  Trans_CatalogStock
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   Dwi Septha Kurniawan <septha.kurniawan@transdigital.co.id>
 *
 * Copyright © 2019 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
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
        SourceInterfaceFactory $sourceInterfaceFactory
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
		$tryHit   = ($hitCount == NULL)? 0 : (int)$hitCount;
		$tryHit++;

		$dataJobs->setStatus(IntegrationJobInterface::STATUS_PROGRESS_SYNC);
		$this->integrationJobRepositoryInterface->save($dataJobs);

		try {
			$items = [];
			$i=0;
			foreach ($datas as $data) {
				$dataStock    = $this->curl->jsonToArray($data->getDataValue());
				$productSku   = $this->validation->validateArray(IntegrationStockInterface::IMS_PRODUCT_SKU, $dataStock);
        		$locationCode = $this->validation->validateArray(IntegrationStockInterface::IMS_LOCATION_CODE, $dataStock);
        		$quantity 	  = ($this->validation->validateArray(IntegrationStockInterface::IMS_QUANTITY, $dataStock) == NULL)? 0 : (int)$this->validation->validateArray(IntegrationStockInterface::IMS_QUANTITY, $dataStock);
        	    $checkSource  = $this->checkSourceExist($locationCode);
        	    $messageValue = '';

        	    //Check source, if does not exist added as new source (store).
        	    if ($checkSource==NULL && strpos($locationCode, ' ')==false){
        	    	$this->addNewSource($locationCode);
        	    	$checkSource  = $this->checkSourceExist($locationCode);
        	    	$messageValue = IntegrationStockInterface::MSG_NEW_STORE;
        	    }

				if ($productSku!=NULL && $locationCode!=NULL){

					if ($checkSource!=NULL){
						$sourceItem = $this->sourceItem->create();
		                $sourceItem->setSku($productSku);
		                $sourceItem->setSourceCode($locationCode);
		                $sourceItem->setQuantity($quantity);
		                $sourceItem->setStatus(IntegrationStockInterface::IMS_STATUS);
		                $items = array($sourceItem);
		            	$this->sourceItemSave->execute($items);
						$this->saveStatusMessage($data, $messageValue, IntegrationDataValueInterface::STATUS_DATA_SUCCESS);
					} else {
						$this->saveStatusMessage($data, IntegrationStockInterface::MSG_NO_STORE, IntegrationDataValueInterface::STATUS_DATA_FAIL_UPDATE);	
					}

                } else {
					$this->saveStatusMessage($data, IntegrationStockInterface::MSG_DATA_STOCK_NULL, IntegrationDataValueInterface::STATUS_DATA_FAIL_UPDATE);
                }
            }

	        $this->sourceItemHelper->stockItemReindex();

		} catch (\Exception $exception) {
			if($dataJobs){
				$dataJobs->setMessage($exception->getMessage());
				if ($tryHit >= 3){
					$dataJobs->setHit($tryHit);
					$dataJobs->setStatus(IntegrationJobInterface::STATUS_PROGRESS_FAIL);
				} else {
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
		return true;
	}

	/**
     * Add New Store
     * @param string $locationCode
     * @return mixed
     */
	public function addNewSource($locationCode) {
		try {
			$inventorySource = $this->sourceInterfaceFactory->create();
	        $inventorySource->setSourceCode($locationCode);
			$inventorySource->setName($locationCode);
			$inventorySource->setEnabled(1);
			$inventorySource->setCountryId('ID');
			$inventorySource->setPostcode('00000');
			$inventorySource->setUseDefaultCarrierConfig(1);
		    $this->sourceRepository->save($inventorySource);
		    $this->integrationAssignSources->assignSource($locationCode);//assign source to stock
		} catch (\Exception $exception) {
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
		$sourceData 	= $this->sourceRepository->getList();		
		$checkSource 	= NULL;

    	if ($sourceData->getTotalCount()) {
        	foreach ($sourceData->getItems() as $sourceDatas) {
        		if($sourceDatas['source_code'] == $locationCode){
	        		$checkSource = $sourceDatas['source_code'];
	        	} 
	        }
        }

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