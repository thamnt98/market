<?php
/**
 * @category Trans
 * @package  Trans_IntegrationEntity
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   Dwi Septha Kurniawan <septha.kurniawan@transdigital.co.id>
 * @author   Imam Kusuma <imam.kusuma@ctcorpdigital.com>
 *
 * Copyright © 2019 CT Corp Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\IntegrationEntity\Model;


use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\StateException;
use Magento\InventoryApi\Api\SourceRepositoryInterface;
use Magento\InventoryApi\Api\Data\SourceInterfaceFactory;
use Trans\IntegrationEntity\Api\Data\IntegrationJobInterface;
use Trans\IntegrationEntity\Api\Data\IntegrationDataValueInterface;
use Trans\IntegrationEntity\Api\IntegrationStoreInterface;
use Trans\IntegrationEntity\Api\IntegrationDataValueRepositoryInterface;
use Trans\IntegrationEntity\Api\IntegrationJobRepositoryInterface;
use Trans\IntegrationEntity\Api\IntegrationAssignSourcesInterface;
use Trans\Integration\Helper\Curl;
use Trans\Integration\Helper\Validation;

/**
 * @inheritdoc
 */
class IntegrationStore implements IntegrationStoreInterface {

	const XML_PATH_EMAIL_RECIPIENT = 'trans_email/ident_general/email';

	/**
     * @var \Magento\Framework\Mail\Template\TransportBuilder
     */
	protected $_transportBuilder;
 	
	/**
     * @var \Magento\Framework\Translate\Inline\StateInterface
     */
 	protected $inlineTranslation;
 	
 	/**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
 	protected $scopeConfig;

	/**
     * @var \Magento\Directory\Model\ResourceModel\Region\CollectionFactory
     */
 	protected $regionCollection;

	/**
     * @var SourceInterfaceFactory
     */
    private $sourceInterfaceFactory;

	/**
     * @var SourceRepositoryInterface
     */
    protected $sourceRepository;

	/**
     * @var IntegrationAssignSourcesInterface
     */
    protected $integrationAssignSources;

	/**
	 * @var IntegrationJobRepositoryInterface 
	 */
	protected $integrationJobRepositoryInterface;
	
	/**
	 * @var IntegrationDataValueRepositoryInterface 
	 */
	protected $integrationDataValueRepositoryInterface;
	
	/**
	 * @var Curl 
	 */
	protected $curl;

	/**
	 * @var Validation 
	 */
	protected $validation;

	/**
     * @param SourceInterfaceFactory $sourceInterfaceFactory
     * @param SourceRepositoryInterface $sourceRepository     
     * @param IntegrationJobRepositoryInterface $integrationJobRepositoryInterface
	 * @param IntegrationDataValueRepositoryInterface $integrationDataValueRepositoryInterface
	 * @param IntegrationAssignSourcesInterface $integrationAssignSources
	 * @param Curl $curl
	 * @param Validation $validation
	 * @param \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder
	 * @param \Magento\Directory\Model\ResourceModel\Region\CollectionFactory $regionCollection
	 * @param \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation
	 * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     */
	public function __construct
	(	
		\Magento\Framework\Mail\Template\TransportBuilder $transportBuilder,
	    \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation,
	    \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
	    \Magento\Directory\Model\ResourceModel\Region\CollectionFactory $regionCollection,
        SourceInterfaceFactory $sourceInterfaceFactory,
        SourceRepositoryInterface $sourceRepository,
		IntegrationJobRepositoryInterface $integrationJobRepositoryInterface,
		IntegrationDataValueRepositoryInterface $integrationDataValueRepositoryInterface,
		IntegrationAssignSourcesInterface $integrationAssignSources,
		Curl $curl,
		Validation $validation
	) {	
		$this->_transportBuilder 					   = $transportBuilder;
		$this->regionCollection 					   = $regionCollection;
	    $this->inlineTranslation					   = $inlineTranslation;
	    $this->scopeConfig 							   = $scopeConfig;
        $this->sourceRepository  					   = $sourceRepository;
        $this->sourceInterfaceFactory 				   = $sourceInterfaceFactory;        
		$this->curl                                    = $curl;
		$this->validation                              = $validation;
		$this->integrationDataValueRepositoryInterface = $integrationDataValueRepositoryInterface;
		$this->integrationJobRepositoryInterface       = $integrationJobRepositoryInterface;
		$this->integrationAssignSources 			   = $integrationAssignSources;
	}

