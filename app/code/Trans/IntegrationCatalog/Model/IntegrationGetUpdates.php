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
use Trans\Integration\Api\IntegrationCommonInterface;

use \Trans\Integration\Api\Data\IntegrationChannelInterface;
use \Trans\Integration\Api\Data\IntegrationChannelInterfaceFactory;
use Trans\Integration\Api\IntegrationChannelRepositoryInterface;

use \Trans\Integration\Api\Data\IntegrationChannelMethodInterface;
use \Trans\Integration\Api\Data\IntegrationChannelMethodInterfaceFactory;
use Trans\Integration\Api\IntegrationChannelMethodRepositoryInterface;


use Trans\IntegrationCatalog\Api\IntegrationJobRepositoryInterface;
use Trans\IntegrationCatalog\Api\Data\IntegrationJobInterface;
use Trans\IntegrationCatalog\Api\Data\IntegrationJobInterfaceFactory;

use Trans\IntegrationCatalog\Api\IntegrationDataValueRepositoryInterface;
use Trans\IntegrationCatalog\Api\Data\IntegrationDataValueInterface;
use Trans\IntegrationCatalog\Api\Data\IntegrationDataValueInterfaceFactory;

use Trans\IntegrationCatalog\Api\IntegrationGetUpdatesInterface;


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

            }else{
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
        if(isset($channel['jobs'])) {
            if($channel['jobs']->getBatchId() != $channel['waiting_jobs']->getBatchId()){
                // API date format / TBD format
                $dateFormat = date('Y-m-d H:i:s',strtotime($channel['jobs']->getLastUpdated()));
                // Greather than equal format param / TBD Format
                $paramGte = '$gt.';
                // Set last updated for API if not initial call
                $result[IntegrationChannelMethodInterface::QUERY_PARAMS]['modified_at'] = $paramGte . $dateFormat;
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
    public function save($channel=[],$data=[],$lastUpdated="")
    {
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
            if($data) {
                foreach($data as $row) {
                    $this->datavalueRepository->saveDataValue($row);
                }

                try {
                    $lastRow = end($data);
                    $dataValue = json_decode($lastRow['data_value'], true); 
                    $channel['waiting_jobs']->setLastUpdated($dataValue['modified_at']);
                } catch (\Exception $e) {
                }
            } else {
                throw new StateException(
                    __($ex->getMessage())
                );  
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
     * @param $channel
     * @return mixed
     * @throws StateException
     */
    public function getCompleteJobs($channel){

        if(!isset($channel['method'])){
            throw new StateException(
                __(IntegrationCommonInterface::MSG_METHOD_NOTAVAILABLE)
            );
        }

        try {
            $collection = $this->jobRepository->getByMdIdFirstItem($channel['method']->getId(),IntegrationJobInterface::STATUS_COMPLETE);

            if(!empty($collection)){
                $channel['jobs'] = $collection;

            }else{
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
     * @param $channel
     * @return mixed
     * @throws StateException
     */
    public function getCompleteDataValue($channel){
        if(!isset($channel['jobs']) || empty($channel['jobs'])){
            throw new StateException(
                __(IntegrationGetUpdatesInterface::MSG_JOBS_NOTAVAILABLE)
            );
        }

        $collection = $this->datavalueRepository->getByJobIdWithStatus($channel['jobs']->getId(),IntegrationDataValueInterface::STATUS_DATA_SUCCESS);
        if(!$collection->getSize()){
            throw new StateException(
                __(IntegrationGetUpdatesInterface::MSG_DATAVALUE_NOTAVAILABLE)
            );
        }

        return $collection;
    }

     /**
     * @param $channel
     * @return mixed
     * @throws StateException
     */
    public function setJobStatus($channel,$status="",$msg=""){
        if(!isset($channel['jobs']) || empty($channel['jobs'])){
            throw new StateException(
                __(IntegrationGetUpdatesInterface::MSG_JOBS_NOTAVAILABLE)
            );
        }
        $job = $this->jobRepository->getById($channel['jobs']->getId());
        $job->setStatus($status);
        if(!empty($msg)){
            $job->setMessages($msg);
        }
        $this->jobRepository->save($job);

        return $job;
    }


}