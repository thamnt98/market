<?php
/**
 * @category Trans
 * @package  Trans_IntegrationEntity
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   J.P <jaka.pondan@ctcorpdigital.com>
 *
 * Copyright © 2019 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\IntegrationEntity\Model;


use Magento\Framework\Exception\StateException;

use Trans\IntegrationEntity\Api\IntegrationInventoryRepositoryInterface;
use \Trans\Integration\Api\Data\IntegrationChannelInterface;
use \Trans\Integration\Api\Data\IntegrationChannelMethodInterface;
use \Trans\IntegrationEntity\Api\Data\IntegrationJobInterface;
use \Trans\IntegrationEntity\Api\IntegrationJobRepositoryInterface;
use \Trans\IntegrationEntity\Api\IntegrationDataValueRepositoryInterface;
use \Trans\IntegrationEntity\Api\Data\IntegrationDataValueInterface;

use \Trans\Integration\Helper\Curl;

class IntegrationInventoryRepository implements IntegrationInventoryRepositoryInterface
{

    /**
     * @var Curl Zend Client
     */
    protected $curl;
    /**
     * @var IntegrationJobRepositoryInterface
     */
    protected $jobRepository;

//    /**
//     * @var IntegrationDataValueRepositoryInterface
//     */
//    protected $datavalueRepository;

    public function __construct(
        Curl $curl
        ,IntegrationJobRepositoryInterface $jobRepository
        ,IntegrationDataValueRepositoryInterface $datavalueRepository
    ){
        $this->curl=$curl;
        $this->jobRepository=$jobRepository;
        $this->datavalueRepository=$datavalueRepository;
    }

    /**
     * @param array $channel
     * @return mixed
     * @throws StateException
     */
    public function prepareRequest($channel=[]){

        $result[IntegrationChannelInterface::URL] = $channel['channel']->getUrl().$channel['method']->getDataPath();
        $result[IntegrationChannelMethodInterface::QUERY_PARAMS]  = $this->curl->jsonToArray($channel['method']->getQueryParams());

        // Set Limit
        $result[IntegrationChannelMethodInterface::QUERY_PARAMS]['_limit'] =100;

        // Set offset
        $result[IntegrationChannelMethodInterface::QUERY_PARAMS]['_offset'] =1;

        $result[IntegrationChannelMethodInterface::HEADERS]  = $this->curl->jsonToArray($channel['method']->getDataHeaders());
        return $result;
    }

    /**
     * Prepare Job
     * @param array $channel
     * @return array
     * @throws StateException
     */
    public function saveJob($channel=[],$response=[]){
        $result = [];
        if(!isset($channel['method'])){
            throw new StateException(
                __('Channel Not Available')
            );
        }

        if(!isset($response['data'])){
            throw new StateException(
                __('Response Data is Not Available')
            );
        }

        if(!isset($response['data']['locations'])){
            throw new StateException(
                __('Response Data Store Location is Not Available')
            );
        }
        try {
            $method = $channel['method'];
            $batchId = uniqid();
            $total = count($response['data']['locations']);

            $result[IntegrationJobInterface::TOTAL_DATA] =$total;
            $result[IntegrationJobInterface::METHOD_ID] = $method->getId();
            $result[IntegrationJobInterface::LIMIT] =$total;
            $result[IntegrationJobInterface::LAST_UPDATED] = NULL ;
            $result[IntegrationJobInterface::BATCH_ID]=$batchId;
            $result[IntegrationJobInterface::STATUS]=IntegrationJobInterface::STATUS_READY;
            $result[IntegrationJobInterface::OFFSET]=null;
            $job = $this->jobRepository->saveJobs($result);

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
    public function saveData($job,$response){

        if(!$job->getId()){
            throw new StateException(
                __('Job is Not Available')
            );
        }

        $stores = $response['data']['locations'];
        $data = [];
        $i=0;
        foreach($stores as $row){
            $data[$i][IntegrationDataValueInterface::JOB_ID] = $job->getId();
            $data[$i][IntegrationDataValueInterface::DATA_VALUE] = json_encode($row);
            $this->datavalueRepository->saveDataValue($data[$i]);
            $i++;
        }

        return $response;
    }


    public function getData(){

        if(!$job->getId()){
            throw new StateException(
                __('Job is Not Available')
            );
        }

        $stores = $response['data']['locations'];
        $data = [];
        $i=0;
        foreach($stores as $row){
            $data[$i][IntegrationDataValueInterface::JOB_ID] = $job->getId();
            $data[$i][IntegrationDataValueInterface::DATA_VALUE] = json_encode($row);
            $this->datavalueRepository->saveDataValue($data[$i]);
            $i++;
        }

        return $response;
    }



}