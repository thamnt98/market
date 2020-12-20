<?php
/**
 * @category Trans
 * @package  Trans_CatalogStock
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   J.P <jaka.pondan@ctcorpdigital.com>
 *
 * Copyright © 2019 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\IntegrationCatalogStock\Model;

use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\Exception\StateException;
use Magento\Framework\Exception\NoSuchEntityException;

use Trans\Integration\Helper\Curl;
use Trans\Integration\Api\IntegrationCommonInterface;

use \Trans\Integration\Api\Data\IntegrationChannelInterface;
use \Trans\Integration\Api\Data\IntegrationChannelInterfaceFactory;
use Trans\Integration\Api\IntegrationChannelRepositoryInterface;

use \Trans\Integration\Api\Data\IntegrationChannelMethodInterface;
use \Trans\Integration\Api\Data\IntegrationChannelMethodInterfaceFactory;
use Trans\Integration\Api\IntegrationChannelMethodRepositoryInterface;


use Trans\IntegrationCatalogStock\Api\IntegrationJobRepositoryInterface;
use Trans\IntegrationCatalogStock\Api\Data\IntegrationJobInterface;
use Trans\IntegrationCatalogStock\Api\Data\IntegrationJobInterfaceFactory;

use Trans\IntegrationCatalogStock\Api\IntegrationDataValueRepositoryInterface;
use Trans\IntegrationCatalogStock\Api\Data\IntegrationDataValueInterface;
use Trans\IntegrationCatalogStock\Api\Data\IntegrationDataValueInterfaceFactory;

use Trans\IntegrationCatalogStock\Api\IntegrationGetUpdatesInterface;


class IntegrationGetUpdates implements IntegrationGetUpdatesInterface
{
    /**
     * @var Curl Zend Client
     */
    protected $curl;

    /**
     * @var IntegrationChannelInterfaceFactory
     */
    protected $channelRepository;

    /**
     * @var IntegrationChannelMethodInterfaceFactory
     */
    protected $methodRepository;

    /**
     * @var IntegrationJobInterfaceFactory
     */
    protected $jobFactory;

    /**
     * @var IntegrationJobRepositoryInterface
     */
    protected $jobRepository;

    /**
     * @var IntegrationCommonInterface
     */
    protected $commonRepository;

    /**
     * @var IntegrationDataValueRepositoryInterface
     */
    protected $datavalueRepository;

    /**
     * @var IntegrationDataValueInterfaceFactory
     */
    protected $datavalueInterface;

     /**
     * @var TimezoneInterface
     */
    protected $timezone;

    /**
     * @param integrationJobInterfaceFactory $integrationJobInterface
     */
    public function __construct(
        Curl $curl
        ,IntegrationChannelRepositoryInterface $channelRepository
        ,IntegrationChannelMethodRepositoryInterface $methodRepository
        ,IntegrationJobRepositoryInterface $jobRepository
        ,IntegrationJobInterfaceFactory $jobFactory
        ,IntegrationCommonInterface $commonRepository
        ,IntegrationDataValueRepositoryInterface $datavalueRepository
        ,IntegrationDataValueInterfaceFactory $datavalueInterface
        ,\Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone

    ) {
        $this->curl=$curl;
        $this->channelRepository=$channelRepository;
        $this->methodRepository=$methodRepository;
        $this->jobRepository=$jobRepository;
        $this->jobFactory=$jobFactory;
        $this->commonRepository=$commonRepository;
        $this->datavalueRepository=$datavalueRepository;
        $this->datavalueInterface=$datavalueInterface;
        $this->timezone			= $timezone;
    }

    /**
     * @param $channel
     * @return mixed
     * @throws StateException
     */
    public function checkWaitingJobs($channel){

        if(!isset($channel['method'])){
            throw new StateException(
                __(IntegrationCommonInterface::MSG_METHOD_NOTAVAILABLE)
            );
        }

        try {
            $collection = $this->jobRepository->getByMdIdFirstItem($channel['method']->getId(),IntegrationJobInterface::STATUS_WAITING);

            if(!empty($collection)){
                $channel['waiting_jobs'] = $collection;
                $channel['last_jobs'] = false;

                if (!empty($channel['waiting_jobs']->getLastJbId()))
                {
                    // get last job
                    $collectionLastJob = $this->jobRepository->getByIdMdIdlastItem($channel['waiting_jobs']->getLastJbId(),$channel['method']->getId(),IntegrationJobInterface::STATUS_COMPLETE);
                    if(!empty($collectionLastJob)) {
                        $channel['last_jobs'] = $collectionLastJob;
                    }
                }

            } else{
                throw new StateException(
                    __(IntegrationCommonInterface::MSG_METHOD_NOTAVAILABLE)
                );
            }

        } catch (\Exception $ex) {
            throw new StateException(
                __(IntegrationGetUpdatesInterface::MSG_DATA_NOTAVAILABLE)
            );
        }

        return $channel;
    }

    /**
     * Prepare parameter for API
     * @param array $channel
     * @return mixed
     * @throws StateException
     */
    public function prepareCall($channel=[]){

        $result[IntegrationChannelInterface::URL] = $channel['channel']->getUrl().$channel['method']->getDataPath();
        $result[IntegrationChannelMethodInterface::QUERY_PARAMS]  = $this->curl->jsonToArray($channel['method']->getQueryParams());

        $dateNow = date('Y-m-d H:i:s',strtotime('-1 day'));
        $result[IntegrationChannelMethodInterface::QUERY_PARAMS]['_modified_at'] = $dateNow;

        if($channel['jobs']->getSize()){
//            print $channel['jobs']->getLastItem()->getId()."====".$channel['waiting_jobs']->getId();
            if($channel['jobs']->getLastItem()->getBatchId() != $channel['waiting_jobs']->getBatchId()){
                
                // API date format / TBD format
                $dateFormat = date('Y-m-d H:i:s',strtotime($channel['jobs']->getLastItem()->getLastUpdated()));
                // $dateFormat = date('Y/m/d H:i:s',strtotime($channel['jobs']->getLastItem()->getLastUpdated()));
                // Greather than equal format param / TBD Format
                $paramGte = '$gte.';
                // Set last updated for API if not initial call
                $result[IntegrationChannelMethodInterface::QUERY_PARAMS]['_modified_at'] = $dateFormat;
            }
            if ($channel['last_jobs']) {
                // API date format / TBD format
                $dateFormat = date('Y-m-d H:i:s',strtotime($channel['last_jobs']->getLastUpdated()));
                // Set last updated for API if not initial call
                $result[IntegrationChannelMethodInterface::QUERY_PARAMS]['_modified_at'] = $dateFormat;
            }

        }
        // Set Limit
        $result[IntegrationChannelMethodInterface::QUERY_PARAMS]['_limit'] =$channel['waiting_jobs']->getLimits();

        // Set offset
        $result[IntegrationChannelMethodInterface::QUERY_PARAMS]['_offset'] =$channel['waiting_jobs']->getOffset();

        $result[IntegrationChannelMethodInterface::HEADERS]  = $this->curl->jsonToArray($channel['method']->getDataHeaders());
        return $result;
    }

    /**
     * Call API
     * @param string $data
     * @return mixed|string
     * @throws StateException
     */
    public function call($data=""){

        $response = $this->curl->get(
            $data[IntegrationChannelInterface::URL] ,
            $data[IntegrationChannelMethodInterface::HEADERS],
            $data[IntegrationChannelMethodInterface::QUERY_PARAMS]
        );
        $response = $this->curl->jsonToArray($response);

        return $response;

    }

    /**
     * Set data for Save
     * @param array $channel
     * @param array $response
     * @param string $date
     * @return array|mixed
     * @throws StateException
     */
    public function prepareData($channel=[],$response=[]){
        $result = [];
        if(!isset($channel['method'])){
            throw new StateException(
                __(IntegrationGetUpdatesInterface::MSG_CHANNEL_NOTAVAILABLE)
            );
        }

        $jobs = $channel['waiting_jobs'];
        $data = $response;

        if(!is_array($data)){
            throw new StateException(
                __(IntegrationGetUpdatesInterface::MSG_RESP_NOTAVAILABLE)
            );
        }
        $checkResp = array_filter($data);
        if(empty($checkResp)){
            throw new StateException(
                __(IntegrationGetUpdatesInterface::MSG_RESP_NOTAVAILABLE)
            );
        }
        try{
            for($i=0;$i<count($data);$i++){
                $result[$i][IntegrationDataValueInterface::JOB_ID] = $jobs->getId();
                $result[$i][IntegrationDataValueInterface::DATA_VALUE] = json_encode($data[$i]);
            }

        } catch (\Exception $ex) {
            throw new StateException(
            __($ex->getMessage())
            );
        }

        return $result;
    }

    /**
     * Set data for Save
     * @param array $channel
     * @param array $response
     * @param string $date
     * @return array|mixed
     * @throws StateException
     */
    public function prepareDataProperResp($channel=[],$response=[]){
        $result = [];
        if(!isset($channel['method'])){
            throw new StateException(
                __(IntegrationGetUpdatesInterface::MSG_CHANNEL_NOTAVAILABLE)
            );
        }

        $jobs = $channel['waiting_jobs'];


        if(!isset($response['data'])){
            throw new StateException(
                __(IntegrationGetUpdatesInterface::MSG_RESP_NOTAVAILABLE)
            );
        }

        $check = array_filter($response['data']);
        if(empty($check)){
            throw new StateException(
                __(IntegrationGetUpdatesInterface::MSG_RESP_NOTAVAILABLE)
            );
        }
        $data = $response['data'];

        try{
            for($i=0;$i<count($data);$i++){
                $result[$i][IntegrationDataValueInterface::JOB_ID] = $jobs->getId();
                $result[$i][IntegrationDataValueInterface::DATA_VALUE] = json_encode($data[$i]);
            }

        } catch (\Exception $ex) {
            throw new StateException(
                __($ex->getMessage())
            );
        }

        return $result;
    }

    /**
     * Save
     * @param array $data
     * @return array|mixed
     * @throws StateException
     */
    public function save($channel=[],$data=[],$lastUpdated=""){


        $checkData = array_filter($data);
        if(empty($checkData)){
            throw new StateException(
                __(IntegrationCheckUpdatesInterface::MSG_DATA_NOTAVAILABLE)
            );
        }
        
        $channel['waiting_jobs']->setLastUpdated($lastUpdated);
        $channel['waiting_jobs']->setStatus(IntegrationJobInterface::STATUS_PROGRESS_GET);

        $start = $this->timezone->date(new \DateTime())->format('Y-m-d H:i:s');
        $channel['waiting_jobs']->setStartJob($start);

        $this->jobRepository->save($channel['waiting_jobs']);

        try{
            foreach($data as $row){
                $this->datavalueRepository->saveDataValue($row);
            }
            try {
                $lastRow = end($data);
                $dataValue = json_decode($lastRow['data_value'], true); 
                $channel['waiting_jobs']->setLastUpdated($dataValue['updated_time']);
            } catch (\Exception $e) {
            }
        } catch (\Exception $ex) {
            throw new StateException(
                __($ex->getMessage())
            );
        }
        $channel['waiting_jobs']->setStatus(IntegrationJobInterface::STATUS_READY);
        $end = $this->timezone->date(new \DateTime())->format('Y-m-d H:i:s');
        $channel['waiting_jobs']->setEndJob($end);

        $this->jobRepository->save($channel['waiting_jobs']);
        return "";

    }

    /**
     * Prepare parameter for API
     * @param array $channel
     * @return mixed
     * @throws StateException
     */
    public function preparePostCall($channel=[],$body){

        $result[IntegrationChannelInterface::URL] = $channel['channel']->getUrl().$channel['method']->getDataPath();
        $result[IntegrationChannelMethodInterface::QUERY_PARAMS]  = $this->curl->jsonToArray($channel['method']->getQueryParams());

        $result[IntegrationChannelMethodInterface::HEADERS]  = $this->curl->jsonToArray($channel['method']->getDataHeaders());
        $result[IntegrationChannelMethodInterface::BODY]  = $body;
        return $result;
    }

    /**
     * Set data for Save
     * @param array $channel
     * @param array $response
     * @param string $date
     * @return array|mixed
     * @throws StateException
     */
    public function setResponseData($channel=[],$response=[]){

        $result = [];
        if(!isset($channel['method'])){
            throw new StateException(
                __(IntegrationGetUpdatesInterface::MSG_CHANNEL_NOTAVAILABLE)
            );
        }

        if(!isset($response['data'])){
            throw new StateException(
                __(IntegrationGetUpdatesInterface::MSG_RESP_NOTAVAILABLE)
            );
        }

        // $result['method'] = $channel['method'];
        $result['data']= $response['data'];
        $result['message']= $response['message'];
    
        
        return $result;
    }

    /**
     * @param array $channel
     * @return mixed
     */
    public function getFirstWaitingJobUsingRawQuery($channel) {

        try {

            if (empty($channel['method'])) {
                throw new StateException(
                    __(IntegrationCommonInterface::MSG_METHOD_NOTAVAILABLE)
                );
            }
    
            $job = $this->jobRepository->getFirstByMdIdStatusUsingRawQuery(
                $channel['method']['id'],
                IntegrationJobInterface::STATUS_WAITING
            );

            if (empty($job)) {
                throw new StateException(
                    __("Aborting Because First-Waiting-Job Is Not Available")
                );
            }

            $channel['first_waiting_job'] = $job;                

            if (!empty($channel['first_waiting_job']['last_jb_id'])) {
                $lastCompleteJobFromWaitingJob = $this->jobRepository->getByIdMdIdStatusUsingRawQuery(
                    $channel['first_waiting_job']['last_jb_id'],
                    $channel['method']['id'],
                    IntegrationJobInterface::STATUS_COMPLETE
                );

                if (!empty($lastCompleteJobFromWaitingJob)) {
                    $channel['last_complete_job_from_waiting_job'] = $lastCompleteJobFromWaitingJob;
                }
            }

            return $channel;

        }
        catch (\Exception $ex) {
            throw new StateException(
                __($ex->getMessage())
            );
        }

    }


    /**
     * @param array $channel
     * @return array
     */
    public function prepareCallUsingRawQuery($channel) {        

        try {
         
            if (empty($channel['method'])) {
                throw new StateException(
                    __(IntegrationCommonInterface::MSG_METHOD_NOTAVAILABLE)
                );
            }
    
            if (empty($channel['channel'])) {
                throw new StateException(
                    __(IntegrationCommonInterface::MSG_CHANNEL_NOTAVAILABLE)
                );
            }
    
            $result = array();
    
            $result[IntegrationChannelInterface::URL] = $channel['channel']['url'] . $channel['method']['path'];
            $result[IntegrationChannelMethodInterface::HEADERS] = $this->curl->jsonToArray($channel['method']['headers']);
            $result[IntegrationChannelMethodInterface::QUERY_PARAMS] = $this->curl->jsonToArray($channel['method']['query_params']);        
            $result[IntegrationChannelMethodInterface::QUERY_PARAMS]['_limit'] = $channel['first_waiting_job']['limit'];
            $result[IntegrationChannelMethodInterface::QUERY_PARAMS]['_offset'] = $channel['first_waiting_job']['offset'];

            if (!empty($channel['last_complete_job_from_waiting_job'])) {
                $modifiedAt = date('Y-m-d H:i:s', strtotime($channel['last_complete_job_from_waiting_job']['last_updated']));
            }
            else if (!empty($channel['last_complete_job'])) {                
                $modifiedAt = date('Y-m-d H:i:s', strtotime($channel['last_complete_job']['last_updated']));
            }
            else {
                $magentoTime = $this->timezone->date(new \DateTime())->format('Y-m-d H:i:s');
                $modifiedAt = date('Y-m-d H:i:s', strtotime('-1 day', strtotime($magentoTime)));
            }

            $result[IntegrationChannelMethodInterface::QUERY_PARAMS]['_modified_at'] = $modifiedAt;
    
            return $result;

        }
        catch (\Exception $ex) {
            throw new StateException(
                __($ex->getMessage())
            );
        }

    }
    

     /**
     * @param array $channel
     * @param array $response
     * @return array
     */
    public function prepareStockDataUsingRawQuery($channel, $response) {

        try {   

            if (empty($channel['first_waiting_job'])) {
                throw new StateException(
                    __(IntegrationCommonInterface::MSG_JOB_NOTAVAILABLE)
                );
            }
    
            if (!is_array($response['data']) || empty($response['data'])) {
                throw new StateException(
                    __(IntegrationCommonInterface::MSG_DATA_NOTAVAILABLE)
                );
            }

            $return = [];
            $result = [];
            $i = -1;
    
            foreach ($response['data'] as $data) {
                $result[$i][IntegrationDataValueInterface::JOB_ID] = $channel['first_waiting_job']['id'];
                $result[$i][IntegrationDataValueInterface::DATA_VALUE] = json_encode($data);
                $i++;
            }

            $return['data'] = $result;
            $return['last_updated'] = $data[$i]['updated_time'];

            return $return;

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
    public function insertStockDataUsingRawQuery($channel, $data) {

        if (empty($channel['first_waiting_job'])) {
            throw new StateException(
                __(IntegrationCommonInterface::MSG_JOB_NOTAVAILABLE)
            );
        }

        if (!is_array($data) || empty($data)) {
            throw new StateException(
                __(IntegrationCheckUpdatesInterface::MSG_DATA_NOTAVAILABLE)
            );
        }
    
    
        try {

            $dataNumber = -1;            

            $updates = array();
            $updates['status'] = IntegrationJobInterface::STATUS_PROGRESS_GET;
            $updates['start_job'] = $this->timezone->date(new \DateTime())->format('Y-m-d H:i:s');
            $this->jobRepository->updateUsingRawQuery($channel['first_waiting_job']['id'], $updates);

            $dataNumber = $this->datavalueRepository->insertBulkUsingRawQuery($data['data']);

            $updates = array();
            $updates['last_updated'] = $data['last_updated'];
            $updates['status'] = IntegrationJobInterface::STATUS_READY;
            $updates['end_job'] = $this->timezone->date(new \DateTime())->format('Y-m-d H:i:s');
            $this->jobRepository->updateUsingRawQuery($channel['first_waiting_job']['id'], $updates);

            return $dataNumber;

        }
        catch (\Exception $ex) {

            try {
                
                $updates = array();            
                $updates['status'] = IntegrationJobInterface::STATUS_WAITING;
                $this->jobRepository->updateUsingRawQuery($channel['first_waiting_job']['id'], $updates);
    
            }
            catch (\Exception $ex1) {
                throw new StateException(
                    __($ex1->getMessage())
                );    
            }
    
            throw new StateException(
                __($ex->getMessage())
            );

        }

    }
    

}