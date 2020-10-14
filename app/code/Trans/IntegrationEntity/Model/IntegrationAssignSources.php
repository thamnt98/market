<?php
/**
 * @category Trans
 * @package  Trans_IntegrationEntity
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   Dwi Septha Kurniawan <septha.kurniawan@transdigital.co.id>
 *
 * Copyright © 2019 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\IntegrationEntity\Model;

use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\StateException;
use Magento\Store\Model\StoreManagerInterface;
use Magento\InventoryApi\Api\StockRepositoryInterface;
use Magento\InventoryApi\Api\SourceRepositoryInterface;
use Magento\InventoryApi\Api\Data\SourceInterfaceFactory;
use Magento\InventoryApi\Api\GetSourcesAssignedToStockOrderedByPriorityInterface;
use Magento\InventoryApi\Api\StockSourceLinksSaveInterface;
use Magento\InventoryApi\Api\Data\StockSourceLinkInterfaceFactory;
use Trans\IntegrationEntity\Api\IntegrationAssignSourcesInterface;

/**
 * @inheritdoc
 */
class IntegrationAssignSources implements IntegrationAssignSourcesInterface {

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;	

	/**
     * @var SourceInterfaceFactory
     */
    private $sourceInterfaceFactory;

	/**
     * @var GetSourcesAssignedToStockOrderedByPriorityInterface
     */
    private $getSourcesAssignedInterface;

	/**
     * @var StockSourceLinksSaveInterface
     */
    private $stockSourceLinksSaveInterface;

	/**
     * @var StockSourceLinkInterfaceFactory
     */
    private $stockSourceLinkInterface;

	/**
     * @var SourceRepositoryInterface
     */
    protected $sourceRepository;

	/**
     * @var StockRepositoryInterface
     */
    protected $stockRepository;

	/**
     * @param StoreManagerInterface $storeManager	
     * @param SourceInterfaceFactory $sourceInterfaceFactory
     * @param SourceRepositoryInterface $sourceRepository 
     * @param StockRepositoryInterface $stockRepository   
     * @param GetSourcesAssignedToStockOrderedByPriorityInterface $getSourcesAssignedInterface
	 * @param StockSourceLinksSaveInterface $stockSourceLinksSaveInterface
	 * @param StockSourceLinkInterfaceFactory $stockSourceLinkInterface
     */

	public function __construct
	(	
        StoreManagerInterface $storeManager,
        SourceInterfaceFactory $sourceInterfaceFactory,
        SourceRepositoryInterface $sourceRepository,
        GetSourcesAssignedToStockOrderedByPriorityInterface $getSourcesAssignedInterface,
     	StockSourceLinksSaveInterface $stockSourceLinksSaveInterface,
     	StockSourceLinkInterfaceFactory $stockSourceLinkInterface,
     	StockRepositoryInterface $stockRepository
	) {	
        $this->storeManager 				 = $storeManager;
        $this->sourceRepository 			 = $sourceRepository;
        $this->stockRepository 				 = $stockRepository;
        $this->SourceInterfaceFactory 		 = $sourceInterfaceFactory;        
        $this->getSourcesAssignedInterface   = $getSourcesAssignedInterface;
        $this->stockSourceLinksSaveInterface = $stockSourceLinksSaveInterface;
        $this->stockSourceLinkInterface 	 = $stockSourceLinkInterface;
	}

	/**
     * Assign Source
     * @param mixed $data
     * @return mixed
     */
	public function assignSource($data) {
		$stockIdActive 		= $this->getStockIdActive();
		$sourceLinkData   	= [];
		$sourceLinks 	  	= [];
		
		if ($stockIdActive!=NULL || $data!=NULL){
			$sourceLinks = $this->stockSourceLinkInterface->create();
			$sourceLinks->setSourceCode($data);
	        $sourceLinks->setStockId($stockIdActive);
	        $sourceLinks->setPriority(IntegrationAssignSourcesInterface::PRIORITY);
	        $sourceLinkData = array($sourceLinks);
			$this->stockSourceLinksSaveInterface->execute($sourceLinkData);	
		}

		return true;
	}

	/**
     * Assign Source Available
     * @param array 
     * @return mixed
     */
	public function assignSourceAvailable() {
		$stockIdActive 		= $this->getStockIdActive();
		$sourceAssignedList = $this->getAllAssignedSource();
		$sourceCollection 	= $this->sourceRepository->getList();
		$sourceList 	  	= [];
		$sourceLinks 	  	= [];
		
		foreach ($sourceCollection->getItems() as $value) {
			if (!in_array($value['source_code'], $sourceAssignedList)){
				$sourceList = $this->stockSourceLinkInterface->create();
				$sourceList->setSourceCode($value['source_code']);
		        $sourceList->setStockId($stockIdActive);
		        $sourceList->setPriority(IntegrationAssignSourcesInterface::PRIORITY);
		        $sourceLinks = array($sourceList);
				$this->stockSourceLinksSaveInterface->execute($sourceLinks);	
			}
		}

		return true;
	}

    /**
     * Get stock id active
     * @param string $data
     * @return mixed
     */
    public function getStockIdActive()
    {	
    	$websiteCode = $this->getWebsiteCode();
    	$stockList   = $this->stockRepository->getList();
    	$stockId 	 = NULL;

    	foreach ($stockList->getItems() as $value) {
    		$data = $value->getExtensionAttributes()->getSalesChannels();
    		
    		foreach ($data as $datas) {
	    		if ($datas) {
	    			if ($datas['code'] == $websiteCode){
	    				$stockId = $value->getStockId();	
	    			}
	    		}
	    	}
    	}

        return $stockId;
    }

    /**
     * Get all assigned source
     * @param string $data
     * @return mixed
     */
    protected function getAllAssignedSource()
    {	
    	$websiteCode 		= $this->getWebsiteCode();
    	$stockList 			= $this->stockRepository->getList();
    	$stockIdList		= NULL;
    	$sourceData 		= NULL;
    	$sourceAssignedList = [];

    	foreach ($stockList->getItems() as $value) {
    		$sourceData = $this->getSourcesAssignedInterface->execute($value->getStockId());
    		
    		foreach ($sourceData as $data) {
    			$sourceAssignedLIst[] = $data['source_code'];
    		}

 			// $stockIdList[] = $value->getStockId();		
    	}

        return $sourceAssignedLIst;
    }

    /**
     * Get website code
     *
     * @return string|null
     */
    public function getWebsiteCode(): ?string
    {	
    	$websiteCode = NULL;
        try {
            $websiteId = $this->storeManager->getDefaultStoreView()->getWebsiteId();
            $websiteCode = $this->storeManager->getWebsite($websiteId)->getCode();
        } catch (Exception $e) {
            $websiteCode = NULL;
        }

        return $websiteCode;
    }

}