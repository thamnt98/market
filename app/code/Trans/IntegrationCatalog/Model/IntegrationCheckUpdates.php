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

use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\Exception\StateException;
use Magento\Framework\Exception\NoSuchEntityException;

use Trans\Integration\Helper\Curl;

use \Trans\Integration\Api\Data\IntegrationChannelInterface;
use \Trans\Integration\Api\Data\IntegrationChannelInterfaceFactory;
use Trans\Integration\Api\IntegrationChannelRepositoryInterface;

use \Trans\Integration\Api\Data\IntegrationChannelMethodInterface;
use \Trans\Integration\Api\Data\IntegrationChannelMethodInterfaceFactory;
use Trans\Integration\Api\IntegrationChannelMethodRepositoryInterface;
use Trans\Integration\Api\IntegrationCommonInterface;

use Trans\IntegrationCatalog\Api\IntegrationJobRepositoryInterface;
use Trans\IntegrationCatalog\Api\IntegrationDataValueRepositoryInterface;
use Trans\IntegrationCatalog\Api\Data\IntegrationJobInterface;
use Trans\IntegrationCatalog\Api\Data\IntegrationJobInterfaceFactory;
use Trans\IntegrationCatalog\Api\IntegrationCheckUpdatesInterface;
use Trans\Integration\Logger\Logger;


