<?php
/**
 * @category Trans
 * @package  Trans_IntegrationCustomer
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   J.P <jaka.pondan@ctcorpdigital.com>
 *
 * Copyright © 2019 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\IntegrationCustomer\Model;

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


use Trans\IntegrationCustomer\Api\IntegrationJobRepositoryInterface;
use Trans\IntegrationCustomer\Api\Data\IntegrationJobInterface;
use Trans\IntegrationCustomer\Api\Data\IntegrationJobInterfaceFactory;

use Trans\IntegrationCustomer\Api\IntegrationDataValueRepositoryInterface;
use Trans\IntegrationCustomer\Api\Data\IntegrationDataValueInterface;
use Trans\IntegrationCustomer\Api\Data\IntegrationDataValueInterfaceFactory;

use Trans\IntegrationCustomer\Api\IntegrationGetUpdatesInterface;


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


    ) {
        $this->curl=$curl;
        $this->channelRepository=$channelRepository;
        $this->methodRepository=$methodRepository;
        $this->jobRepository=$jobRepository;
        $this->jobFactory=$jobFactory;
        $this->commonRepository=$commonRepository;
        $this->datavalueRepository=$datavalueRepository;
        $this->datavalueInterface=$datavalueInterface;


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
        if($channel['jobs']->getSize()){
//            print $channel['jobs']->getLastItem()->getId()."====".$channel['waiting_jobs']->getId();
            if($channel['jobs']->getLastItem()->getBatchId() != $channel['waiting_jobs']->getBatchId()){
                // API date format / TBD format
                $dateFormat = date('Y/m/d H:i:s',strtotime($channel['jobs']->getLastItem()->getLastUpdated()));
                // Greather than equal format param / TBD Format
                $paramGte = '$gte.';
                // Set last updated for API if not initial call
                $result[IntegrationChannelMethodInterface::QUERY_PARAMS]['modified_at'] =$paramGte.$dateFormat;
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
        $channel['waiting_jobs']->setStartJob(DATE('Y-m-d H:i:s'));
        $this->jobRepository->save($channel['waiting_jobs']);

        try{
            foreach($data as $row){

                $this->datavalueRepository->saveDataValue($row);

            }
        } catch (\Exception $ex) {
            throw new StateException(
                __($ex->getMessage())
            );
        }
        $channel['waiting_jobs']->setStatus(IntegrationJobInterface::STATUS_READY);
        $channel['waiting_jobs']->setEndJob(DATE('Y-m-d H:i:s'));
        $this->jobRepository->save($channel['waiting_jobs']);
        return "";

    }


}