	/**
     * Save Store
     * @param array $datas
     * @return mixed
     */
	public function saveStore($datas) {
		$dataStore = [];
		$result    = [];

		$checkData = array_filter($datas->getData());
		if (empty($checkData)) {
			throw new StateException(
				__(IntegrationJobInterface::MSG_DATA_NOTAVAILABLE)
			);
		}

		$jobId    = $datas->getFirstItem()->getJbId();
		$dataJobs = $this->integrationJobRepositoryInterface->getById($jobId);
		$dataJobs->setStatus(IntegrationJobInterface::STATUS_PROGRESS_SYNC);
		$this->integrationJobRepositoryInterface->save($dataJobs);

		try {
			$items = [];
			foreach ($datas as $data) {
				$dataStore         = $this->curl->jsonToArray($data->getDataValue());
				$locationCode 	   = $this->validation->validateArray(IntegrationStoreInterface::IMS_LOCATION_CODE, $dataStore);
				$sourceName   	   = $this->validation->validateArray(IntegrationStoreInterface::IMS_NAME, $dataStore);		
				$enabled 	  	   = ($this->validation->validateArray(IntegrationStoreInterface::IMS_ENABLED, $dataStore) == NULL) ? 1 : $this->validation->validateArray(IntegrationStoreInterface::IMS_ENABLED, $dataStore);
				$counttryId  	   = ($this->validation->validateArray(IntegrationStoreInterface::IMS_COUNTRY_ID, $dataStore) == NULL) ? 'ID' : $this->validation->validateArray(IntegrationStoreInterface::IMS_COUNTRY_ID, $dataStore);
				$postcode 		   = ($this->validation->validateArray(IntegrationStoreInterface::IMS_ZIPCODE, $dataStore) == NULL) ? '00000' : $this->validation->validateArray(IntegrationStoreInterface::IMS_ZIPCODE, $dataStore);
				$useDefaultCarrier = ($this->validation->validateArray(IntegrationStoreInterface::IMS_USE_DEFAULT_CARRIER_CONFIG, $dataStore) == NULL) ? 1 : $this->validation->validateArray(IntegrationStoreInterface::IMS_USE_DEFAULT_CARRIER_CONFIG, $dataStore);

				if ($locationCode != NULL && $sourceName != NULL) {
					if (strpos($locationCode, ' ') == false) {
						$regionId = $this->validation->validateArray(IntegrationStoreInterface::IMS_PROVINCE_ID, $dataStore);
						$provinceId = $regionId ? $regionId : $this->getProvinceId($this->validation->validateArray(IntegrationStoreInterface::IMS_PROVINCE, $dataStore));
						
						$inventorySource = $this->sourceInterfaceFactory->create();
		                $inventorySource->setSourceCode($locationCode);
						$inventorySource->setName($sourceName);
						$inventorySource->setEnabled($enabled);
						$inventorySource->setDescription($this->validation->validateArray(IntegrationStoreInterface::IMS_DESCRIPTION, $dataStore));
						$inventorySource->setLatitude($this->validation->validateArray(IntegrationStoreInterface::IMS_LATITUDE, $dataStore));
						$inventorySource->setLongitude($this->validation->validateArray(IntegrationStoreInterface::IMS_LONGTITUDE, $dataStore));
						$inventorySource->setCountryId($counttryId);
						$inventorySource->setRegionId($provinceId);
						$inventorySource->setRegion($this->validation->validateArray(IntegrationStoreInterface::IMS_PROVINCE, $dataStore));
						$inventorySource->setCity($this->validation->validateArray(IntegrationStoreInterface::IMS_CITY, $dataStore));
						$inventorySource->setStreet($this->validation->validateArray(IntegrationStoreInterface::IMS_ADDRESS, $dataStore));
						$inventorySource->setPostcode($postcode);
						$inventorySource->setContactName($this->validation->validateArray(IntegrationStoreInterface::IMS_CONTACT_NAME, $dataStore));
						$inventorySource->setEmail($this->validation->validateArray(IntegrationStoreInterface::IMS_EMAIL, $dataStore));
						$inventorySource->setPhone($this->validation->validateArray(IntegrationStoreInterface::IMS_PHONE, $dataStore));
						$inventorySource->setFax($this->validation->validateArray(IntegrationStoreInterface::IMS_FAX, $dataStore));
						$inventorySource->setUseDefaultCarrierConfig($useDefaultCarrier);
						$inventorySource->setHourOpen($this->validation->validateArray(IntegrationStoreInterface::IMS_HOUR_OPEN, $dataStore));
						$inventorySource->setHourClose($this->validation->validateArray(IntegrationStoreInterface::IMS_HOUR_CLOSE, $dataStore));
						$inventorySource->setDistrictId($this->validation->validateArray(IntegrationStoreInterface::IMS_DISTRICT_ID, $dataStore));
						$inventorySource->setDistrict($this->validation->validateArray(IntegrationStoreInterface::IMS_DISTRICT, $dataStore));
						$inventorySource->setCityId($this->validation->validateArray(IntegrationStoreInterface::IMS_CITY_ID, $dataStore));
					    $this->sourceRepository->save($inventorySource);
					    $this->integrationAssignSources->assignSource($locationCode);//assign source to stock
						$this->saveStatusMessage($data, '', IntegrationDataValueInterface::STATUS_DATA_SUCCESS);
					} else {
						$this->saveStatusMessage($data, 'Location Code Have Empty Space', IntegrationDataValueInterface::STATUS_DATA_FAIL_UPDATE);
						$this->sendNotifToAdmin($dataStore, 'location code have empty space');
					}

				} else {
					$this->saveStatusMessage($data, IntegrationStoreInterface::MSG_DATA_STORE_ANY_NULL, IntegrationDataValueInterface::STATUS_DATA_FAIL_UPDATE);
					$this->sendNotifToAdmin($dataStore, 'location code or name field empty (null)');
				}					
            }

		} catch (\Exception $exception) {
			$dataJobs->setMessage($exception->getMessage());
			$dataJobs->setStatus(IntegrationJobInterface::STATUS_PROGRESS_FAIL);
			$this->integrationJobRepositoryInterface->save($dataJobs);
		}

		$dataJobs->setStatus(IntegrationJobInterface::STATUS_COMPLETE);
		$this->integrationJobRepositoryInterface->save($dataJobs);
		return true;
	}

