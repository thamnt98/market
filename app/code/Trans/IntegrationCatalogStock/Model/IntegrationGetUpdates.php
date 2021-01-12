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

use Trans\IntegrationCatalogStock\Api\IntegrationMonitoringStockRepositoryInterface;
use Trans\IntegrationCatalogStock\Api\IntegrationMonitoringStockRepositoryInterfaceFactory;

use Trans\Integration\Exception\WarningException;
use Trans\Integration\Exception\ErrorException;
use Trans\Integration\Exception\FatalException;


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
        Curl $curl,
        IntegrationChannelRepositoryInterface $channelRepository,
        IntegrationChannelMethodRepositoryInterface $methodRepository,
        IntegrationJobRepositoryInterface $jobRepository,
        IntegrationJobInterfaceFactory $jobFactory,
        IntegrationCommonInterface $commonRepository,
        IntegrationDataValueRepositoryInterface $datavalueRepository,
        IntegrationDataValueInterfaceFactory $datavalueInterface,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone
    ) {
        $this->curl = $curl;
        $this->channelRepository = $channelRepository;
        $this->methodRepository = $methodRepository;
        $this->jobRepository = $jobRepository;
        $this->jobFactory = $jobFactory;
        $this->commonRepository = $commonRepository;
        $this->datavalueRepository = $datavalueRepository;
        $this->datavalueInterface = $datavalueInterface;
        $this->timezone = $timezone;
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
                throw new ErrorException('parameter channel method in IntegrationGetUpdates.getFirstWaitingJobUsingRawQuery is empty !');
            }
    
            $job = $this->jobRepository->getFirstByMdIdStatusUsingRawQuery(
                $channel['method']['id'],
                IntegrationJobInterface::STATUS_WAITING
            );

            if (empty($job)) {
                throw new WarningException('first-waiting-job not-found then process aborted ...');
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
        catch (WarningException $ex) {
            throw $ex;
        }
        catch (ErrorException $ex) {
            throw $ex;
        }
        catch (FatalException $ex) {
            throw $ex;
        }        
        catch (\Exception $ex) {
            throw $ex;
        }

    }


    /**
     * @param array $channel
     * @return array
     */
    public function prepareCallUsingRawQuery($channel) {        

        try {
         
            if (empty($channel['method'])) {
                throw new ErrorException('parameter channel method in IntegrationGetUpdates.prepareCallUsingRawQuery is empty !');
            }
    
            if (empty($channel['channel'])) {
                throw new ErrorException('parameter channel in IntegrationGetUpdates.prepareCallUsingRawQuery is empty !');
            }
    
            $result = [];
    
            $result[IntegrationChannelInterface::URL] = $channel['channel']['url'] . $channel['method']['path'];
            $result[IntegrationChannelMethodInterface::HEADERS] = $this->curl->jsonToArray($channel['method']['headers']);
            $result[IntegrationChannelMethodInterface::QUERY_PARAMS] = $this->curl->jsonToArray($channel['method']['query_params']);        
            $result[IntegrationChannelMethodInterface::QUERY_PARAMS]['_limit'] = $channel['first_waiting_job']['limit'];
            $result[IntegrationChannelMethodInterface::QUERY_PARAMS]['_offset'] = $channel['first_waiting_job']['offset'];

            // Set _modified_at from last_complete_job_from_waiting_job if exists
            if (!empty($channel['last_complete_job_from_waiting_job']) && !empty($channel['last_complete_job_from_waiting_job']['last_updated'])) {
                $dateTime = new \DateTime($channel['last_complete_job_from_waiting_job']['last_updated']);
                $lastRetrievedAt = $dateTime->format('Y-m-d H:i:s.u');
            }
            // Set _modified_at from last_complete_job if exists
            else if (!empty($channel['last_complete_job']) && !empty($channel['last_complete_job']['last_updated'])) {
                $dateTime = new \DateTime($channel['last_complete_job']['last_updated']);
                $lastRetrievedAt = $dateTime->format('Y-m-d H:i:s.u');
            }
            else {
                $dateTime = new \DateTime();
                $dateTime->modify("-1 day");
                $lastRetrievedAt = $this->timezone->date($dateTime)->format('Y-m-d H:i:s.u');
            }

            $result[IntegrationChannelMethodInterface::QUERY_PARAMS]['_modified_at'] = $lastRetrievedAt;
    
            return $result;

        }
        catch (WarningException $ex) {
            throw $ex;
        }
        catch (ErrorException $ex) {
            throw $ex;
        }
        catch (FatalException $ex) {
            throw $ex;
        }        
        catch (\Exception $ex) {
            throw $ex;
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
                throw new WarningException('first-waiting-job in IntegrationGetUpdates.prepareStockDataUsingRawQuery not found then process aborted ...');
            }
    
            if (!is_array($response['data']) || empty($response['data'])) {
                $updates = [];
                $updates['status'] = IntegrationJobInterface::STATUS_COMPLETE_BUT_DATA_EMPTY;
                $this->jobRepository->updateUsingRawQuery($channel['first_waiting_job']['id'], $updates);

                throw new WarningException('stock-data empty!');
            }
            
            $return = [];

            $stockData = [];
            $stockDataCounter = -1;

            $monitoringStockSql = "";
            $monitoringStockWatcherSql = "";


            foreach ($response['data'] as $data) {

                $stockDataCounter++;

                $stockData[$stockDataCounter][IntegrationDataValueInterface::JOB_ID] = $channel['first_waiting_job']['id'];
                $stockData[$stockDataCounter][IntegrationDataValueInterface::DATA_VALUE] = json_encode($data);                            

                //retrieved_at
                $monitoringStockSql .= ",(sysdate(6)";

                //job_id
                $monitoringStockSql .= "," . $stockData[$stockDataCounter][IntegrationDataValueInterface::JOB_ID];

                //store_code
                if (!empty($data['location_code'])) {                    
                    $monitoringStockSql .= ",'" . $data['location_code'] . "'";
                }
                else {                    
                    $monitoringStockSql .= ",null";
                }

                //sku
                if (!empty($data['product_sku'])) {                    
                    $monitoringStockSql .= ",'" . $data['product_sku'] . "'";
                }
                else {                    
                    $monitoringStockSql .= ",null";
                }                

                //stock_id
                if (!empty($data['stock_id'])) {                    
                    $monitoringStockSql .= ",'" . $data['stock_id'] . "'";
                    $monitoringStockWatcherSql .= ",'" . $data['stock_id'] . "'";
                }
                else {                    
                    $monitoringStockSql .= ",null";
                }

                //stock_name
                if (!empty($data['stock_name'])) {
                    $monitoringStockSql .= ",'" . $data['stock_name'] . "'";
                }
                else {
                    $monitoringStockSql .= ",null";
                }

                //stock_filename
                if (!empty($data['stock_filename'])) {
                    $monitoringStockSql .= ",'" . $data['stock_filename'] . "'";
                }
                else {
                    $monitoringStockSql .= ",null";
                }

                //stock_action
                if (!empty($data['stock_action'])) {
                    $monitoringStockSql .= ",'" . $data['stock_action'] . "'";
                }
                else {
                    $monitoringStockSql .= ",null";
                }
                                
                $monitoringStockSql .= ")";

            }
            

            if ($stockDataCounter > -1) {

                $return['stock_data'] = $stockData;
                
                if (!empty($response['data'][$stockDataCounter]['updated_time'])) {
                    $return['stock_data_last_updated'] = $response['data'][$stockDataCounter]['updated_time'];
                }

                $return['stock_data_job_id'] = $channel['first_waiting_job']['id'];

            }

            if ($monitoringStockSql != "") {

                $monitoringStockSql = "insert ignore into `monitoring_stock` (`retrieved_at`, `job_id`, `store_code`, `sku`, `stock_id`, `stock_name`, `stock_filename`, `stock_action`) values " . substr($monitoringStockSql, 1);
                
                $monitoringStockWatcherSql = "insert ignore into `monitoring_stock_when_watcher_temp` (`id`, `when_at`, `when_type`, `store_code`, `sku`, `stock_id`) select sysdate(6), `retrieved_at`, 'retrieved', `store_code`, `sku`, `stock_id` from monitoring_stock where `stock_id` in (" . substr($monitoringStockWatcherSql, 1) . ")";
    
                $return['monitoring_stock_sql'] = [];
                $return['monitoring_stock_sql'][] = $monitoringStockSql;
                $return['monitoring_stock_sql'][] = $monitoringStockWatcherSql;                

            }


            unset($stockData);
            unset($monitoringStockSql);
            unset($monitoringStockWatcherSql);


            return $return;

        }
        catch (WarningException $ex) {
            throw $ex;
        }
        catch (ErrorException $ex) {
            throw $ex;
        }
        catch (FatalException $ex) {
            throw $ex;
        }        
        catch (\Exception $ex) {
            throw $ex;
        } 

    }
    

    /**
     * @param array $channel
     * @param array $data
     * @return int
     */
    public function insertStockDataUsingRawQuery($channel, $data) {

        if (empty($channel['first_waiting_job'])) {
            throw new WarningException('first-waiting-job in IntegrationGetUpdates.insertStockDataUsingRawQuery not found then process aborted ...');
        }

        if (!is_array($data) || empty($data)) {            
            throw new ErrorException('parameter data in IntegrationGetUpdates.insertStockDataUsingRawQuery is empty!');
        }

    
        try {

            $updates = [];
            $updates['status'] = IntegrationJobInterface::STATUS_PROGRESS_GET;
            $updates['start_job'] = $this->timezone->date(new \DateTime())->format('Y-m-d H:i:s.u');
            $this->jobRepository->updateUsingRawQuery($channel['first_waiting_job']['id'], $updates);

            return $this->datavalueRepository->insertBulkUsingRawQuery($data);

        }
        catch (WarningException $ex) {
            try {                
                $updates = [];
                $updates['status'] = IntegrationJobInterface::STATUS_WAITING;
                $this->jobRepository->updateUsingRawQuery($channel['first_waiting_job']['id'], $updates);
                throw $ex;
            }
            catch (\Exception $ex1) {
                throw $ex1;
            }            
        }
        catch (ErrorException $ex) {
            try {                
                $updates = [];            
                $updates['status'] = IntegrationJobInterface::STATUS_WAITING;
                $this->jobRepository->updateUsingRawQuery($channel['first_waiting_job']['id'], $updates);
                throw $ex;
            }
            catch (\Exception $ex1) {
                throw $ex1;
            }            
        }
        catch (FatalException $ex) {
            try {                
                $updates = [];            
                $updates['status'] = IntegrationJobInterface::STATUS_WAITING;
                $this->jobRepository->updateUsingRawQuery($channel['first_waiting_job']['id'], $updates);
                throw $ex;
            }
            catch (\Exception $ex1) {
                throw $ex1;
            }
        }
        catch (\Exception $ex) {
            try {                
                $updates = [];            
                $updates['status'] = IntegrationJobInterface::STATUS_WAITING;
                $this->jobRepository->updateUsingRawQuery($channel['first_waiting_job']['id'], $updates);
                throw $ex;
            }
            catch (\Exception $ex1) {
                throw $ex1;
            }
        }

    }
    

}