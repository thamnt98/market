<?php
/**
 * @category Trans
 * @package  Trans_Integration
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   J.P <jaka.pondan@ctcorpdigital.com>
 *
 * Copyright © 2019 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\Integration\Model;

use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\Exception\StateException;
use Magento\Framework\Exception\NoSuchEntityException;

use \Trans\Integration\Api\Data\IntegrationChannelInterface;
use \Trans\Integration\Api\Data\IntegrationChannelInterfaceFactory;
use Trans\Integration\Api\IntegrationChannelRepositoryInterface;

use \Trans\Integration\Api\Data\IntegrationChannelMethodInterface;
use \Trans\Integration\Api\Data\IntegrationChannelMethodInterfaceFactory;
use Trans\Integration\Api\IntegrationChannelMethodRepositoryInterface;

//use Trans\Integration\Api\IntegrationJobRepositoryInterface;
//use Trans\Integration\Api\Data\IntegrationJobInterface;
//use Trans\Integration\Api\Data\IntegrationJobInterfaceFactory;

use Trans\Integration\Api\IntegrationCommonInterface;
use Trans\Integration\Helper\Curl;

class IntegrationCommon implements IntegrationCommonInterface
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
     * IntegrationCommon constructor.
     * @param Curl $curl
     * @param IntegrationChannelRepositoryInterface $channelRepository
     * @param IntegrationChannelMethodRepositoryInterface $methodRepository
     */
    public function __construct(
        Curl $curl
        ,IntegrationChannelRepositoryInterface $channelRepository
        ,IntegrationChannelMethodRepositoryInterface $methodRepository
    ) {
        $this->curl=$curl;
        $this->channelRepository=$channelRepository;
        $this->methodRepository=$methodRepository;
    }

    /**
     * @param array $data
     * @throws StateException
     */
    protected function validateData($data=[])
    {
        if(!is_array($data)){
            throw new StateException(
                __(SELF::MSG_DATA_NOTAVAILABLE)
            );
        }
        $checkData = array_filter($data);
        if(empty($checkData)){
            throw new StateException(
                __(SELF::MSG_DATA_NOTAVAILABLE)
            );
        }
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
     * Prepare Channel
     * @param string $tag
     * @return array|mixed
     * @throws StateException
     */
    public function prepareChannel($tag="")
    {
        $result = [];
        try {
            $result['method'] = $this->methodRepository->getByStatusActive($tag);

            $result['channel'] = $this->channelRepository->getById($result['method']->getChId());

        } catch (\Exception $ex) {
            throw new StateException(
                __(self::MSG_CHANNEL_NOTAVAILABLE)
            );
        }

        return $result;
    }

    /**
     * Prepare Channel
     * @param string $tag
     * @return array|mixed
     * @throws StateException
     */
    public function prepareChannelMultiTag($tag=[])
    {
        $result = [];
        try {
            $result['method'] = [];
            $query = $this->methodRepository->getItemInTagByStatusActive($tag);
            $chId= 0;
            if($query){
                foreach($query as $row){
                    $result['method'][] = $row->getId();
                    $chId=$row->getChId();
                }
            }
            $result['channel'] = $this->channelRepository->getById($chId);

        } catch (\Exception $ex) {
            throw new StateException(
                __(self::MSG_CHANNEL_NOTAVAILABLE)
            );
        }

        return $result;
    }

    /**
     * Prepare Authentication
     * @param array $data
     * @return array|mixed
     * @throws StateException
     */
    public function prepareAuth($data=[])
    {
        try {
            $this->validateData($data);

        } catch (\Exception $ex) {
            throw new StateException(
                __($ex->getMessage())
            );
        }
        $result = [
            'auth_token'=> $this->curl->getCentralizeAuthToken(),
//            'auth_token'=> '068642aad6beb6d843f652cf0308be63',
            'data'      => $data
        ];
        return $result ;
    }

    /**
     * Prepare Request Param
     * @param array $channel
     * @param array $data
     * @return array|mixed
     * @throws StateException
     */
    public function prepareRequest($channel=[],$data=[])
    {

        try {
            $this->validateData($channel);
            $this->validateData($data);

        } catch (\Exception $ex) {
            throw new StateException(
                __($ex->getMessage())
            );
        }

        $result =[];

        try {

            if ($channel['method']->getId()) {

                $result['url'] = $channel['channel']->getUrl() . $channel['method']->getPath();
                $result['headers'] = [];
                $result['body'] = json_encode($data);

            }
        } catch (\Exception $ex) {

            throw new StateException(
                __($ex->getMessage())
            );
        }
        return $result;
    }


    /**
     * Get API
     * @param string $data
     * @return mixed|string
     * @throws StateException
     */
    public function get($data=""){

        $response = $this->curl->get(
            $data[IntegrationChannelInterface::URL] ,
            $data[IntegrationChannelMethodInterface::HEADERS],
            $data[IntegrationChannelMethodInterface::QUERY_PARAMS]
        );
        $response = $this->curl->jsonToArray($response);

        return $response;

    }


    /**
     * Post API
     * @param string $data
     * @return mixed|string
     * @throws StateException
     */
    public function post($data=""){

        $response = $this->curl->post(
            $data[IntegrationChannelInterface::URL] ,
            $data[IntegrationChannelMethodInterface::HEADERS],
            $data[IntegrationChannelMethodInterface::BODY]
        );
        $response = $this->curl->jsonToArray($response);

        return $response;

    }


    /**
     * @param array $data
     * @return array
     */
    public function doCallUsingRawQuery($data) 
    {

        try {

            if (!is_array($data) || empty($data)) {
                throw new StateException(__(
                    'Parameter Data are empty !'
                ));
            }
    
            $response = $this->curl->get(
                $data[IntegrationChannelInterface::URL] ,
                $data[IntegrationChannelMethodInterface::HEADERS],
                $data[IntegrationChannelMethodInterface::QUERY_PARAMS]
            );

            return $this->curl->jsonToArray($response);

        }
        catch (\Exception $ex) {
            throw new StateException(
                __($ex->getMessage())
            );
        }        

    }


    /**
     * @param string $tag
     * @return array
     */
    public function prepareChannelUsingRawQuery($tag)
    {                

        try {

            if (empty($tag)) {
                throw new StateException(__(
                    'Parameter Tag are empty !'
                ));
            }

            $result = [];

            $method = $this->methodRepository->getByStatusActiveUsingRawQuery($tag);
            if (!empty($method)) {
                $result['method'] = $method;
                $channel = $this->channelRepository->getByIdUsingRawQuery($result['method']['ch_id']);
                if (!empty($channel)) {
                    $result['channel'] = $channel;
                }
            }                        

            return $result;

        }
        catch (\Exception $ex) {
            throw new StateException(
                __($ex->getMessage())
            );
        }

    }


}