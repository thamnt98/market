<?php
/**
 * @category Trans
 * @package  Trans_IntegrationCustomer
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 * @author   Dwi Septha Kurniawan <septha.kurniawan@transdigital.co.id>
 *
 * Copyright © 2019 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\IntegrationCustomer\Model;
use Magento\Customer\Api\AccountManagementInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\Exception\StateException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Eav\Model\Config;

use Trans\Core\Helper\Customer;
use Trans\Integration\Api\Data\IntegrationChannelMethodInterface;
use Trans\IntegrationCustomer\Api\Data\IntegrationDataValueInterface;
use Trans\IntegrationCustomer\Api\Data\IntegrationJobInterface;

use Trans\Integration\Api\IntegrationCommonInterface;
use Trans\Integration\Api\IntegrationDatabaseInterface;
use Trans\IntegrationCustomer\Api\IntegrationJobRepositoryInterface;
use Trans\IntegrationCustomer\Api\IntegrationDataValueRepositoryInterface;

use Trans\IntegrationCustomer\Api\Data\IntegrationCustomerCentralInterface;
use Trans\IntegrationCustomer\Api\IntegrationCustomerCentralRepositoryInterface;
use Trans\IntegrationCustomer\Model\ResourceModel\IntegrationCustomerCentral\CollectionFactory;

use Trans\IntegrationCustomer\Api\IntegrationCustomerUpdateInterface;
use Magento\Framework\App\ResourceConnection;

class IntegrationCustomerUpdate implements IntegrationCustomerUpdateInterface
{
	/**
     * @var \Trans\Integration\Logger\Logger
     */
    protected $_logger;
    /**
     * @var IntegrationDatabaseInterface
     */
    protected $dbRepository;

    /**
     * @var Customer
     */
    protected $customerHelper;

    /**
     * @var IntegrationCommonInterface
     */
    protected $commonRepository;

    /**
     * @var IntegrationJobRepositoryInterface
     */
    protected $jobRepository;

    /**
     * @var IntegrationDataValueRepositoryInterface
     */
    protected $datavalueRepository;

    /**
     * @var \Magento\Customer\Api\AccountManagementInterface
     */
    protected $accountManagement;

    /**
     * @var IntegrationCustomerCentralRepositoryInterface
     */
    protected $customerCentralRepository;

    /**
     * @var \Magento\Eav\Model\Config
     */  
    protected $eavConfig;  

    /**
     * @var \Trans\IntegrationCustomer\Model\ResourceModel\IntegrationCustomerCentral\CollectionFactory
     */
    private $customerCentralCollectionFactory;
	
	/**
     * @var \Magento\Framework\App\ResourceConnection
     */
    protected $resource;

    /**
     * IntegrationCustomer constructor.
     * @param Customer $customerHelper
     * @param IntegrationCommonInterface $commonRepository
     * @param IntegrationJobRepositoryInterface $jobRepository
     * @param IntegrationDataValueRepositoryInterface $datavalueRepository
     * @param \Trans\Integration\Logger\Logger $_logger
     * @param AccountManagementInterface $accountManagement
     * @param IntegrationCustomerCentralRepositoryInterface $customerCentralRepository
     * @param \Magento\Eav\Model\Config $eavConfig
     * @param \Trans\IntegrationCustomer\Model\ResourceModel\IntegrationCustomerCentral\CollectionFactory $customerCentralCollectionFactory
     * @param \Magento\Framework\App\ResourceConnection $resource
     */

    public function __construct(
        Customer $customerHelper
        , IntegrationCommonInterface $commonRepository
        , IntegrationJobRepositoryInterface $jobRepository
        , IntegrationDataValueRepositoryInterface $datavalueRepository
        , \Trans\Integration\Logger\Logger $_logger
        , AccountManagementInterface $accountManagement
        , IntegrationCustomerCentralRepositoryInterface $customerCentralRepository
        , \Magento\Eav\Model\Config $eavConfig
        , \Trans\IntegrationCustomer\Model\ResourceModel\IntegrationCustomerCentral\CollectionFactory $customerCentralCollectionFactory
        , \Magento\Framework\App\ResourceConnection $resource
    ) {
        $this->customerHelper = $customerHelper;
        $this->commonRepository = $commonRepository;
        $this->jobRepository=$jobRepository;
        $this->datavalueRepository=$datavalueRepository;
        $this->_logger = $_logger;
        $this->accountManagement = $accountManagement;
        $this->customerCentralRepository = $customerCentralRepository;
        $this->eavConfig = $eavConfig;
        $this->customerCentralCollectionFactory = $customerCentralCollectionFactory;
        $this->resource = $resource;
    }
    /**
     * Prepare Data Update Customer
     * @param $customer
     * @return array
     */
    public function prepareDataUpdateCustomer($customer){
        $this->customerData = $customer;
        $password = NULL;
        $dataPassword = $this->getCustomerPassword();
        if(isset($dataPassword['password_hash']) && !empty($dataPassword['password_hash'])){
            $password=$dataPassword['password_hash'];
        }

        $data = [];
        $customerUpdateData = [
            "magento_customer_id" => $customer->getId(),
            "central_id" => $this->getCentralId($customer),
            "customer_name" => $this->customerHelper->generateFullnameByCustomer($customer),
            "customer_home_address" => NULL,
            "customer_phone" => $customer->getCustomAttribute('telephone')->getValue(),
            "customer_email" => $customer->getEmail(),
            "is_email_confirmed" => $this->getEmailConfirmed($customer),
            "customer_password" => $password,
            "customer_ktp_id" => $this->getKtpId($customer),
            "customer_gender" => $this->getGender($customer),
            "customer_date_of_birth" => $customer->getDob(),
            "customer_marital_status" => $this->getMaritalStatus($customer),
            "customer_job_status" => NULL,
            "customer_smi_google" => NULL,
            "customer_smi_facebook" => NULL,
            "customer_smi_twitter" => NULL,
            "customer_smi_instagram" => NULL,
            "updated_at" => $customer->getUpdatedAt(),
        ];
        $data[] = $customerUpdateData;


        return $customerUpdateData;
    }

    /**
     * Get Central Id
     * @param $customer
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function getCentralId($customer){
        $centralId = NULL;

        if($customer->getId()){
            $centralData = $this->customerCentralCollectionFactory->create()
                          ->addFieldToFilter('magento_customer_id', $customer->getId());
            if($centralData->getSize() > 0){ 
                foreach($centralData as $centralDatas)
                { 
                    $centralId = $centralDatas['central_id'];
                }
            }   
        }
        
        return $centralId;
    }

    /**
     * Get Gender
     * @param $customer
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function getGender($customer){
        $gender = NULL;

        $attribute = $this->eavConfig->getAttribute('customer', 'gender');
        $options = $attribute->getSource()->getAllOptions();

        if($customer->getGender()) {
            $gender = $customer->getGender();
            foreach ($options as $option => $value) {
                if ($value['value'] == $gender){
                    $gender = $value['label'];
                }
            }
        }
        
        return $gender;
    }

    /**
     * Get KTP ID
     * @param $customer
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function getKtpId($customer){
        $ktpId = NULL;
        if($customer->getCustomAttribute('nik')) {
            $ktpId = $customer->getCustomAttribute('nik')->getValue();
        }
        
        return $ktpId;
    }

    /**
     * Get MaritalStatus
     * @param $customer
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function getMaritalStatus($customer){
        $marital_status = NULL;

        $attribute = $this->eavConfig->getAttribute('customer', 'marital_status');
        $options = $attribute->getSource()->getAllOptions();

        if($customer->getCustomAttribute('marital_status')) {
            $marital_status = $customer->getCustomAttribute('marital_status')->getValue();
            foreach ($options as $option => $value) {
                if ($value['value'] == $marital_status){
                    $marital_status = $value['label'];
                }
            }
        }
        
        return $marital_status;
    }

    /**
     * Get Email COnfirmasi By int
     * @param $customer
     * @return int
     * @throws \Magento\Framework\Exception\LocalizedException
     */
   protected function getEmailConfirmed($customer){
       $confirmationStatus = $this->accountManagement->getConfirmationStatus($customer->getId());
       switch ($confirmationStatus) {
           case AccountManagementInterface::ACCOUNT_CONFIRMED:
               return 1;
               break;
           default:
               return 0;
               break;
       }
   }

    /**
     * Get Password From DB
     * @return mixed
     */
   protected function getCustomerPassword(){
       $connection = $this->resource->getConnection();
       $raw   = 'SELECT entity_id , password_hash FROM %s WHERE entity_id = %d';
       $query = sprintf($raw, SELF::TABLE_CUSTOMER, $this->customerData->getId());
       $hash  = $connection->fetchRow($query);
       return $hash; 
   }

    /**
     * Prepare Job
     * @param array $channel
     * @return array
     * @throws StateException
     */
    public function prepareJob($channel=[]){
        $result = [];
        if(!isset($channel['method'])){
            throw new StateException(
                __(IntegrationCustomerInterface::MSG_CHANNEL_NOTAVAILABLE)
            );
        }
        $method = $channel['method'];
        $batchId = uniqid();

        $result[IntegrationJobInterface::TOTAL_DATA] =1;
        $result[IntegrationJobInterface::METHOD_ID] = $method->getId();
        $result[IntegrationJobInterface::LIMIT] = (!empty($method->getLimits()))?$method->getLimits():IntegrationChannelMethodInterface::VAL_LIMIT;
        $result[IntegrationJobInterface::LAST_UPDATED] = NULL ;
        $result[IntegrationJobInterface::BATCH_ID]=$batchId;

        $result[IntegrationJobInterface::OFFSET]=null;
        return $result;
    }

    /**
     * Save Job
     * @param array $channel
     * @param array $data
     * @return mixed
     * @throws StateException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function saveJob($channel=[],$data=[]){
        if(!isset($channel['method'])){
            throw new StateException(
                __(IntegrationCustomerInterface::MSG_CHANNEL_NOTAVAILABLE)
            );
        }
        try {
            $collection = $this->jobRepository->getByMdIdFirstItem($channel['method']->getId(),IntegrationJobInterface::STATUS_WAITING);

            if($collection) {

                $job=$collection;
                $job->setTotalData($job->getTotalData() + $data[IntegrationJobInterface::TOTAL_DATA]);
                $this->jobRepository->save($job);

            }else{

                $job = $this->jobRepository->saveJobs($data);
            }

        } catch (\Exception $ex) {
            throw new StateException(
                __($ex->getMessage())
            );
        }
        return $job;
    }

    /**
     * Prepare Data
     * @param $jobs
     * @param $data
     * @return mixed
     * @throws StateException
     */
    public function prepareData($jobs,$data){
        if(!$jobs->getId()){
            throw new StateException(
                __(IntegrationCustomerInterface::MSG_JOB_NOTAVAILABLE)
            );
        }
        $result[IntegrationDataValueInterface::JOB_ID] = $jobs->getId();
        $result[IntegrationDataValueInterface::DATA_VALUE] = json_encode($data);
        return $result;
    }

    /**
     * Get Jobs & Data value
     * @param $jobs
     * @throws StateException
     */
    public function getJobsDataCustomerUpdate($jobs){
        $result = [];
        $collection = [];
        if(!$jobs->getId()){
            throw new StateException(
                __(IntegrationCustomerInterface::MSG_JOB_NOTAVAILABLE)
            );
        }
        try{
            $collection = $this->datavalueRepository->getByJobIdWithStatus($jobs->getId(),1);
            if($collection->getSize()){
                // generate data value
                $i=0;
                foreach($collection as $row){
                    $result[$i] = json_decode($row->getDataValue(),true);
                    $i++;
                }

            }
        } catch (\Exception $ex) {
            throw new StateException(
                __($ex->getMessage())
            );
        }

        return $collection;
    }

    /**
     * Set Job on Progress
     * @param $jobs
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function setJobOnProgress($jobs){
        $jobs->setStatus(IntegrationJobInterface::STATUS_PROGRESS_POST);
        $jobs->setStartJob(date('Y-m-d H:i:s'));
        $this->jobRepository->save($jobs);
    }

    /**
     * Set Job Failed
     * @param $jobs
     * @param $msg
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function setJobFailed($jobs,$msg){
        $jobs->setStatus(IntegrationJobInterface::STATUS_PROGRESS_FAIL);
        $jobs->setEndJob(date('Y-m-d H:i:s'));
        $jobs->setMessages($msg);
        $this->jobRepository->save($jobs);
    }

    /**
     * Set Job Failed
     * @param $jobs
     * @param $msg
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function setJobComplete($jobs){
        $date = date('Y-m-d H:i:s');
        $jobs->setStatus(IntegrationJobInterface::STATUS_COMPLETE);
        $jobs->setEndJob($date);
        $jobs->setLastUpdated($date);
        $this->jobRepository->save($jobs);
    }

    /**
     * Set Job Complete with Error
     * @param $jobs
     * @param $msg
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function setJobCompleteWithError($jobs,$response){       
        $date = date('Y-m-d H:i:s');
        $jobs->setStatus(IntegrationJobInterface::STATUS_COMPLETE_WITH_ERROR);
        $jobs->setEndJob($date);
        $jobs->setMessages(json_encode($response));
        $jobs->setLastUpdated($date);
        $this->jobRepository->save($jobs);
    }

    /**
     * Check Waiting Jobs
     * @param $channel
     * @return mixed
     * @throws StateException
     */
    public function checkWaitingJobs($channel){
        $result= "";
        try {
            $this->validateMethod($channel);
        } catch (\Exception $ex) {
            throw new StateException(
                __($ex->getMessage())
            );
        }

        $collection = $this->jobRepository->getByMdIdFirstItem($channel['method']->getId(),IntegrationJobInterface::STATUS_WAITING);

        if(!empty($collection)){
            $result = $collection;
        }else{
            throw new StateException(
                __(IntegrationCommonInterface::MSG_JOB_NOTAVAILABLE)
            );
        }

        return $result;
    }    

    /**
     * Validate Param
     * @param array $data
     * @throws StateException
     */
    protected function validateMethod($channel=[])
    {
        if(!isset($channel['method'])){
            throw new StateException(
                __(IntegrationCommonInterface::MSG_METHOD_NOTAVAILABLE)
            );
        }
        if(!$channel['method']->getChId()){
            throw new StateException(
                __(IntegrationCommonInterface::MSG_METHOD_NOTAVAILABLE)
            );
        }
    }       

    /**
     * Response Status
     * @param array $response
     * @param array $data
     * @return mixed
     */
    public function setStatusMessage($response, $data)
    {   
        $errorCodeMessage  = array(
                             '711' => 'user not found',
                             '712' => 'user detail not found', 
                             '713' => 'customer phone already exist on other user', 
                             '714' => 'customer email already exist on other user', 
                             '715' => 'customer ktp id already exist on other user');
        $errorCodeList     = array(711, 712, 713, 714, 715);

        $responseStatus    = 1;
        $responseErrorCode = 0;
        $status            = IntegrationDataValueInterface::STATUS_DATA_SUCCESS;
        $message           = '';

        if (isset($response['data'])){
            foreach ($response['data'] as $key => $value) {
                $responseStatus    = $value['status'];
                $responseErrorCode = $value['error_code'];
            }
        }

        if ($responseStatus == 0){
            $status  = IntegrationDataValueInterface::STATUS_DATA_FAIL_UPDATE;
            $message = IntegrationCustomerUpdateInterface::MSG_RESPONSE_ERROR;

            if (in_array($responseErrorCode, $errorCodeList)){
                $message = $errorCodeMessage[$responseErrorCode];
            }
        }

        $data->setMessage($message);
        $data->setStatus($status);
        $this->datavalueRepository->save($data);
    }

}