class IntegrationCheckUpdates implements IntegrationCheckUpdatesInterface
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
     *  @var IntegrationDataValueRepositoryInterface
     */
    protected $datavalueRepository;

     /**
	 * @var Logger
	 */
	protected $logger;

    /**
     * IntegrationCheckUpdates constructor.
     * @param Curl $curl
     * @param IntegrationChannelRepositoryInterface $channelRepository
     * @param IntegrationChannelMethodRepositoryInterface $methodRepository
     * @param IntegrationJobRepositoryInterface $jobRepository
     * @param IntegrationJobInterfaceFactory $jobFactory
     * @param IntegrationCommonInterface $commonRepository
     */
    public function __construct(
        Curl $curl
        ,IntegrationChannelRepositoryInterface $channelRepository
        ,IntegrationChannelMethodRepositoryInterface $methodRepository
        ,IntegrationJobRepositoryInterface $jobRepository
        ,IntegrationJobInterfaceFactory $jobFactory
        ,IntegrationCommonInterface $commonRepository
        ,IntegrationDataValueRepositoryInterface $datavalueRepository
        ,Logger $logger

    ) {
        $this->curl=$curl;
        $this->channelRepository=$channelRepository;
        $this->methodRepository=$methodRepository;
        $this->jobRepository=$jobRepository;
        $this->jobFactory=$jobFactory;
        $this->commonRepository=$commonRepository;
        $this->datavalueRepository=$datavalueRepository;
        $this->logger = $logger;


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
            $collection = $this->jobRepository->getByMdIdWithStatus($channel['method']->getId(),IntegrationJobInterface::STATUS_WAITING);

            if($collection->getSize()){

                $date = $collection->getLastItem()->getLastUpdated();
                if(empty($date)){
                    throw new StateException(
                        __(IntegrationCommonInterface::MSG_UPDATE_ONPROGRESS)
                    );
                }

            }


        } catch (\Exception $ex) {
            throw new StateException(
                __($ex->getMessage())
            );
        }

        return $channel;
    }

    /**
     * @param $channel
     * @return mixed
     * @throws StateException
     */
    public function checkOnProgressJobs($channel){

        if(!isset($channel['method'])){
            throw new StateException(
                __(IntegrationCommonInterface::MSG_METHOD_NOTAVAILABLE)
            );
        }

        try {
            $collection = $this->jobRepository->getJobByMultiStatus($channel['method']->getId(),[IntegrationJobInterface::STATUS_WAITING,IntegrationJobInterface::STATUS_PROGRESS_CATEGORY,IntegrationJobInterface::STATUS_READY]);

            if($collection->getSize()){

                throw new StateException(
                    __(IntegrationCommonInterface::MSG_UPDATE_ONPROGRESS)
                );

            }


        } catch (\Exception $ex) {
            throw new StateException(
                __($ex->getMessage())
            );
        }

        return $channel;
    }

    /**
     * @param $channel
     * @return mixed
     * @throws StateException
     */
    public function checkSaveOnProgressJob($channel){

        if(!isset($channel['method'])){
            throw new StateException(
                __(IntegrationCommonInterface::MSG_METHOD_NOTAVAILABLE)
            );
        }

        try {
            $collection = $this->jobRepository->getByMdIdWithStatus($channel['method']->getId(),IntegrationJobInterface::STATUS_PROGRESS_CATEGORY);
           
            if($collection->getSize()){
                $data = $collection->getFirstItem();
                $totalHit = $this->retryCron($data);
            }


        } catch (\Exception $ex) {
            throw new StateException(
                __($ex->getMessage())
            );
        }

        return $channel;
    }

    /**
     * Retry Cron
     */
    protected function retryCron($instance){
        if(is_null($instance) || empty($instance)){
            $this->logger->error(__FUNCTION__." Error : Empty Job Instance");
            return false;
        }
        try{
            $hit = (int)$instance->getHit();
            $totalHit = $hit+1;
            $status = $instance->getStatus();

            if($totalHit>IntegrationJobInterface::MAX_HIT_ONPROGRESS){
                $status = IntegrationJobInterface::STATUS_READY;
                $this->logger->info(__FUNCTION__." TOTAL HIT : ".$totalHit ." MAX HIT ONPROGRESS : ".IntegrationJobInterface::MAX_HIT_ONPROGRESS);
            }
            if($totalHit>IntegrationJobInterface::MAX_HIT_RETRY){
                $status = IntegrationJobInterface::STATUS_UPDATE_RETRY_FAIL;
                $this->logger->info(__FUNCTION__." TOTAL HIT : ".$totalHit ." MAX HIT RETRY : ".IntegrationJobInterface::MAX_HIT_RETRY);
            }
            $instance->setHit($totalHit);
            // $instance->setStatus($status);
            $instance->setStatus(IntegrationJobInterface::STATUS_READY);

            $this->jobRepository->save($instance);
            if($status==IntegrationJobInterface::STATUS_READY){
                // Run Cron Ready To Retry
                return false;
            }
            if($status==IntegrationJobInterface::STATUS_UPDATE_RETRY_FAIL){
                // Set Cron Fail Maximum Retry
                throw new StateException(
                    __('Cannot Run Cron , Error To Many Retry !! Total Hit '.$totalHit)
                );
            }
        } catch (\Exception $ex) {
            throw new StateException(
                __($ex->getMessage())
            );
        }
        // Set Cron Fail Stil Onprogress
        throw new StateException(
            __('Cannot Run Cron , Save Product Still Onprogress! Total Hit ('.$totalHit.')')
           );
    }

    /**
     * Check Complete Jobs
     * @param $channel
     * @return mixed
     * @throws StateException
     */
    public function checkCompleteJobs($channel){

        if(!isset($channel['method'])){
            throw new StateException(
                __(IntegrationCommonInterface::MSG_METHOD_NOTAVAILABLE)
            );
        }

        try {
            // $channel['jobs'] = $this->jobRepository->getByMdIdWithStatus($channel['method']->getId(),IntegrationJobInterface::STATUS_COMPLETE);
            $channel['jobs'] = $this->jobRepository->getLastCompleteJobBatch($channel);
        } catch (\Exception $ex) {
            throw new StateException(
                __($ex->getMessage())
            );
        }

        return $channel;
    }

    /**
     * Check Complete Jobs
     * @param $channel
     * @return mixed
     * @throws StateException
     */
    public function checkReadyJobs($channel){

        if(!isset($channel['method'])){
            throw new StateException(
                __(IntegrationCommonInterface::MSG_METHOD_NOTAVAILABLE)
            );
        }

        try {

            $channel['jobs'] = $this->jobRepository->getByMdIdWithStatus($channel['method']->getId(),IntegrationJobInterface::STATUS_READY);

        } catch (\Exception $ex) {
            throw new StateException(
                __($ex->getMessage())
            );
        }

        return $channel;
    }
    /**
     * Check Complete Jobs
     * @param $channel
     * @return mixed
     * @throws StateException
     */
    public function checkMultiCompleteJobs($channel){

        if(!isset($channel['method'])){
            throw new StateException(
                __(IntegrationCommonInterface::MSG_METHOD_NOTAVAILABLE)
            );
        }

        try {

            $channel['jobs'] = $this->jobRepository->getByMdIdMultiWithStatus($channel['method'],IntegrationJobInterface::STATUS_READY);

        } catch (\Exception $ex) {
            throw new StateException(
                __($ex->getMessage())
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
        $result[IntegrationChannelMethodInterface::QUERY_PARAMS]['_count'] ='*';
        if(isset($channel['jobs'])){
            // API date format / TBD format
            $dateFormat = date('Y-m-d H:i:s',strtotime($channel['jobs']->getLastUpdated()));
            // $dateFormat = date('Y/m/d H:i:s',strtotime($channel['jobs']->getLastItem()->getLastUpdated()));
            // Greather than equal format param / TBD Format
            $paramGte = '$gt.';
            // Set last updated for API if not initial call
            $result[IntegrationChannelMethodInterface::QUERY_PARAMS]['modified_at'] =$paramGte.$dateFormat;
        }

        $result[IntegrationChannelMethodInterface::HEADERS]  = $this->curl->jsonToArray($channel['method']->getDataHeaders());
        return $result;
    }

    /**
     * Prepare parameter for API
     * @param array $channel
     * @return mixed
     * @throws StateException
     */
    public function prepareCallWithoutCount($channel=[]){
        $result[IntegrationChannelInterface::URL] = $channel['channel']->getUrl().$channel['method']->getDataPath();
        $result[IntegrationChannelMethodInterface::QUERY_PARAMS]  = $this->curl->jsonToArray($channel['method']->getQueryParams());
        // $result[IntegrationChannelMethodInterface::QUERY_PARAMS]['_count'] ='*';
        if(isset($channel['jobs'])){
            // API date format / TBD format
            $dateFormat = date('Y-m-d H:i:s',strtotime($channel['jobs']->getLastUpdated()));
            // Greather than equal format param / TBD Format
            $paramGte = '$gt.';
            // Set last updated for API if not initial call
            $result[IntegrationChannelMethodInterface::QUERY_PARAMS]['modified_at'] =$paramGte.$dateFormat;
        }
        $result[IntegrationChannelMethodInterface::HEADERS]  = $this->curl->jsonToArray($channel['method']->getDataHeaders());
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
    public function prepareJobsData($channel=[],$response=[]){
        $result = [];
        if(!isset($channel['method'])){
            throw new StateException(
                __(IntegrationCheckUpdatesInterface::MSG_CHANNEL_NOTAVAILABLE)
            );
        }

        if(!isset($response['data'])){
            throw new StateException(
                __(IntegrationCheckUpdatesInterface::MSG_RESP_NOTAVAILABLE)
            );
        }

        $method = $channel['method'];
        $data = $response['data'];
        if($data['count']<1){
            throw new StateException(
                __(IntegrationCheckUpdatesInterface::MSG_DATA_NOTAVAILABLE)
            );
        }

        $total  = $data['count'];

        $result[IntegrationJobInterface::TOTAL_DATA] = $total;
        $result[IntegrationJobInterface::METHOD_ID] = $method->getId();
        $result[IntegrationJobInterface::LIMIT] = (!empty($method->getLimits()))?$method->getLimits():IntegrationChannelMethodInterface::VAL_LIMIT;
        $result[IntegrationJobInterface::LAST_UPDATED] = NULL ;
        $result[IntegrationJobInterface::LAST_JB_ID] = isset($channel['jobs']) ? $channel['jobs']->getId() : null;
        $result['job'] = ceil( $result[IntegrationJobInterface::TOTAL_DATA]/ $result[IntegrationJobInterface::LIMIT]);
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
    public function prepareJobsDataProperResp($channel=[],$response=[]){

        $result = [];
        if(!isset($channel['method'])){
            throw new StateException(
                __(IntegrationCheckUpdatesInterface::MSG_CHANNEL_NOTAVAILABLE)
            );
        }

        if(!isset($response['data'])){
            throw new StateException(
                __(IntegrationCheckUpdatesInterface::MSG_RESP_NOTAVAILABLE)
            );
        }

        $method = $channel['method'];
        $data = $response['data'];
        if($data['count']<1){
            throw new StateException(
                __(IntegrationCheckUpdatesInterface::MSG_DATA_NOTAVAILABLE)
            );
        }

        $total  = $data['count'];

        $result[IntegrationJobInterface::TOTAL_DATA] = $total;
        $result[IntegrationJobInterface::METHOD_ID] = $method->getId();
        $result[IntegrationJobInterface::LIMIT] = (!empty($method->getLimits()))?$method->getLimits():IntegrationChannelMethodInterface::VAL_LIMIT;
        $result[IntegrationJobInterface::LAST_UPDATED] = NULL ;
        $result[IntegrationJobInterface::LAST_JB_ID] = isset($channel['jobs']) ? $channel['jobs']->getId() : null;;
        $result['job'] = ceil( $result[IntegrationJobInterface::TOTAL_DATA]/ $result[IntegrationJobInterface::LIMIT]);
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
    public function prepareJobsDataProperRespWithoutCount($channel=[],$response=[]){
        $result = [];
        if(!isset($channel['method'])){
            throw new StateException(
                __(IntegrationCheckUpdatesInterface::MSG_CHANNEL_NOTAVAILABLE)
            );
        }
        if(!isset($response['data'])){
            throw new StateException(
                __(IntegrationCheckUpdatesInterface::MSG_RESP_NOTAVAILABLE)
            );
        }
        $method = $channel['method'];
        $data = $response['data'];
        if(count($data)<1){
            throw new StateException(
                __(IntegrationCheckUpdatesInterface::MSG_DATA_NOTAVAILABLE)
            );
        }
        try{
            $value = 0;
            for($i=0;$i<count($data);$i++){
                $value += $this->checkDataValue(json_encode($data[$i]));
            }
            if($value>0){
                throw new StateException(
                    __('Response Data Value already Exist')
                );
            }

        } catch (\Exception $ex) {
            throw new StateException(
                __($ex->getMessage())
            );
        }

        $total  = count($data);

        $result[IntegrationJobInterface::TOTAL_DATA] = $total;
        $result[IntegrationJobInterface::METHOD_ID] = $method->getId();
        $result[IntegrationJobInterface::LIMIT] = (!empty($method->getLimits()))?$method->getLimits():IntegrationChannelMethodInterface::VAL_LIMIT;
        $result[IntegrationJobInterface::LAST_UPDATED] = NULL ;
        $result[IntegrationJobInterface::LAST_JB_ID] = isset($channel['jobs']) ? $channel['jobs']->getId() : null;;
        $result['job'] = ceil( $result[IntegrationJobInterface::TOTAL_DATA]/ $result[IntegrationJobInterface::LIMIT]);
        return $result;
    }

    public function checkDataValue($dataValue){
        $result = 0;
        $collection = $this->datavalueRepository->getByDataValueWithStatus($dataValue,1);
        if($collection->getSize()){
            $getLastCollection = $collection->getFirstItem();
            $result = $collection->getSize();
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
    public function prepareJobsDataImsStockUpdate($channel=[],$response=[]){

        $result = [];
        if(!isset($channel['method'])){
            throw new StateException(
                __(IntegrationCheckUpdatesInterface::MSG_CHANNEL_NOTAVAILABLE)
            );
        }

        if(!isset($response['data'])){
            throw new StateException(
                __(IntegrationCheckUpdatesInterface::MSG_RESP_NOTAVAILABLE)
            );
        }

        $method = $channel['method'];
        $data = $response['data'];
        if($data['count']<1){
            throw new StateException(
                __(IntegrationCheckUpdatesInterface::MSG_DATA_NOTAVAILABLE)
            );
        }

        $total  = $data['count'];

        $result[IntegrationJobInterface::TOTAL_DATA] = $total;
        $result[IntegrationJobInterface::METHOD_ID] = $method->getId();
        $result[IntegrationJobInterface::LIMIT] = (!empty($method->getLimits()))?$method->getLimits():IntegrationChannelMethodInterface::VAL_LIMIT;
        $result[IntegrationJobInterface::LAST_JB_ID] = isset($channel['jobs']) ? $channel['jobs']->getId() : null;;
        $result[IntegrationJobInterface::LAST_UPDATED] = NULL ;
        $result['job'] = 0;
        return $result;
    }


    /**
     * Save
     * @param array $data
     * @return array|mixed
     * @throws StateException
     */
    public function save($data=[]){


        if(!isset($data['job'])){
            throw new StateException(
                __(IntegrationCheckUpdatesInterface::MSG_DATA_NOTAVAILABLE)
            );
        }
        if($data['job']<1){
            throw new StateException(
                __(IntegrationCheckUpdatesInterface::MSG_DATA_NOTAVAILABLE)
            );
        }
        $result = [];
        $no = 1;
        $batchId = uniqid();
        for($i=0;$i<$data['job'];$i++){
            $result[$i][IntegrationJobInterface::METHOD_ID]=$data[IntegrationJobInterface::METHOD_ID];
            $result[$i][IntegrationJobInterface::LAST_UPDATED]=$data[IntegrationJobInterface::LAST_UPDATED];

            $result[$i][IntegrationJobInterface::TOTAL_DATA]=$data[IntegrationJobInterface::TOTAL_DATA];
            $result[$i][IntegrationJobInterface::LIMIT]=$data[IntegrationJobInterface::LIMIT];

            $result[$i][IntegrationJobInterface::BATCH_ID]=$batchId;
            $result[$i][IntegrationJobInterface::LAST_JB_ID]=$data[IntegrationJobInterface::LAST_JB_ID];
            $result[$i][IntegrationJobInterface::OFFSET]=$no;
            $this->jobRepository->saveJobs($result[$i]);
            $no++;
        }
        return $result;

    }

    /**
     * Save Proper Offset
     * @param array $data
     * @return array|mixed
     * @throws StateException
     */
    public function saveProperOffset($data=[]){


        if(!isset($data['job'])){
            throw new StateException(
                __(IntegrationCheckUpdatesInterface::MSG_DATA_NOTAVAILABLE)
            );
        }
        if($data['job']<1){
            throw new StateException(
                __(IntegrationCheckUpdatesInterface::MSG_DATA_NOTAVAILABLE)
            );
        }
        $result = [];
        $no = 1;
        $batchId = uniqid();
        for($i=0;$i<$data['job'];$i++){
            $result[$i][IntegrationJobInterface::METHOD_ID]=$data[IntegrationJobInterface::METHOD_ID];
            $result[$i][IntegrationJobInterface::LAST_UPDATED]=$data[IntegrationJobInterface::LAST_UPDATED];

            $result[$i][IntegrationJobInterface::TOTAL_DATA]=$data[IntegrationJobInterface::TOTAL_DATA];
            $result[$i][IntegrationJobInterface::LIMIT]=$data[IntegrationJobInterface::LIMIT];

            $result[$i][IntegrationJobInterface::BATCH_ID]=$batchId;
            $result[$i][IntegrationJobInterface::LAST_JB_ID]=$data[IntegrationJobInterface::LAST_JB_ID];
            $result[$i][IntegrationJobInterface::OFFSET]=$result[$i][IntegrationJobInterface::LIMIT]*($no-1);
            $this->jobRepository->saveJobs($result[$i]);
            $no++;
        }
        return $result;

    }




}