	/**
     * Send Notification to Admin
     * @param array $data
     * @param string $message
     */
	protected function sendNotifToAdmin($data, $message){
		$dataStoreName = ($this->validation->validateArray(IntegrationStoreInterface::IMS_NAME, $data) == NULL) ? '' : '"'.$this->validation->validateArray(IntegrationStoreInterface::IMS_NAME, $data).'"';
		// $requestData = json_encode($data);
		$requestData   = '';
		$storeScope    = \Magento\Store\Model\ScopeInterface::SCOPE_STORE; 
		
		$transport     = $this->_transportBuilder
			->setTemplateIdentifier('savestore_notification_admin') 
			->setTemplateOptions(
				['area' => \Magento\Framework\App\Area::AREA_FRONTEND, 
				'store' => \Magento\Store\Model\Store::DEFAULT_STORE_ID,]
		 	)
		 	->setTemplateVars(['storename' => $dataStoreName,'message' => $message, 'data' => $requestData])
		 	->setFrom('general')
		 	->addTo($this->scopeConfig->getValue(self::XML_PATH_EMAIL_RECIPIENT, $storeScope))
		 	->getTransport();
		$transport->sendMessage(); ;
		$this->inlineTranslation->resume();
	}

	/**
	 * get province id
	 *
	 * @param string $provinceName
	 * @return int|null
	 */
	protected function getProvinceId(string $provinceName)
	{
		try {
			$region = $this->regionCollection->create()
	            ->addRegionNameFilter($provinceName)
	            ->getFirstItem();

	        if ($region->getId()) {
	        	return $region->getId();
	        }
		} catch (\Exception $e) {
		}

        return null;
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
		$status    = IntegrationStoreInterface::STATUS_JOB;

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